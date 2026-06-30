<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
// use Spatie\Activitylog\Models\Concerns\LogsActivity;
// use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Configure activity logging.
     */
    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logOnly(['name', 'email'])
    //         ->logOnlyDirty()
    //         ->dontLogEmptyChanges();
    // }

    /**
     * Enquiries taken by the user.
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'taken_by');
    }

    /**
     * Admissions where this user is the instructor.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(AdmissionCourse::class, 'instructor_id');
    }

    public function admissions(): BelongsToMany
    {
        return $this->belongsToMany(Admission::class, 'admission_courses', 'instructor_id', 'admission_id');
    }

    public function admission(): HasOne
    {
        return $this->hasOne(Admission::class, 'user_id');
    }

    public function fcmTokens(): HasMany
    {
        return $this->hasMany(FcmToken::class, 'user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id')->latest();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return !$this->hasRole('Student');
        }

        if ($panel->getId() === 'student') {
            return $this->hasRole('Student');
        }

        return true;
    }
}
