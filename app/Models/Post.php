<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

   protected $fillable = ['text', 'photo', 'user_id'];

   public function user(){
    return $this->belongsTo(User::class);
   }

   public function like(){
    return $this->hasMany(Like::class);
   }

   public function islike(User $user){
    return $this->like->contains('user_id',$user->id);
   }

   public function dislike(){
    return $this->hasMany(Dislike::class);
   }

   public function isdislike(User $user){
    return $this->dislike->contains('user_id',$user->id);
   }

   public function comment(){
    return $this->hasMany(Comment::class);
   }

}
