<div class="row px-3 mb-3">
    <div class="col-md-3">
        <label class="form-label">From</label>
        <input type="date" id="from_date" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label">To</label>
        <input type="date" id="to_date" class="form-control">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary me-2" id="filter">
            <i class="fas fa-search"></i>
            Filter
        </button>
        <button class="btn btn-secondary" id="reset">
            <i class="fas fa-sync"></i>
            Reset
        </button>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('js/datatables-filter.js') }}"></script>
@endpush