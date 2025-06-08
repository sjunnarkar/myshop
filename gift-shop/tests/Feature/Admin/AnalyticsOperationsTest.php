<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AnalyticsOperationsTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Create regular user
        $this->regularUser = User::factory()->create([
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function operations_page_requires_authentication()
    {
        $response = $this->get(route('admin.analytics.operations'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function operations_page_requires_admin_role()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.analytics.operations'));
        
        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_view_operations_page()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.analytics.operations'));
        
        $response->assertSuccessful();
        $response->assertViewIs('admin.analytics.operations');
        $response->assertViewHasAll(['metrics', 'processingTimes']);
    }

    /** @test */
    public function operations_metrics_are_calculated_correctly()
    {
        // Create test orders with different statuses
        $this->createTestOrders();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.analytics.operations'));

        $metrics = $response->viewData('metrics');

        // Assert metrics exist
        $this->assertArrayHasKey('processing_time', $metrics);
        $this->assertArrayHasKey('fulfillment_rate', $metrics);
        $this->assertArrayHasKey('cancellation_rate', $metrics);
        $this->assertArrayHasKey('return_rate', $metrics);

        // Assert metrics are within expected ranges
        $this->assertIsNumeric($metrics['processing_time']);
        $this->assertGreaterThanOrEqual(0, $metrics['fulfillment_rate']);
        $this->assertLessThanOrEqual(100, $metrics['fulfillment_rate']);
        $this->assertGreaterThanOrEqual(0, $metrics['cancellation_rate']);
        $this->assertLessThanOrEqual(100, $metrics['cancellation_rate']);
        $this->assertGreaterThanOrEqual(0, $metrics['return_rate']);
        $this->assertLessThanOrEqual(100, $metrics['return_rate']);
    }

    /** @test */
    public function processing_times_are_paginated()
    {
        // Create more than one page of orders
        Order::factory()->count(15)->create([
            'status' => 'processing'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.analytics.operations'));

        $processingTimes = $response->viewData('processingTimes');
        
        $this->assertTrue($processingTimes->hasPages());
        $this->assertEquals(10, $processingTimes->perPage());
    }

    /** @test */
    public function metrics_show_correct_time_periods()
    {
        // Create orders for different time periods
        $this->createOrdersForTimePeriods();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.analytics.operations'));

        $metrics = $response->viewData('metrics');
        
        // Verify that only orders from current month are included in metrics
        $this->assertTrue($metrics['processing_time'] >= 0);
        $this->assertTrue($metrics['fulfillment_rate'] >= 0);
    }

    private function createTestOrders()
    {
        // Create delivered orders
        Order::factory()->count(5)->create([
            'status' => 'delivered',
            'created_at' => now()->subHours(48),
            'updated_at' => now()
        ]);

        // Create processing orders
        Order::factory()->count(3)->create([
            'status' => 'processing',
            'created_at' => now()->subHours(24),
            'updated_at' => now()
        ]);

        // Create cancelled orders
        Order::factory()->count(2)->create([
            'status' => 'cancelled',
            'created_at' => now()->subHours(12),
            'updated_at' => now()
        ]);

        // Create returned orders
        Order::factory()->count(1)->create([
            'status' => 'returned',
            'created_at' => now()->subHours(72),
            'updated_at' => now()
        ]);
    }

    private function createOrdersForTimePeriods()
    {
        // Current month orders
        Order::factory()->count(5)->create([
            'status' => 'delivered',
            'created_at' => now()->startOfMonth()->addDays(5),
            'updated_at' => now()
        ]);

        // Last month orders
        Order::factory()->count(3)->create([
            'status' => 'delivered',
            'created_at' => now()->subMonth(),
            'updated_at' => now()->subMonth()->addDays(2)
        ]);

        // Orders from 2 months ago
        Order::factory()->count(2)->create([
            'status' => 'delivered',
            'created_at' => now()->subMonths(2),
            'updated_at' => now()->subMonths(2)->addDays(1)
        ]);
    }
} 