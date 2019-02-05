<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use Illuminate\Http\Request;
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

    protected function isNotTempId($id): boolean
    {
        return stripos($id, 'temp_') === false;
    }

    protected function insertOrUpdate(array $attributes, array $data, int $id, string $model)
    {
        foreach ($data as $item) {
            if ($item['deletion'] && $this->isNotTempId($item['id'])) {
                DB::table($model)->where('id', $item['id'])->delete();
            } else if (count($attributes) === count($item)) {
                $template = array_intersect_key($item, $attributes);

                $date = [
                    'budget_template_id' => $id,
                    'updated_at' => Carbon::now(),
                ];

                if ($this->isNotTempId($item['id'])) {
                    DB::table($model)->where('id', $item['id'])->update(array_merge($template, $date));
                } else {
                    $date['created_at'] = Carbon::now();
                    DB::table($model)->insert(array_merge($template, $date));
                }
            }
        }
    }
}
