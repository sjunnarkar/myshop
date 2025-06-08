<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $admin;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'is_admin' => true
        ]);

        // Create a category
        $this->category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Category Description',
            'slug' => 'test-category'
        ]);
    }

    public function test_admin_can_create_product()
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)->post('/admin/products', [
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'base_price' => 99.99,
            'category_id' => $this->category->id,
            'image' => $image,
            'sku' => 'TEST-001',
            'is_active' => true
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'base_price' => 99.99,
            'category_id' => $this->category->id,
            'sku' => 'TEST-001'
        ]);

        // Check if inventory item was created
        $product = Product::where('sku', 'TEST-001')->first();
        $this->assertDatabaseHas('inventory_items', [
            'product_id' => $product->id
        ]);
    }

    public function test_admin_can_update_product()
    {
        $product = Product::create([
            'name' => 'Original Product',
            'description' => 'Original Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'sku' => 'TEST-001',
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/products/{$product->id}", [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 149.99,
            'category_id' => $this->category->id,
            'sku' => 'TEST-001',
            'is_active' => true
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 149.99
        ]);
    }

    public function test_admin_can_manage_inventory()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'sku' => 'TEST-001',
            'is_active' => true
        ]);

        $inventoryItem = InventoryItem::create([
            'product_id' => $product->id,
            'stock_level' => 10,
            'reorder_point' => 5,
            'optimal_stock' => 20,
            'sku' => 'TEST-001',
            'location' => 'A1'
        ]);

        // Test updating inventory
        $response = $this->actingAs($this->admin)->put("/admin/inventory/{$inventoryItem->id}", [
            'stock_level' => 15,
            'reorder_point' => 5,
            'optimal_stock' => 20
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('inventory_items', [
            'id' => $inventoryItem->id,
            'stock_level' => 15
        ]);
    }

    public function test_low_stock_alerts()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'sku' => 'TEST-001',
            'is_active' => true
        ]);

        $inventoryItem = InventoryItem::create([
            'product_id' => $product->id,
            'stock_level' => 3,
            'reorder_point' => 5,
            'optimal_stock' => 20,
            'sku' => 'TEST-001',
            'location' => 'A1'
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/inventory/low-stock');
        $response->assertStatus(200);
        $response->assertSee('Test Product');
    }

    public function test_product_search_and_filtering()
    {
        // Create multiple products
        Product::create([
            'name' => 'Birthday Gift',
            'description' => 'Perfect birthday gift',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'sku' => 'GIFT-001',
            'is_active' => true
        ]);

        Product::create([
            'name' => 'Anniversary Present',
            'description' => 'Perfect anniversary gift',
            'price' => 149.99,
            'category_id' => $this->category->id,
            'sku' => 'GIFT-002',
            'is_active' => true
        ]);

        // Test search
        $response = $this->get('/products/search?query=birthday');
        $response->assertStatus(200);
        $response->assertSee('Birthday Gift');
        $response->assertDontSee('Anniversary Present');

        // Test category filter
        $response = $this->get("/products/category/{$this->category->slug}");
        $response->assertStatus(200);
        $response->assertSee('Birthday Gift');
        $response->assertSee('Anniversary Present');

        // Test price filter
        $response = $this->get('/products/filter?min_price=100&max_price=200');
        $response->assertStatus(200);
        $response->assertDontSee('Birthday Gift');
        $response->assertSee('Anniversary Present');
    }
} 