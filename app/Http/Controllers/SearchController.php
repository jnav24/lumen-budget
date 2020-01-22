<?php

namespace App\Http\Controllers;

use App\Models\Budgets;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SearchController extends Controller
{
    public function runSearch()
    {
        try {
            $validated = $this->validate($this->request, [
                'billType' => [
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
                'type' => [],
                'name' => [
                    'min:3',
                ],
                'notes' => [],
                'startMonth' => [
                    'required',
                    'min:1',
                    'max:12',
                    'numeric',
                ],
                'endMonth' => [
                    'required',
                    'min:1',
                    'max:12',
                    'numeric',
                ],
                'vehicle' => [],
            ]);

            $from = Carbon::create($validated['year'], $validated['startMonth'], 01, 0, 0, 0)->toDateTimeString();
            $to = Carbon::create($validated['year'], $validated['endMonth'], 01, 0, 0, 0)->toDateTimeString();

            $data = Budgets::where('user_id', $this->request->auth->id)
                ->whereBetween('budget_cycle', [$from, $to])
                ->with([$validated['billType'] => function($relation) use ($validated) {
                    $ignoreTypeList = ['miscellaneous'];

                    $relation->when(!empty($validated['name']), function($query) use ($validated) {
                        return $query->where('name', 'LIKE', '%' . $validated['name'] . '%');
                    });

                    $relation->when(
                        !empty($validated['type']) && array_search($validated['billType'], $ignoreTypeList) === false,
                        function($query) use ($validated) {
                            return $query->whereHas('type', function($q) use ($validated) {
                                $q->where('slug', $validated['type']);
                            });
                        });

                    // @todo uncomment when vehicles table gets a notes column
//                    $relation->when(
//                        !empty($validated['notes']) && $validated['billType'] === 'vehicles',
//                        function($query) use ($validated) {
//                            return $query->where('notes', $validated['notes']);
//                        });

                    $relation->when(
                        !empty($validated['vehicle']) && $validated['billType'] === 'vehicles',
                        function($query) use ($validated) {
                            return $query->whereHas('userVehicle', function($q) use ($validated) {
                                $q->where('id', $validated['vehicle']);
                            });
                        });
                }])->get();

            return $this->respondWithOK([
                'data' => $data,
            ]);
        } catch (ValidationException $e) {
            Log::error('SearchController::runSearch - ' . implode(', ', $e->errors()));
            return $this->respondWithBadRequest($e->errors(), 'Errors validating request.');
        } catch (\Exception $e) {
            Log::error('SearchController::runSearch - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Unable to retrieve search at this time');
        }
    }
}