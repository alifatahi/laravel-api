<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
//        Validation
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);

//        Get Input
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

//        Create New User
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

            //       Success Response
            $response = [
                'msg' => 'User Created',
                'user' => $user
            ];

            return response()->json($response, 201);
        }
//      Fail Response
        $response = [
            'msg' => 'An error occurred'
        ];

        return response()->json($response, 404);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signin(Request $request)
    {
//        Validation
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
//      Get Email and Password
        $credentials = $request->only('email', 'password');

        try {
//          Check If User Pass Correct Data and If Exists
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['msg' => 'Invalid Credentials'], 401);
            }
        } catch (JWTException $e) {
//            Server Error
            return response()->json(['msg' => 'Could Not Create Token'], 500);
        }
//        Success Signin
        return response()->json(['token' => $token]);
    }
}
