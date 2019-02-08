<?php

namespace App\Http\Controllers;

use App\Models\Budgets;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

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
            return $this->respondWithBadRequest([], 'Unable to retrieve budgets at this time');
        }
    }

    public function saveBudget()
    {
        try {
            $this->validate($this->request, [
                'name' => 'required',
                'expenses' => 'required',
            ]);

            if (empty($this->request->input('expenses')) || !is_array($this->request->input('expenses'))) {
                throw new \Exception('Invalid request');
            }

            $budget = new Budgets();
            $budget->name = $this->request->input('name');
            $budget->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $budget->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $budget->save();

            $returnExpenses = [];

            foreach ($this->request->input('expenses') as $key => $expenseList) {
                $method = 'save_' . $key;

                if (method_exists($this, $method)) {
                    $returnExpenses[$key] = $this->{$method}($budget->id, $expenseList);
                }
            }

            return $this->respondWithOK([
                'budget' => [
                    'name' => $budget->name,
                    'created_at' => $budget->created_at,
                    'expenses' => $returnExpenses,
                ],
            ]);
        } catch (ValidationException $e) {
            return $this->respondWithBadRequest($e->getMessage(), 'Errors validating request.');
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to save budget at this time.');
        }
    }

    private function save_banks($id, $list)
    {}

    private function save_credit_cards($id, $list)
    {}

    private function save_investments($id, $list)
    {}

    private function save_jobs($id, $list)
    {}

    private function save_medical($id, $list)
    {}

    private function save_miscellaneous($id, $list)
    {}

    private function save_utilities($id, $list)
    {}
}