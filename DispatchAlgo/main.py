from fastapi import FastAPI, HTTPException
from typing import List, Optional
from models import Truck, Area, DistanceMatrix, SimulationResult
from simulate import run_greedy_dispatch

app = FastAPI(title="Truck Dispatch Simulator")

@app.post("/simulate", response_model=SimulationResult)
def simulate(
    trucks: List[Truck],
    areas: List[Area],
    distances: Optional[List[DistanceMatrix]] = None
):
    """
    Run greedy dispatch simulation
    """
    try:
        result = run_greedy_dispatch(trucks, areas, distances)
        return result
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/test-data")
def get_test_data():
    """Returns sample test data matching your requirements"""
    return {
        "trucks": [
            {"id": 1, "name": "Truck 1", "liters": 20, "area_id": 3, "status": "Available"},
            {"id": 2, "name": "Truck 2", "liters": 85, "area_id": 1, "status": "Available"},
            {"id": 3, "name": "Truck 3", "liters": 100, "area_id": 2, "status": "Available"},
            {"id": 4, "name": "Truck 4", "liters": 55, "area_id": 5, "status": "Available"},
            {"id": 5, "name": "Truck 5", "liters": 120, "area_id": 4, "status": "Available"}
        ],
        "areas": [
            {"id": 1, "name": "Area 1", "demand": 80},
            {"id": 2, "name": "Area 2", "demand": 100},
            {"id": 3, "name": "Area 3", "demand": 20},
            {"id": 4, "name": "Area 4", "demand": 50},
            {"id": 5, "name": "Area 5", "demand": 120}
        ],
        "distances": [
            {"from_area_id": 1, "to_area_id": 2, "distance": 10},
            {"from_area_id": 1, "to_area_id": 3, "distance": 15},
            {"from_area_id": 1, "to_area_id": 4, "distance": 20},
            {"from_area_id": 1, "to_area_id": 5, "distance": 25},
            {"from_area_id": 2, "to_area_id": 1, "distance": 10},
            {"from_area_id": 2, "to_area_id": 3, "distance": 12},
            {"from_area_id": 2, "to_area_id": 4, "distance": 18},
            {"from_area_id": 2, "to_area_id": 5, "distance": 22},
            {"from_area_id": 3, "to_area_id": 1, "distance": 15},
            {"from_area_id": 3, "to_area_id": 2, "distance": 12},
            {"from_area_id": 3, "to_area_id": 4, "distance": 8},
            {"from_area_id": 3, "to_area_id": 5, "distance": 14},
            {"from_area_id": 4, "to_area_id": 1, "distance": 20},
            {"from_area_id": 4, "to_area_id": 2, "distance": 18},
            {"from_area_id": 4, "to_area_id": 3, "distance": 8},
            {"from_area_id": 4, "to_area_id": 5, "distance": 10},
            {"from_area_id": 5, "to_area_id": 1, "distance": 25},
            {"from_area_id": 5, "to_area_id": 2, "distance": 22},
            {"from_area_id": 5, "to_area_id": 3, "distance": 14},
            {"from_area_id": 5, "to_area_id": 4, "distance": 10}
        ]
    }

@app.get("/")
def root():
    return {
        "message": "Truck Dispatch Simulator API",
        "endpoints": {
            "POST /simulate": "Run greedy dispatch simulation",
            "GET /test-data": "Get sample test data"
        }
    }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)