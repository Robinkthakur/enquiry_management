<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;

class CoursePolicy
{
    public function viewAny($user): bool
    {
        return $user->hasPermissionTo('courses.view');
    }

    public function view($user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.view');
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('courses.create');
    }

    public function update($user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.update');
    }

    public function delete($user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.delete');
    }

    public function restore($user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.delete');
    }

    public function forceDelete($user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.delete');
    }
}
