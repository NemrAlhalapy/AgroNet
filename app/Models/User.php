<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'number_phone',
        'age',
        'role',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

     public function post(){
    return $this->hasMany(Post::class);
   }

    public function like(){
    return $this->hasMany(Like::class);
   }

   public function dislike(){
    return $this->hasMany(Dislike::class);
   }

   public function comment(){
    return $this->hasMany(Comment::class);
   }

   public function purchases()
{
    return $this->hasMany(Purchase::class);
}

    public function wallet(){
        return $this->hasOne(Wallet::class);
    }

    public function ratings()
{
    return $this->hasMany(Rating::class);
}

    public function engineer(){
        return $this->hasOne(Engineer::class);
    }

    public function farmer(){
        return $this->hasOne(Farmer::class);
    }



}
