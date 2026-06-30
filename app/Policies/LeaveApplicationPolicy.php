<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LeaveApplication;

class LeaveApplicationPolicy
{
    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, LeaveApplication $leave): bool
    {
        if ($user->hasRole('Student')) {
            return $user->admission?->id === $leave->admission_id;
        }
        return true;
    }

    public function create($user): bool
    {
        return true;
    }

    public function update($user, LeaveApplication $leave): bool
    {
        if ($user->hasRole('Student')) {
            return $user->admission?->id === $leave->admission_id && $leave->status === 'Pending';
        }
        return true;
    }

    public function delete($user, LeaveApplication $leave): bool
    {
        if ($user->hasRole('Student')) {
            return $user->admission?->id === $leave->admission_id && $leave->status === 'Pending';
        }
        return true;
    }
}
