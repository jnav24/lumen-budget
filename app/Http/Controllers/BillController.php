<?php

namespace App\Http\Controllers;

use App\Models\BillTypes;
use Illuminate\Http\Request;

class BillController extends Controller
{
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