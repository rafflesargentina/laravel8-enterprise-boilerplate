<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\{ Controller, RedirectsUsers };

use Auth;
use Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use RafflesArgentina\RestfulController\Traits\FormatsValidJsonResponses;
use Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use FormatsValidJsonResponses, RedirectsUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->setUser($user);

        //$user->loadMissing();

        $token = $user->createToken(env('APP_NAME'));
        $accessToken = $token->accessToken;
        $expiresAt = $token->token->expires_at;
        $tokenType = 'bearer';

        $data = compact('accessToken', 'expiresAt', 'tokenType', 'user');

        return $this->registered($request, $user)
            ?: $this->validSuccessJsonResponse('Register success', $data, $this->redirectPath());
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data, [
            //'accepted' => 'accepted',
            'email' => 'required|string|email|unique:users',
            'first_name' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \Raffles\Models\User
     */
    protected function create(array $data)
    {
        $userModel = config('auth.providers.users.model');
	
	return (new $userModel)->create(
            [
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'password' => $data['password'],
            ]
        );
    }
}
