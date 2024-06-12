<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prix extends Model
{
    use HasFactory;
    protected $fillable = ['service_id', 'taille_id', 'prix'];
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function taille()
    {
        return $this->belongsTo(Taille::class);
    }
}
