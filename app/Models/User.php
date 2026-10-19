<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
     * Dapatkan inisial untuk avatar profil user.
     * Super Admin berinisial SA, admin lainnya menggunakan huruf pertama namanya.
     */
    public function getInitialAttribute(): string
    {
        if (strtoupper($this->role ?? '') === 'SUPER_ADMIN') {
            return 'SA';
        }

        $name = trim($this->name ?? '');

        return $name !== '' ? strtoupper(mb_substr($name, 0, 1, 'UTF-8')) : 'A';
    }
}
