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
     * Foreign key name
     *
     * @var string
     */
    protected $tableId;

    /**
     * Expenses types
     * @deprecated replace this with BillTypes::all()->pluck('slug'); this is only being called once
     * @var array
     */
    protected $types = [
        'banks',
        'credit_cards',
        'investments',
        'jobs',
        'medical',
        'miscellaneous',
        'utilities',
        'vehicles',
    ];

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

    /**
     * Insert or update a table in the DB
     *
     * @deprecated
     * @param array $attributes ; array of column names
     * @param array $data ; multidimensional array of records to be saved
     * @param int $id ; foreign key id
     * @param string $model ; name of table
     * @return array; returns the same as $data but with updated ids where necessary
     */
    protected function insertOrUpdate(array $attributes, array $data, int $id, string $model)
    {
        $result = [];
        $date = [
            $this->tableId => $id,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        foreach ($data as $item) {
            $template = array_intersect_key($item, array_flip($attributes));

            if (!empty($template)) {
                if (!empty($item['id']) && $this->isNotTempId($item['id'])) {
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

    protected function convertSlugToSnakeCase(string $string): string
    {
        return str_replace('-', '_', $string);
    }
}
