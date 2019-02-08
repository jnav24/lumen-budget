<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use Carbon\Carbon;
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
     * Budget id name
     *
     * @var string
     */
    protected $tableId;

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

    protected function isNotTempId($id)
    {
        return (stripos($id, 'temp_') === false);
    }

    protected function insertOrUpdate(array $attributes, array $data, int $id, string $model)
    {
        $result = [];

        foreach ($data as $item) {
            if (count($attributes) === count($item)) {
                $template = array_intersect_key($item, array_flip($attributes));

                $date = [
                    $this->tableId => $id,
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ];

                if ($this->isNotTempId($item['id'])) {
                    $savedData = array_merge($template, $date);
                    DB::table($model)->where('id', $item['id'])->update($savedData);
                } else {
                    unset($template['id']);
                    $date['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                    $savedData = array_merge($template, $date);
                    $id = DB::table($model)->insertGetId($savedData);
                    $savedData['id'] = $id;
                }

                unset($savedData[$this->tableId]);
                unset($savedData['created_at']);
                unset($savedData['updated_at']);
                $result[] = $savedData;
            }
        }

        return $result;
    }
}
