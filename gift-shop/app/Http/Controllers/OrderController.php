<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return view('profile.orders.index', compact('orders'));
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

        $order->load(['items.product', 'shippingAddress', 'billingAddress', 'user']);

        return view('profile.orders.show', compact('order'));
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

        // Restore product stock
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        return back()->with('success', 'Order has been cancelled successfully.');
    }
} 