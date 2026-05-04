import copy
from datetime import datetime
from typing import List, Optional
from models import Truck, Area, DistanceMatrix, Step, SimulationResult

def get_distance(from_area_id: int, to_area_id: int, distances: List[DistanceMatrix]) -> float:
    """Get distance between two areas, return large number if not found"""
    if from_area_id == to_area_id:
        return 0
    
    for dist in distances:
        if dist.from_area_id == from_area_id and dist.to_area_id == to_area_id:
            return dist.distance
    
    # Default distance if not in matrix (penalize unknown routes)
    return 9999

def run_greedy_dispatch(
    trucks: List[Truck], 
    areas: List[Area], 
    distances: List[DistanceMatrix] = None
) -> SimulationResult:
    """
    Run greedy algorithm for truck dispatch
    
    Phase 1: Match trucks to their own area
    Phase 2: Match remaining by nearest distance
    Phase 3: Fallback matching for any leftovers
    """
    
    if distances is None:
        distances = []
    
    # Deep copy to avoid modifying original data
    trucks = copy.deepcopy(trucks)
    areas = copy.deepcopy(areas)
    
    # Filter to only available trucks
    trucks = [t for t in trucks if t.status == "Available"]
    
    steps = []
    step_number = 1
    
    # Track original demands for summary
    original_demands = {area.id: area.demand for area in areas}
    original_supplies = {truck.id: truck.liters for truck in trucks}
    
    # ===== PHASE 1: Match trucks to their own area =====
    print(f"[Phase 1] Matching {len(trucks)} trucks to their own areas")
    
    for truck in trucks[:]:  # Iterate over copy
        if truck.liters <= 0:
            continue
            
        # Find the area where this truck resides
        own_area = None
        for area in areas:
            if area.id == truck.area_id and area.demand > 0:
                own_area = area
                break
        
        if own_area:
            # Allocate as much as needed, but not more than truck has
            allocated = min(truck.liters, own_area.demand)
            
            if allocated > 0:
                steps.append(Step(
                    step=step_number,
                    truck_id=truck.id,
                    truck_name=truck.name,
                    area_id=own_area.id,
                    area_name=own_area.name,
                    allocated=allocated,
                    truck_remaining=truck.liters - allocated,
                    area_remaining=own_area.demand - allocated,
                    is_primary_area=True,
                    distance_used=0
                ))
                
                # Update values
                truck.liters -= allocated
                own_area.demand -= allocated
                step_number += 1
                
                print(f"  ✓ {truck.name} → {own_area.name}: {allocated}L (primary)")
    
    # Remove trucks with zero liters
    trucks = [t for t in trucks if t.liters > 0]
    # Remove areas with zero demand
    areas = [a for a in areas if a.demand > 0]
    
    # ===== PHASE 2: Greedy matching by nearest distance =====
    print(f"\n[Phase 2] Cross-area matching with {len(trucks)} trucks and {len(areas)} areas")
    
    while areas and trucks:
        best_match = None
        best_distance = float('inf')
        
        # Find closest truck-area pair
        for area in areas:
            for truck in trucks:
                if truck.liters <= 0 or area.demand <= 0:
                    continue
                
                distance = get_distance(truck.area_id, area.id, distances)
                
                # Prioritize smaller distance
                if distance < best_distance:
                    best_distance = distance
                    best_match = (truck, area)
        
        if not best_match:
            break
            
        truck, area = best_match
        allocated = min(truck.liters, area.demand)
        
        steps.append(Step(
            step=step_number,
            truck_id=truck.id,
            truck_name=truck.name,
            area_id=area.id,
            area_name=area.name,
            allocated=allocated,
            truck_remaining=truck.liters - allocated,
            area_remaining=area.demand - allocated,
            is_primary_area=False,
            distance_used=best_distance
        ))
        
        # Update values
        truck.liters -= allocated
        area.demand -= allocated
        step_number += 1
        
        print(f"  ✓ {truck.name} → {area.name}: {allocated}L (distance: {best_distance})")
        
        # Cleanup
        if truck.liters == 0:
            trucks.remove(truck)
        if area.demand == 0:
            areas.remove(area)
    
    # ===== PHASE 3: Any remaining match (greedy any order) =====
    print(f"\n[Phase 3] Final matching")
    
    # Rebuild lists if needed
    areas = [a for a in areas if a.demand > 0]
    trucks = [t for t in trucks if t.liters > 0]
    
    for area in areas[:]:
        for truck in trucks[:]:
            if truck.liters <= 0 or area.demand <= 0:
                continue
            
            allocated = min(truck.liters, area.demand)
            
            steps.append(Step(
                step=step_number,
                truck_id=truck.id,
                truck_name=truck.name,
                area_id=area.id,
                area_name=area.name,
                allocated=allocated,
                truck_remaining=truck.liters - allocated,
                area_remaining=area.demand - allocated,
                is_primary_area=False,
                distance_used=get_distance(truck.area_id, area.id, distances)
            ))
            
            truck.liters -= allocated
            area.demand -= allocated
            step_number += 1
            
            print(f"  ✓ {truck.name} → {area.name}: {allocated}L (fallback)")
            
            if truck.liters == 0:
                trucks.remove(truck)
            if area.demand == 0:
                areas.remove(area)
                break
    
    # Calculate summary
    total_demand = sum(original_demands.values())
    total_supply = sum(original_supplies.values())
    fulfilled_demand = total_demand - sum(a.demand for a in areas)
    
    summary = {
        "total_demand_liters": total_demand,
        "total_supply_liters": total_supply,
        "fulfilled_demand_liters": fulfilled_demand,
        "unfulfilled_demand_liters": sum(a.demand for a in areas),
        "remaining_supply_liters": sum(t.liters for t in trucks),
        "total_steps": step_number - 1,
        "algorithm_used": "Greedy with proximity",
        "primary_matches": sum(1 for s in steps if s.is_primary_area),
        "cross_matches": sum(1 for s in steps if not s.is_primary_area)
    }
    
    return SimulationResult(
        session_id=f"DISPATCH_{datetime.now().strftime('%Y%m%d_%H%M%S')}",
        steps=steps,
        final_trucks=trucks,
        final_areas=areas,
        summary=summary
    )