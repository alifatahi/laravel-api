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
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        return "It Work";
    }

    /**
     * @param Request $request
     */
    public function signin(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        return "It Work";
    }
}
