<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create a Razorpay order
     */
    public function createRazorpayOrder(Order $order)
    {
        // Ensure order belongs to authenticated user
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $result = $this->paymentService->createRazorpayOrder($order);

        return response()->json($result);
    }

    /**
     * Handle successful Razorpay payment
     */
    public function handleRazorpaySuccess(Request $request, Order $order)
    {
        // Validate request
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string'
        ]);

        // Ensure order belongs to authenticated user
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $result = $this->paymentService->processSuccessfulPayment($order, $request->all());

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    /**
     * Handle failed Razorpay payment
     */
    public function handleRazorpayFailure(Request $request, Order $order)
    {
        // Validate request
        $request->validate([
            'error_code' => 'required|string',
            'error_description' => 'required|string',
            'razorpay_payment_id' => 'required|string'
        ]);

        // Ensure order belongs to authenticated user
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $result = $this->paymentService->handleFailedPayment(
            $order, 
            $request->razorpay_payment_id
        );

        return response()->json([
            'success' => false,
            'message' => $request->error_description,
            'code' => $request->error_code
        ]);
    }

    /**
     * Process PayPal payment
     */
    public function processPayPal(Request $request, Order $order)
    {
        // Validate request
        $request->validate([
            'paypal_order_id' => 'required|string'
        ]);

        // Ensure order belongs to authenticated user
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $result = $this->paymentService->processPayPalPayment($order, $request->all());

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }
} 