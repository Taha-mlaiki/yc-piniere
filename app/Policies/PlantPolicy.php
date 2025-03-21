<?php

use App\Models\Plant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Everyone can view plants
    }

    public function view(User $user, Plant $plant)
    {
        return true; // Everyone can view plant details
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Plant $plant)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Plant $plant)
    {
        return $user->isAdmin();
    }

    public function uploadImage(User $user, Plant $plant)
    {
        return $user->isAdmin();
    }
}