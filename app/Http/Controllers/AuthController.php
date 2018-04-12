<?php

namespace App\Http\Controllers;

use App\User;
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
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
        ]);
//        Check User with require parameters for sigin
        if ($user->save()) {
            $user->signin = [
                'href' => 'v1/user/signin',
                'method' => 'POST',
                'params' => 'email,password'
            ];

            //        Our response
            $response = [
                'msg' => 'User Created',
                'user' => $user
            ];

            return response()->json($response, 201);
        }

        $response = [
            'msg' => 'An error occurred',
            'user' => $user
        ];

        return response()->json($response, 404);
    }

    /**
     * @param Request $request
     */
    public function signin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = [
            'name' => 'Name',
            'email' => $email,
            'password' => $password
        ];

        $response = [
            'msg' => 'User Signed in',
            'user' => $user
        ];
        return response()->json($response, 200);
    }
}
