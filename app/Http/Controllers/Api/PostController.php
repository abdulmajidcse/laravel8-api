<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::latest('id')->get();
        return response()->json($posts, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required|string',
            'content'    => 'required|string',
            'image'    => 'nullable|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }

        $post          = new Post();
        $post->title   = $request->title;
        $post->content = $request->content;

        // image upload and store name in posts table
        if($request->file('image')) {
            $file = $request->file('image');
            $file_name = uniqid() . time();
            $ext = strtolower($file->getClientOriginalExtension());
            $file_full_name = $file_name . "." . $ext;
            $upload_path = "assets/uploads/";
            //upload file
            $file->move($upload_path, $file_full_name);
            // save name in table
            $post->image = $file_full_name;
        }

        $post->save();

        return response()->json(['success' => 'Post saved'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        if($post) {
            return response()->json($post, 200);
        }
        return response()->json(['error' => 'Post not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required|string',
            'content'    => 'required|string',
            'image'    => 'nullable|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }

        $post = Post::find($id);
        if(!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $post->title   = $request->title;
        $post->content = $request->content;

        // image upload and store name in posts table
        if($request->file('image')) {
            $file = $request->file('image');
            $file_name = uniqid() . time();
            $ext = strtolower($file->getClientOriginalExtension());
            $file_full_name = $file_name . "." . $ext;
            $upload_path = "assets/uploads/";
            //upload file
            $file->move($upload_path, $file_full_name);

            //delete image
            if($post->image && file_exists('assets/uploads/'.$post->image)) {
                unlink('assets/uploads/'.$post->image);
            }

            // save name in table
            $post->image = $file_full_name;
        }

        $post->save();

        return response()->json(['success' => 'Post saved'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if(!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        //delete image
        if($post->image && file_exists('assets/uploads/'.$post->image)) {
            unlink('assets/uploads/'.$post->image);
        }

        $post->delete();
        return response()->json(['success' => 'Post deleted'], 200);
    }
}
