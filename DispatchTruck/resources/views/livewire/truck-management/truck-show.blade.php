<div>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Truck Management</h2>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary" wire:click="openCreateModal">
                    <i class="fas fa-plus"></i> Add New Truck
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Search by name or plate..." wire:model.live.debounce.300ms="search">
            </div>
            <div class="col-md-3">
                <select class="form-control" wire:model.live="statusFilter">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="in-transit">In Transit</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" wire:model.live="perPage">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </select>
            </div>
        </div>

        <!-- Messages -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Trucks Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Truck Name</th>
                        <th>Plate Number</th>
                        <th>Capacity (L)</th>
                        <th>Available (L)</th>
                        <th>Current Area</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trucks as $truck)
                        <tr>
                            <td>{{ $truck->id }}</td>
                            <td>{{ $truck->truck_name }}</td>
                            <td>{{ $truck->plate_number }}</td>
                            <td>{{ number_format($truck->capacity_ltrs, 2) }}</td>
                            <td>{{ number_format($truck->available_ltrs, 2) }}</td>
                            <td>{{ $truck->currentArea->area_name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $statusClass = [
                                        'available' => 'success',
                                        'in-transit' => 'warning',
                                        'maintenance' => 'danger'
                                    ][$truck->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ ucfirst($truck->status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" wire:click="openViewModal({{ $truck->id }})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-primary" wire:click="openEditModal({{ $truck->id }})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" wire:click="openDeleteModal({{ $truck->id }})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No trucks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $trucks->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    @if($showCreateModal)
        @include('livewire.truck-management.truck-create')
    @endif

    <!-- Edit Modal -->
    @if($showEditModal)
        @include('livewire.truck-management.truck-edit')
    @endif

    <!-- View Modal - Fixed with null check -->
    @if($showViewModal && $viewingTruck)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Truck Details</h5>
                        <button type="button" class="btn-close" wire:click="$set('showViewModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Truck Name:</label>
                                <p>{{ $viewingTruck->truck_name }}</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Plate Number:</label>
                                <p>{{ $viewingTruck->plate_number }}</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Capacity:</label>
                                <p>{{ number_format($viewingTruck->capacity_ltrs, 2) }} Liters</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Available Liters:</label>
                                <p>{{ number_format($viewingTruck->available_ltrs, 2) }} Liters</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Utilization:</label>
                                <p>{{ number_format(($viewingTruck->available_ltrs / $viewingTruck->capacity_ltrs) * 100, 1) }}%</p>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ ($viewingTruck->available_ltrs / $viewingTruck->capacity_ltrs) * 100 }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Current Area:</label>
                                <p>{{ $viewingTruck->currentArea->area_name ?? 'Not Assigned' }}</p>
                                @if($viewingTruck->currentArea)
                                    <small class="text-muted">
                                        Required: {{ number_format($viewingTruck->currentArea->required_liters, 2) }} L
                                    </small>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Status:</label>
                                <p>
                                    @php
                                        $statusClass = [
                                            'available' => 'success',
                                            'in-transit' => 'warning',
                                            'maintenance' => 'danger'
                                        ][$viewingTruck->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ ucfirst($viewingTruck->status) }}
                                    </span>
                                </p>
                            </div>

                            <div class="col-md-12">
                                <label class="fw-bold">System Information:</label>
                                <table class="table table-sm table-bordered mt-2">
                                    <tr>
                                        <th width="30%">Created At:</th>
                                        <td>{{ $viewingTruck->created_at ? $viewingTruck->created_at->format('F d, Y h:i A') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated:</th>
                                        <td>{{ $viewingTruck->updated_at ? $viewingTruck->updated_at->format('F d, Y h:i A') : 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showViewModal', false)">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete truck <strong>{{ $deletingTruckName }}</strong>?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteTruck">Delete Truck</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>