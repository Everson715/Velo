<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
// CORREÇÃO: O caminho correto para o trait de UUID do Laravel é este:
use Illuminate\Database\Eloquent\Concerns\HasUuids;

#[Fillable(['id','name', 'email', 'password', 'role', 'phone', 'isOnline', 'documentsApproved', 'rating'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
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
            'isOnline' => 'boolean',
            'documentsApproved' => 'boolean',
            'rating' => 'float',
        ];
    }

    /**
     * Get the vehicles for the user.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'userId');
    }
}
