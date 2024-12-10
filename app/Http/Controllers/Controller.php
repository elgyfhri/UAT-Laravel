<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
    public function someApiMethod()
{
    return response()
        ->json(['data' => 'value'])
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
}

}
