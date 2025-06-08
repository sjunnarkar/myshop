<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Status</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td>
                        <span class="badge bg-{{ $order->status === 'processing' ? 'info' : 'primary' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>₹{{ number_format($order->total_amount, 2) }}</td>
                    <td>
                        <a href="{{ route('profile.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                            View Details
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center border-top p-3">
    <div class="small text-muted">
        Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
    </div>
    <nav aria-label="Orders navigation">
        {{ $orders->links('pagination::simple-bootstrap-5') }}
    </nav>
</div> 