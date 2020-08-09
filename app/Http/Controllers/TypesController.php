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
     * Get Bill Types
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function bill()
    {
        try {
            $billTypes = BillTypes::orderBy('slug')->get();
            $types = [];

            $billTypes->each(function ($type) use (&$types) {
                $model = 'App\\Models\\' . $type->model . 'Type';

                if (class_exists($model)) {
                    $types[$type->slug] = $model::all();
                }
            });

            return $this->respondWithOK([
                'bill_types' => $billTypes,
                'types' => $types,
            ]);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve bill types at this time.');
        }
    }
}
