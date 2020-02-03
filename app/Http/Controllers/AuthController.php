<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\User;
use App\Models\UserIp;
use App\Models\UserProfile;
use App\Models\UserVehicles;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
                $vehicles = UserVehicles::where('user_id', $user->id)->get()->toArray();

                if (!$userProfile) {
                    return $this->respondWithBadRequest([], 'There is a problem with your account. Please contact the administrator.');
                }

                return $this->respondWithOK([
                    'token' => $this->jwt($user),
                    'user' => [
                        'email' => $user->username,
                    ] + $userProfile,
                    'vehicles' => $vehicles,
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
        Log::debug('currentUser - ' . json_encode($user));
        Log::debug('currentUser:ips - ' . json_encode($user->ips));

        // @todo if validation fails, send a code to email on file and redirect the front end to page to validate the code
        // GlobalHelper::sendMailable($user->username, new ForgotPasswordMailable($user)); example of sending an email
        $ipList = $user->ips->toArray();
        $ipIndex = array_search($this->request->ip(), array_column($ipList, 'ip'));

        if ($ipIndex === false || empty($ipList[$ipIndex]['verified_at'])) {
//            $token = $this->setUserIpRecord($ipList, $ipIndex);
            $token = 'test';
            // @todo add mail to the queue

            return $this->respondWith([
                'token' => $token,
            ], 'verify-sign-in');
        }


        $userProfile = UserProfile::where('user_id', $user->id)->first()->toArray();
        $vehicles = UserVehicles::where('user_id', $user->id)->get()->toArray();

        return $this->respondWithOK([
            'user' => [
                'email' => $user->username,
            ] + $userProfile,
            'vehicles' => $vehicles,
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

    public function updatePassword()
    {
        try {
            $this->validate($this->request, [
                'newPassword' => 'required',
                'oldPassword' => 'required',
            ]);

            $user = User::where('id', $this->request->auth->id)->first();

            if (empty($user)) {
                return $this->respondWithBadRequest([], 'User does not exist');
            }

            if (!Hash::check($this->request->input('oldPassword'), $user->password)) {
                return $this->respondWithBadRequest([], 'User password is incorrect');
            }

            $user->password = app('hash')->make($this->request->input('newPassword'));
            $user->save();

            return $this->respondWithOK();
        } catch (ValidationException $e) {
            return $this->respondWithBadRequest($e->errors(), 'Validation errors');
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Something went wrong. Try again later.');
        }
    }

    public function validateResetPasswordToken()
    {
        try {
            $this->validate($this->request, [
                'token' => 'required',
            ]);

            $user = User::where('password_reset_token', $this->request->input('token'))->first();

            if (empty($user)) {
                return $this->respondWithBadRequest([], 'Invalid token');
            }

            $currentTimestamp = GlobalHelper::setDefaultDateTimeIfNull();

            if (empty($user->password_reset_token) || $currentTimestamp > $user->password_reset_token) {
                return $this->respondWithBadRequest([], 'Token has expired');
            }

            return $this->respondWithOK();
        } catch (ValidationException $ex) {
            return $this->respondWithBadRequest($ex->errors(), 'Invalid token');
        } catch (\Exception $ex) {
            return $this->respondWithBadRequest([], 'Unable to validate reset password token at this time.');
        }
    }

    /**
     * @param $ipList
     * @param bool $ipIndex
     * @return string|null
     */
    private function setUserIpRecord($ipList, bool $ipIndex)
    {
        $userIp = null;
        $token = null;

        if (!empty($ipList[$ipIndex]['id'])) {
            $userIp = UserIp::find($ipList[$ipIndex]['id']);
            $token = $ipList[$ipIndex]['verify_token'];
        }

        if (empty($userIp)) {
            $userIp = new UserIp();
            $token = GlobalHelper::generateToken(64);
        }

        $userIp->ip = $this->request->ip();
        $userIp->verify_secret = GlobalHelper::generateSecret();
        $userIp->verify_token = $token;
        $userIp->expires_at = Carbon::now()->addMinutes(30);
        $userIp->save();
        return $token;
    }
}