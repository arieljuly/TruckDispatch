"""
Test scenarios for the truck dispatch optimization system
Uses only standard library (no external dependencies)
Run with: python test_scenarios.py
"""

import json
import urllib.request
import urllib.error

API_URL = "http://localhost:8002"

def make_request(endpoint, method="GET", data=None):
    """Make HTTP request using urllib"""
    url = f"{API_URL}{endpoint}"
    
    try:
        if method == "POST" and data:
            json_data = json.dumps(data).encode('utf-8')
            req = urllib.request.Request(
                url, 
                data=json_data,
                headers={'Content-Type': 'application/json'}
            )
        else:
            req = urllib.request.Request(url)
        
        with urllib.request.urlopen(req, timeout=10) as response:
            return response.status, json.loads(response.read().decode('utf-8'))
            
    except urllib.error.HTTPError as e:
        error_body = e.read().decode('utf-8') if e.fp else None
        try:
            error_json = json.loads(error_body) if error_body else None
        except:
            error_json = error_body
        return e.code, error_json
    except urllib.error.URLError as e:
        return None, str(e)

def test_health():
    """Test health endpoint"""
    print("\n" + "="*60)
    print("TEST: Health Check")
    print("="*60)
    
    status, result = make_request("/health")
    
    if status == 200 and result:
        print(f"✅ API is healthy")
        print(f"   Status: {result.get('status')}")
        print(f"   Fuel Predictor: {result.get('fuel_predictor')}")
        return True
    else:
        print(f"❌ Error: {status or result}")
        return False

def test_datasets_info():
    """Test datasets info endpoint"""
    print("\n" + "="*60)
    print("TEST: Datasets Info")
    print("="*60)
    
    status, result = make_request("/api/v2/datasets/info")
    
    if status == 200 and result:
        print(f"✅ Datasets loaded successfully")
        print(f"   Path: {result.get('datasets_path')}")
        datasets = result.get('datasets', {})
        for filename, info in datasets.items():
            if info.get('exists'):
                print(f"   📄 {filename}: {info.get('rows', 0):,} rows, {info.get('size_kb', 0):.2f} KB")
        return True
    else:
        print(f"❌ Error: {status or result}")
        return False

def test_fuel_prediction():
    """Test fuel prediction endpoint"""
    print("\n" + "="*60)
    print("TEST: Fuel Prediction")
    print("="*60)
    
    payload = {
        "distance_km": 100,
        "actual_duration_hours": 2.0,
        "average_mpg": 6.0,
        "idle_time_hours": 1.0,
        "detention_minutes": 30,
        "delay_minutes": 15,
        "on_time_flag": True
    }
    
    status, result = make_request("/api/v2/predict/fuel", method="POST", data=payload)
    
    if status == 200 and result:
        print(f"\n✅ Prediction successful!")
        print(f"   Distance: 100 km")
        print(f"   Predicted Fuel: {result.get('predicted_fuel_liters')} L")
        print(f"   Confidence: {result.get('confidence_score')}")
        print(f"   Efficiency: {result.get('efficiency_km_per_l')} km/L")
        print(f"   Range: {result.get('prediction_interval_lower')} - {result.get('prediction_interval_upper')} L")
        return True
    else:
        print(f"\n❌ Error: Status {status}")
        if result:
            print(f"   Details: {result}")
        return False

def test_validation_rules():
    """Test rule-based validation"""
    print("\n" + "="*60)
    print("TEST: Rule-Based Validation (7 Rules)")
    print("="*60)
    
    payload = {
        "truck": {
            "id": 1,
            "name": "Truck 1",
            "plate_number": "ABC-123",
            "current_area_id": 1,
            "max_capacity_ltrs": 50,
            "fuel_efficiency_km_per_l": 10,
            "status": "available",
            "compartments": [
                {
                    "compartment_no": 1,
                    "fuel_type": "diesel",
                    "capacity_ltrs": 50,
                    "loaded_ltrs": 25
                }
            ]
        },
        "station": {
            "id": 1,
            "name": "Station 1",
            "area_id": 2,
            "required_fuels": {"diesel": 20}
        },
        "current_location_id": 1,
        "driver_hours_used": 0,
        "max_driver_hours": 11
    }
    
    status, result = make_request("/api/v2/dispatch/validate", method="POST", data=payload)
    
    if status == 200 and result:
        print(f"\n✅ Validation complete!")
        print(f"   Can Assign: {result.get('can_assign')}")
        print(f"   Decision: {result.get('decision')}")
        print(f"   Reason: {result.get('reason')}")
        print(f"   Distance: {result.get('distance_km')} km")
        print(f"   Fuel to Reach: {result.get('fuel_to_reach')} L")
        print(f"   Net Available: {result.get('net_available')} L")
        print(f"\n   Validation Checks:")
        for check, passed in result.get('validation_checks', {}).items():
            print(f"     {check}: {'✅' if passed else '❌'}")
        return True
    else:
        print(f"\n❌ Error: Status {status}")
        return False

def test_dispatch_with_loaded_truck():
    """Test dispatch with a properly loaded truck"""
    print("\n" + "="*60)
    print("TEST: Dispatch with Loaded Truck")
    print("="*60)
    
    payload = {
        "trucks": [
            {
                "id": 1,
                "name": "Truck 1",
                "plate_number": "ABC-123",
                "current_area_id": 1,
                "max_capacity_ltrs": 100,
                "fuel_efficiency_km_per_l": 10,
                "status": "available",
                "compartments": [
                    {
                        "compartment_no": 1,
                        "fuel_type": "diesel",
                        "capacity_ltrs": 100,
                        "loaded_ltrs": 80
                    }
                ]
            }
        ],
        "areas": [
            {
                "id": 1,
                "name": "Area 1",
                "stations": [
                    {
                        "id": 1,
                        "name": "Station 1",
                        "area_id": 1,
                        "required_fuels": {"diesel": 15}
                    },
                    {
                        "id": 2,
                        "name": "Station 2",
                        "area_id": 1,
                        "required_fuels": {"diesel": 5}
                    }
                ]
            }
        ],
        "distances": [
            {"from_area_id": 1, "to_area_id": 1, "distance_km": 10}
        ],
        "start_area_id": 1,
        "optimization_mode": "greedy"
    }
    
    status, result = make_request("/api/v2/dispatch/optimize", method="POST", data=payload)
    
    if status == 200 and result:
        print(f"\n✅ Dispatch successful!")
        print(f"   Session ID: {result.get('session_id')}")
        print(f"   Fulfillment Rate: {result.get('summary', {}).get('fulfillment_rate_percent')}%")
        print(f"   Total Trips: {result.get('summary', {}).get('total_trips')}")
        print(f"   Total Distance: {result.get('summary', {}).get('total_distance_km')} km")
        
        for assignment in result.get('assignments', []):
            print(f"\n   Truck: {assignment.get('truck_name')}")
            print(f"     Total Delivered: {assignment.get('total_delivered')} L")
            print(f"     Total Distance: {assignment.get('total_distance_km')} km")
            for stop in assignment.get('stops', []):
                print(f"       → {stop.get('station_name')}: {stop.get('liters')}L of {stop.get('fuel_type')} ({stop.get('distance_from_previous')}km)")
        return True
    else:
        print(f"\n❌ Error: Status {status}")
        if result:
            print(f"   Response: {result}")
        return False

def test_multi_stop_dispatch():
    """Test dispatch with multiple stops and travel distance"""
    print("\n" + "="*60)
    print("TEST: Multi-Stop Dispatch with Travel Distance")
    print("="*60)
    
    payload = {
        "trucks": [
            {
                "id": 1,
                "name": "Truck 1",
                "plate_number": "ABC-123",
                "current_area_id": 1,
                "max_capacity_ltrs": 100,
                "fuel_efficiency_km_per_l": 10,
                "status": "available",
                "compartments": [
                    {
                        "compartment_no": 1,
                        "fuel_type": "diesel",
                        "capacity_ltrs": 100,
                        "loaded_ltrs": 50
                    }
                ]
            },
            {
                "id": 2,
                "name": "Truck 2",
                "plate_number": "XYZ-789",
                "current_area_id": 2,
                "max_capacity_ltrs": 100,
                "fuel_efficiency_km_per_l": 12,
                "status": "available",
                "compartments": [
                    {
                        "compartment_no": 1,
                        "fuel_type": "diesel",
                        "capacity_ltrs": 100,
                        "loaded_ltrs": 40
                    }
                ]
            }
        ],
        "areas": [
            {
                "id": 1,
                "name": "Area 1",
                "stations": [
                    {
                        "id": 1,
                        "name": "Station A",
                        "area_id": 1,
                        "required_fuels": {"diesel": 20}
                    }
                ]
            },
            {
                "id": 2,
                "name": "Area 2",
                "stations": [
                    {
                        "id": 2,
                        "name": "Station B",
                        "area_id": 2,
                        "required_fuels": {"diesel": 15}
                    }
                ]
            }
        ],
        "distances": [
            {"from_area_id": 1, "to_area_id": 1, "distance_km": 0},
            {"from_area_id": 1, "to_area_id": 2, "distance_km": 50},
            {"from_area_id": 2, "to_area_id": 1, "distance_km": 50},
            {"from_area_id": 2, "to_area_id": 2, "distance_km": 0}
        ],
        "start_area_id": 1,
        "optimization_mode": "greedy"
    }
    
    status, result = make_request("/api/v2/dispatch/optimize", method="POST", data=payload)
    
    if status == 200 and result:
        print(f"\n✅ Dispatch successful!")
        print(f"   Session ID: {result.get('session_id')}")
        print(f"   Fulfillment Rate: {result.get('summary', {}).get('fulfillment_rate_percent')}%")
        print(f"   Total Trips: {result.get('summary', {}).get('total_trips')}")
        print(f"   Total Distance: {result.get('summary', {}).get('total_distance_km')} km")
        print(f"   Total Fuel Consumed: {result.get('summary', {}).get('total_fuel_consumed_liters')} L")
        
        for assignment in result.get('assignments', []):
            print(f"\n   Truck: {assignment.get('truck_name')} (Efficiency: {assignment.get('fuel_efficiency_actual')} km/L)")
            print(f"     Delivered: {assignment.get('total_delivered')} L")
            print(f"     Distance: {assignment.get('total_distance_km')} km")
        return True
    else:
        print(f"\n❌ Error: Status {status}")
        if result:
            print(f"   Response: {result}")
        return False

def run_all_tests():
    """Run all test scenarios"""
    print("\n" + "🔧" * 30)
    print("RUNNING TRUCK DISPATCH OPTIMIZATION TESTS")
    print("🔧" * 30)
    
    tests = [
        ("Health Check", test_health),
        ("Datasets Info", test_datasets_info),
        ("Fuel Prediction", test_fuel_prediction),
        ("Rule Validation (7 Rules)", test_validation_rules),
        ("Dispatch with Loaded Truck", test_dispatch_with_loaded_truck),
        ("Multi-Stop Dispatch", test_multi_stop_dispatch),
    ]
    
    results = []
    for name, test_func in tests:
        try:
            result = test_func()
            results.append((name, result))
        except Exception as e:
            print(f"\n❌ {name} failed with error: {e}")
            results.append((name, False))
    
    print("\n" + "="*60)
    print("TEST SUMMARY")
    print("="*60)
    
    passed = sum(1 for _, r in results if r)
    failed = len(results) - passed
    
    for name, result in results:
        status = "✅ PASS" if result else "❌ FAIL"
        print(f"{status} - {name}")
    
    print("\n" + "="*60)
    print(f"TOTAL: {passed} passed, {failed} failed")
    print("="*60)
    
    if failed > 0:
        print("\n💡 Tip: Make sure trucks have 'loaded_ltrs' set properly")
        print("   available_ltrs = capacity_ltrs - loaded_ltrs")

if __name__ == "__main__":
    # First check if API is running
    try:
        status, _ = make_request("/health")
        if status == 200:
            print("✅ API is running!")
            run_all_tests()
        else:
            print(f"\n❌ API returned status {status}")
            print("Please make sure the API is running on port 8002")
    except Exception as e:
        print(f"\n❌ ERROR: Cannot connect to API!")
        print(f"   {e}")
        print("\nPlease start the API first with:")
        print("   python api.py")
        print("\nThen run this test script again.")