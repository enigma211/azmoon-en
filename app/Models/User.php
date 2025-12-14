<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'font_size',
        'theme',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
     * Allow only admin role to access Filament panels.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Get user's push notification subscriptions
     */
    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    /**
     * Check if user has an active paid subscription
     */
    public function hasPaidSubscription(): bool
    {
        return false;
    }

    /**
     * Check if user can access a specific question number in an exam
     * All users now have unlimited access to all questions
     */
    public function canAccessQuestion(int $questionNumber): bool
    {
        // All users (free, guest, paid) can access all questions
        return true;
    }

    /**
     * Get the user's role status for display.
     */
    public function getRoleStatus(): string
    {
        if ($this->hasRole('admin')) {
            return 'Admin';
        }
        return 'Free User';
    }
}
