<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OrderDAO;

class StatisticsController extends Controller
{
    protected $orderDAO;

    public function __construct(OrderDAO $orderDAO)
    {
        $this->orderDAO = $orderDAO;
    }

    public function index()
    {
        try {
            $statistics = [
                'order_stats' => $this->orderDAO->getOrderStatistics(),
                'popular_plants' => $this->orderDAO->getPopularPlants(),
                'sales_by_category' => $this->orderDAO->getSalesByCategory(),
            ];

            return response()->json([
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
