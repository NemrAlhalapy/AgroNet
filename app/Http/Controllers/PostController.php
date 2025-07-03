<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\CreateComment;
use App\Notifications\CreatePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class PostController extends Controller
{
    public function index(){
        $posts=Post::all();
        return response()->json([
            'message'=>'all the posts',
            'data'=>[$posts]
        ]);
    }

    public function show($id){
        $post=Post::findOrFail($id);

         /** @var \App\Models\User $user */
      $user = auth()->user();

      $user->unreadNotifications()
      ->where('data->post_id', $id)
      ->get()
      ->each
      ->markAsRead();


        return response()->json([
            'message'=>'the post',
            'data'=>[$post]
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'text'=>['required', 'string'],
            'photo'=>['nullable', 'image']

        ]);
    $path = null;
     if ($request->hasFile('photo')) {
        $image = $request->file('photo')->getClientOriginalName();
        $path = $request->file('photo')->storeAs('users', $image, 'here'); 
    }

        $post=Post::create([
            'text'=>$request->text,
            'photo'=>$path,
            'user_id'=>auth()->user()->id,
        ]);
        $users=User::where('id','!=',$post->user_id)->get();
        $usercreate=auth()->user()->name;
        Notification::send($users,new CreatePost($post->id,$usercreate));

        return response()->json([
        'message' => 'تم إنشاء المنشور بنجاح',
        'data' => $post]);
    }

    public function update(Request $request,$id){
        $post=Post::findOrFail($id);
         $path = $post->photo;
     if ($request->hasFile('photo')) {
        $image = $request->file('photo')->getClientOriginalName();
        $path = $request->file('photo')->storeAs('users', $image, 'here'); 
    }
        
        $post->update([
            'text'=>$request->text,
            'photo'=>$path,

            
        ]);

        return response()->json([
        'message' => 'تم تعديل المنشور بنجاح',
        'data' => $post]);
    }

    public function delete($id){
        $post=Post::findOrFail($id);
        $post->delete();
        return response()->json([
            'message'=>'the post is delete'
        ]);
    }

     public function like($id)
    {
        $post=Post::findOrFail($id);
        $user = auth()->user();

        if ($post->islike($user)) {
            $post->like()->where('user_id', $user->id)->delete();
        } elseif ($post->isdislike($user)) {
            $post->dislike()->where('user_id', $user->id)->delete();
            $post->like()->create(['user_id' => $user->id]);
        } else {
            $post->like()->create(['user_id' => $user->id]);
        }

        return response()->json(['message' => 'Action completed']);
    }

    public function dislike($id)
    {
        $post=Post::findOrFail($id);
        $user = auth()->user();

        if ($post->isdislike($user)) {
            $post->dislike()->where('user_id', $user->id)->delete();
        } elseif ($post->islike($user)) {
            $post->like()->where('user_id', $user->id)->delete();
            $post->dislike()->create(['user_id' => $user->id]);
        } else {
            $post->dislike()->create(['user_id' => $user->id]);
        }

        return response()->json(['message' => 'Action completed']);
    }

    public function Cstore(Request $request,$id){
        $post=Post::findOrFail($id);
        $request->validate([
            'text'=>['required', 'string']
        ]);

        $comment=Comment::create([
            'text'=>$request->text,
            'user_id'=>auth()->user()->id,
            'post_id'=>$post->id
        ]);
        $userpost=Post::where('id',$comment->post_id)->first();
        $user=User::where('id',$userpost->user_id)->first();
        $usercomment=$comment->user->name;

        Notification::send($user,new CreateComment($comment->id,$usercomment));
        return response()->json([
        'message' => 'تم التعليق',
        'data' => [
            'id' => $comment->id,
            'text' => $comment->text,
            'user_id' => $comment->user_id,
            'post_id' => $comment->post_id,
            'created_at' => $comment->created_at,
            'updated_at' => $comment->updated_at,
        ]
    ]);
    }

    public function Cindex($id){
        
        $comments=Comment::where('post_id',$id)->get();
        return response()->json([
            'message'=>'all the comments',
            'data'=>[$comments]
        ]);
    }

    public function Cupdate(Request $request, $idc)
{
    $comment=Comment::findOrFail($idc);
   
    

    $request->validate([
        'text' => 'required|string',
    ]);

    $comment->update([
        'text' => $request->text,
        
    ]);

    return response()->json([
        'message' => 'تم تعديل التعليق بنجاح',
        'data' => $comment,
    ]);
}

 public function Cdelete($id){
        $comment=Comment::findOrFail($id);
        $comment->delete();
        return response()->json([
            'message'=>'the comment is delete'
        ]);
    }

    public function Cshow($id){
        $comment=Comment::findOrFail($id);

         /** @var \App\Models\User $user */
      $user = auth()->user();

      $user->unreadNotifications()
      ->where('data->comment_id', $id)
      ->get()
      ->each
      ->markAsRead();


        return response()->json([
            'message'=>'the post',
            'data'=>[$comment]
        ]);
    }


}
