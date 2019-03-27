<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected $tableId = 'user_id';

    public function updateUserProfile()
    {
        try {
            $this->validate($this->request, [
                'profile' => 'required',
                'vehicles' => 'required'
            ]);

            $profileAttributes = ['first_name', 'last_name'];
            $profileRequest = array_intersect_key($this->request->input('profile'), array_flip($profileAttributes));

            if (empty($profileRequest) || count($profileAttributes) !== count($profileRequest)) {
                return $this->respondWithBadRequest([], '');
            }

            $profile = UserProfile::where('user_id', $this->request->auth->id)->first();
            $profile->first_name = $profileRequest['first_name'];
            $profile->last_name = $profileRequest['last_name'];
            $profile->save();

            $vehicleAttributes = ['id', 'make', 'model', 'year', 'color', 'license', 'active'];
            // $vechicles = $this->insertOrUpdate($vehicleAttributes, $this->request->input('vehicles'), $this->request->auth->id, 'user_vehicles');
            $vehicles = [];

            return $this->respondWithOK([
                'profile' => array_merge($profile->toArray(), ['email' => $this->request->auth->username]),
                'vehicles' => $vehicles,
            ]);
        } catch(ValidationException $e) {
            return $this->respondWithBadRequest($e->getMessage(), 'Error validating request');
        } catch(\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to save user profile');
        }
    }
}