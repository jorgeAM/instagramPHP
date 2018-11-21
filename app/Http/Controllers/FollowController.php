<?php

namespace App\Http\Controllers;

use App\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
  public function follow(Request $request)
  {
    $this->validate($request, [
        'user_id' => 'required'
      ]);

    $follow = new Follow;
    $follow->user_id = $request->user_id;
    $follow->follower_id = $request->auth->id;
    $follow->save();
    return response()->json(['follow' => $follow]);
  }

  public function unFollow(Request $request)
  {
    $this->validate($request, [
        'user_id' => 'required'
      ]);

    $follow = Follow::where('user_id', $request->user_id)->first();
    if ($follow == null) {
      return response()->json(['message' => 'No se encontro registro']);
    }

    $follow->delete();
    return response()->json(['message' => 'Dejaste de seguir a esta persona']);
  }
}
