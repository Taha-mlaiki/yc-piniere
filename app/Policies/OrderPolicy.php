<?php

namespace App\Policies;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Everyone can view their own orders
    }

    public function view(User $user, Order $order)
    {
        return $user->id === $order->user_id || $user->isEmployee();
    }

    public function create(User $user)
    {
        return true; // All authenticated users can create orders
    }

    public function update(User $user, Order $order)
    {
        return $user->isEmployee();
    }

    public function cancel(User $user, Order $order)
    {
        return $user->id === $order->user_id && $order->canCancel();
    }

    public function updateStatus(User $user, Order $order)
    {
        return $user->isEmployee();
    }
}
