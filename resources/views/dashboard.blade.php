@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

{{-- DASHBOARD HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="fw-bold mb-1">📊 Dashboard</h3>
    <p class="text-muted mb-0">Complete overview of your rental business</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('orders.create') }}" class="btn btn-warning shadow-sm">
      <i class="bi bi-plus-circle me-2"></i>New Order
    </a>
    <a href="{{ route('dispatches.create') }}" class="btn btn-outline-secondary">
      <i class="bi bi-truck me-2"></i>New Dispatch
    </a>
  </div>
</div>

{{-- TODAY'S HIGHLIGHTS --}}
<div class="row g-3 mb-4">
  <div class="col-12">
    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #004d40 0%, #4ba26b 100%);">
      <div class="card-body text-white">
        <div class="row align-items-center">
          <div class="col-md-3 text-center border-end border-white border-opacity-25">
            <div class="d-flex flex-column">
              <small class="opacity-75 mb-1">Orders Today</small>
              <h2 class="fw-bold mb-0">{{ $todayStats['orders_created'] }}</h2>
            </div>
          </div>
          <div class="col-md-3 text-center border-end border-white border-opacity-25">
            <div class="d-flex flex-column">
              <small class="opacity-75 mb-1">Payments Today</small>
              <h2 class="fw-bold mb-0">{{ $todayStats['payments_received'] }}</h2>
            </div>
          </div>
          <div class="col-md-6 text-center">
            <div class="d-flex flex-column">
              <small class="opacity-75 mb-1">Revenue Collected Today</small>
              <h2 class="fw-bold mb-0">₹{{ number_format($todayStats['payment_amount_today'], 2) }}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- FINANCIAL OVERVIEW --}}
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <p class="text-muted mb-1 small">Total Revenue</p>
            <h3 class="fw-bold mb-0 text-success">₹{{ number_format($orderFinancials['total_revenue'], 0) }}</h3>
          </div>
          <div class="icon-box bg-success-subtle text-success">
            <i class="bi bi-currency-rupee"></i>
          </div>
        </div>
        <small class="text-muted">All time orders</small>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <p class="text-muted mb-1 small">Collected</p>
            <h3 class="fw-bold mb-0 text-info">₹{{ number_format($orderFinancials['total_collected'], 0) }}</h3>
          </div>
          <div class="icon-box bg-info-subtle text-info">
            <i class="bi bi-wallet2"></i>
          </div>
        </div>
        <small class="text-muted">Payments received</small>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <p class="text-muted mb-1 small">Pending</p>
            <h3 class="fw-bold mb-0 text-warning">₹{{ number_format($orderFinancials['total_pending'], 0) }}</h3>
          </div>
          <div class="icon-box bg-warning-subtle text-warning">
            <i class="bi bi-hourglass-split"></i>
          </div>
        </div>
        <small class="text-muted">Yet to receive</small>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <p class="text-muted mb-1 small">Collection Rate</p>
            <h3 class="fw-bold mb-0 text-danger">
              @php
                $rate = $orderFinancials['total_revenue'] > 0 
                  ? ($orderFinancials['total_collected'] / $orderFinancials['total_revenue']) * 100 
                  : 0;
              @endphp
              {{ number_format($rate, 1) }}%
            </h3>
          </div>
          <div class="icon-box bg-danger-subtle text-danger">
            <i class="bi bi-graph-up-arrow"></i>
          </div>
        </div>
        <small class="text-muted">Payment efficiency</small>
      </div>
    </div>
  </div>
</div>

{{-- ORDERS & DISPATCHES STATS --}}
<div class="row g-3 mb-4">
  {{-- Orders Stats --}}
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-calendar-check text-primary me-2"></i>Orders Overview
        </h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-6">
            <div class="stat-mini">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Total Orders</span>
                <span class="badge bg-primary">{{ $orderStats['total'] }}</span>
              </div>
              <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-primary" style="width: 100%"></div>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="stat-mini">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Confirmed</span>
                <span class="badge bg-success">{{ $orderStats['confirmed'] }}</span>
              </div>
              <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-success" style="width: {{ $orderStats['total'] > 0 ? ($orderStats['confirmed']/$orderStats['total'])*100 : 0 }}%"></div>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="stat-mini">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Upcoming Events</span>
                <span class="badge bg-warning">{{ $orderStats['upcoming'] }}</span>
              </div>
              <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-warning" style="width: {{ $orderStats['total'] > 0 ? ($orderStats['upcoming']/$orderStats['total'])*100 : 0 }}%"></div>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="stat-mini">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Running Now</span>
                <span class="badge bg-info">{{ $orderStats['running'] }}</span>
              </div>
              <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-info" style="width: {{ $orderStats['total'] > 0 ? ($orderStats['running']/$orderStats['total'])*100 : 0 }}%"></div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <hr class="my-2">
          </div>

          <div class="col-4">
            <div class="text-center">
              <div class="h5 fw-bold mb-0 text-danger">{{ $orderStats['payment_pending'] }}</div>
              <small class="text-muted">Payment Pending</small>
            </div>
          </div>

          <div class="col-4">
            <div class="text-center">
              <div class="h5 fw-bold mb-0 text-warning">{{ $orderStats['payment_partial'] }}</div>
              <small class="text-muted">Partial Paid</small>
            </div>
          </div>

          <div class="col-4">
            <div class="text-center">
              <div class="h5 fw-bold mb-0 text-success">{{ $orderStats['payment_paid'] }}</div>
              <small class="text-muted">Fully Paid</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Dispatches Stats --}}
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-truck text-secondary me-2"></i>Dispatches Overview
        </h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-6">
            <div class="stat-mini">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Total Dispatches</span>
                <span class="badge bg-secondary">{{ $dispatchStats['total'] }}</span>
              </div>
              <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-secondary" style="width: 100%"></div>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="stat-mini">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Active</span>
                <span class="badge bg-info">{{ $dispatchStats['active'] }}</span>
              </div>
              <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-info" style="width: {{ $dispatchStats['total'] > 0 ? ($dispatchStats['active']/$dispatchStats['total'])*100 : 0 }}%"></div>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="stat-mini">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Partial Return</span>
                <span class="badge bg-warning">{{ $dispatchStats['partial'] }}</span>
              </div>
              <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-warning" style="width: {{ $dispatchStats['total'] > 0 ? ($dispatchStats['partial']/$dispatchStats['total'])*100 : 0 }}%"></div>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="stat-mini">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Returned</span>
                <span class="badge bg-success">{{ $dispatchStats['returned'] }}</span>
              </div>
              <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-success" style="width: {{ $dispatchStats['total'] > 0 ? ($dispatchStats['returned']/$dispatchStats['total'])*100 : 0 }}%"></div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <hr class="my-2">
          </div>

          <div class="col-6">
            <div class="text-center">
              <div class="h5 fw-bold mb-0 text-danger">{{ $dispatchPaymentStats['unpaid'] }}</div>
              <small class="text-muted">Unpaid</small>
            </div>
          </div>

          <div class="col-6">
            <div class="text-center">
              <div class="h5 fw-bold mb-0 text-success">{{ $dispatchPaymentStats['advance'] }}</div>
              <small class="text-muted">Advance Received</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- UPCOMING EVENTS & PENDING PAYMENTS --}}
<div class="row g-3 mb-4">
  {{-- Upcoming Events --}}
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-calendar-event text-warning me-2"></i>Upcoming Events (Next 7 Days)
        </h6>
        <a href="{{ route('orders.index') }}" class="text-decoration-none small">View All</a>
      </div>
      <div class="card-body p-0">
        @forelse($upcomingEvents as $event)
        <div class="p-3 border-bottom">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <div class="d-flex align-items-center mb-1">
                <a href="{{ route('orders.show', $event) }}" class="text-decoration-none fw-semibold text-primary me-2">
                  {{ $event->order_code }}
                </a>
                <span class="badge bg-warning-subtle text-warning">
                  {{ \Carbon\Carbon::parse($event->event_from)->diffForHumans() }}
                </span>
              </div>
              <div class="text-muted small mb-1">
                <i class="bi bi-person me-1"></i>{{ $event->client_name }}
              </div>
              <div class="text-muted small">
                <i class="bi bi-calendar-range me-1"></i>
                {{ \Carbon\Carbon::parse($event->event_from)->format('d M') }} - 
                {{ \Carbon\Carbon::parse($event->event_to)->format('d M Y') }}
                <span class="badge bg-primary-subtle text-primary ms-2">
                  {{ $event->event_days }} days
                </span>
              </div>
            </div>
            <div class="text-end">
              <div class="fw-bold text-success">₹{{ number_format($event->total_amount, 0) }}</div>
              @if($event->final_payable > 0)
              <small class="text-danger">₹{{ number_format($event->final_payable, 0) }} pending</small>
              @endif
            </div>
          </div>
        </div>
        @empty
        <div class="p-4 text-center text-muted">
          <i class="bi bi-calendar-x display-4 d-block mb-2"></i>
          <p class="mb-0">No upcoming events in next 7 days</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Pending Payments --}}
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-exclamation-triangle text-danger me-2"></i>Top Pending Payments
        </h6>
        <a href="{{ route('orders.index', ['payment_status' => 'pending']) }}" class="text-decoration-none small">View All</a>
      </div>
      <div class="card-body p-0">
        @forelse($pendingPayments as $order)
        <div class="p-3 border-bottom">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <div class="d-flex align-items-center mb-1">
                <a href="{{ route('orders.show', $order) }}" class="text-decoration-none fw-semibold text-primary me-2">
                  {{ $order->order_code }}
                </a>
                @if($order->payment_status === 'partial')
                <span class="badge bg-warning-subtle text-warning">Partial</span>
                @else
                <span class="badge bg-danger-subtle text-danger">Unpaid</span>
                @endif
              </div>
              <div class="text-muted small mb-1">
                <i class="bi bi-person me-1"></i>{{ $order->client_name }}
              </div>
              <div class="text-muted small">
                <i class="bi bi-phone me-1"></i>{{ $order->client_phone }}
              </div>
            </div>
            <div class="text-end">
              <div class="fw-bold text-danger">₹{{ number_format($order->final_payable, 0) }}</div>
              <small class="text-muted">of ₹{{ number_format($order->total_amount, 0) }}</small>
              <div class="mt-1">
                <button class="btn btn-sm btn-outline-info" 
                        onclick="sendReminder({{ $order->id }})">
                  <i class="bi bi-bell"></i>
                </button>
                <button class="btn btn-sm btn-outline-success" 
                        onclick="recordPayment({{ $order->id }})">
                  <i class="bi bi-cash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        @empty
        <div class="p-4 text-center text-muted">
          <i class="bi bi-check-circle display-4 d-block mb-2 text-success"></i>
          <p class="mb-0">All payments collected! 🎉</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- PAYMENT METHOD BREAKDOWN --}}
@if($paymentMethodStats->count() > 0)
<div class="row g-3 mb-4">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-pie-chart text-info me-2"></i>Payment Methods Breakdown
        </h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          @foreach($paymentMethodStats as $method)
          <div class="col-md-3">
            <div class="payment-method-card p-3 border rounded text-center">
              <div class="h3 mb-2">
                @switch($method->payment_method)
                  @case('gpay') 💳 @break
                  @case('phonepe') 📱 @break
                  @case('paytm') 💰 @break
                  @case('cash') 💵 @break
                  @case('bank_transfer') 🏦 @break
                  @case('upi') 🔗 @break
                  @default 📝 @break
                @endswitch
              </div>
              <div class="fw-semibold mb-1">{{ ucwords(str_replace('_', ' ', $method->payment_method)) }}</div>
              <div class="h5 fw-bold text-success mb-1">₹{{ number_format($method->total, 0) }}</div>
              <small class="text-muted">{{ $method->count }} transactions</small>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endif

{{-- RECENT ACTIVITIES --}}
<div class="row g-3">
  {{-- Recent Orders --}}
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-clock-history text-primary me-2"></i>Recent Orders
        </h6>
        <a href="{{ route('orders.index') }}" class="text-decoration-none small">View All</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="border-0">Code</th>
                <th class="border-0">Client</th>
                <th class="border-0">Event</th>
                <th class="border-0">Amount</th>
                <th class="border-0">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentOrders as $order)
              <tr>
                <td>
                  <a href="{{ route('orders.show', $order) }}" class="text-decoration-none fw-semibold text-primary">
                    {{ $order->order_code }}
                  </a>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                      <i class="bi bi-person-fill"></i>
                    </div>
                    <span class="small">{{ Str::limit($order->client_name, 15) }}</span>
                  </div>
                </td>
                <td class="small text-muted">
                  {{ \Carbon\Carbon::parse($order->event_from)->format('d M') }}
                </td>
                <td class="fw-semibold">₹{{ number_format($order->total_amount, 0) }}</td>
                <td>
                  @php
                    $statusBadges = [
                      'confirmed' => 'bg-primary-subtle text-primary',
                      'completed' => 'bg-success-subtle text-success',
                      'sent' => 'bg-danger-subtle text-danger',
                    ];
                  @endphp
                  <span class="badge {{ $statusBadges[$order->status] ?? 'bg-secondary-subtle text-secondary' }}">
                    {{ ucfirst($order->status) }}
                  </span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">No recent orders</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Recent Dispatches --}}
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-truck text-secondary me-2"></i>Recent Dispatches
        </h6>
        <a href="{{ route('dispatches.index') }}" class="text-decoration-none small">View All</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="border-0">Code</th>
                <th class="border-0">Client</th>
                <th class="border-0">Status</th>
                <th class="border-0">Date</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentDispatches as $dispatch)
              <tr>
                <td>
                  <a href="{{ route('dispatches.show', $dispatch) }}" class="text-decoration-none fw-semibold text-primary">
                    {{ $dispatch->code }}
                  </a>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                      <i class="bi bi-person-fill"></i>
                    </div>
                    <span class="small">{{ Str::limit($dispatch->client->name, 15) }}</span>
                  </div>
                </td>
                <td>
                  @php
                    $dispatchBadges = [
                      'Active' => 'bg-info-subtle text-info',
                      'Partially Returned' => 'bg-warning-subtle text-warning',
                      'Returned' => 'bg-success-subtle text-success',
                    ];
                  @endphp
                  <span class="badge {{ $dispatchBadges[$dispatch->status] ?? 'bg-secondary-subtle text-secondary' }}">
                    {{ $dispatch->status }}
                  </span>
                </td>
                <td class="text-muted small">{{ $dispatch->dispatch_date->format('d M Y') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">No recent dispatches</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.icon-box {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.stat-mini {
  padding: 8px;
  background: #f8f9fa;
  border-radius: 8px;
}

.avatar-sm {
  font-size: 0.75rem;
}

.payment-method-card {
  transition: all 0.3s ease;
  background: #f8f9fa;
}

.payment-method-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  background: white;
}

.card {
  transition: all 0.3s ease;
}

.table-hover tbody tr:hover {
  background-color: rgba(0,0,0,0.02);
}
</style>

<script>
// Quick action functions (you can expand these)
function sendReminder(orderId) {
  window.location.href = `/orders?highlight=${orderId}`;
  // Or open modal directly if you have the functionality
}

function recordPayment(orderId) {
  window.location.href = `/orders?highlight=${orderId}`;
  // Or open payment modal directly
}
</script>

@endsection
