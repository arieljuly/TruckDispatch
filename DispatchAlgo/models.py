from pydantic import BaseModel
from typing import List, Optional
from datetime import datetime

class Truck(BaseModel):
    id: int
    name: str
    liters: float
    area_id: int
    status: str = "Available"

class Area(BaseModel):
    id: int
    name: str
    demand: float

class DistanceMatrix(BaseModel):
    from_area_id: int
    to_area_id: int
    distance: float

class Step(BaseModel):
    step: int
    truck_id: int
    truck_name: str
    area_id: int
    area_name: str
    allocated: float
    truck_remaining: float
    area_remaining: float
    is_primary_area: bool
    distance_used: float

class SimulationResult(BaseModel):
    session_id: str
    steps: List[Step]
    final_trucks: List[Truck]
    final_areas: List[Area]
    summary: dict