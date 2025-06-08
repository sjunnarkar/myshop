<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Helpers\CurrencyHelper;
use App\Models\Product;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartItems = $this->cartService->getCart();
        
        // Calculate totals
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['product']->base_price * $item['quantity'];
        }
        
        // Apply coupon discount if exists
        $discount = 0;
        if (session('coupon')) {
            $coupon = session('coupon');
            if ($coupon['type'] === 'percentage') {
                $discount = $subtotal * ($coupon['discount'] / 100);
            } else {
                $discount = $coupon['discount'];
            }
            $subtotal -= $discount;
        }
        
        // Calculate shipping (example: free for orders over $100, otherwise $10)
        $shipping = $subtotal > 100 ? 0 : 10;
        
        // Calculate tax (example: 10% of subtotal)
        $tax = $subtotal * 0.1;
        
        // Calculate total
        $total = $subtotal + $shipping + $tax;
        
        return view('shop.cart.index', compact(
            'cartItems',
            'subtotal',
            'shipping',
            'tax',
            'total'
        ));
    }

    public function add(Request $request)
    {
        // Debug the incoming request data
        \Log::info('Add to Cart Request Data:', $request->all());
        
        try {
            $product = Product::findOrFail($request->product_id);
            $hasTemplates = $product->customizationTemplates->isNotEmpty();
            
            $validationRules = [
                'product_id' => 'required|exists:products,id',
                'quantity' => ['required', 'integer', 'min:1', 'max:99'],
                'special_instructions' => 'nullable|string'
            ];
            
            if ($hasTemplates) {
                $validationRules['customization_details'] = ['required', 'array'];
                $validationRules['customization_details.*.template_id'] = 'required|exists:customization_templates,id';
                $validationRules['customization_details.*.fields'] = 'required|array';
                $validationRules['customization_details.*.fields.*'] = 'required';
            }
            
            $request->validate($validationRules);

            // Debug the customization details
            if ($hasTemplates) {
                \Log::info('Customization Details:', $request->customization_details);
                $this->validateCustomizationData($request->product_id, $request->customization_details);
            }

            $this->cartService->addToCart(
                $request->product_id,
                $request->quantity,
                $request->customization_details ?? [],
                $request->special_instructions
            );

            return redirect()->back()->with('success', 'Product added to cart successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Error:', $e->errors());
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Add to Cart Error:', ['message' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'There was an error adding the product to cart. Please try again.')
                ->withInput();
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'customization_details' => ['required', 'array'],
            'customization_details.*.template_id' => 'required|exists:customization_templates,id',
            'customization_details.*.fields' => 'required|array',
            'customization_details.*.fields.*' => 'required',
            'special_instructions' => 'nullable|string'
        ]);

        // Validate customization data against templates
        $this->validateCustomizationData($request->product_id, $request->customization_details);

        $this->cartService->updateCart(
            $request->product_id,
            $request->quantity,
            $request->customization_details,
            null, // Removing customized_image as it's now handled through fields
            $request->special_instructions
        );

        if ($request->ajax()) {
            $cartItems = $this->cartService->getCart();
            $subtotal = 0;
            $itemTotal = 0;
            
            foreach ($cartItems as $item) {
                if ($item['id'] == $request->product_id) {
                    $itemTotal = $item['product']->base_price * $item['quantity'];
                }
                $subtotal += $item['product']->base_price * $item['quantity'];
            }
            
            // Calculate shipping (free for orders over 100)
            $shipping = $subtotal > 100 ? 0 : 10;
            
            // Calculate tax (10% of subtotal)
            $tax = $subtotal * 0.1;
            
            // Calculate total
            $total = $subtotal + $shipping + $tax;

            return response()->json([
                'success' => true,
                'item_total' => $itemTotal,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'tax' => $tax,
                'total' => $total
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated successfully.');
    }

    public function remove($productId)
    {
        $this->cartService->removeFromCart($productId);
        return redirect()->back()->with('success', 'Product removed from cart.');
    }

    public function clear()
    {
        $this->cartService->clearCart();
        return redirect()->back()->with('success', 'Cart cleared successfully.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|exists:coupons,code'
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid or expired coupon code.');
        }

        // Store the coupon in session
        session()->put('coupon', [
            'code' => $coupon->code,
            'discount' => $coupon->discount_amount,
            'type' => $coupon->discount_type
        ]);

        return back()->with('success', 'Coupon applied successfully.');
    }

    /**
     * Validate customization data against the product's templates
     */
    private function validateCustomizationData($productId, $customizationDetails)
    {
        $product = Product::with('customizationTemplates')->findOrFail($productId);
        $validTemplateIds = $product->customizationTemplates->pluck('id')->toArray();

        foreach ($customizationDetails as $customization) {
            if (!in_array($customization['template_id'], $validTemplateIds)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'customization_details' => ['Invalid template selected for this product']
                ]);
            }

            $template = \App\Models\CustomizationTemplate::find($customization['template_id']);
            $templateFields = collect($template->fields);

            // Validate that all required fields are present
            foreach ($templateFields as $field) {
                if (!isset($customization['fields'][$field['name']])) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'customization_details' => ["Missing required field: {$field['name']}"]
                    ]);
                }

                // Validate field type
                if ($field['type'] === 'select' && !in_array($customization['fields'][$field['name']], $field['options'])) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'customization_details' => ["Invalid option selected for field: {$field['name']}"]
                    ]);
                }
            }
        }
    }
}
