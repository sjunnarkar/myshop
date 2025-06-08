<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by order number
        if ($request->has('order_number') && !empty($request->order_number)) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }

        // Filter by date range
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => [
                'pending' => 'Pending',
                'processing' => 'Processing',
                'shipped' => 'Shipped',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled'
            ]
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'shippingAddress', 'billingAddress', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;

        // If order is being cancelled and was not cancelled before
        if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            // Restore product stock
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
        }
        // If order was cancelled and is being restored
        elseif ($oldStatus === 'cancelled' && $request->status !== 'cancelled') {
            // Deduct product stock again
            foreach ($order->items as $item) {
                $item->product->decrement('stock', $item->quantity);
            }
        }

        $order->save();

        // Send notification to customer about status change
        $order->user->notify(new OrderStatusChanged($order, $oldStatus));

        return back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Generate order invoice.
     */
    public function invoice(Order $order)
    {
        $order->load(['user', 'items.product', 'shippingAddress', 'billingAddress']);
        
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        
        return $pdf->download('order-' . $order->order_number . '.pdf');
    }

    /**
     * Delete the specified order.
     */
    public function destroy(Order $order)
    {
        // Only allow deletion of cancelled orders
        if ($order->status !== 'cancelled') {
            return back()->with('error', 'Only cancelled orders can be deleted.');
        }

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
