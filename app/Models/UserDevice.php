<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserDevice extends Model
{
    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'user_devices';

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public static function getRequestedDevice(Request $request, $id)
    {
        return (new static())
            ->where('user_id', $id)
            ->where('ip', $request->ip())
            ->where('agent', $request->header('User-Agent'))
            ->first();
    }
}