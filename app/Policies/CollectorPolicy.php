<?php


namespace App\Policies;


use App\Models\Collector;
use App\Models\User;

class CollectorPolicy
{
    public function update(User $user, Collector $collector)
    {
        return $user->id === $collector->user_id;
    }

    public function delete(User $user, Collector $collector)
    {
        return $user->id === $collector->user_id;
    }
}