<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Certificate;

class CertificatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('certificates.view');
    }

    public function view(User $user, Certificate $certificate): bool
    {
        return $user->hasPermissionTo('certificates.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('certificates.manage');
    }

    public function update(User $user, Certificate $certificate): bool
    {
        return $user->hasPermissionTo('certificates.manage');
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        return $user->hasPermissionTo('certificates.manage');
    }
}
