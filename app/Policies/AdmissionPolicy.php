<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admission;

class AdmissionPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasPermissionTo('admissions.view');
    }

    public function view($user, Admission $admission): bool
    {
        return $user->hasPermissionTo('admissions.view');
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('admissions.create');
    }

    public function update($user, Admission $admission): bool
    {
        return $user->hasPermissionTo('admissions.update');
    }

    public function delete($user, Admission $admission): bool
    {
        return $user->hasPermissionTo('admissions.delete');
    }

    public function restore($user, Admission $admission): bool
    {
        return $user->hasPermissionTo('admissions.delete');
    }

    public function forceDelete($user, Admission $admission): bool
    {
        return $user->hasPermissionTo('admissions.delete');
    }
}
