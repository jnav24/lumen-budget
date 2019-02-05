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
}
