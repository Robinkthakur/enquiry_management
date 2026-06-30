<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Enquiry;

class EnquiryPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasPermissionTo('enquiries.view');
    }

    public function view($user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.view');
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('enquiries.create');
    }

    public function update($user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.update');
    }

    public function delete($user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.delete');
    }

    public function restore($user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.delete');
    }

    public function forceDelete($user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.delete');
    }
}
