<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        return "It Work";
    }

    /**
     * @param Request $request
     */
    public function signin(Request $request)
    {
        return "It Work";
    }
}
