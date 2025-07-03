<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Engineer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'university',
        'specialty',
        'years_of_experience',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consultations()
{
    return $this->hasMany(Consultation::class, 'engineer_id');
}
}
