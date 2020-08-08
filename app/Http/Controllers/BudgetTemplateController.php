<?php

namespace App\Http\Controllers;

use App\Models\BillTypes;
use App\Models\BudgetTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BudgetTemplateController extends Controller
{
    protected $tableId = 'budget_template_id';

    public function deleteBudgetTemplate()
    {
        try {
            $this->validate($this->request, [
                'id' => 'required|numeric',
                'type' => 'required|min:3',
            ]);

            $type = BillTypes::where('slug', $this->request->input('type'))->firstOrFail();
            $model = 'App\\Models\\' . $type->model;

            if (class_exists($model)) {
                $model::find($this->request->input('id'))->delete();
                return $this->respondWithOK([]);
            }

            return $this->respondWithBadRequest([], 'Something with wrong. Please try again later.');
        } catch (ValidationException $ex) {
            return $this->respondWithBadRequest($ex->errors(), 'Errors validating request.');
        } catch (\Exception $e) {
            Log::error('BudgetTemplateController::deleteBudgetTemplate - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Unable to delete budget template at this time.');
        }
    }

    public function getAllBudgetTemplates()
    {
        try {
            $data = BudgetTemplate::where('user_id', $this->request->auth->id)
                ->with('banks')
                ->with('credit_cards')
                ->with('investments')
                ->with('jobs')
                ->with('medical')
                ->with('miscellaneous')
                ->with('utilities')
                ->with('vehicles')
                ->first()
                ->toArray();

            if (empty($data)) {
                return $this->respondWithBadRequest([], 'User does not have any budget templates');
            }

            $budgets = [
                'template' => [
                    'id' => $data['id'],
                    'expenses' => [
                        'banks' => $data['banks'],
                        'credit_cards' => $data['credit_cards'],
                        'investments' => $data['investments'],
                        'jobs' => $data['jobs'],
                        'medical' => $data['medical'],
                        'miscellaneous' => $data['miscellaneous'],
                        'utilities' => $data['utilities'],
                        'vehicles' => $data['vehicles'],
                    ]
                ],
            ];

            return $this->respondWithOK($budgets);
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to retrieve budgets at this time.');
        }
    }

    public function saveBudgetTemplates()
    {
        try {
            if (empty($this->request->input('expenses'))) {
                return $this->respondWithBadRequest([], 'Missing expenses.');
            }

            $template = BudgetTemplate::firstOrCreate([
                'user_id' => $this->request->auth->id,
            ]);

            $savedData = $this->saveExpenses($template->id, $this->request->input('expenses'), true);

            return $this->respondWithOK([
                'templates' => [
                    'id' => $template->id,
                    'expenses' => $savedData,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('BudgetTemplateController::saveBudgetTemplates - ' . $e->getMessage());
            return $this->respondWithBadRequest([], 'Unable to save budget template at this time.');
        }
    }
}
