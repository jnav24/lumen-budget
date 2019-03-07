<?php

namespace App\Http\Controllers;

use App\Models\Budgets;
use Carbon\Carbon;

class BudgetAggregationController extends Controller
{
    public function getYearlyAggregation($year)
    {
        try {
            $data = Budgets::where('user_id', $this->request->auth)
//                ->where('budget_cycle', Carbon::now()->format('Y') . '-%')
                ->with('aggregations')
                ->orderBy('budget_cycle', 'desc')
                ->get()
                ->toArray();

            return $this->respondWithOK($this->sortAggregateData($data));
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve aggregation at this time');
        }
    }

    private function sortAggregateData($data)
    {
        return [
            '2019' => [
                'earned' => ['0', '0', '0', '0', '0', '0', '0', '0', '681.55', '342.67', '12000.12', '6842.24'],
                'saved' => ['0', '0', '0', '0', '0', '0', '0', '0', '523.97', '433.74', '10000.12', '16842.24'],
                'spent' => ['0', '0', '0', '0', '0', '0', '0', '0', '986.33', '224.84', '1000.12', '842.24'],
            ]
        ];
    }
}