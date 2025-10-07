<?php

// app/Http/Controllers/RepostController.php
namespace App\Http\Controllers;

use App\Models\Repost;
use App\Models\Tweet;
use Illuminate\Support\Facades\Auth;

class RepostController extends Controller
{
    public function store(Tweet $tweet)
    {
        // 自分の投稿はリポスト不可にしたい場合はこの if を残す
        if ($tweet->user_id === Auth::id()) return back();

        Repost::firstOrCreate([
            'user_id'  => Auth::id(),
            'tweet_id' => $tweet->id,
        ]);
        return back();
    }

    public function destroy(Tweet $tweet)
    {
        Repost::where('user_id', Auth::id())
              ->where('tweet_id', $tweet->id)
              ->delete();
        return back();
    }
}

