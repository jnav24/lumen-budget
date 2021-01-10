<?php

namespace App\Http\Controllers;

use App\Models\Budgets;
use App\Models\CreditCard;
use App\Models\Medical;
use App\Models\Miscellaneous;
use App\Models\Utility;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BudgetAggregationController extends Controller
{
    public function getYearlyAggregation()
    {
        try {
            $data = Budgets::where('user_id', $this->request->auth->id)
                ->with('aggregations')
                ->orderBy('budget_cycle', 'desc')
                ->get()
                ->toArray();
            $aggregateData = [];

            if (!empty($data)) {
                $aggregateData = $this->sortAggregateData($data);
            }

            return $this->respondWithOK([
                'aggregations' => $aggregateData,
            ]);
        } catch (\Exception $e) {
            Log::error('BudgetAggregationController::getYearlyAggregation - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Unable to retrieve aggregation at this time');
        }
    }

    public function getSingleYearAggregation($year)
    {
        try {
            $aggregateData = [];
            $data = Budgets::where('user_id', $this->request->auth->id)
                ->where('budget_cycle', 'like', $year . '-%')
                ->with('aggregations')
                ->orderBy('budget_cycle', 'asc')
                ->get()
                ->toArray();

            if (!empty($data)) {
                $aggregateData = $this->sortAggregateData($data);
            }

            return $this->respondWithOK(['aggregate' => $aggregateData]);
        } catch (\Exception $e) {
            Log::error('BudgetAggregationController::getSingleYearAggregation - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Unable to retrieve aggregation at this time');
        }
    }

    public function getCountOfUnPaidBills()
    {
        try {
            $budget = Budgets::where('user_id', $this->request->auth->id)
                ->orderBy('budget_cycle', 'desc')
                ->take(1)
                ->get(['id'])
                ->toArray();
            $creditCards = 0;
            $utilities = 0;
            $misc = 0;
            $medical = 0;
            $vehicles = 0;

            if (!empty($budget)) {
                $budget = $budget[0];
                $creditCards = CreditCard::where(function ($query) {
                        $query->whereNull('confirmation')
                            ->orWhere('confirmation', '');
                    })
                    ->where('budget_id', $budget['id'])
                    ->count();
                $utilities = Utility::where(function ($query) {
                        $query->whereNull('confirmation')
                            ->orWhere('confirmation', '');
                    })
                    ->where('budget_id', $budget['id'])
                    ->count();
                $misc = Miscellaneous::where(function ($query) {
                        $query->whereNull('confirmation')
                            ->orWhere('confirmation', '');
                    })
                    ->where('budget_id', $budget['id'])
                    ->count();
                $medical = Medical::where(function ($query) {
                        $query->whereNull('confirmation')
                            ->orWhere('confirmation', '');
                    })
                    ->where('budget_id', $budget['id'])
                    ->count();
                $vehicles = Vehicle::with(['type' => function ($relation) {
                        $relation->whereIn('slug', ['finance', 'lease']);
                    }])
                    ->where(function ($query) {
                        $query->whereNull('confirmation')
                            ->orWhere('confirmation', '');
                    })
                    ->where('budget_id', $budget['id'])
                    ->count();
            }


            return $this->respondWithOK([
                'unpaid' => [
                    'id' => $budget['id'],
                    'totals' => [
                        'credit_cards' => $creditCards,
                        'medical' => $medical,
                        'miscellaneous' => $misc,
                        'utilities' => $utilities,
                        'vehicles' => $vehicles,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve count of unpaid bills at this time.');
        }
    }

    /**
     * Sorts data from the budgets table joined with budget aggregation table
     *
     * @param $data
     * @return array
     */
    private function sortAggregateData($data)
    {
        $aggregateData = [];

        foreach ($data as $item) {
            $year = Carbon::createFromTimeString($item['budget_cycle'])->format('Y');
            $cycleMonth = (int)Carbon::createFromTimeString($item['budget_cycle'])->format('n');

            if (empty($aggregateData[$year])) {
                $aggregateData[$year] = [
                    'earned' => [],
                    'saved' => [],
                    'spent' => [],
                ];
            }

            foreach ($item['aggregations'] as $aggregate) {
                $keys = array_keys($aggregateData[$year][$aggregate['type']]);

                if (!empty($keys)) {
                    $end = $keys[count($keys)-1];

                    if (($end - 1) !== $cycleMonth) {
                        for ($i = ($end-1); $i > $cycleMonth-1; $i--) {
                            $aggregateData[$year][$aggregate['type']][$i] = '0.00';
                        }
                    }
                }

                $aggregateData[$year][$aggregate['type']][$cycleMonth-1] = $aggregate['value'];
            }
        }

        foreach ($aggregateData as $year => $data) {
            foreach ($data as $type => $aggregates) {
                $aggregateData[$year][$type] = array_values(array_reverse($aggregates));
            }
        }

        return $aggregateData;
    }
}
