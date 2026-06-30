<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class Admin extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasUuids, HasRoles;

    protected $table = 'admins';

    /**
     * Spatie Permission guard name. We set it to 'web' to reuse roles/permissions
     * seeded on the 'web' guard.
     */
    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Enquiries taken by this admin.
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'taken_by');
    }

    /**
     * Admission courses where this admin is the instructor.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(AdmissionCourse::class, 'instructor_id');
    }

    public function admissions(): BelongsToMany
    {
        return $this->belongsToMany(Admission::class, 'admission_courses', 'instructor_id', 'admission_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return true;
        }
        return false;
    }
}
