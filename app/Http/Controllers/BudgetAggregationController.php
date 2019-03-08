<?php

namespace App\Http\Controllers;

use App\Models\Budgets;
use Carbon\Carbon;

class BudgetAggregationController extends Controller
{
    public function getYearlyAggregation()
    {
        try {
            $data = Budgets::where('user_id', $this->request->auth)
                ->with('aggregations')
                ->orderBy('budget_cycle', 'desc')
                ->get()
                ->toArray();

            return $this->respondWithOK(['budget' => $this->sortAggregateData($data)]);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve aggregation at this time');
        }
    }

    private function sortAggregateData($data)
    {
        return [
            '2019' => [
                'earned' => ['123.45', '653.12', '1023.78', '951.11', '681.55', '1325.43', '342.67', '452.11', '1200.12', '652.37', '6842.24', '645.00'],
                'saved' => ['523.97', '2360.10', '1440.29',  '1000.12', '687.27', '433.74', '774.14', '530.32', '648.41', '982.11', '945.23', '1842.24'],
                'spent' => ['1000.12', '998.87', '832.42', '986.33', '654.23', '753.10', '842.24', '852.41', '633.22', '751.01', '457.57', '224.84'],
            ],
            '2018' => [
                'earned' => ['0', '0', '0', '0', '0', '0', '0', '0', '681.55', '342.67', '12000.12', '6842.24'],
                'saved' => ['0', '0', '0', '0', '0', '0', '0', '0', '523.97', '433.74', '1100.12', '1842.24'],
                'spent' => ['0', '0', '0', '0', '0', '0', '0', '0', '986.33', '224.84', '1000.12', '842.24'],
            ],
        ];
    }
}