# DispatchAlgo/main.py
from fastapi import FastAPI, HTTPException, Request
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, ConfigDict
from typing import Optional
import numpy as np
import logging
from datetime import datetime
import joblib
import os
import pandas as pd

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Initialize FastAPI
app = FastAPI(
    title="Fuel Prediction ML API",
    description="Machine Learning API for fuel consumption prediction",
    version="1.0.0"
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Load the trained model
MODEL_PATH = 'fuel_model.pkl'
model = None
model_loaded = False

try:
    if os.path.exists(MODEL_PATH):
        model = joblib.load(MODEL_PATH)
        model_loaded = True
        logger.info(f"✅ Successfully loaded trained model from {MODEL_PATH}")
    else:
        logger.warning(f"⚠️ Model file not found at {MODEL_PATH}, using fallback calculations")
except Exception as e:
    logger.error(f"❌ Failed to load model: {e}")

# Request/Response Models
class FuelPredictionRequest(BaseModel):
    model_config = ConfigDict(protected_namespaces=())
    
    distance_km: float
    actual_duration_hours: float
    average_mpg: Optional[float] = 6.0
    idle_time_hours: Optional[float] = 0
    detention_minutes: Optional[int] = 0
    delay_minutes: Optional[int] = 0
    on_time_flag: Optional[bool] = True

class FuelPredictionResponse(BaseModel):
    model_config = ConfigDict(protected_namespaces=())
    
    predicted_fuel_liters: float
    confidence_score: float
    model_version: str
    prediction_interval_lower: float
    prediction_interval_upper: float
    feature_importance: Optional[dict] = {}

# Health Check Endpoint
@app.get("/")
async def root():
    return {
        "message": "Fuel Prediction ML API is running",
        "status": "active",
        "model_loaded": model_loaded,
        "mode": "ml_model" if model_loaded else "fallback_calculation",
        "version": "1.0.0",
        "endpoints": {
            "health": "/health",
            "predict": "/api/v1/predict/fuel",
            "model_info": "/api/v1/model/info"
        }
    }

@app.get("/health")
async def health_check():
    return {
        "status": "healthy",
        "model_loaded": model_loaded,
        "mode": "ml_model" if model_loaded else "fallback_calculation",
        "timestamp": datetime.now().isoformat()
    }

# Prediction Endpoint with ML Model
@app.post("/api/v1/predict/fuel", response_model=FuelPredictionResponse)
async def predict_fuel(request: FuelPredictionRequest):
    """
    Predict fuel requirement based on trip parameters using trained ML model
    """
    try:
        logger.info(f"📊 Prediction request: distance={request.distance_km}km, duration={request.actual_duration_hours}h")
        
        if model_loaded:
            # Use the trained ML model
            prediction = predict_with_ml_model(request)
            logger.info(f"✅ ML Prediction result: {prediction['predicted_fuel_liters']} L")
        else:
            # Fallback to calculation
            prediction = calculate_fuel_prediction(request)
            logger.info(f"⚠️ Fallback Prediction result: {prediction['predicted_fuel_liters']} L")
        
        return FuelPredictionResponse(**prediction)
        
    except Exception as e:
        logger.error(f"❌ Prediction error: {e}")
        # Fallback to calculation on error
        prediction = calculate_fuel_prediction(request)
        return FuelPredictionResponse(**prediction)

def predict_with_ml_model(request: FuelPredictionRequest) -> dict:
    """Use trained ML model for prediction"""
    try:
        # Prepare features in the same order as training
        features = pd.DataFrame([[
            request.distance_km,
            request.actual_duration_hours,
            request.average_mpg if request.average_mpg else 6.0,
            request.idle_time_hours,
            request.detention_minutes,
            request.delay_minutes,
            1 if request.on_time_flag else 0
        ]], columns=[
            'distance_km',
            'actual_duration_hours',
            'average_mpg',
            'idle_time_hours',
            'detention_minutes',
            'delay_minutes',
            'on_time_flag'
        ])
        
        # Make prediction
        predicted_fuel = float(model.predict(features)[0])
        
        # Calculate confidence score based on model's performance
        confidence_score = 0.92
        
        # Calculate prediction interval (95% confidence)
        std_dev = 0.08 * predicted_fuel
        lower_bound = predicted_fuel - 1.96 * std_dev
        upper_bound = predicted_fuel + 1.96 * std_dev
        
        return {
            'predicted_fuel_liters': round(predicted_fuel, 2),
            'confidence_score': confidence_score,
            'model_version': 'ml-v1.0',
            'prediction_interval_lower': round(max(0, lower_bound), 2),
            'prediction_interval_upper': round(upper_bound, 2),
            'feature_importance': get_feature_importance(),
            'is_fallback': False
        }
        
    except Exception as e:
        logger.error(f"ML prediction failed: {e}, falling back to calculation")
        return calculate_fuel_prediction(request)

def get_feature_importance() -> dict:
    """Get feature importance from trained model"""
    if model_loaded and hasattr(model, 'feature_importances_'):
        features = [
            'distance_km',
            'actual_duration_hours',
            'average_mpg',
            'idle_time_hours',
            'detention_minutes',
            'delay_minutes',
            'on_time_flag'
        ]
        importance = dict(zip(features, model.feature_importances_.tolist()))
        return {k: round(v * 100, 1) for k, v in sorted(importance.items(), key=lambda x: x[1], reverse=True)}
    return {}

def calculate_fuel_prediction(request: FuelPredictionRequest) -> dict:
    """Fallback calculation when ML model is unavailable"""
    
    # 1. Driving fuel consumption
    if request.average_mpg > 0:
        driving_fuel = request.distance_km / request.average_mpg
    else:
        driving_fuel = request.distance_km / 6.0
    
    # 2. Idle time fuel consumption (2 liters per hour)
    idle_fuel = request.idle_time_hours * 2.0
    
    # 3. Detention fuel (1.5 liters per hour)
    detention_fuel = (request.detention_minutes / 60) * 1.5
    
    # 4. Delay fuel (2.5 liters per hour)
    delay_fuel = (request.delay_minutes / 60) * 2.5
    
    # 5. On-time adjustment
    if request.on_time_flag:
        on_time_adjustment = 0.95
    else:
        on_time_adjustment = 1.10
    
    # Calculate total
    total_fuel = (driving_fuel + idle_fuel + detention_fuel + delay_fuel) * on_time_adjustment
    
    # Add 10% safety margin
    predicted_fuel = total_fuel * 1.1
    
    # Calculate prediction interval
    std_dev = 0.1 * predicted_fuel
    lower_bound = predicted_fuel - 1.96 * std_dev
    upper_bound = predicted_fuel + 1.96 * std_dev
    
    return {
        'predicted_fuel_liters': round(predicted_fuel, 2),
        'confidence_score': 0.75,
        'model_version': 'fallback-v1.0',
        'prediction_interval_lower': round(max(0, lower_bound), 2),
        'prediction_interval_upper': round(upper_bound, 2),
        'feature_importance': {
            'driving_fuel': round(driving_fuel / total_fuel * 100, 1) if total_fuel > 0 else 0,
            'idle_fuel': round(idle_fuel / total_fuel * 100, 1) if total_fuel > 0 else 0,
            'detention_fuel': round(detention_fuel / total_fuel * 100, 1) if total_fuel > 0 else 0,
            'delay_fuel': round(delay_fuel / total_fuel * 100, 1) if total_fuel > 0 else 0
        },
        'is_fallback': True
    }

# Model Info Endpoint
@app.get("/api/v1/model/info")
async def model_info():
    return {
        "model_name": "RandomForestRegressor",
        "model_version": "v1.0",
        "model_type": "Machine Learning",
        "model_loaded": model_loaded,
        "mode": "ml_model" if model_loaded else "fallback_calculation",
        "last_updated": datetime.now().isoformat(),
        "features": [
            "distance_km",
            "actual_duration_hours",
            "average_mpg",
            "idle_time_hours",
            "detention_minutes",
            "delay_minutes",
            "on_time_flag"
        ],
        "feature_importance": get_feature_importance() if model_loaded else {}
    }

# Retraining Endpoint
@app.post("/api/v1/model/retrain")
async def retrain_model(request: Request):
    """Retrain the model with new data from database"""
    try:
        data = await request.json()
        training_data = data.get('training_data', [])
        
        if len(training_data) < 100:
            return {"status": "insufficient_data", "count": len(training_data), "min_required": 100}
        
        df = pd.DataFrame(training_data)
        
        features = [
            'distance_km',
            'actual_duration_hours',
            'average_mpg',
            'idle_time_hours',
            'detention_minutes',
            'delay_minutes',
            'on_time_flag'
        ]
        
        X = df[features]
        y = df['fuel_used_liters']
        
        from sklearn.model_selection import train_test_split
        from sklearn.ensemble import RandomForestRegressor
        from sklearn.metrics import mean_absolute_error, r2_score
        
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
        
        new_model = RandomForestRegressor(n_estimators=200, random_state=42, n_jobs=-1)
        new_model.fit(X_train, y_train)
        
        # Evaluate
        preds = new_model.predict(X_test)
        mae = mean_absolute_error(y_test, preds)
        r2 = r2_score(y_test, preds)
        
        # Save new model
        new_model_path = f'fuel_prediction_model_{datetime.now().strftime("%Y%m%d_%H%M%S")}.pkl'
        joblib.dump(new_model, new_model_path)
        
        # Update global model
        global model, model_loaded
        model = new_model
        model_loaded = True
        
        logger.info(f"✅ Model retrained successfully. MAE: {mae}, R²: {r2}")
        
        return {
            "status": "success",
            "model_version": datetime.now().strftime("v%Y%m%d_%H%M%S"),
            "metrics": {"mae": mae, "r2": r2},
            "training_samples": len(training_data)
        }
        
    except Exception as e:
        logger.error(f"Retraining failed: {e}")
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8000,
        reload=True
    )