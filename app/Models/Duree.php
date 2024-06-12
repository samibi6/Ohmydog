<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duree extends Model
{
    use HasFactory;
    protected $fillable = ['service_id', 'taille_id', 'etat_id', 'duree'];
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function taille()
    {
        return $this->belongsTo(Taille::class);
    }
    public function etat()
    {
        return $this->belongsTo(Etat::class);
    }
    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }
}
