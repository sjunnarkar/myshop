<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Coupon;
use App\Models\NewsletterSubscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $metrics = $this->getDashboardMetrics();
        $salesTrend = $this->getDailySalesTrend(30);
        $topProducts = $this->getTopProducts(5);
        $topCategories = $this->getTopCategories(5);

        return view('admin.analytics.index', compact('metrics', 'salesTrend', 'topProducts', 'topCategories'));
    }

    public function sales()
    {
        $metrics = [
            'today' => Order::whereDate('created_at', Carbon::today())->sum('total'),
            'this_month' => Order::whereMonth('created_at', Carbon::now()->month)->sum('total'),
            'last_month' => Order::whereMonth('created_at', Carbon::now()->subMonth()->month)->sum('total'),
            'average_order' => Order::avg('total') ?? 0,
        ];

        $salesByPayment = Order::select('payment_method', 
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        $hourlyDistribution = Order::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view('admin.analytics.sales', compact('metrics', 'salesByPayment', 'hourlyDistribution'));
    }

    public function customers()
    {
        $metrics = [
            'total_customers' => User::where('is_admin', false)->count(),
            'new_customers' => User::where('is_admin', false)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count(),
            'avg_order_value' => Order::avg('total') ?? 0,
            'retention_rate' => $this->calculateRetentionRate(),
        ];

        $acquisitionTrend = User::where('is_admin', false)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->limit(30)
            ->get();

        $segments = $this->getCustomerSegments();

        $topCustomers = User::where('is_admin', false)
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderByDesc('orders_sum_total')
            ->limit(10)
            ->get()
            ->map(function ($customer) {
                $customer->total_orders = $customer->orders_count;
                $customer->total_spent = $customer->orders_sum_total;
                $customer->avg_order_value = $customer->total_spent / $customer->total_orders;
                $customer->last_order_date = $customer->orders()->latest()->first() ? $customer->orders()->latest()->first()->created_at : $customer->created_at;
                return $customer;
            });

        $geographicData = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'users.region as name',
                DB::raw('COUNT(DISTINCT users.id) as customer_count'),
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total) as total_revenue'),
                DB::raw('AVG(orders.total) as avg_order_value')
            )
            ->groupBy('users.region')
            ->get();

        return view('admin.analytics.customers', compact('metrics', 'acquisitionTrend', 'segments', 'topCustomers', 'geographicData'));
    }

    public function products()
    {
        $metrics = [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'active')->count(),
            'low_stock' => Product::where('stock', '<=', 10)->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
        ];

        $topProducts = Product::withCount('orderItems as units_sold')
            ->withSum('orderItems', 'total as revenue')
            ->orderByDesc('units_sold')
            ->limit(10)
            ->get();

        $categoryPerformance = Category::withCount(['products as total_products', 'activeProducts as active_products'])
            ->withSum('products.orderItems', 'total as revenue')
            ->withAvg('products.reviews', 'rating as avg_rating')
            ->get()
            ->map(function ($category) {
                $category->total_sales = $category->products->sum(function ($product) {
                    return $product->orderItems->count();
                });
                return $category;
            });

        $productPerformance = Product::with('category')
            ->withCount('orderItems as units_sold')
            ->withSum('orderItems', 'total as revenue')
            ->withAvg('reviews', 'rating as avg_rating')
            ->get()
            ->map(function ($product) {
                $product->stock_level = $product->stock > 0 ? min(100, ($product->stock / 100) * 100) : 0;
                return $product;
            });

        return view('admin.analytics.products', compact('metrics', 'topProducts', 'categoryPerformance', 'productPerformance'));
    }

    public function marketing(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request->period);

        $metrics = [
            'active_campaigns' => Campaign::where('status', 'active')
                ->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->count(),
            'subscribers' => NewsletterSubscriber::where('status', 'subscribed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'active_coupons' => Coupon::where('is_active', true)
                ->where('expiry_date', '>', Carbon::now())
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'conversion_rate' => $this->calculateConversionRate($startDate, $endDate),
        ];

        $newsletterMetrics = NewsletterSubscriber::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as subscribers'),
            DB::raw('AVG(CASE WHEN last_opened_at IS NOT NULL THEN 1 ELSE 0 END) * 100 as open_rate')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $couponUsage = Coupon::withCount(['orders as usage_count' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['orders as total_savings' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'discount')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();

        $campaigns = Campaign::withCount(['conversions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['orders as revenue' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->get()
            ->map(function ($campaign) {
                $campaign->roi = $campaign->cost > 0 ? (($campaign->revenue - $campaign->cost) / $campaign->cost) * 100 : 0;
                return $campaign;
            });

        $coupons = Coupon::withCount(['orders as usage_count' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['orders as total_savings' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'discount')
            ->withAvg(['orders as avg_order_value' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total')
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'metrics' => $metrics,
                'newsletterMetrics' => $newsletterMetrics,
                'couponUsage' => $couponUsage,
                'campaigns' => $campaigns,
                'coupons' => $coupons,
            ]);
        }

        return view('admin.analytics.marketing', compact(
            'metrics',
            'newsletterMetrics',
            'couponUsage',
            'campaigns',
            'coupons',
            'startDate',
            'endDate'
        ));
    }

    public function operations()
    {
        // Get operational metrics
        $metrics = $this->getOperationalMetrics();

        // Get order processing times
        $processingTimes = Order::where('status', '!=', 'cancelled')
            ->select(
                'id',
                'order_number',
                'created_at',
                'updated_at',
                DB::raw('TIMESTAMPDIFF(HOUR, created_at, updated_at) as processing_hours')
            )
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.analytics.operations', compact(
            'metrics',
            'processingTimes'
        ));
    }

    // API Methods for Chart Data
    public function salesTrends()
    {
        $trends = $this->getDailySalesTrend(30);
        return response()->json($trends);
    }

    public function categoryPerformance()
    {
        $performance = Category::withCount(['products as sales_count' => function($query) {
                $query->whereHas('orderItems.order', function($q) {
                    $q->where('status', '!=', 'cancelled');
                });
            }])
            ->withSum(['products' => function($query) {
                $query->whereHas('orderItems.order', function($q) {
                    $q->where('status', '!=', 'cancelled');
                });
            }], 'base_price')
            ->get();

        return response()->json($performance);
    }

    public function customerMetrics()
    {
        $metrics = $this->getCustomerMetrics();
        return response()->json($metrics);
    }

    public function productPerformance()
    {
        $performance = Product::withCount(['orderItems as sales_count' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('status', '!=', 'cancelled');
                });
            }])
            ->withSum(['orderItems' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('status', '!=', 'cancelled');
                });
            }], 'quantity')
            ->orderByDesc('sales_count')
            ->take(10)
            ->get();

        return response()->json($performance);
    }

    // Private Helper Methods
    private function getDashboardMetrics()
    {
        return [
            'total_revenue' => Order::sum('total'),
            'average_order_value' => Order::avg('total') ?? 0,
            'total_customers' => User::where('is_admin', false)->count(),
            'total_orders' => Order::count(),
        ];
    }

    private function getDailySalesTrend($days)
    {
        return Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as revenue'),
            DB::raw('COUNT(*) as orders')
        )
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getTopProducts($limit)
    {
        return Product::withCount('orderItems as units_sold')
            ->withSum('orderItems', 'total as revenue')
            ->orderByDesc('units_sold')
            ->limit($limit)
            ->get();
    }

    private function getTopCategories($limit)
    {
        return Category::withCount('products as total_products')
            ->withSum('products.orderItems', 'total as revenue')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }

    private function calculateRetentionRate()
    {
        $totalCustomers = User::where('is_admin', false)->count();
        $repeatCustomers = User::where('is_admin', false)
            ->whereHas('orders', function ($query) {
                $query->havingRaw('COUNT(*) > 1');
            })
            ->count();

        return $totalCustomers > 0 ? ($repeatCustomers / $totalCustomers) * 100 : 0;
    }

    private function calculateConversionRate($startDate, $endDate)
    {
        $totalVisits = session('total_visits', 1000); // Placeholder, implement actual tracking
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        return $totalVisits > 0 ? ($totalOrders / $totalVisits) * 100 : 0;
    }

    private function getCustomerSegments()
    {
        $segments = collect([
            [
                'name' => 'New Customers',
                'count' => User::where('is_admin', false)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->count()
            ],
            [
                'name' => 'Regular Customers',
                'count' => User::where('is_admin', false)
                    ->whereHas('orders', function ($query) {
                        $query->havingRaw('COUNT(*) BETWEEN 2 AND 5');
                    })
                    ->count()
            ],
            [
                'name' => 'VIP Customers',
                'count' => User::where('is_admin', false)
                    ->whereHas('orders', function ($query) {
                        $query->havingRaw('COUNT(*) > 5');
                    })
                    ->count()
            ],
            [
                'name' => 'Inactive Customers',
                'count' => User::where('is_admin', false)
                    ->whereDoesntHave('orders', function ($query) {
                        $query->where('created_at', '>=', Carbon::now()->subMonths(3));
                    })
                    ->count()
            ]
        ]);

        return $segments;
    }

    private function getCustomerMetrics()
    {
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_customers' => User::where('is_admin', false)->count(),
            'new_this_month' => User::where('is_admin', false)
                ->where('created_at', '>=', $thisMonth)
                ->count(),
            'with_orders' => User::where('is_admin', false)
                ->whereHas('orders', function($query) {
                    $query->where('status', '!=', 'cancelled');
                })
                ->count(),
            'average_value' => Order::where('status', '!=', 'cancelled')
                ->select('user_id', DB::raw('AVG(total_amount) as avg_value'))
                ->groupBy('user_id')
                ->avg('avg_value')
        ];
    }

    private function getProductMetrics()
    {
        return [
            'total_products' => Product::count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'low_stock' => Product::where('stock', '>', 0)
                ->where('stock', '<=', 10)
                ->count(),
            'never_sold' => Product::whereDoesntHave('orderItems')
                ->count()
        ];
    }

    private function getCouponMetrics()
    {
        return [
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::where('is_active', true)
                ->where(function($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->count(),
            'total_usage' => DB::table('orders')
                ->whereNotNull('coupon_id')
                ->where('status', '!=', 'cancelled')
                ->count(),
            'total_savings' => DB::table('orders')
                ->whereNotNull('coupon_id')
                ->where('status', '!=', 'cancelled')
                ->sum('discount_amount')
        ];
    }

    private function getOperationalMetrics()
    {
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'processing_time' => Order::where('status', '!=', 'cancelled')
                ->where('created_at', '>=', $thisMonth)
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours'))
                ->first()->avg_hours,
            'fulfillment_rate' => Order::where('created_at', '>=', $thisMonth)
                ->whereIn('status', ['delivered', 'shipped'])
                ->count() / max(1, Order::where('created_at', '>=', $thisMonth)->count()) * 100,
            'cancellation_rate' => Order::where('created_at', '>=', $thisMonth)
                ->where('status', 'cancelled')
                ->count() / max(1, Order::where('created_at', '>=', $thisMonth)->count()) * 100,
            'return_rate' => Order::where('created_at', '>=', $thisMonth)
                ->where('status', 'returned')
                ->count() / max(1, Order::where('created_at', '>=', $thisMonth)->count()) * 100
        ];
    }

    private function getDateRange($period = null)
    {
        $end = Carbon::now();
        $start = match($period) {
            '7days' => Carbon::now()->subDays(7),
            '30days' => Carbon::now()->subDays(30),
            '90days' => Carbon::now()->subDays(90),
            'year' => Carbon::now()->subYear(),
            'custom' => request('start_date') ? Carbon::parse(request('start_date')) : Carbon::now()->subDays(30),
            default => Carbon::now()->subDays(30),
        };

        if ($period === 'custom' && request('end_date')) {
            $end = Carbon::parse(request('end_date'));
        }

        return [$start, $end];
    }
} 