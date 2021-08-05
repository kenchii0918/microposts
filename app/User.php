<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    //このユーザが所有する投稿
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    //このユーザがフォローしているユーザ
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    //このユーザをフォローしているユーザ
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
    //$userIdで指定されたユーザーをフォローする
    public function follow($userId)
    {
        //すでにフォローしていないか確認
        $exist = $this->is_following($userId);
        //対象が自分か確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me){
            //すでにフォローしていれば何もしない
            return false;
        } else {
            //未フォローならフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    
    //$userIdで指定されたユーザーのフォローを解除する
    public function unfollow($userId)
    {
        //すでにフォローしていないか確認
        $exist = $this->is_following($userId);
        //対象が自分自身かを確認
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            //フォローしていればフォローを解除
            $this->followings()->detach($userId);
            return true;
        } else {
            //未フォローであれば何もしない
            return false;
        }
    }
    
    
    //指定された$userIdのユーザをこのユーザがフォロー中であるか調べる
    public function is_following($userId)
    {
        //フォローしているユーザーの中に$userIdがあるか確認する
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    
    //このユーザーとフォロー中ユーザーの投稿に絞り込む
    public function feed_microposts()
    {
        //このユーザーがフォロー中のユーザーidを取得し配列する
        $userIds = $this->followings()->pluck('users.id')->toArray();
        //このユーザのidもその配列に追加
        $userIds[] = $this->id;
        //それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
    }
    
    
    
    
    //このユーザーがお気に中のmicropost
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    
    //$micropostIdで指定されたmicropostoをお気に入りする
    public function favorite($micropostId)
    {
        //すでにお気にしていないか確認
        $exist = $this->is_favorite($micropostId);
        
        
        if ($exist){
            //すでにお気にしていれば何もしない
            return false;
        } else {
            //未お気にならお気にする
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    
    //$micropostIdで指定されたお気にを解除する
    public function unfavorite($micropostId)
    {
        //すでにお気にしていないか確認
        $exist = $this->is_favorite($micropostId);
        
        if ($exist) {
            //お気にしていればお気にを解除
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            //未お気にであれば何もしない
            return false;
        }
    }
    
     //指定された$micropostIdのmicropostをこのユーザがお気に中であるか調べる
    public function is_favorite($micropostId)
    {
        //dd('注目している投稿番号は' . $micropostId);
        //お気にしているお気に入りの中に$micropostIdがあるか確認する
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
    
}
