<?php

namespace App\Http\Controllers;

use App\Models\Budgets;
use App\Models\CreditCards;
use App\Models\Medical;
use App\Models\Miscellaneous;
use App\Models\Utilities;
use Carbon\Carbon;

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
            $returned = [];
            $years = [];

            if (!empty($data)) {
                foreach ($data as $item) {
                    $year = Carbon::createFromTimeString($item['budget_cycle'])->format('Y');

                    if (!in_array($year, $years)) {
                        $years[] = $year;
                        $returned[$year] = $this->sortAggregateData($data);
                    } else {
                        // remove unneeded data from copy of the $data
                    }
                }
            }

            return $this->respondWithOK(['aggregations' => $returned]);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve aggregation at this time');
        }
    }

    public function getSingleYearAggregation($year)
    {
        try {
            $returned = [];
            $data = Budgets::where('user_id', $this->request->auth->id)
                ->where('budget_cycle', 'like', $year . '-%')
                ->with('aggregations')
                ->orderBy('budget_cycle', 'asc')
                ->get()
                ->toArray();

            if (!empty($data)) {
                $returned[$year] = $this->sortAggregateData($data);
            }

            return $this->respondWithOK(['aggregate' => $returned]);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], $e->getMessage() . ': Unable to retrieve aggregation at this time');
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

            if (!empty($budget)) {
                $budget = $budget[0];
                $creditCards = CreditCards::where(function ($query) {
                        $query->whereNull('confirmation')
                            ->orWhere('confirmation', '');
                    })
                    ->where('budget_id', $budget['id'])
                    ->count();
                $utilities = Utilities::where(function ($query) {
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
            }


            return $this->respondWithOK([
                'unpaid' => [
                    'id' => $budget['id'],
                    'totals' => [
                        'credit_cards' => $creditCards,
                        'medical' => $medical,
                        'miscellaneous' => $misc,
                        'utilities' => $utilities,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve count of unpaid bills at this time.');
        }
    }

    private function sortAggregateData($data)
    {
        $returned = [
            'earned' => ['0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'],
            'saved' => ['0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'],
            'spent' => ['0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'],
        ];

        foreach ($data as $item) {
            $cycleMonth = (int)Carbon::createFromTimeString($item['budget_cycle'])->format('n');

            foreach ($item['aggregations'] as $aggregate) {
                $returned[$aggregate['type']][$cycleMonth-1] = $aggregate['value'];
            }
        }

        return $returned;
    }
}