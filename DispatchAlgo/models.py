from enum import Enum
from typing import List, Dict, Optional
from dataclasses import dataclass, field
from datetime import datetime
from pydantic import BaseModel, Field

# ==================== ENUMS ====================

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

# ==================== CORE DATA MODELS ====================

@dataclass
class Compartment:
    """Truck compartment for fuel storage"""
    compartment_no: int
    fuel_type: FuelType
    capacity_ltrs: float
    loaded_ltrs: float = 0.0
    
    @property
    def available_ltrs(self) -> float:
        return self.capacity_ltrs - self.loaded_ltrs
    
    def can_load(self, liters: float, fuel_type: FuelType) -> bool:
        return (self.fuel_type == fuel_type and 
                self.available_ltrs >= liters)
    
    def to_dict(self) -> dict:
        return {
            "compartment_no": self.compartment_no,
            "fuel_type": self.fuel_type.value,
            "capacity_ltrs": self.capacity_ltrs,
            "loaded_ltrs": self.loaded_ltrs,
            "available_ltrs": self.available_ltrs
        }

@dataclass
class Truck:
    """Truck model with compartments"""
    id: int
    name: str
    plate_number: str
    current_area_id: int
    max_capacity_ltrs: float
    fuel_efficiency_km_per_l: float
    status: TruckStatus = TruckStatus.AVAILABLE
    compartments: List[Compartment] = field(default_factory=list)
    
    @property
    def total_loaded(self) -> float:
        return sum(c.loaded_ltrs for c in self.compartments)
    
    @property
    def total_available(self) -> float:
        """Calculate total available fuel across all compartments"""
        return sum(c.available_ltrs for c in self.compartments)
    
    @property
    def total_capacity(self) -> float:
        return sum(c.capacity_ltrs for c in self.compartments)
    
    def has_fuel_type(self, fuel_type: FuelType) -> bool:
        return any(c.fuel_type == fuel_type and c.available_ltrs > 0 
                   for c in self.compartments)
    
    def get_available_by_fuel_type(self, fuel_type: FuelType) -> float:
        return sum(c.available_ltrs for c in self.compartments 
                   if c.fuel_type == fuel_type)
    
    def load_fuel(self, fuel_type: FuelType, liters: float) -> bool:
        """Load fuel into appropriate compartment"""
        remaining = liters
        for compartment in self.compartments:
            if compartment.fuel_type == fuel_type and compartment.available_ltrs > 0:
                load = min(remaining, compartment.available_ltrs)
                compartment.loaded_ltrs += load
                remaining -= load
                if remaining <= 0:
                    return True
        return remaining <= 0
    
    def unload_fuel(self, fuel_type: FuelType, liters: float) -> bool:
        """Unload fuel from compartment"""
        remaining = liters
        for compartment in self.compartments:
            if compartment.fuel_type == fuel_type and compartment.loaded_ltrs > 0:
                unload = min(remaining, compartment.loaded_ltrs)
                compartment.loaded_ltrs -= unload
                remaining -= unload
                if remaining <= 0:
                    return True
        return remaining <= 0
    
    def calculate_fuel_cost_for_trip(self, distance_km: float) -> float:
        """Calculate fuel consumed for a given distance"""
        if self.fuel_efficiency_km_per_l <= 0:
            return distance_km / 10.0
        return distance_km / self.fuel_efficiency_km_per_l
    
    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "name": self.name,
            "plate_number": self.plate_number,
            "current_area_id": self.current_area_id,
            "max_capacity_ltrs": self.max_capacity_ltrs,
            "fuel_efficiency_km_per_l": self.fuel_efficiency_km_per_l,
            "status": self.status.value,
            "total_available_ltrs": self.total_available,
            "compartments": [c.to_dict() for c in self.compartments]
        }

@dataclass
class Station:
    """Station that requires fuel delivery"""
    id: int
    name: str
    area_id: int
    required_fuels: Dict[FuelType, float] = field(default_factory=dict)
    
    @property
    def total_required(self) -> float:
        return sum(self.required_fuels.values())
    
    def get_remaining(self) -> Dict[FuelType, float]:
        return {ft: qty for ft, qty in self.required_fuels.items() if qty > 0}
    
    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "name": self.name,
            "area_id": self.area_id,
            "total_required": self.total_required,
            "required_fuels": {k.value: v for k, v in self.required_fuels.items()}
        }

@dataclass
class Area:
    """Geographic area containing stations"""
    id: int
    name: str
    stations: List[Station] = field(default_factory=list)
    
    @property
    def total_demand(self) -> float:
        return sum(s.total_required for s in self.stations)
    
    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "name": self.name,
            "total_demand": self.total_demand,
            "stations": [s.to_dict() for s in self.stations]
        }

@dataclass
class DistanceMatrix:
    """Distance between locations"""
    from_area_id: int
    to_area_id: int
    distance_km: float

@dataclass
class DeliveryStop:
    """A single delivery stop in a route"""
    station_id: int
    station_name: str
    fuel_type: FuelType
    liters: float
    distance_from_previous: float
    is_primary: bool = False
    
    def to_dict(self) -> dict:
        return {
            "station_id": self.station_id,
            "station_name": self.station_name,
            "fuel_type": self.fuel_type.value,
            "liters": round(self.liters, 2),
            "distance_from_previous": round(self.distance_from_previous, 2),
            "is_primary": self.is_primary
        }

@dataclass
class DispatchAssignment:
    """Final assignment decision"""
    truck_id: int
    truck_name: str
    truck_plate: str
    stops: List[DeliveryStop]
    total_distance_km: float
    total_fuel_consumed: float
    total_delivered: float
    is_optimal: bool = True
    validation_messages: List[str] = field(default_factory=list)
    
    @property
    def fuel_efficiency_actual(self) -> float:
        if self.total_fuel_consumed > 0:
            return self.total_distance_km / self.total_fuel_consumed
        return 0
    
    def to_dict(self) -> dict:
        return {
            "truck_id": self.truck_id,
            "truck_name": self.truck_name,
            "truck_plate": self.truck_plate,
            "stops": [s.to_dict() for s in self.stops],
            "total_distance_km": round(self.total_distance_km, 2),
            "total_fuel_consumed": round(self.total_fuel_consumed, 2),
            "total_delivered": round(self.total_delivered, 2),
            "fuel_efficiency_actual": round(self.fuel_efficiency_actual, 2),
            "is_optimal": self.is_optimal,
            "validation_messages": self.validation_messages
        }

@dataclass
class DispatchResult:
    """Complete dispatch optimization result"""
    session_id: str
    assignments: List[DispatchAssignment]
    unfulfilled_stations: List[Station]
    summary: dict
    timestamp: datetime = field(default_factory=datetime.now)
    
    def to_dict(self) -> dict:
        return {
            "session_id": self.session_id,
            "assignments": [a.to_dict() for a in self.assignments],
            "unfulfilled_stations": [s.to_dict() for s in self.unfulfilled_stations],
            "summary": self.summary,
            "timestamp": self.timestamp.isoformat()
        }

# ==================== API REQUEST MODELS (Pydantic) ====================

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
    start_area_id: int = None
    driver_hours_used: float = 0
    max_driver_hours: float = 11
    optimization_mode: str = "greedy"

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

class FuelPredictionRequest(BaseModel):
    distance_km: float
    actual_duration_hours: float = Field(default=2.0, ge=0)
    average_mpg: float = Field(default=6.0, gt=0)
    idle_time_hours: float = Field(default=0, ge=0)
    detention_minutes: int = Field(default=0, ge=0)
    delay_minutes: int = Field(default=0, ge=0)
    on_time_flag: bool = Field(default=True)

# ==================== API RESPONSE MODELS (Pydantic) ====================

class ValidationResultResponse(BaseModel):
    can_assign: bool
    decision: str
    reason: str
    fuel_to_reach: float
    net_available: float
    distance_km: float
    validation_checks: dict

class ContinueRouteResponse(BaseModel):
    should_continue: bool
    reason: str
    cost_savings: float
    alternative_cost: float
    current_cost: float

class FuelPredictionResponse(BaseModel):
    predicted_fuel_liters: float
    confidence_score: float
    efficiency_km_per_l: float
    prediction_interval_lower: float
    prediction_interval_upper: float