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

//        Create object of User with require parameters for sigin
        $user = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'signin' => [
                'href' => 'v1/user/signin',
                'method' => 'POST',
                'params' => 'email,password'
            ],
        ];

//        Our response
        $response = [
            'msg' => 'User Created',
            'user' => $user
        ];

        return response()->json($response, 201);
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
