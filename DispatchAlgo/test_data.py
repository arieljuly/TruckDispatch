from models import Truck, Area, DistanceMatrix
from simulate import run_greedy_dispatch

# Test data
trucks = [
    Truck(id=1, name="Truck 1", liters=20, area_id=3),
    Truck(id=2, name="Truck 2", liters=85, area_id=1),
    Truck(id=3, name="Truck 3", liters=100, area_id=2),
    Truck(id=4, name="Truck 4", liters=55, area_id=5),
    Truck(id=5, name="Truck 5", liters=120, area_id=4)
]

areas = [
    Area(id=1, name="Area 1", demand=80),
    Area(id=2, name="Area 2", demand=100),
    Area(id=3, name="Area 3", demand=20),
    Area(id=4, name="Area 4", demand=50),
    Area(id=5, name="Area 5", demand=120)
]

# Run simulation
result = run_greedy_dispatch(trucks, areas)

print("\n" + "="*50)
print("SIMULATION RESULTS")
print("="*50)

for step in result.steps:
    print(f"Step {step.step}: {step.truck_name} → {step.area_name}: {step.allocated}L "
          f"(Primary: {step.is_primary_area}, Distance: {step.distance_used})")

print("\n" + "="*50)
print("SUMMARY")
print("="*50)
for key, value in result.summary.items():
    print(f"{key}: {value}")