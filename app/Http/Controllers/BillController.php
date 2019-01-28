<?php

namespace App\Http\Controllers;

use App\Models\BillTypes;

class BillController extends Controller
{
    public function types()
    {
        try {
            $types = ['types' => BillTypes::all()->toArray()];
            return $this->respondWithOK($types);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve bill types at this time.');
        }
    }
}