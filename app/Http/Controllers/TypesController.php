<?php

namespace App\Http\Controllers;

use App\Models\BillTypes;

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
