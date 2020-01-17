<?php

namespace App\Http\Controllers;

use App\Models\Budgets;
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
                'vehicle' => [],
            ]);

            Log::debug($validated);

            // @todo add date range to this query

            DB::enableQueryLog();
            $data = Budgets::where('user_id', $this->request->auth->id)
                ->where('budget_cycle', 'LIKE', $validated['year'] . '%')
                ->with([$validated['billType'] => function($relation) use ($validated) {
                    $relation->when(!empty($validated['name']), function($query) use ($validated) {
                        return $query->where('name', 'LIKE', '%' . $validated['name'] . '%');
                    });

                    $relation->when(!empty($validated['type']), function($query) use ($validated) {
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
                            return $query->whereHas('vehicle', function($q) use ($validated) {
                                $q->where('id', $validated['vehicle']);
                            });
                        });
                }]);


            $result = $data->get();
            Log::debug(DB::getQueryLog());
            Log::debug(json_encode($result));

            return $this->respondWithOK([
                'data' => $result,
            ]);
        } catch (ValidationException $e) {
            return $this->respondWithBadRequest($e->errors(), 'Errors validating request.');
        } catch (\Exception $e) {
            Log::error('SearchController::runSearch - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Unable to retrieve search at this time');
        }
    }
}