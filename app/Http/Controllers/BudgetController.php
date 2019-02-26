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
            $data = Budgets::where('user_id', $this->request->auth->id)->orderBy('id', 'desc')->get();
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
                    'budget_cycle' => $data['budget_cycle'],
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

            $expenses = $this->request->input('expenses');

            if (!empty($this->request->input('id'))) {
                $budget = Budgets::find($this->request->input('id'));

                if (empty($budget)) {
                    $budget = new Budgets();
                    $budget->created_at = Carbon::now()->format('Y-m-d H:i:s');
                    $expenses['jobs'] = $this->generatePaidExpenses($expenses['jobs']);
                }
            } else {
                $budget = new Budgets();
                $budget->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $expenses['jobs'] = $this->generatePaidExpenses($expenses['jobs']);
            }

            $budget->user_id = $this->request->auth->id;
            $budget->name = $this->request->input('name');
            $budget->budget_cycle = $this->request->input('cycle');
            $budget->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $budget->save();

            $returnExpenses = [];

            foreach ($expenses as $key => $expenseList) {
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

    /**
     * Saves banks info; called dynamically from saveBudget()
     *
     * @param integer $id Budget Id; Foreign Key
     * @param array $expenses {
     *      @value integer ['id'] (optional)
     *      @value integer ['bank_type_id']
     *      @value string ['amount']
     *      @value string ['name']
     * }
     * @return array {
     *      @value integer ['id']
     *      @value integer ['bank_type_id']
     *      @value string ['amount']
     *      @value string ['name']
     * }
     */
    private function save_banks($id, $expenses)
    {
        $attributes = $this->getBanksAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'banks');
    }

    /**
     * Saves credit card info; called dynamically from saveBudget()
     *
     * @param integer $id Budget Id; Foreign Key
     * @param array $expenses {
     *      @value integer ['id'] (optional)
     *      @value string ['name']
     *      @value string ['limit']
     *      @value string ['last_4'] (optional)
     *      @value string ['exp_month'] (optional)
     *      @value string ['exp_year'] (optional)
     *      @value integer ['apr'] (optional)
     *      @value integer ['due_date']
     *      @value integer ['credit_card_type_id']
     *      @value Datetime ['pay_date'] (optional)
     *      @value string ['confirmation'] (optional)
     *      @value string ['amount'] (optional)
     * }
     * @return array {
     *      @value integer ['id']
     *      @value string ['name']
     *      @value string ['limit']
     *      @value string ['last_4']
     *      @value string ['exp_month']
     *      @value string ['exp_year']
     *      @value integer ['apr']
     *      @value integer ['due_date']
     *      @value integer ['credit_card_type_id']
     *      @value Datetime ['pay_date']
     *      @value string ['confirmation']
     *      @value string ['amount']
     * }
     */
    private function save_credit_cards($id, $expenses)
    {
        $attributes = $this->getCreditCardsAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'credit_cards');
    }

    /**
     * Saves investments info; called dynamically from saveBudget()
     *
     * @param integer $id Budget Id; Foreign Key
     * @param array $expenses {
     *      @value integer ['id'] (optional)
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['investment_type_id']
     * }
     * @return array {
     *      @value integer ['id']
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['investment_type_id']
     * }
     */
    private function save_investments($id, $expenses)
    {
        $attributes = $this->getInvestmentAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'investments');
    }

    /**
     * Saves jobs info; called dynamically from saveBudget()
     *
     * @param integer $id Budget Id; Foreign Key
     * @param array $expenses {
     *      @value integer ['id'] (optional)
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['job_type_id']
     *      @value Datetime ['initial_pay_date']
     * }
     * @return array {
     *      @value integer ['id']
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['job_type_id']
     *      @value Datetime ['initial_pay_date']
     * }
     */
    private function save_jobs($id, $expenses)
    {
        $attributes = $this->getJobsAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'jobs');
    }

    /**
     * Saves medical info; called dynamically from saveBudget()
     *
     * @param integer $id Budget Id; Foreign Key
     * @param array $expenses {
     *      @value integer ['id'] (optional)
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['due_date']
     *      @value integer ['medical_type_id']
     *      @value Datetime ['pay_date'] (optional)
     *      @value string ['confirmation'] (optional)
     * }
     * @return array {
     *      @value integer ['id']
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['due_date']
     *      @value integer ['medical_type_id']
     *      @value Datetime ['pay_date']
     *      @value string ['confirmation']
     * }
     */
    private function save_medical($id, $expenses)
    {
        $attributes = $this->getMedicalAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'medical');
    }

    /**
     * Saves miscellaneous info; called dynamically from saveBudget()
     *
     * @param integer $id Budget Id; Foreign Key
     * @param array $expenses {
     *      @value integer ['id'] (optional)
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['due_date']
     *      @value Datetime ['pay_date'] (optional)
     *      @value string ['confirmation'] (optional)
     * }
     * @return array {
     *      @value integer ['id']
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['due_date']
     *      @value Datetime ['pay_date']
     *      @value string ['confirmation']
     * }
     */
    private function save_miscellaneous($id, $expenses)
    {
        $attributes = $this->getMiscellaneousAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'miscellaneous');
    }

    /**
     * Saves utilities info; called dynamically from saveBudget()
     *
     * @param integer $id Budget Id; Foreign Key
     * @param array $expenses {
     *      @value integer ['id'] (optional)
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['due_date']
     *      @value integer ['utility_type_id']
     *      @value Datetime ['pay_date'] (optional)
     *      @value string ['confirmation'] (optional)
     * }
     * @return array {
     *      @value integer ['id']
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['due_date']
     *      @value integer ['utility_type_id']
     *      @value Datetime ['pay_date']
     *      @value string ['confirmation']
     * }
     */
    private function save_utilities($id, $expenses)
    {
        $attributes = $this->getUtilitiesAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'utilities');
    }

    /**
     * For employment, create a record for all pay dates in a billing cycle based on user input
     *
     * @param array $expenses {
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['job_type_id']
     *      @value Datetime ['initial_pay_date']
     * }
     * @return array {
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['job_type_id']
     *      @value Datetime ['initial_pay_date']
     * }
     */
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

    /**
     * Get bi-weekly pay periods for a billing cycle; called dynamically from generatePaidExpenses()
     *
     * @param array $job {
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['job_type_id']
     *      @value Datetime ['initial_pay_date']
     * }
     * @param Carbon $startPay
     * @param Carbon $currentMonth
     * @return array {
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['job_type_id']
     *      @value Datetime ['initial_pay_date']
     * }
     */
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

    /**
     * Get one time payment billing cycle; called dynamically from generatePaidExpenses()
     *
     * @param array $job {
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['job_type_id']
     *      @value Datetime ['initial_pay_date']
     * }
     * @param Carbon $startPay
     * @param Carbon $currentMonth
     * @return array {
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['job_type_id']
     *      @value Datetime ['initial_pay_date']
     * }
     */
    private function get_one_time($job, $startPay, $currentMonth) {
        return $job;
    }
}