@extends('layouts.app')

@section('title', 'Inventory')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
.stat-card {
    border-radius: 16px;
    border: none;
    transition: transform .2s, box-shadow .2s;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.1) !important;
}
.stat-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Walkie Inventory</h4>
            <p class="text-muted mb-0">Track and manage serial numbers of all Walkie Talkies</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.scan') }}" class="btn btn-warning shadow-sm">
                <i class="bi bi-upc-scan me-2"></i>Scan Barcode
            </a>
            <a href="{{ route('inventory.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Manual Add
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card stat-card shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-box-seam"></i></div>
                    <div>
                        <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                        <div class="small text-muted">Total Units</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-check-circle"></i></div>
                    <div>
                        <div class="fw-bold fs-4">{{ $stats['available'] }}</div>
                        <div class="small text-muted">Available</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-truck"></i></div>
                    <div>
                        <div class="fw-bold fs-4">{{ $stats['rented'] }}</div>
                        <div class="small text-muted">On Rent</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-tools"></i></div>
                    <div>
                        <div class="fw-bold fs-4">{{ $stats['maintenance'] }}</div>
                        <div class="small text-muted">Maintenance</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm" style="border-radius:16px">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="inv-table" class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Serial Number</th>
                            <th>Walkie Model</th>
                            <th>Status</th>
                            <th>Condition</th>
                            <th>Purchase Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

@push('scripts')
<script>
$(document).ready(function () {
    const table = $('#inv-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("inventory.data") }}',
        columns: [
            { data: 'id', name: 'id', width: '60px' },
            {
                data: 'serial_number', name: 'serial_number',
                render: d => `<span class="font-monospace fw-bold" style="letter-spacing:1px">${d}</span>`
            },
            { data: 'item_name', name: 'items.name', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'condition', name: 'condition', orderable: false },
            { data: 'purchase_date', name: 'purchase_date' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        language: {
            emptyTable: 'कोई inventory item नहीं है',
            zeroRecords: 'कोई matching item नहीं मिली',
        }
    });

    // Delete
    $('#inv-table').on('click', '.delete-inv-btn', function () {
        Swal.fire({
            title: 'Delete?',
            text: 'इस serial number को delete करना चाहते हैं?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'हाँ, Delete करो',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                const id = $(this).data('id');
                $.ajax({
                    url: `/inventory/${id}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: () => {
                        table.ajax.reload();
                        Swal.fire('Deleted!', 'Serial number delete हो गई।', 'success');
                    }
                });
            }
        });
    });
});
</script>
@endpush
