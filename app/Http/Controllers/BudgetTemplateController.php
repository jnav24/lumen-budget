<?php

namespace App\Http\Controllers;

use App\Models\BillTypes;
use App\Models\BudgetTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            $sql = BudgetTemplate::where('user_id', $this->request->auth->id);
            ['data' => $data, 'expenses' => $expenses] = $this->getAllRelationships($sql);

            $budgets = [
                'template' => [
                    'id' => $data->id,
                    'expenses' => $expenses,
                ],
            ];

            return $this->respondWithOK($budgets);
        } catch (ModelNotFoundException $e) {
            return $this->respondWithBadRequest([], 'User does not have any budget templates');
        } catch (\Exception $e) {
            Log::error('BudgetTemplate::getAllBudgetTemplates - ' . $e->getMessage());
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
