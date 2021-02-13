<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::latest('id')->get();
        if($users->count() > 0) {
            return response()->json($users, 200);
        }
        return response()->json(['error' => 'User not found', 'sttus' => 404], 404);
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
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'photo'    => 'nullable|mimes:jpg,jpeg,png',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        // photo upload and store name in users table
        if($request->file('photo')) {
            $file = $request->file('photo');
            $file_name = uniqid() . time();
            $ext = strtolower($file->getClientOriginalExtension());
            $file_full_name = $file_name . "." . $ext;
            $upload_path = "assets/uploads/";
            //upload file
            $file->move($upload_path, $file_full_name);
            // save name in table
            $user->photo = $file_full_name;
        }

        $user->save();

        return response()->json(['success' => 'User saved', 'sttus' => 201], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if($user) {
            return response()->json($user, 200);
        }
        return response()->json(['error' => 'User not found', 'sttus' => 404], 404);
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
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email,'.$id,
            'photo'    => 'nullable|mimes:jpg,jpeg,png',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = User::find($id);
        if(!$user) {
            return response()->json(['error' => 'User not found', 'sttus' => 404], 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if($request->password) {
            $user->password = Hash::make($request->password);
        }

        // photo upload and store name in users table
        if($request->file('photo')) {
            $file = $request->file('photo');
            $file_name = uniqid() . time();
            $ext = strtolower($file->getClientOriginalExtension());
            $file_full_name = $file_name . "." . $ext;
            $upload_path = "assets/uploads/";
            //upload file
            $file->move($upload_path, $file_full_name);

            //delete photo
            if($user->photo && file_exists('assets/uploads/'.$user->photo)) {
                unlink('assets/uploads/'.$user->photo);
            }

            // save name in table
            $user->photo = $file_full_name;
        }

        $user->save();

        return response()->json(['success' => 'User saved', 'sttus' => 201], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user) {
            return response()->json(['error' => 'User not found', 'sttus' => 404], 404);
        }

        //delete photo
        if($user->photo && file_exists('assets/uploads/'.$user->photo)) {
            unlink('assets/uploads/'.$user->photo);
        }

        $user->delete();
        return response()->json(['success' => 'User deleted', 'sttus' => 200], 200);
    }
}
