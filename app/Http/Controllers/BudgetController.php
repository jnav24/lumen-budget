<?php

namespace App\Http\Controllers;

use App\Models\Budgets;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class BudgetController extends Controller
{
    protected $tableId = 'budget_id';

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
            $budget->user_id = $this->request->auth->id;
            $budget->name = $this->request->input('name');
            $budget->budget_cycle = Carbon::now()->format('Y-m-d H:i:s');
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

    public function updateBudget()
    {
        // @TODO when saving a budget, update the budget_template values; i.e. bank amount, pay periods etc
    }

    private function save_banks($id, $expenses)
    {
        $attributes = $this->getBanksAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'banks');
    }

    private function save_credit_cards($id, $expenses)
    {
        $attributes = $this->getCreditCardsAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'credit_cards');
    }

    private function save_investments($id, $expenses)
    {
        $attributes = $this->getInvestmentAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'investments');
    }

    private function save_jobs($id, $expenses)
    {
        $attributes = $this->getJobsAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'jobs');
    }

    private function save_medical($id, $expenses)
    {
        $attributes = $this->getMedicalAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'medical');
    }

    private function save_miscellaneous($id, $expenses)
    {
        $attributes = $this->getMiscellaneousAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'miscellaneous');
    }

    private function save_utilities($id, $expenses)
    {
        $attributes = $this->getUtilitiesAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'utilities');
    }
}