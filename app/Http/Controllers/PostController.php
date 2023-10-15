<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        return response()->json($posts, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = ['required' => 'The :attribute field is required.'];
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ], $messages);
        if($validator->fails()){
            return response()->json(['msg'=>$validator->errors()], 200);
        }else{
            $post = Post::create([
                'title' => $request->title,
                'description' => $request->description,
            ]);
            return response()->json([$post, 'msg'=>'New Item created successful.'], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);

        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::findOrFail($id);
        $post->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);
        return response()->json(['msg'=> 'Update Successfull.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        $post->delete();
        return response()->json(['msg', 'delete successful.'], 200);
    }
}
