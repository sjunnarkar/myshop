<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\InventoryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user;
    private $admin;
    private $product;
    private $inventoryItem;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create(['is_admin' => true]);

        // Create category
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Category Description',
            'slug' => 'test-category'
        ]);

        // Create product
        $this->product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $category->id,
            'sku' => 'TEST-001',
            'is_active' => true
        ]);

        // Create inventory item
        $this->inventoryItem = InventoryItem::create([
            'product_id' => $this->product->id,
            'stock_level' => 10,
            'reorder_point' => 5,
            'optimal_stock' => 20,
            'sku' => 'TEST-001',
            'location' => 'A1'
        ]);
    }

    public function test_user_can_create_order()
    {
        $response = $this->actingAs($this->user)->post('/orders', [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'price' => $this->product->price
                ]
            ],
            'shipping_address' => '123 Test St',
            'billing_address' => '123 Test St',
            'payment_method' => 'credit_card',
            'notes' => 'Test order notes'
        ]);

        $response->assertRedirect();
        
        // Check order creation
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'pending',
            'shipping_address' => '123 Test St'
        ]);

        // Check order items
        $order = Order::where('user_id', $this->user->id)->first();
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        // Check inventory update
        $this->assertDatabaseHas('inventory_items', [
            'id' => $this->inventoryItem->id,
            'stock_level' => 8  // Original 10 - 2
        ]);
    }

    public function test_admin_can_update_order_status()
    {
        // Create an order
        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total' => 199.98,
            'shipping_address' => '123 Test St',
            'billing_address' => '123 Test St',
            'payment_method' => 'credit_card'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 99.99
        ]);

        // Admin updates order status
        $response = $this->actingAs($this->admin)->put("/admin/orders/{$order->id}", [
            'status' => 'processing'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing'
        ]);
    }

    public function test_order_cancellation()
    {
        // Create an order
        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total' => 199.98,
            'shipping_address' => '123 Test St',
            'billing_address' => '123 Test St',
            'payment_method' => 'credit_card'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 99.99
        ]);

        // Initial inventory level is 10
        $initialStock = $this->inventoryItem->stock_level;

        // Cancel order
        $response = $this->actingAs($this->user)->post("/orders/{$order->id}/cancel");

        $response->assertRedirect();
        
        // Check order status
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled'
        ]);

        // Check inventory restoration
        $this->assertDatabaseHas('inventory_items', [
            'id' => $this->inventoryItem->id,
            'stock_level' => $initialStock  // Stock should be restored
        ]);
    }

    public function test_order_validation()
    {
        // Test with insufficient stock
        $response = $this->actingAs($this->user)->post('/orders', [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 20,  // More than available stock
                    'price' => $this->product->price
                ]
            ],
            'shipping_address' => '123 Test St',
            'billing_address' => '123 Test St',
            'payment_method' => 'credit_card'
        ]);

        $response->assertStatus(422);  // Unprocessable Entity

        // Test with invalid product
        $response = $this->actingAs($this->user)->post('/orders', [
            'items' => [
                [
                    'product_id' => 999999,  // Non-existent product
                    'quantity' => 1,
                    'price' => 99.99
                ]
            ],
            'shipping_address' => '123 Test St',
            'billing_address' => '123 Test St',
            'payment_method' => 'credit_card'
        ]);

        $response->assertStatus(422);
    }

    public function test_order_history_and_details()
    {
        // Create multiple orders
        $order1 = Order::create([
            'user_id' => $this->user->id,
            'status' => 'completed',
            'total' => 199.98,
            'shipping_address' => '123 Test St',
            'billing_address' => '123 Test St',
            'payment_method' => 'credit_card'
        ]);

        $order2 = Order::create([
            'user_id' => $this->user->id,
            'status' => 'processing',
            'total' => 99.99,
            'shipping_address' => '123 Test St',
            'billing_address' => '123 Test St',
            'payment_method' => 'credit_card'
        ]);

        // Test order history
        $response = $this->actingAs($this->user)->get('/orders');
        $response->assertStatus(200);
        $response->assertSee($order1->id);
        $response->assertSee($order2->id);

        // Test order details
        $response = $this->actingAs($this->user)->get("/orders/{$order1->id}");
        $response->assertStatus(200);
        $response->assertSee($order1->total);
        $response->assertSee($order1->status);
    }
}
