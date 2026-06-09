<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attendance;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('attendance.view');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if (!$user->hasPermissionTo('attendance.view')) {
            return false;
        }
        if ($user->hasRole('Instructor')) {
            return $attendance->student->instructor_id === $user->id;
        }
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('attendance.manage');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if (!$user->hasPermissionTo('attendance.manage')) {
            return false;
        }
        if ($user->hasRole('Instructor')) {
            return $attendance->student->instructor_id === $user->id;
        }
        return true;
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        if (!$user->hasPermissionTo('attendance.manage')) {
            return false;
        }
        if ($user->hasRole('Instructor')) {
            return $attendance->student->instructor_id === $user->id;
        }
        return true;
    }
}
