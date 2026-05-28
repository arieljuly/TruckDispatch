#!/usr/bin/env python
"""
Train the fuel prediction model using datasets from Datasets folder
Run with: python train_model.py
"""

import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error, r2_score, mean_squared_error
import joblib
import os
import sys

def load_and_prepare_data(datasets_path="Datasets"):
    """Load and prepare data from CSV files"""
    
    print("="*60)
    print("TRAINING FUEL PREDICTION MODEL")
    print("="*60)
    
    # Define file paths
    trips_path = os.path.join(datasets_path, 'trips.csv')
    events_path = os.path.join(datasets_path, 'delivery_events.csv')
    trucks_path = os.path.join(datasets_path, 'trucks.csv')
    routes_path = os.path.join(datasets_path, 'routes.csv')
    facilities_path = os.path.join(datasets_path, 'facilities.csv')
    
    # Check if files exist
    if not os.path.exists(trips_path):
        print(f"\n❌ Error: trips.csv not found in {datasets_path}")
        print("Creating sample data for training...")
        return create_sample_data()
    
    print(f"\n📂 Loading data from {datasets_path}")
    
    # Load trips
    trips = pd.read_csv(trips_path)
    print(f"  ✅ Loaded trips.csv: {len(trips)} records")
    
    # Load events if exists
    if os.path.exists(events_path):
        events = pd.read_csv(events_path)
        print(f"  ✅ Loaded delivery_events.csv: {len(events)} records")
        
        # Aggregate events
        event_summary = events.groupby('trip_id').agg({
            'detention_minutes': 'sum',
            'on_time_flag': 'min'
        }).reset_index()
        
        df = trips.merge(event_summary, on='trip_id', how='left')
    else:
        print(f"  ⚠️ delivery_events.csv not found, using default values")
        df = trips.copy()
        df['detention_minutes'] = 0
        df['on_time_flag'] = 1
    
    # Load trucks if exists
    if os.path.exists(trucks_path):
        trucks = pd.read_csv(trucks_path)
        print(f"  ✅ Loaded trucks.csv: {len(trucks)} records")
        
        if 'truck_id' in df.columns and 'truck_id' in trucks.columns:
            if 'avg_fuel_efficiency' in trucks.columns:
                df = df.merge(trucks[['truck_id', 'avg_fuel_efficiency']], on='truck_id', how='left')
    
    # Convert units
    print("\n📊 Processing data...")
    
    if 'actual_distance_miles' in df.columns:
        df['distance_km'] = df['actual_distance_miles'] * 1.60934
    elif 'distance_km' not in df.columns:
        df['distance_km'] = df.get('distance', 100)
    
    if 'fuel_gallons_used' in df.columns:
        df['fuel_used_liters'] = df['fuel_gallons_used'] * 3.78541
    elif 'fuel_used_liters' not in df.columns:
        df['fuel_used_liters'] = df['distance_km'] / 6.0
    
    # Fill missing values
    df['actual_duration_hours'] = df.get('actual_duration_hours', df['distance_km'] / 50)
    df['idle_time_hours'] = df.get('idle_time_hours', 0)
    df['detention_minutes'] = df.get('detention_minutes', 0).fillna(0)
    df['delay_minutes'] = df.get('delay_minutes', 0).fillna(0)
    df['on_time_flag'] = df.get('on_time_flag', 1).fillna(1).astype(int)
    df['average_mpg'] = df.get('average_mpg', 6.0)
    
    # Use truck efficiency if available
    if 'avg_fuel_efficiency' in df.columns:
        df['average_mpg'] = df['avg_fuel_efficiency'].fillna(df['average_mpg'])
    
    # Drop rows with missing target
    df = df.dropna(subset=['fuel_used_liters'])
    
    print(f"  ✅ Final dataset: {len(df)} records")
    
    return df

def create_sample_data():
    """Create sample data for training"""
    print("\n📊 Creating sample training data...")
    
    np.random.seed(42)
    n_samples = 1000
    
    data = {
        'distance_km': np.random.uniform(10, 500, n_samples),
        'actual_duration_hours': np.random.uniform(0.5, 10, n_samples),
        'average_mpg': np.random.uniform(4, 12, n_samples),
        'idle_time_hours': np.random.exponential(0.5, n_samples),
        'detention_minutes': np.random.poisson(10, n_samples),
        'delay_minutes': np.random.poisson(5, n_samples),
        'on_time_flag': np.random.choice([0, 1], n_samples, p=[0.2, 0.8]),
    }
    
    # Calculate fuel used based on features
    data['fuel_used_liters'] = (
        data['distance_km'] / data['average_mpg'] +
        data['idle_time_hours'] * 2 +
        data['detention_minutes'] / 60 * 1.5 +
        data['delay_minutes'] / 60 * 2.5
    ) * np.where(data['on_time_flag'] == 1, 0.95, 1.10)
    
    # Add noise
    data['fuel_used_liters'] = data['fuel_used_liters'] * np.random.uniform(0.9, 1.1, n_samples)
    
    df = pd.DataFrame(data)
    print(f"  ✅ Created {len(df)} sample records")
    
    return df

def train_model(df):
    """Train the Random Forest model"""
    
    # Features
    features = [
        'distance_km',
        'actual_duration_hours',
        'average_mpg',
        'idle_time_hours',
        'detention_minutes',
        'delay_minutes',
        'on_time_flag'
    ]
    
    target = 'fuel_used_liters'
    
    # Prepare data
    X = df[features]
    y = df[target]
    
    # Split data
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42
    )
    
    print(f"\n📈 Training data: {len(X_train)} samples")
    print(f"📊 Testing data: {len(X_test)} samples")
    
    # Train model
    print("\n🎯 Training Random Forest Regressor...")
    
    model = RandomForestRegressor(
        n_estimators=100,
        max_depth=15,
        min_samples_split=10,
        min_samples_leaf=5,
        random_state=42,
        n_jobs=-1,
        verbose=1
    )
    
    model.fit(X_train, y_train)
    
    # Evaluate
    y_pred = model.predict(X_test)
    
    mae = mean_absolute_error(y_test, y_pred)
    rmse = np.sqrt(mean_squared_error(y_test, y_pred))
    r2 = r2_score(y_test, y_pred)
    
    print("\n" + "="*60)
    print("MODEL PERFORMANCE")
    print("="*60)
    print(f"✅ Mean Absolute Error (MAE): {mae:.2f} liters")
    print(f"✅ Root Mean Square Error (RMSE): {rmse:.2f} liters")
    print(f"✅ R² Score: {r2:.4f}")
    
    # Feature importance
    print("\n📈 Feature Importance:")
    importance = dict(zip(features, model.feature_importances_))
    for feature, imp in sorted(importance.items(), key=lambda x: x[1], reverse=True):
        bars = "█" * int(imp * 50)
        print(f"   {feature:25s}: {imp:.4f} ({imp*100:.1f}%) {bars}")
    
    return model, features

def save_model(model, features):
    """Save the trained model"""
    
    model_path = 'fuel_model.pkl'
    features_path = 'model_features.pkl'
    
    joblib.dump(model, model_path)
    joblib.dump(features, features_path)
    
    model_size = os.path.getsize(model_path) / 1024 / 1024
    
    print(f"\n💾 Model saved to: {model_path}")
    print(f"   Size: {model_size:.2f} MB")
    print(f"📋 Features saved to: {features_path}")
    
    return model_path

def test_predictions(model, features):
    """Test the model with sample predictions"""
    
    print("\n" + "="*60)
    print("TESTING MODEL PREDICTIONS")
    print("="*60)
    
    test_cases = [
        {
            "name": "Normal trip",
            "features": [100, 2, 6.0, 0, 0, 0, 1],
            "description": "100km, 2hrs, on-time"
        },
        {
            "name": "With idle time",
            "features": [100, 2, 6.0, 1, 0, 0, 1],
            "description": "100km, 2hrs, 1hr idle"
        },
        {
            "name": "With detention",
            "features": [100, 2, 6.0, 0, 30, 0, 1],
            "description": "100km, 2hrs, 30min detention"
        },
        {
            "name": "With delay",
            "features": [100, 2, 6.0, 0, 0, 15, 1],
            "description": "100km, 2hrs, 15min delay"
        },
        {
            "name": "Late delivery",
            "features": [100, 2, 6.0, 0, 0, 0, 0],
            "description": "100km, 2hrs, late"
        },
        {
            "name": "Long distance",
            "features": [300, 6, 8.0, 0.5, 10, 5, 1],
            "description": "300km, 6hrs, efficient truck"
        }
    ]
    
    for test in test_cases:
        prediction = model.predict([test['features']])[0]
        print(f"\n🧪 {test['name']}: {test['description']}")
        print(f"   → Predicted fuel: {prediction:.2f} liters")

def main():
    """Main training function"""
    
    # Load or create data
    df = load_and_prepare_data()
    
    if df.empty:
        print("\n❌ No data available for training")
        sys.exit(1)
    
    # Train model
    model, features = train_model(df)
    
    # Save model
    save_model(model, features)
    
    # Test predictions
    test_predictions(model, features)
    
    print("\n" + "="*60)
    print("✅ TRAINING COMPLETE! Model is ready for use.")
    print("="*60)
    print("\nTo use the model, start the API: python api.py")
    print("Then call POST /api/v2/predict/fuel endpoint")

if __name__ == "__main__":
    main()