<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content'];
    
    //この投稿を所有するユーザー(Userモデルとの関係を定義)
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    //このmicropostをお気に中のユーザー
    public function favorite_users()
    {
        return $this->belongsToMany(User::class, 'favorites', 'micropost_id', 'user_id')->withTimestamps();
    }
    
    

    
    

    

}
