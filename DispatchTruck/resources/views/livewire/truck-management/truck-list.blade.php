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
                        <button class="btn btn-sm btn-info" wire:click="openViewModal({{ $truck->id }})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary" wire:click="openEditModal({{ $truck->id }})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" wire:click="openDeleteModal({{ $truck->id }})">
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