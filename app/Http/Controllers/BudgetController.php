<?php

namespace App\Http\Controllers;

use App\Models\BudgetTemplates;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
                return $this->respondWithBadRequest([], 'noob');
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
                return $this->respondWithBadRequest([], 'Unable to save budget template at this time.');
            }

            dd($this->request->input('expenses'));

            if (!empty($this->request->input('id'))) {
                $template = BudgetTemplates::find($this->request->input('id'));

                if (empty($template)) {
                    return $this->respondWithBadRequest([], 'Unable to save budget template at this time.');
                }
            } else {
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

    private function insertOrUpdate(array $attributes, array $data, int $id, string $model)
    {
        foreach ($data as $item) {
            // this might already be an array, line 33
            $item = $item->toArray();

            if (count($attributes) === count($item)) {
                $template = array_intersect_key($item, $attributes); // add $id to array

                if (!empty($item['id'])) {
                    $date = [
                        'updated_at' => Carbon::now(),
                    ];
                    DB::table($model)->where('id', $item['id'])->update(array_merge($template, $date));
                } else {
                    $date = [
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    DB::table($model)->insert(array_merge($template, $date));
                }
            }
        }
    }

    private function bank_templates($expenses, $id)
    {
        $attributes = ['name', 'amount', 'bank_type_id', 'budget_template_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'bank_templates');
    }

    private function credit_card_templates($expenses, $id)
    {
        $attributes = ['name', 'limit', 'last_4', 'exp_month', 'exp_year', 'apr', 'due_date', 'credit_card_type_id', 'budget_template_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'credit_card_templates');
    }

    private function investment_templates($expenses, $id)
    {
        $attributes = ['name', 'amount', 'investment_type_id', 'budget_template_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'investment_templates');
    }

    private function job_templates($expenses, $id)
    {
        $attributes = ['name', 'amount', 'job_type_id', 'budget_template_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'job_templates');
    }

    private function medical_templates($expenses, $id)
    {
        $attributes = ['name', 'amount', 'due_date', 'medical_type_id', 'budget_template_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'medical_templates');
    }

    private function miscellaneous_templates($expenses, $id)
    {
        $attributes = ['name', 'amount', 'due_date', 'budget_template_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'miscellaneous_templates');
    }

    private function utility_templates($expenses, $id)
    {
        $attributes = ['name', 'amount', 'due_date', 'utility_type_id', 'budget_template_id'];
        $this->insertOrUpdate($attributes, $expenses, $id, 'utility_templates');
    }
}