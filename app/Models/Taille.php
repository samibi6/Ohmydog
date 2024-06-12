<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taille extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'exemple'];
    public function prix()
    {
        return $this->hasMany(Prix::class);
    }
    public function duree()
    {
        return $this->hasMany(Duree::class);
    }
}
