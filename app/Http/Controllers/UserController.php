<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function updateUserProfile()
    {
        try {
            $this->validate($this->request, [
                'profile' => 'required',
                'vehicles' => 'required'
            ]);

            $profileAttributes = ['first_name', 'last_name'];
            $vehicleAttributes = ['make', 'model', 'year', 'color', 'license'];

            return $this->respondWithOK([
                'profile' => [],
                'vehicles' => [],
            ]);
        } catch(ValidationException $e) {
            return $this->respondWithBadRequest($e->getMessage(), 'Error validating request');
        } catch(\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to save user profile');
        }
    }
}