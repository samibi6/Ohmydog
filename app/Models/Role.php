<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Constantes des rôles disponibles
    public const ADMIN = 'admin';
    public const USER = 'user';

    public static function roles(): array
    {
        return [
            self::ADMIN,
            self::USER,
        ];
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }
}
