<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Publication;
use Illuminate\Http\Request;

class CommentController extends Controller
{
  public function getComments($publicationId)
  {
    $publication = Publication::find($publicationId);
    if ($publication == null) {
      return response()->json(['message' => 'No existe publicación']);
    }

    $comments = Comment::with('user')->where('publication_id', $publicationId)->get();
    return response()->json(['comments' => $comments]);
  }

  public function saveComment($publicationId, Request $request)
  {
    $this->validate($request, [
        'body' => 'required'
      ]);

    $publication = Publication::find($publicationId);
    if ($publication == null) {
      return response()->json(['message' => 'No existe publicación']);
    }

    $comment = new Comment;
    $comment->body = $request->body;
    $comment->user_id = $request->auth->id;
    $comment->publication_id = $publicationId;
    $comment->save();
    return response()->json(['comment' => $comment]);
  }

  public function updateComment($publicationId, $commentId, Request $request)
  {
    $this->validate($request, [
        'body' => 'required'
      ]);

    $publication = Publication::find($publicationId);
    if ($publication == null) {
      return response()->json(['message' => 'No existe publicación']);
    }

    $comment = Comment::find($commentId);
    if ($comment == null) {
      return response()->json(['message' => 'No existe comentario']);
    }

    if ($request->auth->id != $comment->user_id) {
      return response()->json(['message' => 'Solo puedes editar tus propios comentarios']);
    }
    $comment->body = $request->body;
    $comment->save();
    return response()->json(['comment' => $comment]);
  }

  public function deleteComment($publicationId, $commentId, Request $request)
  {
    $publication = Publication::find($publicationId);
    if ($publication == null) {
      return response()->json(['message' => 'No existe publicación']);
    }

    $comment = Comment::find($commentId);
    if ($comment == null) {
      return response()->json(['message' => 'No existe comentario']);
    }

    if ($request->auth->id != $comment->user_id) {
      return response()->json(['message' => 'Solo puedes eliminar tus propios comentarios']);
    }

    $comment->delete();
    return response()->json(['message' => 'Comentario eliminado']);
  }
}
