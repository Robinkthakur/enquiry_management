<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FeePayment;

class FeePaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('fees.view');
    }

    public function view(User $user, FeePayment $payment): bool
    {
        return $user->hasPermissionTo('fees.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('fees.manage');
    }

    public function update(User $user, FeePayment $payment): bool
    {
        return $user->hasPermissionTo('fees.manage');
    }

    public function delete(User $user, FeePayment $payment): bool
    {
        return $user->hasPermissionTo('fees.manage');
    }
}
