<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register user and get access token
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        
        return $this->getAccessToken($request);
    }

    /**
     * login user and get access token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = User::where('email', $request->email)->first();

        if($user && Hash::check($request->password, $user->password)) {
            return $this->getAccessToken($request);
        }

        return response()->json(['error' => 'Email or Password are wrong']);
    }

    private function getAccessToken($authInfo)
    {
        $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => 8,
            'client_secret' => 'A7VQdLLZ20j539xBX5Lqg2Rh0gmD0175cmGMYR90',
            'username' => $authInfo->email,
            'password' => $authInfo->password,
            'scope' => '',
        ]);
        
        return $response->json();
    }
}
