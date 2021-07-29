@extends('layouts.app')

@section('content')
    <div class="row">
        <aside class="col-sm-4">
            {{--ユーザー情報--}}
            @include('users.card')
        </aside>
        <div class="col-sm-8">
            {{--タブ--}}
            @include('user.navtabs')
            @if (Auth::id() == $user->id)
                {{-- 投稿フォーム --}}
                @include('microposts.form')
            @endif
            {{-- 投稿一覧 --}}
            @include('microposts.microposts')
        </div>
    </div>
@endsection