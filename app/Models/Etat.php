<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etat extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'description'];
    public function duree()
    {
        return $this->hasMany(Duree::class);
    }
}
