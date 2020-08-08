<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Models\UserVehicle;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected $tableId = 'user_id';

    public function updateUserProfile()
    {
        try {
            $this->validate($this->request, [
                'profile' => 'required',
                'vehicles' => 'required|array'
            ]);

            $profileAttributes = ['first_name', 'last_name'];
            $profileRequest = array_intersect_key(
                $this->request->input('profile'),
                array_flip($profileAttributes)
            );

            if (empty($profileRequest) || count($profileAttributes) !== count($profileRequest)) {
                return $this->respondWithBadRequest([], '');
            }

            $profile = UserProfile::where('user_id', $this->request->auth->id)->first();
            $profile->first_name = $profileRequest['first_name'];
            $profile->last_name = $profileRequest['last_name'];
            $profile->save();

            $attributes = (new UserVehicle())->getAttributes();

            $vehicles = array_map(function ($vehicle) use ($attributes) {
                return UserVehicle::updateOrCreate(
                    ['id' => $this->isNotTempId($vehicle['id']) ? $vehicle['id'] : null],
                    array_intersect_key(
                        $vehicle,
                        $attributes
                    )
                );
            }, $this->request->input('vehicles'));

            return $this->respondWithOK([
                'profile' => array_merge(
                    $profile->toArray(),
                    ['email' => $this->request->auth->username]
                ),
                'vehicles' => $vehicles,
            ]);
        } catch(ValidationException $e) {
            return $this->respondWithBadRequest($e->errors(), 'Error validating request');
        } catch(\Exception $e) {
            Log::error('UserController::updateUserProfile - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Unable to save user profile');
        }
    }
}
