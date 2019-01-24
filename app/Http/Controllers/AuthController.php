<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create a new controller instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Create a new JWT token.
     *
     * @param  \App\Models\User $user
     * @return string
     */
    protected function jwt(User $user) {
        $payload = [
            'iss' => "lumen-jwt",
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + 60*60
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @return mixed
     */
    public function authenticate()
    {
        try {
            $this->validate($this->request, [
                'username' => 'required|email',
                'password' => 'required|min:8|max:24'
            ]);

            $user = User::where('username', $this->request->input('username'))->first();

            if (!$user) {
                return $this->respondWithBadRequest([], 'Username does not exist');
            }

            if (Hash::check($this->request->input('password'), $user->password)) {
                $userProfile = UserProfile::where('user_id', $user->id)->first()->toArray();

                if (!$userProfile) {
                    return $this->respondWithBadRequest([], 'There is a problem with your account. Please contact the administrator.');
                }

                return $this->respondWithOK([
                    'token' => $this->jwt($user),
                    'user' => [
                        'email' => $user->username,
                    ] + $userProfile
                ]);
            }

            return $this->respondWithBadRequest([], 'Username and/or password is incorrect.');
        } catch (ValidationException $ex) {
            return $this->respondWithBadRequest($ex->errors(), 'Errors validating request.');
        } catch (\Exception $ex) {
            return $this->respondWithBadRequest([], $ex->getMessage());
        }
    }

    /**
     * Registers a new user
     * Password must have; a capital & lowercase letter, a number, a special character
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function register()
    {
        try {
            $this->validate($this->request, [
                'first_name' => [
                    'min:3',
                    'required',
                ],
                'last_name' => [
                    'min:3',
                    'required',
                ],
                'username' => [
                    'required',
                    'email',
                    'unique:users',
                ],
                'password' => [
                    'max:24',
                    'min:8',
                    'required',
                    'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
                ],
                'confirm_password' => [
                    'required',
                    'same:password'
                ]
            ]);

            $user = new User();
            $userProfile = new UserProfile();

            $user->username = $this->request->input('username');
            $user->password = app('hash')->make($this->request->input('password'));
            $user->save();

            $userProfile->user_id = $user->id;
            $userProfile->image = '';
            $userProfile->first_name = $this->request->input('first_name');
            $userProfile->last_name = $this->request->input('last_name');
            $userProfile->save();

            return $this->respondWithOK();
        } catch (ValidationException $ex) {
            return $this->respondWithBadRequest($ex->errors(), 'Errors validating request.');
        } catch (\Exception $ex) {
            return $this->respondWithBadRequest([], 'Something unexpected happened');
        }
    }

    public function currentUser()
    {
        $user = $this->request->auth;
        $userProfile = UserProfile::where('user_id', $user->id)->first()->toArray();

        return $this->respondWithOK([
            'user' =>[
                'email' => $user->username,
            ] + $userProfile
        ]);
    }
}