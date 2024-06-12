<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypePoil extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'illustration'];
    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }
}
