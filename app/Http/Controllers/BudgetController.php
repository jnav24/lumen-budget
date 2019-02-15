<?php

namespace App\Http\Controllers;

use App\Models\Budgets;
use App\Models\JobTypes;
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

    public function getSingleBudgetExpenses($id)
    {
        try {
            $data = Budgets::where('user_id', $this->request->auth->id)
                ->where('id', $id)
                ->with('banks')
                ->with('credit_cards')
                ->with('investments')
                ->with('jobs')
                ->with('medical')
                ->with('miscellaneous')
                ->with('utilities')
                ->first();

            return $this->respondWithOK([
                'budget' => [
                    'id' => $id,
                    'name' => $data['name'],
                    'budget_cycle' => $data->created_at->toDateTimeString(),
                    'expenses' => [
                        'banks' => $data['banks'],
                        'credit_cards' => $data['credit_cards'],
                        'investments' => $data['investments'],
                        'jobs' => $data['jobs'],
                        'medical' => $data['medical'],
                        'miscellaneous' => $data['miscellaneous'],
                        'utilities' => $data['utilities'],
                    ],
                ],
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
                'cycle' => 'required',
                'expenses' => 'required',
            ]);

            if (empty($this->request->input('expenses')) || !is_array($this->request->input('expenses'))) {
                throw new \Exception('Invalid request');
            }

            $budget = new Budgets();
            $budget->user_id = $this->request->auth->id;
            $budget->name = $this->request->input('name');
            $budget->budget_cycle = $this->request->input('cycle');
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
                    'id' => $budget->id,
                    'name' => $budget->name,
                    'budget_cycle' => $budget->budget_cycle,
                    'created_at' => $budget->created_at->toDateTimeString(),
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
        $expenses = $this->generatePaidExpenses($expenses);
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

    private function generatePaidExpenses($expenses)
    {
        $currentMonth = Carbon::createFromTimeString($this->request->input('cycle'));
        $results = [];

        foreach ($expenses as $job) {
            $type = JobTypes::find($job['job_type_id'])->toArray();
            $startPay = Carbon::createFromTimeString($job['initial_pay_date']);


            $method = 'get_' . $type['slug'];

            if (method_exists($this, $method)) {
                $results[] = $this->{$method}($job, $startPay, $currentMonth);
            }
        }

        return $results;
    }

    private function get_bi_weekly($job, $startPay, $currentMonth)
    {
        $results = [];

        $totalWeeks = ($currentMonth->weekOfYear - $startPay->weekOfYear);
        $nextMonth = Carbon::createFromTimeString($this->request->input('cycle'))->addMonth();
        $payWeek = $currentMonth;

        if ($totalWeeks % 2) {
            $payWeek->addDays(7);
        }

        $payWeek->addDays($startPay->dayOfWeek - $payWeek->dayOfWeek);
        $results[] = [
            'name' => $job['name'],
            'amount' => $job['amount'],
            'job_type_id' => $job['job_type_id'],
            'initial_pay_date' => $payWeek->toDateString(),
        ];

        for ($i = 0; $i < ($nextMonth->weekOfYear - $payWeek->weekOfYear); $i = ($i+2)) {
            if ($i === 0) {
                continue;
            }

            $payWeek->addDays(14);

            if ($currentMonth->format('M') === $payWeek->format('M')) {
                $results[] = [
                    'name' => $job['name'],
                    'amount' => $job['amount'],
                    'job_type_id' => $job['job_type_id'],
                    'initial_pay_date' => $payWeek->toDateString(),
                ];
            }

        }

        return $results;
    }
}