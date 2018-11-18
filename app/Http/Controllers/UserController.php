<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function getUsers()
  {
    $users = User::all();
    if (sizeof($users) <= 0) {
      return response()->json(['message' => 'Aún no registras usuarios']);
    }

    return response()->json(['users' => $users]);
  }

  public function login(Request $request)
  {
    $this->validate($request, [
        'name' => 'required',
        'lastname' => 'required',
        'nickname' => 'required|unique:users',
        'birthday' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6'
      ]);

      $data = $request->all();
      $data['password'] = password_hash($request->password, PASSWORD_BCRYPT);
      $user = User::create($data);
      return response()->json(['user' => $user], 201);
  }

  public function getUser($id)
  {
    $user = User::find($id);
    if ($user == null) {
      return response()->json(['message' => 'No existe usuario']);
    }

    return response()->json(['user' => $user], 200);
  }

  public function updateUser($id, Request $request)
  {
      $user = User::find($id);
      if ($user == null) {
        return response()->json(['message' => 'No existe usuario']);
      }

      $user->update($request->all());
      return response()->json(['user' => $user], 200);
  }

  public function deleteUser($id)
  {
    $user = User::find($id);
    if ($user == null) {
      return response()->json(['message' => 'No existe usuario']);
    }

    $user->delete();
    return response()->json(['message' => 'Usuario eliminado']);
  }
}
