<?php

namespace App\Http\Controllers;

use App\Models\BankTypes;
use App\Models\BillTypes;
use App\Models\CreditCardTypes;
use App\Models\InvestmentTypes;
use App\Models\IncomeType;
use App\Models\MedicalTypes;
use App\Models\UtilityTypes;
use App\Models\VehicleTypes;

class TypesController extends Controller
{
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
            $types = ['types' => BillTypes::orderBy('slug')->get()->toArray()];
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
     * Get Job Types
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function job()
    {
        try {
            $types = ['types' => IncomeType::all()->toArray()];
            return $this->respondWithOK($types);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve job types at this time.');
        }
    }

    /**
     * Get Medical Types
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function medical()
    {
        try {
            $types = ['types' => MedicalTypes::all()->toArray()];
            return $this->respondWithOK($types);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve medical types at this time.');
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

    /**
     * Get Vehicle Types
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function vehicle()
    {
        try {
            $types = ['types' => VehicleTypes::all()->toArray()];
            return $this->respondWithOK($types);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve vehicle types at this time.');
        }
    }
}
