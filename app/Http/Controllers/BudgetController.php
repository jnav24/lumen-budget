<?php

namespace App\Http\Controllers;

use App\Models\Budgets;

class BudgetController extends Controller
{
    public function getAllBudgets()
    {
        try {
            $data = Budgets::where('user_id', $this->request->auth->id)->get();
            return $this->respondWithOK([
                'templates' => $data,
            ]);
        } catch (\Exception $e) {
            $this->respondWithBadRequest([], 'Unable to retrieve budgets at this time');
        }
    }
}