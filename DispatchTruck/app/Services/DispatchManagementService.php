<?php

namespace App\Services;

use App\Models\DispatchSession;
use App\Models\DispatchAllocation;
use App\Models\DispatchAllocationItem;
use App\Models\Truck;
use App\Models\Area;
use App\Models\Station;
use App\Models\DeliveryRequest;
use App\Models\PurchaseOrderItem;
use App\Models\TruckCompartment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DispatchManagementService
{
    protected $fuelPredictionService;
    protected $optimizationService;

    public function __construct(
        FuelPredictionService $fuelPredictionService,
        DispatchOptimizationService $optimizationService
    ) {
        $this->fuelPredictionService = $fuelPredictionService;
        $this->optimizationService = $optimizationService;
    }

    /**
     * Run complete greedy optimization for area
     */
    public function runGreedyOptimizationForArea(Area $area, array $options = []): ?array
    {
        $defaults = [
            'start_area_id' => null,
            'optimization_mode' => 'greedy',
            'include_all_trucks' => true
        ];
        $options = array_merge($defaults, $options);

        // Get all available trucks
        $trucks = Truck::with(['compartments.fuelType', 'currentArea'])
            ->where('status', 'available')
            ->whereNull('deleted_at')
            ->get();

        if ($trucks->isEmpty()) {
            Log::warning('No available trucks for optimization');
            return null;
        }

        // Get all stations with pending requirements in this area
        $stations = $area->stations()
            ->with(['purchaseOrders.items.fuelType'])
            ->get()
            ->filter(function ($station) {
                // Only include stations with pending deliveries
                $hasPending = false;
                foreach ($station->purchaseOrders as $po) {
                    foreach ($po->items as $item) {
                        if (($item->qty_liters - $item->delivered_ltrs) > 0) {
                            $hasPending = true;
                            break;
                        }
                    }
                }
                return $hasPending;
            });

        if ($stations->isEmpty()) {
            Log::info('No stations with pending requirements in area', ['area_id' => $area->id]);
            return null;
        }

        // Get distance matrix
        $distances = DistanceMatrix::all();

        // Run Python optimization
        $result = $this->optimizationService->runGreedyOptimization(
            $trucks,
            collect([$area]),
            $distances,
            $options['start_area_id'],
            $options['optimization_mode']
        );

        if ($result) {
            // Log the optimization result
            Log::info('Greedy optimization completed', [
                'area_id' => $area->id,
                'session_id' => $result['session_id'],
                'fulfillment_rate' => $result['summary']['fulfillment_rate_percent'],
                'trips' => count($result['assignments'])
            ]);

            // Store dispatch session
            $session = $this->createDispatchSessionFromOptimizationResult($result, $area);

            $result['db_session_id'] = $session->id;
        }

        return $result;
    }

    /**
     * Create dispatch session from optimization result
     */
    protected function createDispatchSessionFromOptimizationResult(array $result, Area $area): DispatchSession
    {
        return DB::transaction(function () use ($result, $area) {
            $summary = $result['summary'];

            $session = DispatchSession::create([
                'algorithm_used' => 'greedy_optimization',
                'total_demand' => $summary['total_demand_liters'],
                'total_supply' => $summary['total_delivered_liters'],
                'status' => 'pending',
                'notes' => "Greedy optimization from Python API - Session {$result['session_id']}",
                'optimization_method' => 'ai_ml',
                'distance_km' => $summary['total_distance_km'],
                'area_id' => $area->id,
            ]);

            // Create allocations from assignments
            foreach ($result['assignments'] as $assignment) {
                $truck = Truck::find($assignment['truck_id']);

                if ($truck) {
                    $allocation = DispatchAllocation::create([
                        'dispatch_session_id' => $session->id,
                        'truck_id' => $assignment['truck_id'],
                        'liters_allocated' => $assignment['total_delivered'],
                        'distance_used' => $assignment['total_distance_km'],
                        'status' => 'planned'
                    ]);

                    // Create allocation items for each stop
                    foreach ($assignment['stops'] as $stop) {
                        // Find the purchase order item for this station and fuel type
                        $poItem = $this->findPendingPurchaseOrderItem(
                            $stop['station_id'],
                            $stop['fuel_type']
                        );

                        if ($poItem) {
                            // Find appropriate compartment
                            $compartment = $truck->compartments
                                ->where('current_fuel_type_id', $poItem->fuel_type_id)
                                ->where('available_ltrs', '>=', $stop['liters'])
                                ->first();

                            if ($compartment) {
                                DispatchAllocationItem::create([
                                    'dispatch_allocation_id' => $allocation->id,
                                    'purchase_order_item_id' => $poItem->id,
                                    'truck_compartment_id' => $compartment->id,
                                    'liters_allocated' => $stop['liters'],
                                    'status' => 'pending'
                                ]);
                            }
                        }
                    }
                }
            }

            return $session;
        });
    }

    /**
     * Find pending purchase order item for station and fuel type
     */
    protected function findPendingPurchaseOrderItem(int $stationId, string $fuelType): ?PurchaseOrderItem
    {
        return PurchaseOrderItem::whereHas('purchaseOrder', function ($query) use ($stationId) {
            $query->where('station_id', $stationId)
                ->whereIn('status', ['pending', 'partial']);
        })
            ->whereHas('fuelType', function ($query) use ($fuelType) {
                $query->where('fuel_code', $fuelType);
            })
            ->whereRaw('qty_liters - delivered_ltrs > 0')
            ->first();
    }

    /**
     * Execute a dispatch allocation (actual delivery)
     */
    public function executeAllocation(DispatchAllocation $allocation, array $deliveryDetails): bool
    {
        return DB::transaction(function () use ($allocation, $deliveryDetails) {
            // Mark allocation as in progress
            $allocation->status = 'in_progress';
            $allocation->save();

            foreach ($deliveryDetails as $delivery) {
                $item = DispatchAllocationItem::find($delivery['item_id']);

                if ($item && $item->status === 'pending') {
                    $item->markAsDelivered();

                    // Update truck compartment
                    $compartment = $item->truckCompartment;
                    $compartment->loaded_ltrs += $item->liters_allocated;
                    $compartment->available_ltrs = $compartment->capacity_ltrs - $compartment->loaded_ltrs;
                    $compartment->save();
                }
            }

            // Check if all items are delivered
            $pendingItems = $allocation->items()->where('status', 'pending')->count();

            if ($pendingItems === 0) {
                $allocation->status = 'completed';
                $allocation->save();

                // Update dispatch session
                $session = $allocation->dispatchSession;
                $allCompleted = $session->allocations()
                    ->where('status', '!=', 'completed')
                    ->count() === 0;

                if ($allCompleted) {
                    $session->status = 'executed';
                    $session->save();
                }
            }

            Log::info('Allocation executed', [
                'allocation_id' => $allocation->id,
                'deliveries' => count($deliveryDetails)
            ]);

            return true;
        });
    }

    /**
     * Get optimal route for a truck (single truck optimization)
     */
    public function getOptimalRouteForTruck(Truck $truck, array $stationIds, ?int $startAreaId = null): array
    {
        $stations = Station::whereIn('id', $stationIds)->get();

        // Get distances between stations
        $distances = [];
        $currentLocation = $startAreaId ?? $truck->current_area_id;

        foreach ($stations as $station) {
            $distances[$station->id] = $this->optimizationService->getDistanceBetweenAreas(
                $currentLocation,
                $station->area_id
            );
            $currentLocation = $station->area_id;
        }

        // Sort by nearest first (greedy)
        uasort($distances, function ($a, $b) {
            return $a <=> $b;
        });

        $route = [];
        $currentLocation = $startAreaId ?? $truck->current_area_id;
        $totalDistance = 0;
        $totalFuelNeeded = 0;

        foreach (array_keys($distances) as $stationId) {
            $station = $stations->firstWhere('id', $stationId);
            $distance = $this->optimizationService->getDistanceBetweenAreas($currentLocation, $station->area_id);

            $route[] = [
                'station_id' => $station->id,
                'station_name' => $station->station_name,
                'distance_km' => $distance,
                'fuel_needed' => $distance / $this->optimizationService->getTruckEfficiency($truck)
            ];

            $totalDistance += $distance;
            $totalFuelNeeded += $distance / $this->optimizationService->getTruckEfficiency($truck);
            $currentLocation = $station->area_id;
        }

        return [
            'truck' => [
                'id' => $truck->id,
                'name' => $truck->truck_name,
                'efficiency_km_per_l' => $this->optimizationService->getTruckEfficiency($truck),
                'available_fuel_ltrs' => $truck->compartments->sum('available_ltrs')
            ],
            'route' => $route,
            'total_distance_km' => round($totalDistance, 2),
            'total_fuel_required' => round($totalFuelNeeded, 2),
            'can_complete' => $truck->compartments->sum('available_ltrs') >= $totalFuelNeeded,
            'stops_count' => count($route)
        ];
    }

    /**
     * Get dispatch session with full optimization details
     */
    public function getDispatchWithAlternatives(DispatchSession $session): array
    {
        $alternatives = $this->optimizationService->getRecommendations(
            $session->total_demand,
            $session->allocations->first()?->area_id,
            5
        );

        return [
            'session' => $session->load(['recommendedTruck', 'assignedTruck', 'executor', 'allocations.items']),
            'recommended_truck' => $session->recommendedTruck,
            'assigned_truck' => $session->assignedTruck,
            'alternative_trucks' => $this->optimizationService->formatRecommendationsForDisplay($alternatives),
            'optimization_summary' => [
                'total_demand' => $session->total_demand,
                'total_allocated' => $session->allocations->sum('liters_allocated'),
                'total_distance' => $session->allocations->sum('distance_used'),
                'allocation_count' => $session->allocations->count()
            ]
        ];
    }

    /**
     * Validate if a specific truck can serve a station with rule-based checks
     */
    public function validateTruckForStation(Truck $truck, Station $station, int $currentLocationId): array
    {
        return $this->optimizationService->validateTruckForStation($truck, $station, $currentLocationId);
    }

    /**
     * Check if truck should continue to next station
     */
    public function shouldTruckContinue(
        Truck $truck,
        Station $currentStation,
        Station $nextStation,
        Collection $alternativeTrucks
    ): array {
        $remainingCapacity = $truck->compartments->sum('available_ltrs');

        return $this->optimizationService->validateContinueToNextStation(
            $truck,
            $currentStation,
            $nextStation,
            $remainingCapacity,
            $alternativeTrucks
        );
    }
}