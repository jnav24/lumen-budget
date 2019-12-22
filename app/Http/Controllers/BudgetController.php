<?php

namespace App\Http\Controllers;

use App\Models\Banks;
use App\Models\BudgetAggregation;
use App\Models\Budgets;
use App\Models\CreditCards;
use App\Models\Investments;
use App\Models\Jobs;
use App\Models\JobTypes;
use App\Models\Medical;
use App\Models\Miscellaneous;
use App\Models\Utilities;
use App\Models\Vehicles;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BudgetController extends Controller
{
    protected $tableId = 'budget_id';
    private $earned = ['jobs'];
    private $spent = ['credit_cards', 'medical', 'miscellaneous', 'utilities', 'vehicles'];

    public function getAllBudgets()
    {
        try {
            $data = Budgets::where('user_id', $this->request->auth->id)->orderBy('id', 'desc')->get();
            return $this->respondWithOK([
                'templates' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('BudgetController::getAllBudgets - ' . $e->getMessage());
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
                ->with('vehicles')
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
                        'vehicles' => $data['vehicles'],
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('BudgetController::getSingleBudgetExpenses - ' . $e->getMessage());
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

            DB::beginTransaction();
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

            $this->setupAndSaveAggregation($budget->id, $expenses);
            DB::commit();

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
            DB::rollBack();
            Log::error('BudgetController::saveBudget - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Unable to save budget at this time.');
        }
    }

    public function deleteBudget($id) {
        try {
            Banks::where($this->tableId, $id)->delete();
            CreditCards::where($this->tableId, $id)->delete();
            Investments::where($this->tableId, $id)->delete();
            Jobs::where($this->tableId, $id)->delete();
            Medical::where($this->tableId, $id)->delete();
            Miscellaneous::where($this->tableId, $id)->delete();
            Utilities::where($this->tableId, $id)->delete();
            Vehicles::where($this->tableId, $id)->delete();
            Budgets::find($id)->delete();
            return $this->respondWithOK([]);
        } catch (\Exception $e) {
            Log::error('BudgetController::deleteBudget - ' . $e->getMessage());
            return $this->respondWithBadRequest([], $e->getMessage() . ': Unable to delete budget at this time');
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
     *      @value Datetime ['paid_date'] (optional)
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
     *      @value Datetime ['paid_date']
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
     *      @value Datetime ['paid_date'] (optional)
     *      @value string ['confirmation'] (optional)
     * }
     * @return array {
     *      @value integer ['id']
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['due_date']
     *      @value integer ['medical_type_id']
     *      @value Datetime ['paid_date']
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
     *      @value Datetime ['paid_date'] (optional)
     *      @value string ['confirmation'] (optional)
     * }
     * @return array {
     *      @value integer ['id']
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['due_date']
     *      @value Datetime ['paid_date']
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
     *      @value Datetime ['paid_date'] (optional)
     *      @value string ['confirmation'] (optional)
     * }
     * @return array {
     *      @value integer ['id']
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['due_date']
     *      @value integer ['utility_type_id']
     *      @value Datetime ['paid_date']
     *      @value string ['confirmation']
     * }
     */
    private function save_utilities($id, $expenses)
    {
        $attributes = $this->getUtilitiesAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'utilities');
    }

    /**
     * Saves vehicles; called dynamically from saveBudget()
     *
     * @param integer $id budget id; foreign key
     * @param array $expenses {
     *      @value integer ['id'] (optional)
     *      @value string ['mileage']
     *      @value string ['amount']
     *      @value integer ['due_date']
     *      @value integer ['user_vehicle_id']
     *      @value integer ['vehicle_type_id']
     *      @value Datetime ['paid_date'] (optional)
     *      @value string ['confirmation'] (optional)
     * }
     * @return array {
     *      @value integer ['id']
     *      @value string ['mileage']
     *      @value string ['amount']
     *      @value integer ['due_date']
     *      @value integer ['user_vehicle_id']
     *      @value integer ['vehicle_type_id']
     *      @value Datetime ['paid_date']
     *      @value string ['confirmation']
     * }
     */
    private function save_vehicles($id, $expenses)
    {
        $attributes = $this->getVehiclesAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'vehicles');
    }

    private function setupAndSaveAggregation($budgetId, $allExpenses)
    {
        $earnedTotal = $this->getAggregationTotal($this->earned, $allExpenses);
        $spentTotal = $this->getAggregationTotal($this->spent, $allExpenses);
        $savedTotal = number_format((float)($earnedTotal - $spentTotal), 2, '.', '');

        $this->saveAggregation($budgetId, 'earned', $earnedTotal);
        $this->saveAggregation($budgetId, 'saved', $savedTotal);
        $this->saveAggregation($budgetId, 'spent', $spentTotal);
    }

    private function getAggregationTotal($attributes, $allExpenses)
    {
        $total = 0;

        $keys = array_keys($allExpenses);
        $intersect = array_intersect($attributes, $keys);

        foreach ($intersect as $key => $value) {
            foreach ($allExpenses[$value] as $item) {
                if ($this->canAddAmount($item)) {
                    $total = ((float)$item['amount'] + $total);
                }
            }
        }

        return number_format((float)$total, 2, '.', '');
    }

    /**
     * Checks to see if amount can be add to the aggregate table
     *
     * @param array $item
     * @return boolean
     */
    private function canAddAmount($item)
    {
        $result = false;

        if (!empty($item['amount'])) {
            $result = true;

            if (!empty($item['not_track_amount'])) {
                $result = !$item['not_track_amount'];
            }
        }

        return $result;
    }

    private function saveAggregation($budgetId, $type, $total)
    {
        $budget = BudgetAggregation::where('budget_id', $budgetId)
            ->where('type', $type)
            ->where('user_id', $this->request->auth->id)
            ->first();

        if (empty($budget)) {
            $budget = new BudgetAggregation();
            $budget->type = $type;
            $budget->budget_id = $budgetId;
            $budget->user_id = $this->request->auth->id;
            $budget->created_at = Carbon::now()->format('Y-m-d H:i:s');
        }

        $budget->value = $total;
        $budget->updated_at = Carbon::now()->format('Y-m-d H:i:s');
        $budget->save();
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
                $results = array_merge($results, $this->{$method}($job, $startPay, $currentMonth));
            }
        }

        return $results;
    }

    /**
     * Get weekly pay periods for a billing cycle; called dynamically from generatePaidExpenses()
     *
     * @param array $job {
     *      @value integer | string ['id']
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['job_type_id']
     *      @value Datetime ['initial_pay_date']
     * }
     * @param Carbon $startPay
     * @param Carbon $currentMonth
     * @return array {
     *      @value integer | string ['id']
     *      @value string ['name']
     *      @value string ['amount']
     *      @value integer ['job_type_id']
     *      @value Datetime ['initial_pay_date']
     * }
     */
    private function get_weekly($job, $startPay, $currentMonth)
    {
        $results = [];

        $day = $startPay->format('D');
        $month = $currentMonth->format('M');
        $initialDate = $startPay;

        for ($i = 1; $i < 8; $i++) {
            $date = Carbon::createFromTimeString($currentMonth->format('Y-m') . '-0' . $i .' 00:00:00');

            if ($date->format('D') === $day) {
                $initialDate = $date;
            }
        }

        $currentMonthWeek = $currentMonth->weekOfYear;

        if ($currentMonth->format('M') === 'Dec') {
            $nextMonthWeek = 52;
        } else {
            $nextMonthWeek = $currentMonth->addMonth()->weekOfYear;
        }

        for ($i = 0; $i <= ($nextMonthWeek-$currentMonthWeek); $i++) {
            if ($initialDate->format('M') === $month) {
                $results[] = [
                    'id' => $job['id'],
                    'name' => $job['name'],
                    'amount' => $job['amount'],
                    'job_type_id' => $job['job_type_id'],
                    'initial_pay_date' => $initialDate->toDateTimeString(),
                ];
            }

            $initialDate->addDays(7);
        }

        return $results;
    }

    /**
     * Get bi-weekly pay periods for a billing cycle; called dynamically from generatePaidExpenses()
     *
     * @param array $job {
     *      @value string ['id']; a temp id is expected
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

        $nextMonth = Carbon::createFromTimeString($this->request->input('cycle'))->addMonth();
        $endWeek = $nextMonth->weekOfYear;
        $startWeek = $currentMonth->weekOfYear;
        $payWeek = clone $currentMonth;

        $addDays = ($startPay->dayOfWeek - $payWeek->dayOfWeek);

        if ($startPay->dayOfWeek < $payWeek->dayOfWeek) {
            $addDays = (7 - $payWeek->dayOfWeek) + $startPay->dayOfWeek;
        }

        $payWeek->addDays($addDays);
        $totalWeeks = ($payWeek->weekOfYear - $startPay->weekOfYear);

        if ($totalWeeks % 2) {
            $payWeek->addDays(7);
        }

        if ($currentMonth->format('M') === 'Dec') {
            $endWeek = 52;
        }

        for ($i = 0; $i <= ($endWeek - $startWeek); $i = ($i+2)) {
            if ($currentMonth->format('M') === $payWeek->format('M')) {
                $results[] = [
                    'id' => $job['id'],
                    'name' => $job['name'],
                    'amount' => $job['amount'],
                    'job_type_id' => $job['job_type_id'],
                    'initial_pay_date' => $payWeek->toDateTimeString(),
                    'int' => $i,
                ];
                $payWeek->addDays(14);
            }
        }

        return $results;
    }

    /**
     * Get semi-monthly pay periods for a billing cycle; called dynamically from generatePaidExpenses()
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
    private function get_semi_monthly($job, $startPay, $currentMonth)
    {
        $results = [];
        $dates = [
            $currentMonth->format('Y-m') . '-15 00:00:00',
            $currentMonth->endOfMonth()->toDateTimeString(),
        ];

        foreach ($dates as $date) {
            $job['initial_pay_date'] = $date;
            $results[] = $job;
        }

        return $results;
    }

    /**
     * Get monthly pay periods for a billing cycle; called dynamically from generatePaidExpenses()
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
    private function get_monthly($job, $startPay, $currentMonth)
    {
        $newPayPeriod = $startPay->addMonth();
        $date = $newPayPeriod;

        if ($newPayPeriod->format('Y-m') !== $currentMonth->format('Y-m')) {
            $day = $startPay->format('d');
            $currentCycle = $currentMonth->format('Y-m');
            $date = Carbon::createFromTimeString($currentCycle . '-' . $day . ' 00:00:00');
        }

        return [
            'id' => $job['id'],
            'name' => $job['name'],
            'amount' => $job['amount'],
            'job_type_id' => $job['job_type_id'],
            'initial_pay_date' => $date->toDateTimeString(),
        ];
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