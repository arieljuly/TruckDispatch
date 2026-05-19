<?php

namespace App\Livewire\DispatchManagement;

use App\Models\DispatchSession;
use Livewire\Component;
use Livewire\WithPagination;

class DispatchHistory extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $statusFilter = '';
    public $search = '';
    public $perPage = 10;

    protected $queryString = ['dateFrom', 'dateTo', 'statusFilter', 'search'];

    public function render()
    {
        $sessions = DispatchSession::query()
            ->with(['recommendedTruck', 'assignedTruck', 'executor'])
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->search, function ($query) {
                $query->where('id', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        $analytics = app(\App\Services\DispatchManagementService::class)->getAnalytics();

        return view('livewire.dispatch-management.dispatch-history', [
            'sessions' => $sessions,
            'analytics' => $analytics,
            'statuses' => ['pending', 'executed', 'failed']
        ]);
    }

    public function getPredictionAccuracyProperty()
    {
        $sessions = DispatchSession::whereNotNull('actual_fuel_used')
            ->where('predicted_fuel_liters', '>', 0)
            ->get();

        if ($sessions->isEmpty())
            return null;

        $errors = $sessions->map(function ($session) {
            return abs($session->actual_fuel_used - $session->predicted_fuel_liters) / $session->predicted_fuel_liters * 100;
        });

        return round(100 - $errors->avg(), 2);
    }
}