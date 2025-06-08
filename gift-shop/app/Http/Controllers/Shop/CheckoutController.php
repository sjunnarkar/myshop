<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderNotification;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Show the checkout page.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                $cartItems[] = (object)[
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'options' => $item['customization_details'] ?? [],
                    'subtotal' => $product->base_price * $item['quantity']
                ];
                $subtotal += $product->base_price * $item['quantity'];
            }
        }

        $user = auth()->user();
        // Get all user addresses
        $addresses = $user->addresses()->get();

        // Calculate totals (matching cart page calculation)
        $shipping = $subtotal > 100 ? 0 : 10; // Free shipping for orders over 100
        $tax = $subtotal * 0.1; // 10% tax rate
        $total = $subtotal + $shipping + $tax;

        return view('shop.checkout.index', compact(
            'cartItems',
            'addresses',
            'subtotal',
            'shipping',
            'tax',
            'total'
        ));
    }

    /**
     * Process the checkout.
     */
    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // Validate shipping and billing addresses
        $request->validate([
            'shipping_address_id' => 'required|exists:user_addresses,id',
            'billing_address_id' => 'required|exists:user_addresses,id',
        ]);

        try {
            DB::beginTransaction();

            // Get shipping and billing addresses
            $shippingAddress = UserAddress::findOrFail($request->shipping_address_id);
            $billingAddress = UserAddress::findOrFail($request->billing_address_id);

            // Calculate totals
            $subtotal = 0;
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                if ($product) {
                    $subtotal += $product->base_price * $item['quantity'];
                }
            }

            // Calculate shipping and tax
            $shipping_cost = $subtotal > 100 ? 0 : 10; // Free shipping for orders over 100
            $tax = $subtotal * 0.1; // 10% tax
            $total_amount = $subtotal + $shipping_cost + $tax;

            // Create the order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $this->generateOrderNumber(),
                'total_amount' => $total_amount,
                'shipping_cost' => $shipping_cost,
                'payment_method' => 'test', // Using test payment method for now
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $request->notes,
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->billing_address_id,
            ]);

            // Create order items
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                if ($product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->base_price,
                        'subtotal' => $product->base_price * $item['quantity'],
                        'customization_details' => $item['customization_details'] ?? null,
                    ]);

                    // Update product stock
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // For testing purposes, mark the order as paid and processing
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing'
            ]);

            // Clear cart
            session()->forget('cart');
            if (auth()->check()) {
                \App\Models\CartItem::where('user_id', auth()->id())->delete();
            }

            DB::commit();

            return redirect()->to(route('profile.show') . '#orders')
                ->with('success', 'Order placed successfully! Order number: ' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order processing error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'There was an error processing your order. Please try again.')
                ->withInput();
        }
    }

    /**
     * Generate a unique order number.
     */
    private function generateOrderNumber()
    {
        $prefix = date('Ymd');
        $unique = false;
        $orderNumber = '';

        while (!$unique) {
            $orderNumber = $prefix . strtoupper(Str::random(6));
            if (!Order::where('order_number', $orderNumber)->exists()) {
                $unique = true;
            }
        }

        return $orderNumber;
    }

    /**
     * Process the payment.
     * This is a placeholder - you should integrate with a real payment gateway.
     */
    private function processPayment(Request $request, Order $order)
    {
        try {
            // Validate card details
            if (!$this->validateCardDetails($request)) {
                throw new \Exception('Invalid card details provided.');
            }

            // For test payment method
            if ($request->payment_method === 'test') {
                // Simulate payment processing delay
                sleep(1);
                
                // Check if card number ends with success test number
                if (substr($request->card_number, -4) === '4242') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing'
                    ]);
                    return true;
                } else {
                    throw new \Exception('Test payment failed. For testing, use a card number ending in 4242.');
                }
            }

            throw new \Exception('Unsupported payment method.');
        } catch (\Exception $e) {
            \Log::error('Payment processing error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate card details
     */
    private function validateCardDetails(Request $request)
    {
        // Basic card validation
        $cardNumber = str_replace(' ', '', $request->card_number);
        if (!preg_match('/^\d{16}$/', $cardNumber)) {
            return false;
        }

        // Expiry date validation (MM/YY format)
        if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $request->card_expiry)) {
            return false;
        }

        // Parse expiry date
        list($month, $year) = explode('/', $request->card_expiry);
        $expiry = \DateTime::createFromFormat('y-m-d', $year . '-' . $month . '-01');
        $now = new \DateTime();

        // Check if card is expired
        if ($expiry < $now) {
            return false;
        }

        // CVV validation
        if (!preg_match('/^\d{3,4}$/', $request->card_cvv)) {
            return false;
        }

        return true;
    }

    public function success(Request $request)
    {
        $orderNumber = $request->session()->get('success');
        if (!$orderNumber) {
            return redirect()->route('shop.index');
        }

        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('shop.checkout.success', compact('order'));
    }
}
