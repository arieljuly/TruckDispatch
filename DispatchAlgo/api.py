"""
Truck Dispatch Optimization API - Standalone Python Application
Reads data from Datasets folder
Implements Greedy Algorithm + Rule-Based Validation
"""

from fastapi import FastAPI, HTTPException, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from datetime import datetime
import logging
from typing import List, Dict, Optional
import pandas as pd
import os

from models import *
from optimizer import GreedyDispatchOptimizer
from fuel_predictor import FuelPredictor

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Initialize FastAPI
app = FastAPI(
    title="Truck Dispatch Optimization API",
    description="Greedy Algorithm with Rule-Based Validation for Truck Dispatch",
    version="2.0.0"
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Initialize components with datasets path
DATASETS_PATH = "Datasets"
fuel_predictor = FuelPredictor(datasets_path=DATASETS_PATH)

# ==================== HELPER FUNCTIONS ====================

def convert_to_model_objects(request: DispatchOptimizeRequest):
    """Convert API request to model objects"""
    
    # Fuel type mapping
    fuel_type_map = {
        "diesel": FuelType.DIESEL,
        "premium": FuelType.PREMIUM,
        "regular": FuelType.REGULAR,
        "kerosene": FuelType.KEROSENE
    }
    
    # Convert trucks
    trucks = []
    for t in request.trucks:
        compartments = []
        for c in t.compartments:
            compartments.append(Compartment(
                compartment_no=c.compartment_no,
                fuel_type=fuel_type_map.get(c.fuel_type.lower(), FuelType.DIESEL),
                capacity_ltrs=c.capacity_ltrs,
                loaded_ltrs=c.loaded_ltrs
            ))
        
        status_map = {
            "available": TruckStatus.AVAILABLE,
            "in_transit": TruckStatus.IN_TRANSIT,
            "maintenance": TruckStatus.MAINTENANCE
        }
        
        trucks.append(Truck(
            id=t.id,
            name=t.name,
            plate_number=t.plate_number,
            current_area_id=t.current_area_id,
            max_capacity_ltrs=t.max_capacity_ltrs,
            fuel_efficiency_km_per_l=t.fuel_efficiency_km_per_l,
            status=status_map.get(t.status.lower(), TruckStatus.AVAILABLE),
            compartments=compartments
        ))
    
    # Convert areas and stations
    areas = []
    for a in request.areas:
        stations = []
        for s in a.stations:
            required_fuels = {}
            for fuel_type, qty in s.required_fuels.items():
                if qty > 0:
                    required_fuels[fuel_type_map.get(fuel_type.lower(), FuelType.DIESEL)] = qty
            
            stations.append(Station(
                id=s.id,
                name=s.name,
                area_id=s.area_id,
                required_fuels=required_fuels
            ))
        
        areas.append(Area(
            id=a.id,
            name=a.name,
            stations=stations
        ))
    
    # Convert distances
    distances = []
    for d in request.distances:
        distances.append(DistanceMatrix(
            from_area_id=d.from_area_id,
            to_area_id=d.to_area_id,
            distance_km=d.distance_km
        ))
    
    return trucks, areas, distances

def load_trucks_from_csv() -> List[Dict]:
    """Load trucks from CSV file if available"""
    trucks_path = os.path.join(DATASETS_PATH, 'trucks.csv')
    
    if os.path.exists(trucks_path):
        df = pd.read_csv(trucks_path)
        return df.to_dict('records')
    return []

def load_routes_from_csv() -> List[Dict]:
    """Load routes from CSV file if available"""
    routes_path = os.path.join(DATASETS_PATH, 'routes.csv')
    
    if os.path.exists(routes_path):
        df = pd.read_csv(routes_path)
        return df.to_dict('records')
    return []

def load_facilities_from_csv() -> List[Dict]:
    """Load facilities/stations from CSV file if available"""
    facilities_path = os.path.join(DATASETS_PATH, 'facilities.csv')
    
    if os.path.exists(facilities_path):
        df = pd.read_csv(facilities_path)
        return df.to_dict('records')
    return []

# ==================== API ENDPOINTS ====================

@app.get("/")
async def root():
    return {
        "message": "Truck Dispatch Optimization API v2",
        "status": "active",
        "algorithm": "Greedy Dispatch with Rule-Based Validation",
        "datasets_path": DATASETS_PATH,
        "features": [
            "7 Rule-Based Validations",
            "Greedy Route Optimization",
            "Multi-Fuel Type Support",
            "Cost-Based Continue Decision",
            "ML Fuel Prediction (using Datasets)"
        ],
        "endpoints": {
            "optimize": "POST /api/v2/dispatch/optimize",
            "validate": "POST /api/v2/dispatch/validate",
            "continue_check": "POST /api/v2/dispatch/check-continue",
            "predict_fuel": "POST /api/v2/predict/fuel",
            "train_model": "POST /api/v2/train/model",
            "datasets_info": "GET /api/v2/datasets/info",
            "health": "GET /health"
        }
    }

@app.get("/health")
async def health_check():
    # Check if datasets exist
    datasets_exist = {
        "trips.csv": os.path.exists(os.path.join(DATASETS_PATH, 'trips.csv')),
        "trucks.csv": os.path.exists(os.path.join(DATASETS_PATH, 'trucks.csv')),
        "delivery_events.csv": os.path.exists(os.path.join(DATASETS_PATH, 'delivery_events.csv')),
        "routes.csv": os.path.exists(os.path.join(DATASETS_PATH, 'routes.csv')),
        "facilities.csv": os.path.exists(os.path.join(DATASETS_PATH, 'facilities.csv'))
    }
    
    return {
        "status": "healthy",
        "timestamp": datetime.now().isoformat(),
        "algorithm_ready": True,
        "fuel_predictor": "loaded" if fuel_predictor.model else "fallback_mode",
        "datasets_available": datasets_exist,
        "datasets_path": DATASETS_PATH
    }

@app.get("/api/v2/datasets/info")
async def get_datasets_info():
    """Get information about available datasets"""
    datasets_info = {}
    
    for filename in ['trips.csv', 'trucks.csv', 'delivery_events.csv', 'routes.csv', 'facilities.csv']:
        filepath = os.path.join(DATASETS_PATH, filename)
        if os.path.exists(filepath):
            df = pd.read_csv(filepath)
            datasets_info[filename] = {
                "exists": True,
                "rows": len(df),
                "columns": list(df.columns),
                "size_kb": round(os.path.getsize(filepath) / 1024, 2)
            }
        else:
            datasets_info[filename] = {"exists": False}
    
    return {
        "datasets_path": DATASETS_PATH,
        "datasets": datasets_info
    }

@app.post("/api/v2/train/model")
async def train_model(background_tasks: BackgroundTasks):
    """Train the fuel prediction model using datasets"""
    try:
        logger.info("Starting model training...")
        
        result = fuel_predictor.train_model()
        
        if "error" in result:
            raise HTTPException(status_code=500, detail=result["error"])
        
        return {
            "status": "success",
            "message": "Model trained successfully",
            "metrics": {
                "train_r2": result.get("train_r2"),
                "test_r2": result.get("test_r2"),
                "training_samples": result.get("training_samples"),
                "test_samples": result.get("test_samples")
            },
            "feature_importance": result.get("feature_importance"),
            "features_used": result.get("features_used")
        }
        
    except Exception as e:
        logger.error(f"Training error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/api/v2/dispatch/optimize")
async def optimize_dispatch(request: DispatchOptimizeRequest):
    """
    Main dispatch optimization endpoint.
    Uses greedy algorithm with 7 rule-based validations.
    
    Example request:
    {
        "trucks": [...],
        "areas": [...],
        "distances": [...],
        "start_area_id": 1,
        "optimization_mode": "greedy"
    }
    """
    try:
        logger.info(f"Optimization request received: {len(request.trucks)} trucks, {len(request.areas)} areas")
        
        # Convert to model objects
        trucks, areas, distances = convert_to_model_objects(request)
        
        # Create optimizer
        optimizer = GreedyDispatchOptimizer(distances)
        
        # Run optimization
        result = optimizer.greedy_dispatch(
            trucks=trucks,
            areas=areas,
            start_area_id=request.start_area_id,
            driver_hours_used=request.driver_hours_used,
            max_driver_hours=request.max_driver_hours,
            optimization_mode=request.optimization_mode
        )
        
        logger.info(f"Optimization complete: {len(result.assignments)} assignments, "
                   f"fulfillment: {result.summary['fulfillment_rate_percent']}%")
        
        return result.to_dict()
        
    except Exception as e:
        logger.error(f"Optimization error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/api/v2/dispatch/validate")
async def validate_truck(request: ValidateTruckRequest):
    """
    Validate if a truck can serve a station (7 rule checks)
    
    Returns detailed validation results for each rule
    """
    try:
        # Convert to model objects
        fuel_type_map = {
            "diesel": FuelType.DIESEL,
            "premium": FuelType.PREMIUM,
            "regular": FuelType.REGULAR,
            "kerosene": FuelType.KEROSENE
        }
        
        # Build truck object
        compartments = []
        for c in request.truck.compartments:
            compartments.append(Compartment(
                compartment_no=c.compartment_no,
                fuel_type=fuel_type_map.get(c.fuel_type.lower(), FuelType.DIESEL),
                capacity_ltrs=c.capacity_ltrs,
                loaded_ltrs=c.loaded_ltrs
            ))
        
        truck = Truck(
            id=request.truck.id,
            name=request.truck.name,
            plate_number=request.truck.plate_number,
            current_area_id=request.truck.current_area_id,
            max_capacity_ltrs=request.truck.max_capacity_ltrs,
            fuel_efficiency_km_per_l=request.truck.fuel_efficiency_km_per_l,
            status=TruckStatus(request.truck.status),
            compartments=compartments
        )
        
        # Build station object
        required_fuels = {}
        for fuel_type, qty in request.station.required_fuels.items():
            if qty > 0:
                required_fuels[fuel_type_map.get(fuel_type.lower(), FuelType.DIESEL)] = qty
        
        station = Station(
            id=request.station.id,
            name=request.station.name,
            area_id=request.station.area_id,
            required_fuels=required_fuels
        )
        
        # Create optimizer for distance lookup
        optimizer = GreedyDispatchOptimizer([])
        
        # Validate
        can_assign, decision, reason, checks = optimizer.validate_truck_for_station(
            truck=truck,
            station=station,
            current_location_id=request.current_location_id,
            driver_hours_used=request.driver_hours_used,
            max_driver_hours=request.max_driver_hours
        )
        
        distance = optimizer.get_distance(request.current_location_id, request.station.area_id)
        fuel_to_reach = truck.calculate_fuel_cost_for_trip(distance)
        net_available = truck.total_available - fuel_to_reach
        
        return ValidationResultResponse(
            can_assign=can_assign,
            decision=decision.value,
            reason=reason,
            fuel_to_reach=round(fuel_to_reach, 2),
            net_available=round(net_available, 2),
            distance_km=round(distance, 2),
            validation_checks=checks
        )
        
    except Exception as e:
        logger.error(f"Validation error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/api/v2/dispatch/check-continue")
async def check_continue_route(request: ContinueRouteRequest):
    """
    Check if truck should continue to next station (cost-based decision)
    
    Returns decision with cost comparison
    """
    try:
        # Convert to model objects
        fuel_type_map = {
            "diesel": FuelType.DIESEL,
            "premium": FuelType.PREMIUM,
            "regular": FuelType.REGULAR,
            "kerosene": FuelType.KEROSENE
        }
        
        # Build truck object
        compartments = []
        for c in request.truck.compartments:
            compartments.append(Compartment(
                compartment_no=c.compartment_no,
                fuel_type=fuel_type_map.get(c.fuel_type.lower(), FuelType.DIESEL),
                capacity_ltrs=c.capacity_ltrs,
                loaded_ltrs=c.loaded_ltrs
            ))
        
        truck = Truck(
            id=request.truck.id,
            name=request.truck.name,
            plate_number=request.truck.plate_number,
            current_area_id=request.truck.current_area_id,
            max_capacity_ltrs=request.truck.max_capacity_ltrs,
            fuel_efficiency_km_per_l=request.truck.fuel_efficiency_km_per_l,
            status=TruckStatus(request.truck.status),
            compartments=compartments
        )
        
        # Build current station
        current_required = {}
        for fuel_type, qty in request.current_station.required_fuels.items():
            if qty > 0:
                current_required[fuel_type_map.get(fuel_type.lower(), FuelType.DIESEL)] = qty
        
        current_station = Station(
            id=request.current_station.id,
            name=request.current_station.name,
            area_id=request.current_station.area_id,
            required_fuels=current_required
        )
        
        # Build next station
        next_required = {}
        for fuel_type, qty in request.next_station.required_fuels.items():
            if qty > 0:
                next_required[fuel_type_map.get(fuel_type.lower(), FuelType.DIESEL)] = qty
        
        next_station = Station(
            id=request.next_station.id,
            name=request.next_station.name,
            area_id=request.next_station.area_id,
            required_fuels=next_required
        )
        
        # Build alternative trucks
        alternative_trucks = []
        for alt in request.alternative_trucks:
            alt_compartments = []
            for c in alt.compartments:
                alt_compartments.append(Compartment(
                    compartment_no=c.compartment_no,
                    fuel_type=fuel_type_map.get(c.fuel_type.lower(), FuelType.DIESEL),
                    capacity_ltrs=c.capacity_ltrs,
                    loaded_ltrs=c.loaded_ltrs
                ))
            
            alternative_trucks.append(Truck(
                id=alt.id,
                name=alt.name,
                plate_number=alt.plate_number,
                current_area_id=alt.current_area_id,
                max_capacity_ltrs=alt.max_capacity_ltrs,
                fuel_efficiency_km_per_l=alt.fuel_efficiency_km_per_l,
                status=TruckStatus(alt.status),
                compartments=alt_compartments
            ))
        
        # Create optimizer
        optimizer = GreedyDispatchOptimizer([])
        
        # Check continue decision
        should_continue, reason, savings, current_cost, alt_cost = optimizer.should_continue_to_next_station(
            truck=truck,
            current_station=current_station,
            next_station=next_station,
            remaining_capacity=request.remaining_capacity,
            alternative_trucks=alternative_trucks
        )
        
        return ContinueRouteResponse(
            should_continue=should_continue,
            reason=reason,
            cost_savings=round(savings, 2),
            alternative_cost=round(alt_cost, 2),
            current_cost=round(current_cost, 2)
        )
        
    except Exception as e:
        logger.error(f"Continue check error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/api/v2/predict/fuel")
async def predict_fuel(request: FuelPredictionRequest):
    """
    Predict fuel consumption for a delivery route
    
    Uses ML model if available (trained from Datasets), otherwise fallback calculation
    """
    try:
        logger.info(f"Fuel prediction request: distance={request.distance_km}km, duration={request.actual_duration_hours}h")
        
        predicted_fuel, confidence, lower_bound, upper_bound = fuel_predictor.predict_fuel(
            distance_km=request.distance_km,
            actual_duration_hours=request.actual_duration_hours,
            average_mpg=request.average_mpg,
            idle_time_hours=request.idle_time_hours,
            detention_minutes=request.detention_minutes,
            delay_minutes=request.delay_minutes,
            on_time_flag=request.on_time_flag
        )
        
        efficiency = request.distance_km / predicted_fuel if predicted_fuel > 0 else 0
        
        return {
            "predicted_fuel_liters": round(predicted_fuel, 2),
            "confidence_score": round(confidence, 4),
            "efficiency_km_per_l": round(efficiency, 2),
            "prediction_interval_lower": round(lower_bound, 2),
            "prediction_interval_upper": round(upper_bound, 2)
        }
        
    except Exception as e:
        logger.error(f"Fuel prediction error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))
if __name__ == "__main__":
    import uvicorn
    
    # Check if datasets exist
    print("\n" + "="*60)
    print("🚛 TRUCK DISPATCH OPTIMIZATION API")
    print("="*60)
    print(f"📍 API URL: http://localhost:8002")
    print(f"📖 Documentation: http://localhost:8002/docs")
    print(f"🏥 Health Check: http://localhost:8002/health")
    print(f"📁 Datasets Path: {DATASETS_PATH}")
    print("="*60)
    
    # Check datasets
    print("\n📂 Checking datasets...")
    for filename in ['trips.csv', 'trucks.csv', 'delivery_events.csv', 'routes.csv', 'facilities.csv']:
        filepath = os.path.join(DATASETS_PATH, filename)
        if os.path.exists(filepath):
            print(f"  ✅ {filename} found")
        else:
            print(f"  ⚠️ {filename} not found")
    
    print("\n🚀 Starting API server...\n")
    
    uvicorn.run(
        "api:app",
        host="0.0.0.0",
        port=8002,
        reload=True
    )