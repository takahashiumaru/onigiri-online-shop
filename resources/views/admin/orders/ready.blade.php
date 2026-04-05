@extends('layouts.admin')
@section('styles')
<style>
    /* Premium Custom Dropdown & Action Group */
    .delivery-action-container {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 280px;
    }

    .custom-dropdown {
        position: relative;
        flex-grow: 1;
    }

    .dropdown-trigger {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1.5px solid #e5e7eb;
        padding: 0 16px;
        border-radius: 12px;
        cursor: pointer;
        font-size: 0.88rem;
        font-weight: 500;
        color: #374151;
        height: 48px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        width: 100%;
    }

    .dropdown-trigger:hover {
        border-color: var(--brand);
        background: #fdf2f2;
    }

    .dropdown-trigger i.chevron {
        margin-left: auto;
        font-size: 0.8rem;
        transition: transform 0.2s ease;
        color: #9ca3af;
    }

    .custom-dropdown.active .dropdown-trigger {
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(var(--brand-rgb), 0.1);
    }

    .custom-dropdown.active .dropdown-trigger i.chevron {
        transform: rotate(180deg);
        color: var(--brand);
    }

    .dropdown-menu-list {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: white;
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        border: 1px solid rgba(0, 0, 0, 0.05);
        z-index: 9999;
        display: none;
        max-height: 220px;
        overflow-y: auto;
        padding: 6px;
        transform-origin: top;
    }

    .custom-dropdown.active .dropdown-menu-list {
        display: block;
        animation: dropdownAnim 0.2s ease-out;
    }

    .dropdown-item-option {
        padding: 12px 14px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 0.88rem;
        color: #4b5563;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.15s;
    }

    .dropdown-item-option:hover {
        background: #f8fafc;
        color: var(--brand);
    }

    .dropdown-item-option i {
        font-size: 1.1rem;
        color: #9ca3af;
    }

    .dropdown-item-option:hover i {
        color: var(--brand);
    }

    .dropdown-item-option.is-selected {
        background: var(--brand-light);
        color: var(--brand);
        font-weight: 700;
    }

    @keyframes dropdownAnim {
        from { opacity: 0; transform: scaleY(0.95); }
        to { opacity: 1; transform: scaleY(1); }
    }

    .btn-submit-assignment {
        background: var(--brand);
        color: white;
        border: none;
        border-radius: 12px;
        height: 48px;
        padding: 0 18px;
        font-weight: 700;
        font-size: 0.88rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-submit-assignment:hover {
        background: var(--brand-600);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(var(--brand-rgb), 0.25);
    }

    .btn-submit-assignment:active {
        transform: translateY(0);
    }

    .btn-submit-assignment:disabled {
        background: #e5e7eb;
        color: #9ca3af;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    /* Mobile Table Layout */
    @media (max-width: 991px) {
        .delivery-action-container {
            min-width: 100%;
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="bi bi-box-seam text-primary me-2"></i>Siap Dikirim</h5>
        <small class="text-muted">Tugaskan kurir untuk pesanan yang sudah diproses.</small>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
    <div class="card-body p-3">
        <form action="{{ route('admin.orders.ready') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 ps-3">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control bg-light border-0 py-2" style="border-radius: 0 14px 14px 0; height: 48px;" placeholder="Cari No. pesanan atau pelanggan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center bg-light px-3 py-2" style="border-radius: 14px; height: 48px;">
                    <i class="bi bi-check-circle-fill me-2 text-success"></i>
                    <span class="small fw-bold">Status: Diproses</span>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold shadow-sm" style="border-radius: 14px; height: 48px;">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: visible;">
    <div class="table-responsive" style="overflow: visible;">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 border-0 small text-uppercase fw-bold text-muted">No. Pesanan</th>
                    <th class="py-3 border-0 small text-uppercase fw-bold text-muted">Pelanggan</th>
                    <th class="py-3 border-0 small text-uppercase fw-bold text-muted">Alamat</th>
                    <th class="py-3 border-0 small text-uppercase fw-bold text-muted">Status</th>
                    <th class="pe-4 py-3 border-0 small text-uppercase fw-bold text-muted">Aksi Pengiriman</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($orders as $order)
                <tr>
                    <td class="ps-4 py-4">
                        <a href="{{ route('admin.orders.show', $order) }}" class="fw-bold text-primary text-decoration-none">
                            #{{ $order->order_number }}
                        </a>
                        <div class="text-muted small" style="font-size: 0.75rem;"><i class="bi bi-calendar3 me-1"></i>{{ $order->created_at->format('d M, H:i') }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ strtoupper(substr($order->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold small">{{ $order->user->name }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $order->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-muted small" style="max-width: 220px; line-height: 1.4;">
                            <i class="bi bi-geo-alt me-1 text-danger"></i>{{ Str::limit($order->shipping_address, 50) }}
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background: #ecfeff; color: #0891b2; font-weight: 700; font-size: 0.7rem; padding: 7px 14px; border-radius: 12px; border: 1px solid #cffafe;">
                            DIPROSES
                        </span>
                    </td>
                    <td class="pe-4 py-4">
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="mb-0 delivery-form-submit">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="shipped">
                            <input type="hidden" name="courier_id" class="hidden-courier-id-input" required>
                            
                            <div class="delivery-action-container">
                                <div class="custom-dropdown">
                                    <div class="dropdown-trigger">
                                        <i class="bi bi-person-badge"></i>
                                        <span class="selected-label-text">Pilih Kurir</span>
                                        <i class="bi bi-chevron-down chevron"></i>
                                    </div>
                                    <div class="dropdown-menu-list">
                                        @foreach($couriers as $courier)
                                        <div class="dropdown-item-option" data-value="{{ $courier->id }}">
                                            <i class="bi bi-person-circle"></i>
                                            <span>{{ $courier->name }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" class="btn-submit-assignment" disabled>
                                    <span>Tugaskan</span>
                                    <i class="bi bi-arrow-right-short fs-5"></i>
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted small">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            Belum ada pesanan yang siap dikirim.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.custom-dropdown');
    
    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('.dropdown-trigger');
        const optionsList = dropdown.querySelector('.dropdown-menu-list');
        const options = dropdown.querySelectorAll('.dropdown-item-option');
        const form = dropdown.closest('form');
        const hiddenInput = form.querySelector('.hidden-courier-id-input');
        const labelText = dropdown.querySelector('.selected-label-text');
        const submitBtn = form.querySelector('.btn-submit-assignment');

        // Toggle dropdown logic
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isActive = dropdown.classList.contains('active');
            // Close all dropdowns first
            document.querySelectorAll('.custom-dropdown').forEach(d => d.classList.remove('active'));
            if (!isActive) dropdown.classList.add('active');
        });

        // Selection logic
        options.forEach(option => {
            option.addEventListener('click', () => {
                const val = option.dataset.value;
                const name = option.querySelector('span').innerText;
                
                hiddenInput.value = val;
                labelText.innerText = name;
                labelText.classList.add('fw-bold', 'text-dark');
                
                options.forEach(opt => opt.classList.remove('is-selected'));
                option.classList.add('is-selected');
                
                dropdown.classList.remove('active');
                submitBtn.disabled = false; // Enable tugaskan button after selection
            });
        });
    });

    // Close on outside click
    document.addEventListener('click', () => {
        document.querySelectorAll('.custom-dropdown').forEach(d => d.classList.remove('active'));
    });

    // Prevent submitting without courier just in case
    const forms = document.querySelectorAll('.delivery-form-submit');
    forms.forEach(f => {
        f.addEventListener('submit', function(e) {
            if(!this.querySelector('.hidden-courier-id-input').value) {
                e.preventDefault();
                alert('Silakan pilih kurir terlebih dahulu!');
            }
        });
    });
});
</script>
@endsection



