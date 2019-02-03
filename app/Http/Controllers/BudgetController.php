<?php

namespace App\Http\Controllers;

class BudgetController extends Controller
{
    public function getAllBudgetTemplates()
    {
        try {
            $budgets = [
                'budgets' => [],
            ];

            return $this->respondWithOK($budgets);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve budgets at this time.');
        }
    }
}