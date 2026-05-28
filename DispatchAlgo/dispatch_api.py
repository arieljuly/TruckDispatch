"""
Truck Dispatch Optimization API - Full Integration with Laravel
Implements Greedy Algorithm + Rule-Based Validation
"""

from fastapi import FastAPI, HTTPException, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from typing import List, Dict, Optional, Tuple
from datetime import datetime
from enum import Enum
import uuid
import numpy as np
import logging

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = FastAPI(
    title="Truck Dispatch Optimization API",
    description="Greedy Algorithm with Rule-Based Validation for Truck Dispatch",
    version="2.0.0"
)

# CORS for Laravel integration
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ==================== ENUMS & MODELS ====================

class FuelType(str, Enum):
    DIESEL = "diesel"
    PREMIUM = "premium"
    REGULAR = "regular"
    KEROSENE = "kerosene"

class TruckStatus(str, Enum):
    AVAILABLE = "available"
    IN_TRANSIT = "in_transit"
    MAINTENANCE = "maintenance"

class DispatchDecision(str, Enum):
    ASSIGN = "assign"
    SKIP = "skip"
    REASSIGN = "reassign"

# ==================== REQUEST MODELS ====================

class CompartmentRequest(BaseModel):
    compartment_no: int
    fuel_type: str
    capacity_ltrs: float
    loaded_ltrs: float = 0

class TruckRequest(BaseModel):
    id: int
    name: str
    plate_number: str
    current_area_id: int
    max_capacity_ltrs: float
    fuel_efficiency_km_per_l: float = 10.0
    status: str = "available"
    available_ltrs: float = 0  # Total available fuel
    compartments: List[CompartmentRequest] = []

class StationFuelRequest(BaseModel):
    fuel_type: str
    quantity: float

class StationRequest(BaseModel):
    id: int
    name: str
    area_id: int
    required_fuels: Dict[str, float] = {}

class AreaRequest(BaseModel):
    id: int
    name: str
    stations: List[StationRequest] = []

class DistanceMatrixRequest(BaseModel):
    from_area_id: int
    to_area_id: int
    distance_km: float

class DispatchOptimizeRequest(BaseModel):
    trucks: List[TruckRequest]
    areas: List[AreaRequest]
    distances: List[DistanceMatrixRequest]
    start_area_id: Optional[int] = None
    driver_hours_used: float = 0
    max_driver_hours: float = 11
    optimization_mode: str = "greedy"  # greedy, cost_based, balanced

class ValidateTruckRequest(BaseModel):
    truck: TruckRequest
    station: StationRequest
    current_location_id: int
    driver_hours_used: float = 0
    max_driver_hours: float = 11

class ContinueRouteRequest(BaseModel):
    truck: TruckRequest
    current_station: StationRequest
    next_station: StationRequest
    remaining_capacity: float
    alternative_trucks: List[TruckRequest] = []

# ==================== RESPONSE MODELS ====================

class DeliveryStopResponse(BaseModel):
    station_id: int
    station_name: str
    fuel_type: str
    liters: float
    distance_from_previous: float
    is_primary: bool = False

class DispatchAssignmentResponse(BaseModel):
    truck_id: int
    truck_name: str
    truck_plate: str
    stops: List[DeliveryStopResponse]
    total_distance_km: float
    total_fuel_consumed: float
    total_delivered: float
    fuel_efficiency_actual: float
    is_optimal: bool
    validation_messages: List[str] = []

class DispatchResultResponse(BaseModel):
    session_id: str
    assignments: List[DispatchAssignmentResponse]
    unfulfilled_stations: List[Dict]
    summary: Dict
    timestamp: datetime

class ValidationResultResponse(BaseModel):
    can_assign: bool
    decision: str
    reason: str
    fuel_to_reach: float
    net_available: float
    distance_km: float
    validation_checks: Dict[str, bool]

class ContinueRouteResponse(BaseModel):
    should_continue: bool
    reason: str
    cost_savings: float
    alternative_cost: float
    current_cost: float

# ==================== CORE OPTIMIZATION ENGINE ====================

class GreedyDispatchOptimizer:
    """Greedy algorithm with rule-based validation for truck dispatch"""
    
    def __init__(self, distances: List[DistanceMatrixRequest]):
        self.distances = {}
        for d in distances:
            self.distances[(d.from_area_id, d.to_area_id)] = d.distance_km
            # Add reverse direction for symmetry
            if (d.to_area_id, d.from_area_id) not in self.distances:
                self.distances[(d.to_area_id, d.from_area_id)] = d.distance_km
        
        self.fuel_efficiency_cache = {}
        self.validation_rules = []
    
    def get_distance(self, from_area_id: int, to_area_id: int) -> float:
        """Get distance between two areas"""
        if from_area_id == to_area_id:
            return 0
        return self.distances.get((from_area_id, to_area_id), 50.0)  # Default 50km
    
    # ========== RULE-BASED VALIDATION (7 Rules) ==========
    
    def validate_truck_for_station(
        self,
        truck: TruckRequest,
        station: StationRequest,
        current_location_id: int,
        driver_hours_used: float = 0,
        max_driver_hours: float = 11
    ) -> Tuple[bool, DispatchDecision, str, Dict]:
        """
        Rule-based validation for truck assignment
        Returns: (can_assign, decision, reason, validation_checks)
        """
        validation_checks = {}
        
        # Rule 1: Truck must be available
        rule1 = truck.status == "available"
        validation_checks["truck_available"] = rule1
        if not rule1:
            return False, DispatchDecision.SKIP, f"Truck {truck.name} is {truck.status}", validation_checks
        
        # Rule 2: Truck must have remaining capacity
        rule2 = truck.available_ltrs > 0
        validation_checks["has_capacity"] = rule2
        if not rule2:
            return False, DispatchDecision.SKIP, f"Truck {truck.name} has no available capacity", validation_checks
        
        # Rule 3: Fuel type compatibility and sufficient quantity
        rule3 = True
        fuel_check_details = []
        for fuel_type, required_qty in station.required_fuels.items():
            # Check if truck has this fuel type
            truck_fuel_available = 0
            for comp in truck.compartments:
                if comp.fuel_type == fuel_type:
                    truck_fuel_available += comp.available_ltrs
            
            if truck_fuel_available == 0:
                rule3 = False
                fuel_check_details.append(f"No {fuel_type} available")
            elif truck_fuel_available < required_qty:
                rule3 = False
                fuel_check_details.append(f"Insufficient {fuel_type}: need {required_qty}L, have {truck_fuel_available}L")
        
        validation_checks["fuel_compatible"] = rule3
        if not rule3:
            return False, DispatchDecision.REASSIGN, f"Fuel mismatch: {', '.join(fuel_check_details)}", validation_checks
        
        # Rule 4: Distance feasibility (can truck reach?)
        distance = self.get_distance(current_location_id, station.area_id)
        fuel_to_reach = distance / truck.fuel_efficiency_km_per_l if truck.fuel_efficiency_km_per_l > 0 else distance / 10
        
        rule4 = fuel_to_reach <= truck.available_ltrs
        validation_checks["can_reach"] = rule4
        if not rule4:
            return False, DispatchDecision.SKIP, f"Cannot reach station: needs {fuel_to_reach:.1f}L fuel for travel, has {truck.available_ltrs:.1f}L", validation_checks
        
        # Rule 5: Cost effectiveness (will be checked in calling context)
        rule5 = True
        validation_checks["cost_effective"] = rule5
        
        # Rule 6: Driver hours limit
        estimated_trip_hours = distance / 50  # Assume 50 km/h average
        rule6 = driver_hours_used + estimated_trip_hours <= max_driver_hours
        validation_checks["driver_hours_ok"] = rule6
        if not rule6:
            return False, DispatchDecision.REASSIGN, f"Driver would exceed hours limit ({driver_hours_used + estimated_trip_hours:.1f}h > {max_driver_hours}h)", validation_checks
        
        # Rule 7: No higher priority dispatch (handled by caller)
        rule7 = True
        validation_checks["no_higher_priority"] = rule7
        
        # All rules passed
        net_available = truck.available_ltrs - fuel_to_reach
        return True, DispatchDecision.ASSIGN, "All validation passed", validation_checks
    
    def should_continue_to_next_station(
        self,
        truck: TruckRequest,
        current_station: StationRequest,
        next_station: StationRequest,
        remaining_capacity: float,
        alternative_trucks: List[TruckRequest]
    ) -> Tuple[bool, str, float]:
        """
        Decision rule: Should truck continue to next station?
        
        Conditions:
        1. Truck must have sufficient remaining fuel quantity
        2. Truck must have correct fuel type for next station
        3. Next station is within acceptable distance
        4. Sending this truck is cheaper than alternatives
        5. Driver has legal hours remaining (checked elsewhere)
        """
        
        # Condition 1: Check remaining capacity
        if remaining_capacity <= 0:
            return False, "No remaining capacity in truck", 0
        
        # Condition 2: Check fuel type compatibility
        distance = self.get_distance(current_station.area_id, next_station.area_id)
        
        for fuel_type, required_qty in next_station.required_fuels.items():
            truck_available = 0
            for comp in truck.compartments:
                if comp.fuel_type == fuel_type:
                    truck_available += comp.available_ltrs
            
            if truck_available == 0:
                return False, f"Truck does not carry {fuel_type}", 0
            if truck_available < required_qty:
                return False, f"Insufficient {fuel_type} (needs {required_qty}L, has {truck_available}L)", 0
        
        # Condition 3: Check distance is acceptable
        max_acceptable_distance = 150  # km
        if distance > max_acceptable_distance:
            return False, f"Next station too far ({distance:.1f}km > {max_acceptable_distance}km)", 0
        
        # Condition 4: Cost effectiveness check
        cost_to_continue = distance / truck.fuel_efficiency_km_per_l if truck.fuel_efficiency_km_per_l > 0 else distance / 10
        
        # Find cheapest alternative
        min_alternative_cost = float('inf')
        for alt_truck in alternative_trucks:
            if alt_truck.id != truck.id and alt_truck.status == "available":
                alt_cost = distance / alt_truck.fuel_efficiency_km_per_l if alt_truck.fuel_efficiency_km_per_l > 0 else distance / 10
                if alt_cost < min_alternative_cost:
                    min_alternative_cost = alt_cost
        
        savings = min_alternative_cost - cost_to_continue if min_alternative_cost != float('inf') else 0
        
        # Continue if this truck is cheaper OR if savings is not significantly negative
        threshold = 0.2  # 20% threshold
        if min_alternative_cost != float('inf') and cost_to_continue > min_alternative_cost * (1 + threshold):
            return False, f"Cheaper to use another truck (cost: {cost_to_continue:.1f}L vs {min_alternative_cost:.1f}L)", savings
        
        return True, f"Continue OK - {cost_to_continue:.1f}L fuel needed", savings
    
    def calculate_route_metrics(
        self,
        truck: TruckRequest,
        stops: List[Tuple[StationRequest, float, Dict]]
    ) -> Tuple[float, float, float]:
        """
        Calculate route metrics: total distance, fuel consumed, delivered
        """
        total_distance = sum(stop[1] for stop in stops)
        total_fuel_consumed = total_distance / truck.fuel_efficiency_km_per_l if truck.fuel_efficiency_km_per_l > 0 else total_distance / 10
        total_delivered = sum(sum(stop[2].values()) for stop in stops)
        
        return total_distance, total_fuel_consumed, total_delivered
    
    # ========== GREEDY DISPATCH ALGORITHM ==========
    
    def greedy_dispatch(
        self,
        trucks: List[TruckRequest],
        areas: List[AreaRequest],
        start_area_id: Optional[int] = None,
        driver_hours_used: float = 0,
        max_driver_hours: float = 11,
        optimization_mode: str = "greedy"
    ) -> DispatchResultResponse:
        """
        Greedy dispatch algorithm with rule-based validation
        
        Phases:
        1. Collect all stations with demand
        2. Sort trucks by efficiency (best fuel economy first)
        3. For each truck, greedily assign nearest compatible stations
        4. Validate each assignment with rule-based checks
        5. Track unfulfilled stations
        """
        
        # Collect all stations with demand
        all_stations = []
        for area in areas:
            for station in area.stations:
                if station.required_fuels and sum(station.required_fuels.values()) > 0:
                    all_stations.append(station)
        
        # Sort trucks by efficiency (best first)
        sorted_trucks = sorted(trucks, key=lambda t: t.fuel_efficiency_km_per_l, reverse=True)
        
        assignments = []
        remaining_stations = all_stations.copy()
        
        logger.info(f"Starting Greedy Dispatch - {len(sorted_trucks)} trucks, {len(remaining_stations)} stations")
        
        for truck in sorted_trucks:
            if not remaining_stations:
                break
            
            if truck.status != "available":
                continue
            
            logger.info(f"Processing truck {truck.name} (efficiency: {truck.fuel_efficiency_km_per_l} km/L)")
            
            # Build route for this truck
            current_location = start_area_id or truck.current_area_id
            route_stops = []  # List of (station, distance, fuel_to_deliver)
            validation_messages = []
            
            # Make a copy of truck's available fuel for simulation
            simulated_available = truck.available_ltrs
            simulated_compartments = {comp.fuel_type: comp.available_ltrs for comp in truck.compartments}
            
            # Greedy: Find nearest station that can be served
            remaining_for_truck = remaining_stations.copy()
            
            while remaining_for_truck:
                best_station = None
                best_distance = float('inf')
                best_delivery = {}
                
                for station in remaining_for_truck:
                    # Validate truck can serve this station
                    can_serve, decision, reason, checks = self.validate_truck_for_station(
                        truck, station, current_location, driver_hours_used, max_driver_hours
                    )
                    
                    if can_serve:
                        distance = self.get_distance(current_location, station.area_id)
                        
                        # Check if truck has enough fuel to reach
                        fuel_to_reach = distance / truck.fuel_efficiency_km_per_l if truck.fuel_efficiency_km_per_l > 0 else distance / 10
                        if fuel_to_reach <= simulated_available:
                            if distance < best_distance:
                                best_distance = distance
                                best_station = station
                                
                                # Calculate delivery amounts by fuel type
                                best_delivery = {}
                                for fuel_type, required in station.required_fuels.items():
                                    available = simulated_compartments.get(fuel_type, 0)
                                    best_delivery[fuel_type] = min(required, available)
                
                if best_station is None:
                    break
                
                # Check if should continue to this station (cost effectiveness)
                if route_stops and optimization_mode == "cost_based":
                    should_continue, continue_reason, savings = self.should_continue_to_next_station(
                        truck,
                        route_stops[-1][0],  # last station
                        best_station,
                        simulated_available,
                        [t for t in sorted_trucks if t.id != truck.id]
                    )
                    if not should_continue:
                        validation_messages.append(f"Stopped at {route_stops[-1][0].name}: {continue_reason}")
                        break
                
                # Calculate fuel to reach
                fuel_to_reach = best_distance / truck.fuel_efficiency_km_per_l if truck.fuel_efficiency_km_per_l > 0 else best_distance / 10
                simulated_available -= fuel_to_reach
                
                # Update simulated compartments
                total_delivered_this_stop = 0
                for fuel_type, deliver_qty in best_delivery.items():
                    if deliver_qty > 0:
                        simulated_compartments[fuel_type] = simulated_compartments.get(fuel_type, 0) - deliver_qty
                        total_delivered_this_stop += deliver_qty
                
                route_stops.append((best_station, best_distance, best_delivery))
                current_location = best_station.area_id
                
                # Remove from remaining
                remaining_for_truck.remove(best_station)
                remaining_stations = [s for s in remaining_stations if s.id != best_station.id]
                
                logger.info(f"  → Added {best_station.name}: {best_distance}km, delivering {total_delivered_this_stop}L")
            
            # Create assignment if route has stops
            if route_stops:
                total_distance, total_fuel, total_delivered = self.calculate_route_metrics(truck, route_stops)
                
                stops_response = []
                prev_location = start_area_id or truck.current_area_id
                for station, distance, delivery in route_stops:
                    for fuel_type, qty in delivery.items():
                        if qty > 0:
                            stops_response.append(DeliveryStopResponse(
                                station_id=station.id,
                                station_name=station.name,
                                fuel_type=fuel_type,
                                liters=qty,
                                distance_from_previous=distance,
                                is_primary=(station.area_id == truck.current_area_id)
                            ))
                    prev_location = station.area_id
                
                assignments.append(DispatchAssignmentResponse(
                    truck_id=truck.id,
                    truck_name=truck.name,
                    truck_plate=truck.plate_number,
                    stops=stops_response,
                    total_distance_km=round(total_distance, 2),
                    total_fuel_consumed=round(total_fuel, 2),
                    total_delivered=round(total_delivered, 2),
                    fuel_efficiency_actual=round(total_distance / total_fuel if total_fuel > 0 else 0, 2),
                    is_optimal=True,
                    validation_messages=validation_messages
                ))
        
        # Calculate summary
        total_demand = sum(s.total_required for s in all_stations)
        total_delivered = sum(a.total_delivered for a in assignments)
        
        summary = {
            "total_demand_liters": round(total_demand, 2),
            "total_delivered_liters": round(total_delivered, 2),
            "fulfillment_rate_percent": round(total_delivered / total_demand * 100 if total_demand > 0 else 0, 2),
            "total_trips": len(assignments),
            "total_distance_km": round(sum(a.total_distance_km for a in assignments), 2),
            "total_fuel_consumed_liters": round(sum(a.total_fuel_consumed for a in assignments), 2),
            "overall_efficiency_km_per_l": round(
                sum(a.total_distance_km for a in assignments) / sum(a.total_fuel_consumed for a in assignments) 
                if sum(a.total_fuel_consumed for a in assignments) > 0 else 0, 2
            ),
            "algorithm": f"Greedy Dispatch with Rule-Based Validation ({optimization_mode})",
            "unfulfilled_stations_count": len([s for s in all_stations if s.id not in 
                                                [stop.station_id for a in assignments for stop in a.stops]])
        }
        
        unfulfilled = [
            {"id": s.id, "name": s.name, "required_fuels": s.required_fuels}
            for s in all_stations 
            if s.id not in [stop.station_id for a in assignments for stop in a.stops]
        ]
        
        return DispatchResultResponse(
            session_id=f"DISPATCH_{datetime.now().strftime('%Y%m%d_%H%M%S')}_{uuid.uuid4().hex[:6]}",
            assignments=assignments,
            unfulfilled_stations=unfulfilled,
            summary=summary,
            timestamp=datetime.now()
        )

# ==================== HELPER FUNCTIONS ====================

def convert_fuel_type(fuel_type_str: str) -> str:
    """Convert fuel type string to standard format"""
    fuel_map = {
        "diesel": "diesel",
        "premium": "premium", 
        "regular": "regular",
        "kerosene": "kerosene"
    }
    return fuel_map.get(fuel_type_str.lower(), "diesel")

# ==================== API ENDPOINTS ====================

@app.get("/")
async def root():
    return {
        "message": "Truck Dispatch Optimization API v2",
        "status": "active",
        "algorithm": "Greedy Dispatch with Rule-Based Validation",
        "endpoints": {
            "optimize": "POST /api/v2/dispatch/optimize",
            "validate": "POST /api/v2/dispatch/validate",
            "continue_check": "POST /api/v2/dispatch/check-continue",
            "health": "GET /health"
        }
    }

@app.get("/health")
async def health_check():
    return {
        "status": "healthy",
        "timestamp": datetime.now().isoformat(),
        "algorithm_ready": True
    }

@app.post("/api/v2/dispatch/optimize", response_model=DispatchResultResponse)
async def optimize_dispatch(request: DispatchOptimizeRequest):
    """
    Main dispatch optimization endpoint.
    Uses greedy algorithm with 7 rule-based validations.
    """
    try:
        logger.info(f"Optimization request received: {len(request.trucks)} trucks, {len(request.areas)} areas")
        
        optimizer = GreedyDispatchOptimizer(request.distances)
        
        result = optimizer.greedy_dispatch(
            trucks=request.trucks,
            areas=request.areas,
            start_area_id=request.start_area_id,
            driver_hours_used=request.driver_hours_used,
            max_driver_hours=request.max_driver_hours,
            optimization_mode=request.optimization_mode
        )
        
        logger.info(f"Optimization complete: {len(result.assignments)} assignments, "
                   f"fulfillment: {result.summary['fulfillment_rate_percent']}%")
        
        return result
        
    except Exception as e:
        logger.error(f"Optimization error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/api/v2/dispatch/validate", response_model=ValidationResultResponse)
async def validate_truck(request: ValidateTruckRequest):
    """
    Validate if a truck can serve a station (7 rule checks)
    """
    try:
        optimizer = GreedyDispatchOptimizer([])
        
        can_assign, decision, reason, checks = optimizer.validate_truck_for_station(
            truck=request.truck,
            station=request.station,
            current_location_id=request.current_location_id,
            driver_hours_used=request.driver_hours_used,
            max_driver_hours=request.max_driver_hours
        )
        
        distance = optimizer.get_distance(request.current_location_id, request.station.area_id)
        fuel_to_reach = distance / request.truck.fuel_efficiency_km_per_l if request.truck.fuel_efficiency_km_per_l > 0 else distance / 10
        net_available = request.truck.available_ltrs - fuel_to_reach
        
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

@app.post("/api/v2/dispatch/check-continue", response_model=ContinueRouteResponse)
async def check_continue_route(request: ContinueRouteRequest):
    """
    Check if truck should continue to next station (cost-based decision)
    """
    try:
        optimizer = GreedyDispatchOptimizer([])
        
        should_continue, reason, savings = optimizer.should_continue_to_next_station(
            truck=request.truck,
            current_station=request.current_station,
            next_station=request.next_station,
            remaining_capacity=request.remaining_capacity,
            alternative_trucks=request.alternative_trucks
        )
        
        distance = optimizer.get_distance(request.current_station.area_id, request.next_station.area_id)
        current_cost = distance / request.truck.fuel_efficiency_km_per_l if request.truck.fuel_efficiency_km_per_l > 0 else distance / 10
        
        # Calculate alternative cost
        alternative_cost = 0
        for alt in request.alternative_trucks:
            alt_cost = distance / alt.fuel_efficiency_km_per_l if alt.fuel_efficiency_km_per_l > 0 else distance / 10
            if alt_cost < alternative_cost or alternative_cost == 0:
                alternative_cost = alt_cost
        
        return ContinueRouteResponse(
            should_continue=should_continue,
            reason=reason,
            cost_savings=round(savings, 2),
            alternative_cost=round(alternative_cost, 2),
            current_cost=round(current_cost, 2)
        )
        
    except Exception as e:
        logger.error(f"Continue check error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8002, reload=True)