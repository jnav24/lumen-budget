<?php

namespace App\Http\Controllers;

use App\Models\User;
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
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('username', $this->request->input('email'))->first();

            if (!$user) {
                return $this->respondWithBadRequest([], 'Username does not exist');
            }

            if (Hash::check($this->request->input('password'), $user->password)) {
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
}