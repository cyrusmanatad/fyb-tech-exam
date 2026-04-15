<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Revenue Performance — current week vs previous week
     * GET /analytics/revenue
     */
    public function revenue(Request $request)
    {
        $currentStart  = now()->startOfWeek();  // Monday
        $currentEnd    = now()->endOfWeek();    // Sunday
        $previousStart = now()->subWeek()->startOfWeek();
        $previousEnd   = now()->subWeek()->endOfWeek();

        $currentWeek  = $this->revenueByDay($currentStart, $currentEnd);
        $previousWeek = $this->revenueByDay($previousStart, $previousEnd);

        // Total revenue for the week
        $currentTotal  = array_sum($currentWeek);
        $previousTotal = array_sum($previousWeek);

        // Growth percentage
        $growth = $previousTotal > 0
            ? round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1)
            : 0;

        return response()->json([
            'data' => [
                'categories'    => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'current'       => $currentWeek,
                'previous'      => $previousWeek,
                'current_total' => number_format($currentTotal, 2),
                'prev_total'    => number_format($previousTotal, 2),
                'growth'        => $growth, // e.g. 12.5 means +12.5%
            ]
        ]);
    }

    /**
     * Helper — get revenue per day for a given date range
     * Returns array of 7 values [Mon, Tue, Wed, Thu, Fri, Sat, Sun]
     */
    private function revenueByDay(Carbon $start, Carbon $end): array
    {
        $orders = Order::query()
            ->select(
                DB::raw('DAYOFWEEK(created_at) as day_of_week'), // 1=Sun, 2=Mon...7=Sat
                DB::raw('SUM(total) as revenue')
            )
            ->whereBetween('created_at', [$start, $end])
            ->whereNotIn('status', [
                OrderStatus::CANCELLED->value,
                OrderStatus::REFUNDED->value,
            ])
            ->groupBy('day_of_week')
            ->pluck('revenue', 'day_of_week') // [2 => 1200, 3 => 800, ...]
            ->toArray();

        // Map to Mon-Sun (DAYOFWEEK: 1=Sun, 2=Mon, 3=Tue... 7=Sat)
        // We want index 0=Mon, 1=Tue... 6=Sun
        $dayMap = [
            0 => 2, // Mon
            1 => 3, // Tue
            2 => 4, // Wed
            3 => 5, // Thu
            4 => 6, // Fri
            5 => 7, // Sat
            6 => 1, // Sun
        ];

        return array_map(
            fn($dayOfWeek) => round((float) ($orders[$dayOfWeek] ?? 0), 2),
            $dayMap
        );
    }

    /**
     * Category Distribution
     * GET /analytics/categories
     */
    public function categories()
    {
        $categories = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereNotIn('orders.status', [
                OrderStatus::CANCELLED->value,
                OrderStatus::REFUNDED->value,
            ])
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(order_items.id) as total_orders'),
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        $grandTotal = $categories->sum('total_revenue');

        $data = $categories->map(fn($cat) => [
            'name'          => $cat->name,
            'total_revenue' => round((float) $cat->total_revenue, 2),
            'total_orders'  => (int) $cat->total_orders,
            'percentage'    => $grandTotal > 0
                ? round(($cat->total_revenue / $grandTotal) * 100, 1)
                : 0,
        ]);

        return response()->json([
            'data' => [
                'labels'     => $data->pluck('name')->values(),
                'series'     => $data->pluck('total_revenue')->values(),
                'categories' => $data->values(),
            ]
        ]);
    }

    /**
     * KPI Summary
     * GET /analytics/kpi
     */
    public function kpi()
    {
        $currentStart  = now()->startOfMonth();
        $previousStart = now()->subMonth()->startOfMonth();
        $previousEnd   = now()->subMonth()->endOfMonth();

        // Net Revenue
        $currentRevenue = Order::whereBetween('created_at', [$currentStart, now()])
            ->whereNotIn('status', [
                OrderStatus::CANCELLED->value,
                OrderStatus::REFUNDED->value,
            ])
            ->sum('total');

        $previousRevenue = Order::whereBetween('created_at', [$previousStart, $previousEnd])
            ->whereNotIn('status', [
                OrderStatus::CANCELLED->value,
                OrderStatus::REFUNDED->value,
            ])
            ->sum('total');

        // Average Order Value
        $currentOrderCount = Order::whereBetween('created_at', [$currentStart, now()])
            ->whereNotIn('status', [
                OrderStatus::CANCELLED->value,
                OrderStatus::REFUNDED->value,
            ])
            ->count();

        $previousOrderCount = Order::whereBetween('created_at', [$previousStart, $previousEnd])
            ->whereNotIn('status', [
                OrderStatus::CANCELLED->value,
                OrderStatus::REFUNDED->value,
            ])
            ->count();

        $currentAov  = $currentOrderCount  > 0 ? $currentRevenue  / $currentOrderCount  : 0;
        $previousAov = $previousOrderCount > 0 ? $previousRevenue / $previousOrderCount : 0;

        // Store Sessions (Unique Visitors via login activity)
        $currentSessions = DB::table('user_logins')
            ->whereBetween('logged_in_at', [$currentStart, now()])
            ->count();

        $previousSessions = DB::table('user_logins')
            ->whereBetween('logged_in_at', [$previousStart, $previousEnd])
            ->count();

        // Conversion Rate (Sessions that led to an order)
        $currentConversions = Order::whereBetween('created_at', [$currentStart, now()])
            ->distinct('user_id')
            ->count('user_id');

        $previousConversions = Order::whereBetween('created_at', [$previousStart, $previousEnd])
            ->distinct('user_id')
            ->count('user_id');

        $currentConvRate  = $currentSessions  > 0
            ? round(($currentConversions  / $currentSessions)  * 100, 2)
            : 0;

        $previousConvRate = $previousSessions > 0
            ? round(($previousConversions / $previousSessions) * 100, 2)
            : 0;

        return response()->json([
            'data' => [
                'net_revenue' => [
                    'value'    => number_format($currentRevenue, 2),
                    'raw'      => $currentRevenue,
                    'trend'    => $this->trend($currentRevenue, $previousRevenue),
                    'currency' => 'PHP',
                ],
                'conversion_rate' => [
                    'value' => $currentConvRate . '%',
                    'raw'   => $currentConvRate,
                    'trend' => $this->trend($currentConvRate, $previousConvRate),
                ],
                'store_sessions' => [
                    'value' => number_format($currentSessions),
                    'raw'   => $currentSessions,
                    'trend' => $this->trend($currentSessions, $previousSessions),
                ],
                'avg_order_value' => [
                    'value'    => number_format($currentAov, 2),
                    'raw'      => round($currentAov, 2),
                    'trend'    => $this->trend($currentAov, $previousAov),
                    'currency' => 'PHP',
                ],
            ]
        ]);
    }

    /**
     * Calculate trend percentage between current and previous value
     */
    private function trend(float $current, float $previous): array
    {
        if ($previous == 0) {
            return [
                'percentage' => 0,
                'direction'  => 'up',
                'label'      => '0%',
            ];
        }

        $percentage = round((($current - $previous) / $previous) * 100, 1);

        return [
            'percentage' => abs($percentage),
            'direction'  => $percentage >= 0 ? 'up' : 'down',
            'label'      => ($percentage >= 0 ? '+' : '-') . abs($percentage) . '%',
        ];
    }
}