<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admission;

class AdmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('admissions.view');
    }

    public function view(User $user, Admission $admission): bool
    {
        return $user->hasPermissionTo('admissions.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('admissions.create');
    }

    public function update(User $user, Admission $admission): bool
    {
        return $user->hasPermissionTo('admissions.update');
    }

    public function delete(User $user, Admission $admission): bool
    {
        return $user->hasPermissionTo('admissions.delete');
    }

    public function restore(User $user, Admission $admission): bool
    {
        return $user->hasPermissionTo('admissions.delete');
    }

    public function forceDelete(User $user, Admission $admission): bool
    {
        return $user->hasPermissionTo('admissions.delete');
    }
}
