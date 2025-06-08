@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('admin_title')
    <i class="bi bi-speedometer2"></i> Dashboard
@endsection

@section('admin_content')
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-0">Total Orders</h6>
                        <h2 class="mb-0">{{ App\Models\Order::count() }}</h2>
                    </div>
                    <i class="bi bi-cart fs-1"></i>
                </div>
            </div>
            <div class="card-footer bg-primary-dark border-0">
                <a href="{{ route('admin.orders.index') }}" class="text-white text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-0">Products</h6>
                        <h2 class="mb-0">{{ App\Models\Product::count() }}</h2>
                    </div>
                    <i class="bi bi-box fs-1"></i>
                </div>
            </div>
            <div class="card-footer bg-success-dark border-0">
                <a href="{{ route('admin.products.index') }}" class="text-white text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-0">Categories</h6>
                        <h2 class="mb-0">{{ App\Models\Category::count() }}</h2>
                    </div>
                    <i class="bi bi-grid fs-1"></i>
                </div>
            </div>
            <div class="card-footer bg-info-dark border-0">
                <a href="{{ route('admin.categories.index') }}" class="text-white text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-0">Templates</h6>
                        <h2 class="mb-0">{{ App\Models\CustomizationTemplate::count() }}</h2>
                    </div>
                    <i class="bi bi-brush fs-1"></i>
                </div>
            </div>
            <div class="card-footer bg-warning-dark border-0">
                <a href="{{ route('admin.customization-templates.index') }}" class="text-white text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Orders</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(App\Models\Order::latest()->take(5)->get() as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->shipping_name }}</td>
                                <td><span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'warning' : 'secondary') }}">{{ ucfirst($order->status) }}</span></td>
                                <td>₹{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Low Stock Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(App\Models\Product::where('stock', '<', 10)->take(5)->get() as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td><span class="badge bg-danger">{{ $product->stock }}</span></td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-primary">Update Stock</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 