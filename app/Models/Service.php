<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'description'];
    public function prix()
    {
        return $this->hasMany(Prix::class);
    }
    public function duree()
    {
        return $this->hasMany(Duree::class);
    }
}
