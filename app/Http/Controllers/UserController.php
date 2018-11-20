<?php

namespace App\Http\Controllers;

use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;

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

  public function saveUser(Request $request)
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

  public function login(Request $request)
  {
    $this->validate($request, [
        'email' => 'required|email',
        'password' => 'required|min:6'
      ]);

    $user = User::where('email', $request->email)->first();
    if ($user == null) {
      return response()->json(['message' => 'Usuario no existe'], 400);
    }

    if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'token' => $this->jwt($user)
            ], 200);
    }

    return response()->json(['message' => 'Contraseña incorrecta'], 400);
  }

  protected function jwt(User $user)
  {
    $payload = [
      'iss' => "lumen-jwt", // Issuer of the token
      'sub' => $user->id, // Subject of the token
      'iat' => time(), // Time when JWT was issued.
      'exp' => time() + (7 * 24 * 60 * 60) // Expiration time
    ];

    return JWT::encode($payload, env('JWT_SECRET'));
  }

  public function uploadAvatar($id, Request $request)
  {
    if ($request->auth->id != $id) {
      return response()->json(['message' => 'No puedes modificar un usuario que no sea el tuyo']);
    }

    $user = User::find($id);
    if ($user == null) {
      return response()->json(['message' => 'No existe usuario']);
    }

    if($request->file('avatar')){
      $extension = $request->file('avatar')->getClientOriginalExtension();
      $picName = $request->file('avatar')->getClientOriginalName();
      if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
        $picName = time() . '.' . $extension;
        $destinationPath = 'uploads/users';
        if ($user->avatar) {
          unlink('uploads/users/' . $user->avatar);
        }

        $request->file('avatar')->move($destinationPath, $picName);
        $user->avatar = $picName;
        $user->save();
        return response()->json(['user' => $user]);
      }
      return response()->json(['message' => 'Solo puedes subir imagenes en formato JPG o PNG']);

   }else {
     return response()->json(['message' => 'No has seleccionado una imagen']);
   }
  }

}
