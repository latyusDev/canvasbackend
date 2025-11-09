<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $userDetails = $request->all();
        $userDetails['password'] = Hash::make($userDetails['password']);
        $user = User::create($userDetails);
        $this->createToken($user,201);
    }

    public function login(Request $request)
    {
        $request->validate(['email'=>'required','password'=>'required']);
        $user = User::whereEmail($request->email)->first();
        if(!$user || !Hash::check($request->password,$user->password)){
            return response([
                'message'=>'user not found'
            ]);
        }
        $this->createToken($user,200);
    }

    public function redirectToGithub()
    {
        return Socialite::driver('github')->stateless()->redirect();
    }

    public function handleGithubCallback()
    {
        $githubUser = Socialite::driver('github')->stateless()->user();
        $getUser = User::whereEmail($githubUser)->first();
        if(!$getUser){
            $user = User::create([
                'name'=>$githubUser->name,
                'email'=>$githubUser->email
            ]);
            $this->createToken($user,201);
        }
        $this->createToken($getUser,201);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $getUser = User::whereEmail($googleUser->email)->first();
         if(!$getUser){
            $user = User::create([
                'name'=>$googleUser->name,
                'email'=>$googleUser->email
            ]);
            $this->createToken($user,201);
         }
            $this->createToken($getUser,201);
    }

    private function createToken($user,$status)
    {
        $token = $user->createToken('latyus')->plainTextToken;
        return response([
            'user'=>$user,
            'token'=>$token
        ],$status);
    }

}
