<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::latest()->paginate(10);
        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        $products = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        return view('admin.discounts.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['percentage', 'fixed', 'buy_x_get_y'])],
            'value' => ['required', 'numeric', 'min:0'],
            'buy_x' => ['nullable', 'required_if:type,buy_x_get_y', 'integer', 'min:1'],
            'get_y' => ['nullable', 'required_if:type,buy_x_get_y', 'integer', 'min:1'],
            'minimum_spend' => ['nullable', 'numeric', 'min:0'],
            'maximum_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit_per_user' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'applicable_products' => ['nullable', 'array'],
            'applicable_products.*' => ['exists:products,id'],
            'applicable_categories' => ['nullable', 'array'],
            'applicable_categories.*' => ['exists:categories,id'],
            'stackable' => ['boolean'],
            'priority' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        if ($validated['type'] === 'percentage') {
            $request->validate([
                'value' => ['max:100'],
            ]);
        }

        Discount::create($validated);

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Discount created successfully.');
    }

    public function edit(Discount $discount)
    {
        $products = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        return view('admin.discounts.edit', compact('discount', 'products', 'categories'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['percentage', 'fixed', 'buy_x_get_y'])],
            'value' => ['required', 'numeric', 'min:0'],
            'buy_x' => ['nullable', 'required_if:type,buy_x_get_y', 'integer', 'min:1'],
            'get_y' => ['nullable', 'required_if:type,buy_x_get_y', 'integer', 'min:1'],
            'minimum_spend' => ['nullable', 'numeric', 'min:0'],
            'maximum_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit_per_user' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'applicable_products' => ['nullable', 'array'],
            'applicable_products.*' => ['exists:products,id'],
            'applicable_categories' => ['nullable', 'array'],
            'applicable_categories.*' => ['exists:categories,id'],
            'stackable' => ['boolean'],
            'priority' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        if ($validated['type'] === 'percentage') {
            $request->validate([
                'value' => ['max:100'],
            ]);
        }

        $discount->update($validated);

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Discount deleted successfully.');
    }
} 