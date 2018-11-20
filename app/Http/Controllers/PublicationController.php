<?php

namespace App\Http\Controllers;

use App\Publication;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
  public function getPublications()
  {
    $publications = Publication::all();
    if (sizeof($publications) <= 0) {
      return response()->json(['message' => 'Aún no publicas nada']);
    }

    return response()->json(['publications' => $publications]);
  }

  public function savePublication(Request $request)
  {
    $this->validate($request, [
        'body' => 'required'
      ]);

    $publication = new Publication;
    $publication->body = $request->body;
    $publication->user_id = $request->auth->id;
    $publication->save();
    return response()->json(['publication' => $publication]);
  }

  public function getPublication($id)
  {
    $publication = Publication::find($id);
    if ($publication == null) {
      return response()->json(['message' => 'No existe publicación']);
    }

    return response()->json(['publication' => $publication]);
  }

  public function updatePublication($id, Request $request)
  {
    $this->validate($request, [
        'body' => 'required'
      ]);

    $publication = Publication::find($id);
    if ($publication == null) {
      return response()->json(['message' => 'No existe publicación']);
    }

    $publication->body = $request->body;
    $publication->save();
    return response()->json(['publication' => $publication]);
  }

  public function uploadImageToPublication($id, Request $request)
  {
    $publication = Publication::find($id);
    if ($publication == null) {
      return response()->json(['message' => 'No existe publicación']);
    }

    if ($request->auth->id != $publication->user_id) {
      return response()->json(['message' => 'No puedes modificar una publicación que no sea tuya']);
    }

    if($request->file('image')){
      $extension = $request->file('image')->getClientOriginalExtension();
      $picName = $request->file('image')->getClientOriginalName();
      if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
        $picName = time() . '.' . $extension;
        $destinationPath = 'uploads/publications';
        if ($publication->image) {
          unlink('uploads/publications/' . $publication->image);
        }

        $request->file('image')->move($destinationPath, $picName);
        $publication->image = $picName;
        $publication->save();
        return response()->json(['publication' => $publication]);
      }
      return response()->json(['message' => 'Solo puedes subir imagenes en formato JPG o PNG']);

    }else {
      return response()->json(['message' => 'No has seleccionado una imagen']);
    }
  }
}
