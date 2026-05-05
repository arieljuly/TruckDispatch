<?php

namespace App\Livewire;

use Livewire\Component;

class TruckDispatch extends Component
{
    public $result = null;
    public $isLoading = false;
    public $error = null;

    public function runSimulation()
    {
        $this->isLoading = true;
        
        $trucks = [
            ['id' => 1, 'name' => 'Truck A', 'capacity' => 5000],
            ['id' => 2, 'name' => 'Truck B', 'capacity' => 8000],
            ['id' => 3, 'name' => 'Truck C', 'capacity' => 6000],
        ];

        $areas = [
            ['id' => 1, 'name' => 'North Area', 'demand' => 4500, 'primary_truck_id' => 1],
            ['id' => 2, 'name' => 'South Area', 'demand' => 7000, 'primary_truck_id' => 2],
            ['id' => 3, 'name' => 'East Area', 'demand' => 3000, 'primary_truck_id' => 3],
            ['id' => 4, 'name' => 'West Area', 'demand' => 5500, 'primary_truck_id' => 1],
        ];

        $this->result = $this->greedyDispatch($trucks, $areas);
        $this->isLoading = false;
    }

    private function greedyDispatch($trucks, $areas)
    {
        $sortedAreas = collect($areas)->sortByDesc('demand')->values();
        $availableTrucks = collect($trucks);
        $steps = [];
        $totalFulfilled = 0;
        $primaryMatches = 0;
        $crossMatches = 0;

        foreach ($sortedAreas as $area) {
            $remainingDemand = $area['demand'];
            $primaryTruck = $availableTrucks->firstWhere('id', $area['primary_truck_id']);

            if ($primaryTruck && $primaryTruck['capacity'] > 0) {
                $allocated = min($primaryTruck['capacity'], $remainingDemand);
                if ($allocated > 0) {
                    $steps[] = [
                        'step' => count($steps) + 1,
                        'truck_name' => $primaryTruck['name'],
                        'area_name' => $area['name'],
                        'allocated' => $allocated,
                        'is_primary_area' => true
                    ];
                    $remainingDemand -= $allocated;
                    $totalFulfilled += $allocated;
                    $primaryMatches++;
                    $primaryTruck['capacity'] -= $allocated;
                    $availableTrucks = $availableTrucks->map(function ($truck) use ($primaryTruck) {
                        if ($truck['id'] === $primaryTruck['id']) {
                            $truck['capacity'] = $primaryTruck['capacity'];
                        }
                        return $truck;
                    });
                }
            }

            if ($remainingDemand > 0) {
                $otherTrucks = $availableTrucks->where('id', '!=', $area['primary_truck_id'])->sortByDesc('capacity');
                foreach ($otherTrucks as $truck) {
                    if ($remainingDemand <= 0) break;
                    if ($truck['capacity'] > 0) {
                        $allocated = min($truck['capacity'], $remainingDemand);
                        $steps[] = [
                            'step' => count($steps) + 1,
                            'truck_name' => $truck['name'],
                            'area_name' => $area['name'],
                            'allocated' => $allocated,
                            'is_primary_area' => false
                        ];
                        $remainingDemand -= $allocated;
                        $totalFulfilled += $allocated;
                        $crossMatches++;
                        $truck['capacity'] -= $allocated;
                        $availableTrucks = $availableTrucks->map(function ($t) use ($truck) {
                            if ($t['id'] === $truck['id']) {
                                $t['capacity'] = $truck['capacity'];
                            }
                            return $t;
                        });
                    }
                }
            }
        }

        return [
            'summary' => [
                'total_demand_liters' => collect($areas)->sum('demand'),
                'total_supply_liters' => collect($trucks)->sum('capacity'),
                'fulfilled_demand_liters' => $totalFulfilled,
                'algorithm_used' => 'Greedy Algorithm',
                'primary_matches' => $primaryMatches,
                'cross_matches' => $crossMatches,
            ],
            'steps' => $steps,
        ];
    }

    public function clearResults()
    {
        $this->result = null;
        $this->error = null;
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.truck-dispatch');
    }
}