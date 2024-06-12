<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'date', 'heure', 'race', 'type_poil_id', 'duree_id', 'heure_fin'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function duree()
    {
        return $this->belongsTo(Duree::class);
    }
    public function typePoil()
    {
        return $this->belongsTo(TypePoil::class);
    }
}
