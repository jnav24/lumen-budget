<?php

namespace App\Http\Controllers;

use App\Models\Budgets;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SearchController extends Controller
{
    public function runSearch()
    {
        try {
            $this->validate($this->request, [
                'type' => [
                    'required',
                    'min:3',
                    Rule::in($this->types),
                ],
                'year' => [
                    'required',
                    'numeric',
                    'digits:4',
                    'min:2019',
                    'max:' . (date('Y')+1)
                ],
            ]);

            $data = Budgets::where('user_id', $this->request->auth->id)
                ->where('budget_cycle', 'LIKE', $this->request->input('year') . '%')
                ->with([$this->request->input('type') => function($q) {

                }])
                ->get();

            return $this->respondWithOK([
                'data' => $data,
            ]);
        } catch (ValidationException $e) {
            return $this->respondWithBadRequest($e->errors(), 'Errors validating request.');
        } catch (\Exception $e) {
            Log::error('SearchController::runSearch - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Unable to retrieve search at this time');
        }
    }
}