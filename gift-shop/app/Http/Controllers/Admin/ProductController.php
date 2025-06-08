<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\CustomizationTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::active()->sorted()->get();
        $customizationTemplates = CustomizationTemplate::all();
        return view('admin.products.create', compact('categories', 'customizationTemplates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:255|unique:products',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'dimensions' => 'nullable|array',
            'dimensions.width' => 'nullable|numeric',
            'dimensions.height' => 'nullable|numeric',
            'dimensions.length' => 'nullable|numeric',
            'printing_areas' => 'nullable|array',
            'customization_options' => 'nullable|array',
            'customization_templates' => 'nullable|array|exists:customization_templates,id',
        ]);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('products', 'public');
            $validated['thumbnail'] = $path;
        }

        $additionalImages = [];
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                $path = $image->store('products', 'public');
                $additionalImages[] = $path;
            }
            $validated['additional_images'] = $additionalImages;
        }

        $validated['slug'] = Str::slug($validated['name']);
        
        $product = Product::create($validated);

        // Sync customization templates
        if (isset($validated['customization_templates'])) {
            $product->customizationTemplates()->sync($validated['customization_templates']);
        }

        // Create inventory item for the product
        $product->inventory()->create([
            'stock_level' => $validated['stock'],
            'reorder_point' => 10, // Default reorder point
            'track_inventory' => true,
            'allow_backorders' => false,
            'unit_cost' => $validated['base_price'] * 0.7, // Assuming 30% margin
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->sorted()->get();
        $customizationTemplates = CustomizationTemplate::all();
        return view('admin.products.edit', compact('product', 'categories', 'customizationTemplates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:255|unique:products,name,' . $product->id,
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'dimensions' => 'nullable|array',
            'dimensions.width' => 'nullable|numeric',
            'dimensions.height' => 'nullable|numeric',
            'dimensions.length' => 'nullable|numeric',
            'printing_areas' => 'nullable|array',
            'customization_options' => 'nullable|array',
            'existing_images' => 'nullable|array',
            'removed_images' => 'nullable|array',
            'customization_templates' => 'nullable|array|exists:customization_templates,id',
        ]);

        // Handle is_active checkbox - if not checked, it won't be in the request
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $path = $request->file('thumbnail')->store('products', 'public');
            $validated['thumbnail'] = $path;
        }

        // Handle additional images
        $currentImages = $product->additional_images ?? [];
        $existingImages = $request->input('existing_images', []);
        $removedImages = $request->input('removed_images', []);
        
        // Remove deleted images from storage and array
        foreach ($removedImages as $image) {
            Storage::disk('public')->delete($image);
            $currentImages = array_diff($currentImages, [$image]);
        }
        
        // Add new images
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                $path = $image->store('products', 'public');
                $currentImages[] = $path;
            }
        }
        
        $validated['additional_images'] = array_values(array_filter($currentImages));
        $validated['slug'] = Str::slug($validated['name']);
        
        $product->update($validated);

        // Sync customization templates
        if (isset($validated['customization_templates'])) {
            $product->customizationTemplates()->sync($validated['customization_templates']);
        } else {
            $product->customizationTemplates()->detach();
        }

        // Update inventory item if it exists, or create it if it doesn't
        if ($product->inventory) {
            $product->inventory->update([
                'stock_level' => $validated['stock'],
                'unit_cost' => $validated['base_price'] * 0.7, // Assuming 30% margin
            ]);
        } else {
            $product->inventory()->create([
                'stock_level' => $validated['stock'],
                'reorder_point' => 10, // Default reorder point
                'track_inventory' => true,
                'allow_backorders' => false,
                'unit_cost' => $validated['base_price'] * 0.7, // Assuming 30% margin
            ]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // Delete thumbnail if exists
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        // Delete additional images if they exist
        if (!empty($product->additional_images)) {
            foreach ($product->additional_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
