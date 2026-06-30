<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Certificate;

class CertificatePolicy
{
    public function viewAny($user): bool
    {
        return $user->hasPermissionTo('certificates.view');
    }

    public function view($user, Certificate $certificate): bool
    {
        return $user->hasPermissionTo('certificates.view');
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('certificates.manage');
    }

    public function update($user, Certificate $certificate): bool
    {
        return $user->hasPermissionTo('certificates.manage');
    }

    public function delete($user, Certificate $certificate): bool
    {
        return $user->hasPermissionTo('certificates.manage');
    }
}
