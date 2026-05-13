<div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Truck</h5>
                <button type="button" class="btn-close" wire:click="$set('showEditModal', false)"></button>
            </div>
            <form wire:submit.prevent="updateTruck">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Truck Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('truck_name') is-invalid @enderror" 
                                   wire:model="truck_name">
                            @error('truck_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Plate Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('plate_number') is-invalid @enderror" 
                                   wire:model="plate_number">
                            @error('plate_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Capacity (Liters) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('capacity_ltrs') is-invalid @enderror" 
                                   wire:model.live="capacity_ltrs">
                            @error('capacity_ltrs')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Available Liters <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('available_ltrs') is-invalid @enderror" 
                                   wire:model.live="available_ltrs">
                            @error('available_ltrs')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Area</label>
                            <select class="form-control @error('current_area_id') is-invalid @enderror" 
                                    wire:model="current_area_id">
                                <option value="">Select Area</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                @endforeach
                            </select>
                            @error('current_area_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    wire:model="status">
                                <option value="available">Available</option>
                                <option value="in-transit">In Transit</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showEditModal', false)">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Truck</button>
                </div>
            </form>
        </div>
    </div>
</div>