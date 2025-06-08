<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $products = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        return view('admin.coupons.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:coupons'],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0'],
            'minimum_spend' => ['nullable', 'numeric', 'min:0'],
            'maximum_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'applicable_products' => ['nullable', 'array'],
            'applicable_products.*' => ['exists:products,id'],
            'applicable_categories' => ['nullable', 'array'],
            'applicable_categories.*' => ['exists:categories,id'],
            'first_order_only' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        if ($validated['type'] === 'percentage') {
            $request->validate([
                'value' => ['max:100'],
            ]);
        }

        Coupon::create($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        $products = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        return view('admin.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', Rule::unique('coupons')->ignore($coupon)],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0'],
            'minimum_spend' => ['nullable', 'numeric', 'min:0'],
            'maximum_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'applicable_products' => ['nullable', 'array'],
            'applicable_products.*' => ['exists:products,id'],
            'applicable_categories' => ['nullable', 'array'],
            'applicable_categories.*' => ['exists:categories,id'],
            'first_order_only' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        if ($validated['type'] === 'percentage') {
            $request->validate([
                'value' => ['max:100'],
            ]);
        }

        $coupon->update($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }
} 