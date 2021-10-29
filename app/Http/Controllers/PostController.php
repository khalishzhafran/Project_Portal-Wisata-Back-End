<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with(['user:id,name,profile'])->orderBy('id', 'desc')->get();
        return $posts;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:png,jpg,jpeg,gif,jfif'
        ]);

        $folder = 'post_images';
        $image = $request->file('image');
        $imageName = time() . '-' . $image->getClientOriginalName();
        $image->move($folder, $imageName);

        return Post::create([
            'user_id' => auth()->user()->id,
            'image' => $imageName,
            'caption' => $request->caption
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return $post;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $post->update([
            'caption' => $request->caption
        ]);

        return response([
            'message' => 'Post edited successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $path = 'post_images/' . $post->getRawOriginal('image');
        if (File::exists($path)) {
            File::delete($path);
        }

        $post->delete();

        return response([
            'message' => 'Post deleted successfully'
        ]);
    }

    public function userPosts()
    {
        // $posts = Post::where('user_id', auth()->user()->id)->get();
        // return $posts;

        return auth()->user()->post;
    }
}
