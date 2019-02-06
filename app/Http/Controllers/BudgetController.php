<?php

namespace App\Http\Controllers;

use App\Models\BudgetTemplates;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function getAllBudgetTemplates()
    {
        try {
            $data = BudgetTemplates::where('user_id', $this->request->auth->id)
                ->with('banks')
                ->with('credit_cards')
                ->with('investments')
                ->with('jobs')
                ->with('medical')
                ->with('miscellaneous')
                ->with('utilities')
                ->first()
                ->toArray();

            if (empty($data)) {
                return $this->respondWithBadRequest([], 'User does not have any budget templates');
            }

            $budgets = [
                'template' => [
                    'id' => $data['id'],
                    'expenses' => [
                        'banks' => $data['banks'],
                        'credit_cards' => $data['credit_cards'],
                        'investments' => $data['investments'],
                        'jobs' => $data['jobs'],
                        'medical' => $data['medical'],
                        'miscellaneous' => $data['miscellaneous'],
                        'utilities' => $data['utilities'],
                    ]
                ],
            ];

            return $this->respondWithOK($budgets);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve budgets at this time.');
        }
    }

    public function saveBudgetTemplates()
    {
        try {
            if (empty($this->request->input('expenses'))) {
                return $this->respondWithBadRequest([], 'Missing expenses.');
            }

            $template = BudgetTemplates::where('user_id', $this->request->auth->id)->first();

            if (empty($template)) {
                $template = new BudgetTemplates();
                $template->created_at = Carbon::now();
            }

            $template->user_id = $this->request->auth->id;
            $template->updated_at = Carbon::now();
            $template->save();

            foreach ($this->request->input('expenses') as $type => $expenses) {
                $method = $type . '_templates';

                if (method_exists($this, $method)) {
                    $this->{$method}($expenses, $template->id);
                }
            }

            return $this->respondWithOK(['templates' => 'saved']);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to save budget template at this time.');
        }
    }

    private function banks_templates($expenses, $id)
    {
        $attributes = ['id', 'name', 'amount', 'bank_type_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'bank_templates');
    }

    private function credit_cards_templates($expenses, $id)
    {
        $attributes = ['id', 'name', 'limit', 'last_4', 'exp_month', 'exp_year', 'apr', 'due_date', 'credit_card_type_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'credit_card_templates');
    }

    private function investments_templates($expenses, $id)
    {
        $attributes = ['id', 'name', 'amount', 'investment_type_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'investment_templates');
    }

    private function jobs_templates($expenses, $id)
    {
        $attributes = ['id', 'name', 'amount', 'job_type_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'job_templates');
    }

    private function medical_templates($expenses, $id)
    {
        $attributes = ['id', 'name', 'amount', 'due_date', 'medical_type_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'medical_templates');
    }

    private function miscellaneous_templates($expenses, $id)
    {
        $attributes = ['id', 'name', 'amount', 'due_date'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'miscellaneous_templates');
    }

    private function utilities_templates($expenses, $id)
    {
        $attributes = ['id', 'name', 'amount', 'due_date', 'utility_type_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'utility_templates');
    }
}