<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'price',
        'description',
        'photo',
        'company_id',
        'average_rating',
        'ratings_count',
        'type',
    ];

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function purchases()
{
    return $this->hasMany(Purchase::class);
}

public function ratings()
{
    return $this->hasMany(Rating::class);
}

}
