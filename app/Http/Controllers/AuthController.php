<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Mail\VerifyTokenMailable;
use App\Models\User;
use App\Models\UserDevice;
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
                $device = UserDevice::getRequestedDevice($this->request, $user->id);

                if (empty($device)) {
                    $device = $this->setUserDeviceRecord($user->id);
                    GlobalHelper::sendMailable(
                        $user->username,
                        new VerifyTokenMailable($user, $device)
                    );
                } else {
                    if ($this->isDeviceExpired($device)) {
                        $device->verify_secret = GlobalHelper::generateSecret();
                        $device->verify_token = GlobalHelper::generateToken(64);
                        $device->expires_at = Carbon::now()->addMinutes(30);
                        $device->save();
                    }

                    if ($this->isNotValidDevice($device)) {
                        GlobalHelper::sendMailable(
                            $user->username,
                            new VerifyTokenMailable($user, $device)
                        );
                    }
                }

                return $this->respondWithOK([
                    'token' => $this->jwt($user),
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
        try {
            $user = $this->request->auth;
            $verifyList = [];
            $device = UserDevice::getRequestedDevice($this->request, $user->id);

            if (empty($device)) {
                $device = $this->setUserDeviceRecord($user->id);
            }

            if ($this->isNotValidDevice($device)) {
                $verifyList = [
                    'token' => $device->verify_token,
                ];
            }

            $userProfile = UserProfile::where('user_id', $user->id)->first()->toArray();
            $vehicles = UserVehicles::where('user_id', $user->id)->get()->toArray();

            return $this->respondWithOK([
                'user' => [
                        'email' => $user->username,
                    ] + $userProfile,
                'vehicles' => $vehicles,
                'verify' => $verifyList,
            ]);
        } catch(\Exception $e) {
            Log::debug('AuthController::currentUser - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Something unexpected has occurred');
        }
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
     * Verify token if still active
     *
     * @param string $id
     * @param string $token
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function verifyToken(string $id, string $token)
    {
        try {
            if (!is_numeric($id)) {
                return $this->respondWithBadRequest([], 'Token or user does not exist');
            }

            $user = User::with('devices')->find($id);

            if (empty($user)) {
                return $this->respondWithBadRequest([], 'Token or user does not exist');
            }

            $devices = $user->devices;
            $index = $devices
                ->pluck('verify_token')
                ->search($token);

            if ($index === false) {
                return $this->respondWithBadRequest([], 'Token or user does not exist');
            }

            $device = $devices->splice($index, 1)->shift();

            if (!empty($device->verified_at)) {
                return $this->respondWithBadRequest([], 'Token is no longer valid');
            }

            return $this->respondWithOK([
                'expires_at' => $device->expires_at,
            ]);
        } catch (ValidationException $e) {
            return $this->respondWithBadRequest($e->errors(), 'Errors validating request.');
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function submitVerifyToken()
    {
        try {
            $valid = $this->validate($this->request, [
                'id' => 'required|numeric',
                'token' => 'required|alpha_num',
                'verify' => 'required|alpha_num',
            ]);

            $device = UserDevice::where('user_id', $valid['id'])
                ->where('verify_token', $valid['token'])
                ->where('verify_secret', $valid['verify'])
                ->whereNull('verified_at')
                ->first();

            $isNotExpired = Carbon::createFromTimeString($device->expires_at)->gt(Carbon::now());

            if (!empty($device) && $isNotExpired) {
                $device->expires_at = Carbon::now();
                $device->verified_at = Carbon::now();
                $device->save();
                return $this->respondWithOK([], 'Verification completed successfully!');
            }

            return $this->respondWithBadRequest([], 'The info you provided is either incorrect, expired or does not exist');
        } catch (ValidationException $e) {
            return $this->respondWithBadRequest($e->errors(), 'Errors validating request.');
        } catch (\Exception $e) {
            Log::error('AuthController::submitVerifyToken - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Something unexpected has occurred');
        }
    }

    /**
     * Resend verify token to email; occurs on expired token
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function resendVerifyToken()
    {
        try {
            $valid = $this->validate($this->request, [
                'id' => 'required|numeric',
                'token' => 'required|alpha_num',
            ]);

            $user = User::find($valid['id']);
            $device = $user->devices->where('verify_token', $valid['token'])->first();

            if (!empty($device)) {
                $device->verify_secret = GlobalHelper::generateSecret();
                $device->verify_token = GlobalHelper::generateToken(64);
                $device->expires_at = Carbon::now()->addMinutes(30);
                $device->save();
                // @todo send email; add to queue
                return $this->respondWithOK();
            }

            return $this->respondWithBadRequest([], 'The token either has expired or does not exist');
        } catch (ValidationException $e) {
            return $this->respondWithBadRequest($e->errors(), 'Errors validating request.');
        } catch (\Exception $e) {
            Log::error('AuthController::resendVerifyToken - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Something unexpected has occurred');
        }
    }

    /**
     * Create device record and returns token
     *
     * @return UserDevice
     */
    private function setUserDeviceRecord($id)
    {
        $userDevice = new UserDevice();
        $userDevice->user_id = $id;
        $userDevice->ip = $this->request->ip();
        $userDevice->agent = $this->request->header('user-agent');
        $userDevice->verify_secret = GlobalHelper::generateSecret();
        $userDevice->verify_token = GlobalHelper::generateToken(64);
        $userDevice->expires_at = Carbon::now()->addMinutes(30);
        $userDevice->save();

        return $userDevice;
    }

    private function isNotValidDevice($device): bool
    {
        return empty($device->verify_at);
    }

    private function isDeviceExpired($device): bool
    {
        return Carbon::createFromTimeString($device->expires_at)->lt(Carbon::now());
    }
}