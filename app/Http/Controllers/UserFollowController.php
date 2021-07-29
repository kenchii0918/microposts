<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    
    //ユーザーをフォローする
    public function store($id)
    {
        // 認証済みユーザ（閲覧者）が、 idのユーザをフォローする
        \Auth::user()->follow($id);
        
        return back();
    }
    
    
    //ユーザーをあんフォローする
    public function destroy($id)
    {
        //認証済みユーザー（閲覧者）が、idのユーザーをあんフォローする
        \Auth::user()->unfollow($id);
        
        return back();
    }
    
    
    //このユーザーに関係するモデルの件数をロードする
    public function loadRelationshipCounts()
    {
        $this->loadCounts(['microposts', 'followings', 'followers']);
    }
}
