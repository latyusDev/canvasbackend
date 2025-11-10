<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use GuzzleHttp\Client;
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
        $token = $this->getToken($user);
        return response([
            'user'=>$user,
            'token'=>$token
        ],201);
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
        $token = $this->getToken($user);
        return response([
            'user'=>$user,
            'token'=>$token
        ],200);
    }

    public function redirectToGithub()
    {
        return Socialite::driver('github')->stateless()->redirect();
    }

    public function handleGithubCallback()
    {
        // Create Guzzle client with SSL verification disabled
            $guzzleClient = new Client([
                'verify' => false, // Only for local development!
                'timeout' => 30,
            ]);
            
            // Get user from GitHub with custom Guzzle client
            $githubUser = Socialite::driver('github')
                ->setHttpClient($guzzleClient)
                ->stateless()
                ->user();
            $getUser = User::whereEmail($githubUser->email)->first();
            if(!$getUser){
                $user = User::create([
                    'name'=>$githubUser->name,
                    'email'=>$githubUser->email
                ]);
                $token = $this->getToken($user);
                  return response([
                    'user'=>$user,
                    'token'=>$token
                ],201);
            }
          \Log::info('GitHub user retrieved', [
                'email' => $githubUser->getEmail(),
                'name' => $githubUser->getName(),
            ]);
            $token = $this->getToken($getUser);
            return redirect('http://localhost:5173')
                  ->cookie('token', $token, 60 * 24, '/', null, false, true);
    }
    

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
          $guzzleClient = new Client([
                'verify' => false, // Only for local development!
                'timeout' => 30,
            ]);
            
        $googleUser = Socialite::driver('google')
                ->setHttpClient($guzzleClient)
                ->stateless()
                ->user();
                \Log::info('google user',['googleUser'=>$googleUser]);
        $getUser = User::whereEmail($googleUser->email)->first();
         if(!$getUser){
            $user = User::create([
                'name'=>$googleUser->name,
                'email'=>$googleUser->email
            ]);
             $token = $this->getToken($user);
            return response([
                'user'=>$user,
                'token'=>$token
            ],201);
         }
           $token = $this->getToken($getUser);
             return redirect('http://localhost:5173')
                  ->cookie('token', $token, 60 * 24, '/', null, false, true);
    }

    private function getToken($user)
    {
        $token = $user->createToken('latyus')->plainTextToken;
        return $token;
    }

}
