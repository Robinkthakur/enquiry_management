<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FeePayment;

class FeePaymentPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasRole('Student') || $user->hasPermissionTo('fees.view');
    }

    public function view($user, FeePayment $payment): bool
    {
        if ($user->hasRole('Student')) {
            return $user->admission?->id === $payment->admission_id;
        }
        return $user->hasPermissionTo('fees.view');
    }

    public function create($user): bool
    {
        return $user->hasRole('Student') || $user->hasPermissionTo('fees.manage');
    }

    public function update($user, FeePayment $payment): bool
    {
        return $user->hasPermissionTo('fees.manage');
    }

    public function delete($user, FeePayment $payment): bool
    {
        return $user->hasPermissionTo('fees.manage');
    }
}
