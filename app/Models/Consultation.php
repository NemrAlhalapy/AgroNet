<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'engineer_id',
        'question',
        'answer',
        'status',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function engineer()
    {
        return $this->belongsTo(Engineer::class);
    }
}
