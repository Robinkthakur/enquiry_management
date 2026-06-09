<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('courses.view');
    }

    public function view(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('courses.create');
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.update');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.delete');
    }

    public function restore(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.delete');
    }

    public function forceDelete(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.delete');
    }
}
