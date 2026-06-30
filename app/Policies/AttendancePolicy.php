<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attendance;

class AttendancePolicy
{
    public function viewAny($user): bool
    {
        return $user->hasRole('Student') || $user->hasPermissionTo('attendance.view');
    }

    public function view($user, Attendance $attendance): bool
    {
        if ($user->hasRole('Student')) {
            return $user->admission?->id === $attendance->admission_id;
        }
        if (!$user->hasPermissionTo('attendance.view')) {
            return false;
        }
        if ($user->hasRole('Instructor')) {
            return $attendance->student->instructor_id === $user->id;
        }
        return true;
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('attendance.manage');
    }

    public function update($user, Attendance $attendance): bool
    {
        if (!$user->hasPermissionTo('attendance.manage')) {
            return false;
        }
        if ($user->hasRole('Instructor')) {
            return $attendance->student->instructor_id === $user->id;
        }
        return true;
    }

    public function delete($user, Attendance $attendance): bool
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
