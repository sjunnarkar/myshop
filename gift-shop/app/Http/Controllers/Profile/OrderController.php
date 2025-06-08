<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if (request()->ajax()) {
            return view('profile.orders.partials.order-table', compact('orders'));
        }

        return view('profile.orders.index', compact('orders'));
    }

    /**
     * Get paginated orders for AJAX requests
     */
    public function paginate()
    {
        $orders = auth()->user()->orders()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.orders.partials.order-table', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Eager load all necessary relationships with explicit select statements
        $order->load([
            'items.product',
            'shippingAddress' => function($query) {
                $query->select('id', 'name', 'street_address', 'city', 'state', 'postal_code', 'country', 'phone');
            },
            'billingAddress' => function($query) {
                $query->select('id', 'name', 'street_address', 'city', 'state', 'postal_code', 'country', 'phone');
            }
        ]);

        return view('profile.orders.show', compact('order'));
    }

    /**
     * Display the specified order details for AJAX request.
     */
    public function details(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'shippingAddress', 'billingAddress']);

        return view('profile.orders.details', compact('order'));
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow cancellation of pending orders
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order has been cancelled successfully.');
    }
} 