<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Socialite;
use App\User;
use Illuminate\Support\Facades\Auth;
use Hash;

class GoogleLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $userSocial = Socialite::driver('google')->stateless()->user();
        //dd($userSocial);

        //check if user exists and log the user in
        $user = User::where('user_id',$userSocial->user['id'])->first();
        if($user){
          if(Auth::loginUsingId($user->id)){
            return redirect()->route('home');
          }
        }

        $userSignup = User::create([
            'user_id' => $userSocial->user['id'],
            'name' => $userSocial->user['name'],
            //'email' => 'mrnobody@gmail.com',
            'email' => $userSocial->user['email'],
            'password' => Hash::make('pekkapekka'),
            'avatar' => $userSocial->user['picture'],
            'facebook_profile' => $userSocial->user['id'],
            // 'gender' => $userSocial->user->gender,
        ]);

        //else sign the user up
        //finally log the user in
        if($userSignup){
          if(Auth::loginUsingId($userSignup->id)){
            return redirect()->route('home');
          }
        }
        // $user->token;
    }

}
