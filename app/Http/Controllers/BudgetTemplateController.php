<?php

namespace App\Http\Controllers;

use App\Models\BankTemplates;
use App\Models\BudgetTemplates;
use App\Models\CreditCardTemplates;
use App\Models\InvestmentTemplates;
use App\Models\IncomeTemplate;
use App\Models\MedicalTemplates;
use App\Models\MiscellaneousTemplates;
use App\Models\UtilityTemplates;
use App\Models\VehicleTemplates;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class BudgetTemplateController extends Controller
{
    protected $tableId = 'budget_template_id';

    public function deleteBudgetTemplate()
    {
        try {
            $this->validate($this->request, [
                'id' => 'required',
                'type' => 'required',
            ]);

            $method = 'delete_' . $this->request->input('type') . '_templates';

            if (method_exists($this, $method)) {
                $this->{$method}($this->request->input('id'));
                return $this->respondWithOK([]);
            }

            return $this->respondWithBadRequest([], 'Something with wrong. Please try again later.');
        } catch (ValidationException $ex) {
            return $this->respondWithBadRequest($ex->errors(), 'Errors validating request.');
        } catch (\Exception $e) {
            return $this->respondWithBadRequest([], 'Unable to delete budget template at this time.');
        }
    }

    public function getAllBudgetTemplates()
    {
        try {
            $data = BudgetTemplates::where('user_id', $this->request->auth->id)
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

            $template = BudgetTemplates::where('user_id', $this->request->auth->id)->first();

            if (empty($template)) {
                $template = new BudgetTemplates();
                $template->created_at = Carbon::now();
            }

            $template->user_id = $this->request->auth->id;
            $template->updated_at = Carbon::now();
            $template->save();
            $savedData = [];

            foreach ($this->request->input('expenses') as $type => $expenses) {
                $method = $type . '_templates';

                if (method_exists($this, $method)) {
                    $savedData[$type] = $this->{$method}($expenses, $template->id);
                }
            }

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

    /**
     * Dynamic method called from deleteBudgetTemplate()
     *
     * @param int $id
     * @throws \Exception
     */
    private function delete_banks_templates(int $id)
    {
        if (empty(BankTemplates::find($id)->delete())) {
            throw new \Exception();
        }
    }

    /**
     * Dynamic method called from deleteBudgetTemplate()
     *
     * @param int $id
     * @throws \Exception
     */
    private function delete_credit_cards_templates(int $id)
    {
        if (empty(CreditCardTemplates::find($id)->delete())) {
            throw new \Exception();
        }
    }

    /**
     * Dynamic method called from deleteBudgetTemplate()
     *
     * @param int $id
     * @throws \Exception
     */
    private function delete_investments_templates(int $id)
    {
        if (empty(InvestmentTemplates::find($id)->delete())) {
            throw new \Exception();
        }
    }

    /**
     * Dynamic method called from deleteBudgetTemplate()
     *
     * @param int $id
     * @throws \Exception
     */
    private function delete_jobs_templates(int $id)
    {
        if (empty(IncomeTemplate::find($id)->delete())) {
            throw new \Exception();
        }
    }

    /**
     * Dynamic method called from deleteBudgetTemplate()
     *
     * @param int $id
     * @throws \Exception
     */
    private function delete_medical_templates(int $id)
    {
        if (empty(MedicalTemplates::find($id)->delete())) {
            throw new \Exception();
        }
    }

    /**
     * Dynamic method called from deleteBudgetTemplate()
     *
     * @param int $id
     * @throws \Exception
     */
    private function delete_miscellaneous_templates(int $id)
    {
        if (empty(MiscellaneousTemplates::find($id)->delete())) {
            throw new \Exception();
        }
    }

    /**
     * Dynamic method called from deleteBudgetTemplate()
     *
     * @param int $id
     * @throws \Exception
     */
    private function delete_utilities_templates(int $id)
    {
        if (empty(UtilityTemplates::find($id)->delete())) {
            throw new \Exception();
        }
    }

    /**
     * Dynamic method called from deleteBudgetTemplate()
     *
     * @param int $id
     * @throws \Exception
     */
    private function delete_vehicles_templates(int $id)
    {
        if (empty(VehicleTemplates::find($id)->delete())) {
            throw new \Exception();
        }
    }

    /**
     * Dynamic method called from saveBudgetTemplates()
     *
     * @param $expenses
     * @param $id
     * @return array
     */
    private function banks_templates($expenses, $id)
    {
        $attributes = $this->getBanksAttributes();
        if (($key = array_search('bank_template_id', $attributes)) !== false) {
            unset($attributes[$key]);
        }
        return $this->insertOrUpdate($attributes, $expenses, $id, 'bank_templates');
    }

    /**
     * Dynamic method called from saveBudgetTemplates()
     *
     * @param $expenses
     * @param $id
     * @return array
     */
    private function credit_cards_templates($expenses, $id)
    {
        $attributes = $this->getCreditCardsAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'credit_card_templates');
    }

    /**
     * Dynamic method called from saveBudgetTemplates()
     *
     * @param $expenses
     * @param $id
     * @return array
     */
    private function investments_templates($expenses, $id)
    {
        $attributes = $this->getInvestmentAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'investment_templates');
    }

    /**
     * Dynamic method called from saveBudgetTemplates()
     *
     * @param $expenses
     * @param $id
     * @return array
     */
    private function jobs_templates($expenses, $id)
    {
        $attributes = $this->getJobsAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'job_templates');
    }

    /**
     * Dynamic method called from saveBudgetTemplates()
     *
     * @param $expenses
     * @param $id
     * @return array
     */
    private function medical_templates($expenses, $id)
    {
        $attributes = $this->getMedicalAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'medical_templates');
    }

    /**
     * Dynamic method called from saveBudgetTemplates()
     *
     * @param $expenses
     * @param $id
     * @return array
     */
    private function miscellaneous_templates($expenses, $id)
    {
        $attributes = $this->getMiscellaneousAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'miscellaneous_templates');
    }

    /**
     * Dynamic method called from saveBudgetTemplates()
     *
     * @param $expenses
     * @param $id
     * @return array
     */
    private function utilities_templates($expenses, $id)
    {
        $attributes = $this->getUtilitiesAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'utility_templates');
    }

    /**
     * Dynamic method called from saveBudgetTemplates()
     *
     * @param $expenses
     * @param $id
     * @return array
     */
    private function vehicles_templates($expenses, $id)
    {
        $attributes = $this->getVehiclesAttributes();
        return $this->insertOrUpdate($attributes, $expenses, $id, 'vehicle_templates');
    }
}
