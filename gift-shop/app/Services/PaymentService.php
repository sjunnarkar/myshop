<?php

namespace App\Services;

use App\Models\Order;
use Exception;

class PaymentService
{
    /**
     * Create a dummy Razorpay order for testing
     */
    public function createRazorpayOrder(Order $order)
    {
        try {
            // Generate a dummy order ID
            $dummyOrderId = 'order_' . uniqid();

            return [
                'success' => true,
                'order_id' => $dummyOrderId,
                'amount' => $this->convertToPaise($order->total),
                'currency' => 'INR',
                'key' => config('services.razorpay.key')
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process a successful dummy payment
     */
    public function processSuccessfulPayment(Order $order, array $paymentData)
    {
        try {
            // In test mode, we'll always mark the payment as successful
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'payment_id' => $paymentData['razorpay_payment_id'] ?? 'test_' . uniqid()
            ]);

            return [
                'success' => true,
                'message' => 'Payment processed successfully (Test Mode)'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Handle payment failure
     */
    public function handleFailedPayment(Order $order, string $paymentId)
    {
        $order->update([
            'payment_status' => 'failed',
            'payment_id' => $paymentId
        ]);

        return [
            'success' => false,
            'message' => 'Payment failed'
        ];
    }

    /**
     * Convert amount to paise for Razorpay (1 INR = 100 paise)
     */
    private function convertToPaise(float $amount): int
    {
        return (int) ($amount * 100);
    }

    /**
     * Process PayPal payment (dummy implementation)
     */
    public function processPayPalPayment(Order $order, array $paypalData)
    {
        try {
            // Simulate successful payment
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'payment_id' => 'PAYPAL_' . uniqid()
            ]);

            return [
                'success' => true,
                'message' => 'PayPal payment processed successfully (Test Mode)'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
} 