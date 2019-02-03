<?php

namespace App\Http\Controllers;

class BudgetController extends Controller
{
    public function getAllBudgetTemplates()
    {
        try {
            $budgets = [
                'templates' => [],
            ];

            return $this->respondWithOK($budgets);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve budgets at this time.');
        }
    }

    public function saveBudgetTemplates()
    {
        return $this->respondWithOK(['templates' => 'saved']);
    }
}