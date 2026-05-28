from typing import List, Dict, Tuple, Optional
from datetime import datetime
import uuid
import logging
from models import *

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class GreedyDispatchOptimizer:
    """
    Greedy Algorithm with Rule-Based Validation for Truck Dispatch
    
    Features:
    1. 7 Rule-Based Validations for truck assignment
    2. Greedy route optimization (nearest station first)
    3. Cost-based decision for continuing to next station
    4. Multi-fuel type support
    """
    
    def __init__(self, distances: List[DistanceMatrix]):
        self.distances = {}
        for d in distances:
            self.distances[(d.from_area_id, d.to_area_id)] = d.distance_km
            # Add reverse direction for symmetry
            if (d.to_area_id, d.from_area_id) not in self.distances:
                self.distances[(d.to_area_id, d.from_area_id)] = d.distance_km
    
    def get_distance(self, from_area_id: int, to_area_id: int) -> float:
        """Get distance between two areas"""
        if from_area_id == to_area_id:
            return 0
        return self.distances.get((from_area_id, to_area_id), 50.0)
    
    # ==================== RULE-BASED VALIDATION (7 RULES) ====================
    
    def validate_truck_for_station(
        self,
        truck: Truck,
        station: Station,
        current_location_id: int,
        driver_hours_used: float = 0,
        max_driver_hours: float = 11
    ) -> Tuple[bool, DispatchDecision, str, dict]:
        """
        Apply 7 rule-based validations for truck assignment
        
        Rules:
        1. Truck must be available
        2. Truck must have remaining capacity
        3. Fuel type compatibility and sufficient quantity
        4. Distance feasibility (can truck reach?)
        5. Cost effectiveness (checked in calling context)
        6. Driver hours limit
        7. No higher priority dispatch (handled by caller)
        """
        validation_checks = {}
        
        # Rule 1: Truck must be available
        rule1 = truck.status == TruckStatus.AVAILABLE
        validation_checks["truck_available"] = rule1
        if not rule1:
            return False, DispatchDecision.SKIP, f"Truck {truck.name} is {truck.status.value}", validation_checks
        
        # Rule 2: Truck must have remaining capacity
        rule2 = truck.total_available > 0
        validation_checks["has_capacity"] = rule2
        if not rule2:
            return False, DispatchDecision.SKIP, f"Truck {truck.name} has no available capacity", validation_checks
        
        # Rule 3: Fuel type compatibility and sufficient quantity
        rule3 = True
        fuel_check_details = []
        
        for fuel_type, required_qty in station.required_fuels.items():
            if required_qty <= 0:
                continue
                
            truck_available = truck.get_available_by_fuel_type(fuel_type)
            
            if truck_available <= 0:
                rule3 = False
                fuel_check_details.append(f"No {fuel_type.value} available")
            elif truck_available < required_qty:
                rule3 = False
                fuel_check_details.append(f"Insufficient {fuel_type.value}: need {required_qty}L, have {truck_available}L")
        
        validation_checks["fuel_compatible"] = rule3
        if not rule3:
            return False, DispatchDecision.REASSIGN, f"Fuel mismatch: {', '.join(fuel_check_details)}", validation_checks
        
        # Rule 4: Distance feasibility (can truck reach?)
        distance = self.get_distance(current_location_id, station.area_id)
        fuel_to_reach = truck.calculate_fuel_cost_for_trip(distance)
        
        rule4 = fuel_to_reach <= truck.total_available
        validation_checks["can_reach"] = rule4
        if not rule4:
            return False, DispatchDecision.SKIP, f"Cannot reach station: needs {fuel_to_reach:.1f}L fuel for travel, has {truck.total_available:.1f}L", validation_checks
        
        # Rule 5: Cost effectiveness (will be checked separately)
        rule5 = True
        validation_checks["cost_effective"] = rule5
        
        # Rule 6: Driver hours limit
        estimated_trip_hours = distance / 50  # Assume 50 km/h average
        rule6 = driver_hours_used + estimated_trip_hours <= max_driver_hours
        validation_checks["driver_hours_ok"] = rule6
        if not rule6:
            return False, DispatchDecision.REASSIGN, f"Driver would exceed hours limit ({driver_hours_used + estimated_trip_hours:.1f}h > {max_driver_hours}h)", validation_checks
        
        # Rule 7: No higher priority (handled by caller)
        rule7 = True
        validation_checks["no_higher_priority"] = rule7
        
        net_available = truck.total_available - fuel_to_reach
        
        return True, DispatchDecision.ASSIGN, "All validation passed", validation_checks
    
    def should_continue_to_next_station(
        self,
        truck: Truck,
        current_station: Station,
        next_station: Station,
        remaining_capacity: float,
        alternative_trucks: List[Truck]
    ) -> Tuple[bool, str, float, float, float]:
        """
        Decision rule: Should truck continue to next station?
        
        Conditions:
        1. Truck must have sufficient remaining fuel quantity
        2. Truck must have correct fuel type for next station
        3. Next station is within acceptable distance
        4. Sending this truck is cheaper than alternatives
        5. Driver has legal hours remaining (checked elsewhere)
        
        Returns:
            (should_continue, reason, cost_savings, current_cost, alternative_cost)
        """
        
        # Condition 1: Check remaining capacity
        if remaining_capacity <= 0:
            return False, "No remaining capacity in truck", 0, 0, 0
        
        # Condition 2: Check fuel type compatibility
        distance = self.get_distance(current_station.area_id, next_station.area_id)
        
        for fuel_type, required_qty in next_station.required_fuels.items():
            if required_qty <= 0:
                continue
            truck_available = truck.get_available_by_fuel_type(fuel_type)
            
            if truck_available <= 0:
                return False, f"Truck does not carry {fuel_type.value}", 0, 0, 0
            if truck_available < required_qty:
                return False, f"Insufficient {fuel_type.value} (needs {required_qty}L, has {truck_available}L)", 0, 0, 0
        
        # Condition 3: Check distance is acceptable
        max_acceptable_distance = 150  # km
        if distance > max_acceptable_distance:
            return False, f"Next station too far ({distance:.1f}km > {max_acceptable_distance}km)", 0, 0, 0
        
        # Condition 4: Cost effectiveness check
        current_cost = truck.calculate_fuel_cost_for_trip(distance)
        
        # Find cheapest alternative
        min_alternative_cost = float('inf')
        best_alternative = None
        
        for alt_truck in alternative_trucks:
            if alt_truck.id != truck.id and alt_truck.status == TruckStatus.AVAILABLE:
                alt_cost = alt_truck.calculate_fuel_cost_for_trip(distance)
                if alt_cost < min_alternative_cost:
                    min_alternative_cost = alt_cost
                    best_alternative = alt_truck
        
        savings = min_alternative_cost - current_cost if min_alternative_cost != float('inf') else 0
        
        # Continue if this truck is cheaper OR if savings is not significantly negative
        threshold = 0.2  # 20% threshold
        if min_alternative_cost != float('inf') and current_cost > min_alternative_cost * (1 + threshold):
            return False, f"Cheaper to use {best_alternative.name if best_alternative else 'another truck'} (cost: {current_cost:.1f}L vs {min_alternative_cost:.1f}L)", savings, current_cost, min_alternative_cost
        
        return True, f"Continue OK - {current_cost:.1f}L fuel needed", savings, current_cost, min_alternative_cost
    
    # ==================== GREEDY DISPATCH ALGORITHM ====================
    
    def greedy_dispatch(
        self,
        trucks: List[Truck],
        areas: List[Area],
        start_area_id: int = None,
        driver_hours_used: float = 0,
        max_driver_hours: float = 11,
        optimization_mode: str = "greedy"
    ) -> DispatchResult:
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
                if station.total_required > 0:
                    all_stations.append(station)
        
        if not all_stations:
            return DispatchResult(
                session_id=f"DISPATCH_{datetime.now().strftime('%Y%m%d_%H%M%S')}_{uuid.uuid4().hex[:6]}",
                assignments=[],
                unfulfilled_stations=[],
                summary={"message": "No stations with demand found"}
            )
        
        # Filter available trucks
        available_trucks = [t for t in trucks if t.status == TruckStatus.AVAILABLE]
        
        # Sort trucks by efficiency (best first)
        sorted_trucks = sorted(available_trucks, key=lambda t: t.fuel_efficiency_km_per_l, reverse=True)
        
        assignments = []
        remaining_stations = all_stations.copy()
        
        logger.info(f"Starting Greedy Dispatch")
        logger.info(f"  Trucks: {len(sorted_trucks)}")
        logger.info(f"  Stations needing delivery: {len(remaining_stations)}")
        logger.info(f"  Total demand: {sum(s.total_required for s in remaining_stations):.1f} L")
        
        for truck in sorted_trucks:
            if not remaining_stations:
                break
            
            logger.info(f"\nProcessing {truck.name} (efficiency: {truck.fuel_efficiency_km_per_l} km/L)")
            logger.info(f"  Available fuel: {truck.total_available:.1f} L")
            
            # Build route for this truck
            current_location = start_area_id or truck.current_area_id
            route_stops = []  # List of (station, distance, fuel_to_deliver_by_type)
            validation_messages = []
            total_delivered = 0
            
            # Make a copy of truck's available fuel for simulation
            simulated_available = truck.total_available
            simulated_compartments = {
                ft: truck.get_available_by_fuel_type(ft) 
                for ft in FuelType
            }
            
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
                        fuel_to_reach = truck.calculate_fuel_cost_for_trip(distance)
                        if fuel_to_reach <= simulated_available:
                            if distance < best_distance:
                                best_distance = distance
                                best_station = station
                                
                                # Calculate delivery amounts by fuel type
                                best_delivery = {}
                                for fuel_type, required in station.required_fuels.items():
                                    if required > 0:
                                        available = simulated_compartments.get(fuel_type, 0)
                                        deliver = min(required, available)
                                        if deliver > 0:
                                            best_delivery[fuel_type] = deliver
                
                if best_station is None:
                    break
                
                # Check if should continue to this station (cost effectiveness)
                if route_stops and optimization_mode == "cost_based":
                    should_continue, continue_reason, savings, curr_cost, alt_cost = self.should_continue_to_next_station(
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
                fuel_to_reach = truck.calculate_fuel_cost_for_trip(best_distance)
                simulated_available -= fuel_to_reach
                
                # Update simulated compartments
                total_delivered_this_stop = 0
                for fuel_type, deliver_qty in best_delivery.items():
                    if deliver_qty > 0:
                        simulated_compartments[fuel_type] = simulated_compartments.get(fuel_type, 0) - deliver_qty
                        total_delivered_this_stop += deliver_qty
                
                route_stops.append((best_station, best_distance, best_delivery))
                current_location = best_station.area_id
                total_delivered += total_delivered_this_stop
                
                # Update driver hours
                driver_hours_used += best_distance / 50
                
                # Remove from remaining
                remaining_for_truck.remove(best_station)
                remaining_stations = [s for s in remaining_stations if s.id != best_station.id]
                
                logger.info(f"  → Added {best_station.name}: {best_distance}km, delivering {total_delivered_this_stop}L")
                logger.info(f"    Remaining fuel: {simulated_available:.1f}L")
            
            # Create assignment if route has stops
            if route_stops:
                # Calculate total metrics
                stops = []
                prev_location = start_area_id or truck.current_area_id
                
                for station, distance, delivery in route_stops:
                    for fuel_type, qty in delivery.items():
                        if qty > 0:
                            is_primary = (station.area_id == truck.current_area_id)
                            stops.append(DeliveryStop(
                                station_id=station.id,
                                station_name=station.name,
                                fuel_type=fuel_type,
                                liters=qty,
                                distance_from_previous=distance,
                                is_primary=is_primary
                            ))
                    prev_location = station.area_id
                
                total_distance = sum(s.distance_from_previous for s in stops)
                total_fuel = truck.calculate_fuel_cost_for_trip(total_distance)
                
                assignments.append(DispatchAssignment(
                    truck_id=truck.id,
                    truck_name=truck.name,
                    truck_plate=truck.plate_number,
                    stops=stops,
                    total_distance_km=total_distance,
                    total_fuel_consumed=total_fuel,
                    total_delivered=total_delivered,
                    is_optimal=True,
                    validation_messages=validation_messages
                ))
                
                logger.info(f"\n  ✅ {truck.name} assigned:")
                logger.info(f"     Stops: {len(stops)}")
                logger.info(f"     Distance: {total_distance:.1f} km")
                logger.info(f"     Fuel: {total_fuel:.1f} L")
                logger.info(f"     Delivered: {total_delivered:.1f} L")
        
        # Calculate summary
        total_demand = sum(s.total_required for s in all_stations)
        total_delivered_sum = sum(a.total_delivered for a in assignments)
        
        summary = {
            "total_demand_liters": round(total_demand, 2),
            "total_delivered_liters": round(total_delivered_sum, 2),
            "total_undelivered_liters": round(total_demand - total_delivered_sum, 2),
            "fulfillment_rate_percent": round(total_delivered_sum / total_demand * 100 if total_demand > 0 else 0, 2),
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
        
        unfulfilled = [s for s in all_stations if s.id not in 
                       [stop.station_id for a in assignments for stop in a.stops]]
        
        logger.info(f"\n{'='*60}")
        logger.info(f"DISPATCH SUMMARY")
        logger.info(f"{'='*60}")
        for key, value in summary.items():
            logger.info(f"  {key}: {value}")
        
        return DispatchResult(
            session_id=f"DISPATCH_{datetime.now().strftime('%Y%m%d_%H%M%S')}_{uuid.uuid4().hex[:6]}",
            assignments=assignments,
            unfulfilled_stations=unfulfilled,
            summary=summary
        )