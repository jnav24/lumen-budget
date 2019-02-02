<?php

namespace App\Http\Controllers;

use App\Models\BankTypes;
use App\Models\BillTypes;
use App\Models\CreditCardTypes;
use App\Models\InvestmentTypes;
use App\Models\UtilityTypes;
use Illuminate\Http\Request;

class TypesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param Request $request
     *
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get Bank Types
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function bank()
    {
        try {
            $types = ['types' => BankTypes::all()->toArray()];
            return $this->respondWithOK($types);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve bank types at this time.');
        }
    }

    /**
     * Get Bill Types
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function bill()
    {
        try {
            $types = ['types' => BillTypes::all()->toArray()];
            return $this->respondWithOK($types);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve bill types at this time.');
        }
    }

    /**
     * Get Credit Card Types
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function creditCard()
    {
        try {
            $types = ['types' => CreditCardTypes::all()->toArray()];
            return $this->respondWithOK($types);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve credit card types at this time.');
        }
    }

    /**
     * Get Investment Types
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function investment()
    {
        try {
            $types = ['types' => InvestmentTypes::all()->toArray()];
            return $this->respondWithOK($types);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve investment types at this time.');
        }
    }

    /**
     * Get Utility Types
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function utility()
    {
        try {
            $types = ['types' => UtilityTypes::all()->toArray()];
            return $this->respondWithOK($types);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve utility types at this time.');
        }
    }
}