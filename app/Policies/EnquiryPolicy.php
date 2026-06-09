<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Enquiry;

class EnquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('enquiries.view');
    }

    public function view(User $user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('enquiries.create');
    }

    public function update(User $user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.update');
    }

    public function delete(User $user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.delete');
    }

    public function restore(User $user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.delete');
    }

    public function forceDelete(User $user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.delete');
    }
}
