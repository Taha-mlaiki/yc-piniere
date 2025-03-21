<?php

use App\D\BaseDAO;
use App\Models\Order;
use App\Models\Plant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderDAO extends BaseDAO
{
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->with('items.plant')->get();
    }

    public function createOrder(int $userId, string $shippingAddress, array $items): Order
    {
        return DB::transaction(function () use ($userId, $shippingAddress, $items) {
            $totalAmount = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $plant = Plant::where('slug', $item['plant_slug'])->firstOrFail();
                $subtotal = $plant->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'plant_id' => $plant->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $plant->price,
                ];
            }

            $order = $this->model->create([
                'user_id' => $userId,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'shipping_address' => $shippingAddress,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            return $order->load('items.plant');
        });
    }

    public function updateStatus(Order $order, string $status): bool
    {
        return $order->update(['status' => $status]);
    }

    public function getOrderStatistics()
    {
        return [
            'total_orders' => $this->model->count(),
            'total_sales' => $this->model->sum('total_amount'),
            'orders_by_status' => $this->model->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status'),
        ];
    }

    public function getPopularPlants($limit = 10)
    {
        return DB::table('order_items')
            ->join('plants', 'order_items.plant_id', '=', 'plants.id')
            ->select('plants.name', 'plants.slug', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('plants.id', 'plants.name', 'plants.slug')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }

    public function getSalesByCategory()
    {
        return DB::table('order_items')
            ->join('plants', 'order_items.plant_id', '=', 'plants.id')
            ->join('categories', 'plants.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();
    }
}
