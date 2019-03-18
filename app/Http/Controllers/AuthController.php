<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\User;
use App\Models\UserProfile;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Mail\ForgotPasswordMailable;

class AuthController extends Controller
{
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
            'exp' => time() + 60 * 160
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

    public function forgetPassword()
    {
        try {
            $this->validate($this->request, [
                'username' => 'required|email'
            ]);

            $user = User::where('username', $this->request->input('username'))->with('profile')->first();

            if(empty($user)) {
                return $this->respondWithBadRequest([], 'Username not found');
            }

            GlobalHelper::sendMailable($user->username, new ForgotPasswordMailable($user));
            return $this->respondWithOK();
        } catch(ValidationException $ex) {
            return $this->respondWithBadRequest($ex->errors(), 'Invalid username');
        } catch (\Exception $ex) {
            return $this->respondWithBadRequest([], 'Unable to send forget my password');
        }
    }

    public function resetPassword()
    {
        try {
            $this->validate($this->request, [
                'token' => 'required',
                'password' => 'required',
                'confirm_password' => 'required',
            ]);

            if ($this->request->input('password') !== $this->request->input('confirm_password')) {
                return $this->respondWithBadRequest([], '');
            }

            $user = User::where('password_reset_token', $this->request->input('token'))->first();

            if (empty($user)) {
                return $this->respondWithBadRequest([], 'Invalid token.');
            }

            $currentTimestamp = GlobalHelper::setDefaultDateTimeIfNull();

            if (empty($user->password_reset_token) || $currentTimestamp > $user->password_reset_token) {
                return $this->respondWithBadRequest([], 'Token has expired');
            }

            $user->password_reset_token = null;
            $user->password_reset_expires = null;
            $user->password = app('hash')->make($this->request->input('password'));
            $user->save();

            return $this->respondWithOK();
        } catch (ValidationException $ex) {
            return $this->respondWithBadRequest($ex->errors(), 'Invalid token');
        } catch (\Exception $ex) {
            return $this->respondWithBadRequest([], 'Unable to reset password at this time.');
        }
    }
}