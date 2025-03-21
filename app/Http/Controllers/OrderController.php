<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OrderDAO;
use OrderDTO;
use OrderStatusDTO;
class OrderController extends Controller
{
    protected $orderDAO;

    public function __construct(OrderDAO $orderDAO)
    {
        $this->orderDAO = $orderDAO;
    }

    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->isEmployee()) {
                $orders = $this->orderDAO->all();
            } else {
                $orders = $this->orderDAO->findByUser($user->id);
            }

            return response()->json([
                'orders' => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve orders',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $orderDTO = new OrderDTO($request->all());
            
            $order = $this->orderDAO->createOrder(
                $request->user()->id, 
                $orderDTO->shipping_address,
                $orderDTO->items
            );

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $order = $this->orderDAO->find($id);
            
            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }

            $user = request()->user();
            if (!$user->isEmployee() && $order->user_id !== $user->id) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }

            return response()->json([
                'order' => $order->load('items.plant'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $order = $this->orderDAO->find($id);
            
            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }

            $user = $request->user();
            if (!$user->isEmployee() && $order->user_id !== $user->id) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }

            $orderStatusDTO = new OrderStatusDTO($request->all());
            
            // If the user is not an employee, they can only cancel their own orders
            if (!$user->isEmployee()) {
                if ($orderStatusDTO->status !== 'cancelled') {
                    return response()->json([
                        'message' => 'Unauthorized to change status other than cancellation',
                    ], 403);
                }

                if (!$order->canCancel()) {
                    return response()->json([
                        'message' => 'Order cannot be cancelled at this stage',
                    ], 422);
                }
            }

            $this->orderDAO->updateStatus($order, $orderStatusDTO->status);

            return response()->json([
                'message' => 'Order status updated successfully',
                'order' => $order->fresh(),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update order status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancel($id)
    {
        try {
            $order = $this->orderDAO->find($id);
            
            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }

            $user = request()->user();
            if ($order->user_id !== $user->id) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }

            if (!$order->canCancel()) {
                return response()->json([
                    'message' => 'Order cannot be cancelled at this stage',
                ], 422);
            }

            $this->orderDAO->updateStatus($order, 'cancelled');

            return response()->json([
                'message' => 'Order cancelled successfully',
                'order' => $order->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
  
