<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Models\BillTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use APIResponse;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new controller instance.
     *
     * @param Request $request
     *
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Checks if id is temp id or not
     *
     * @param $id
     * @return bool
     */
    protected function isNotTempId($id)
    {
        return (stripos($id, 'temp_') === false);
    }

    protected function convertSlugToSnakeCase(string $string): string
    {
        return str_replace('-', '_', $string);
    }

    /**
     * Save all expenses; works on template expenses as well
     *
     * @param $budgetId
     * @param array $expenses {
     *      @value array {
     *          @value string ['name']
     *          @value string ['amount']
     *          @value integer ['income_type_id']
     *          @value Datetime ['initial_pay_date']
     *      }
     * }
     * @param bool $isTemplate
     * @return array $expenses
     * @var $expenses[string]array
     * @throws \Exception
     */
    protected function saveExpenses($budgetId, $expenses, $isTemplate = false)
    {
        try {
            DB::beginTransaction();

            $types = BillTypes::all();
            $slugs = $types->pluck('slug');

            $returnExpenses = [];

            foreach ($expenses as $key => $expenseList) {
                $index = $slugs->search($key);
                $model = 'App\\Models\\' . $types[$index]->model . (!$isTemplate ? null : 'Template');
                $id = !$isTemplate ? 'budget_id' : 'budget_template_id';

                if (class_exists($model)) {
                    $class = new $model();

                    $returnExpenses[$key] = array_map(
                        function ($expense) use ($model, $class, $budgetId, $id, $isTemplate) {
                            $expenseId = $this->isNotTempId($expense['id']) ? $expense['id'] : null;
                            $notTrack = !empty($expense['not_track_amount']) ? (int)$expense['not_track_amount'] : 0;

                            if ($class->getTable() === 'banks' &&  empty($expense['bank_template_id'])) {
                                $expense['bank_template_id'] = 0;
                            }

                            return $model::updateOrCreate(
                                ['id' => $expenseId],
                                array_merge(
                                    array_intersect_key($expense, $class->getAttributes()),
                                    [$id => $budgetId],
                                    (!$isTemplate ? ['not_track_amount' => $notTrack] : [])
                                )
                            );
                        },
                        $expenseList
                    );
                }
            }

            DB::commit();
            return $returnExpenses;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    protected function getAllRelationships($sql)
    {
        $expenses = [];
        $types = BillTypes::all();
        $slugs = $types->pluck('slug');

        foreach ($slugs as $slug) {
            $sql->with($this->convertSlugToSnakeCase($slug));
        }

        $data = $sql->firstOrFail();

        foreach ($slugs as $slug) {
            $snakeSlug = $this->convertSlugToSnakeCase($slug);
            $expenses[$slug] = $data->{$snakeSlug}->toArray();
        }

        return [
            'data' => $data,
            'expenses' => $expenses,
        ];
    }
}
