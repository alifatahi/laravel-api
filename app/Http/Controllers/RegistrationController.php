<?php

namespace App\Http\Controllers;

use App\Meeting;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class RegistrationController
 * @package App\Http\Controllers
 */
class RegistrationController extends Controller
{
    /**
     * RegistrationController constructor.
     */
    public function __construct()
    {
//        Protect our Route with JWT middleware
        $this->middleware('jwt.auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        Validation
        $this->validate($request, [
            'meeting_id' => 'required',
            'user_id' => 'required',
        ]);

//        Get Input
        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');

//        Find Meeting and User
        $meeting = Meeting::findOrFail($meeting_id);
        $user = User::findOrFail($user_id);

        // Fail Response
        $message = [
            'msg' => 'User is already Register',
            'user' => $user,
            'meeting' => $meeting,
            'unregistered' => [
                'href' => 'v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE'
            ]
        ];

//        Check if User is already Register for meeting
        if ($meeting->users()->where('users.id', $user->id)->first()) {
            return response()->json($message, 404);
        }

//        Signup User For Meeting
        $user->meetings()->attach($meeting);

//        Success Response
        $response = [
            'msg' => 'User Register for Meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' => [
                'href' => 'v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE'
            ]
        ];
//        Now we use response method which is like simple return things but with this method we able to pass HTTP status code
//        Now we also use json method with response its automatically set Content-Type Header to: application/json as wel as
//        convert array to JSON using json_encode

//        now as we said we can use status code and here we use 200 (ok) Every thing is successful
        return response()->json($response, 201);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        Find Meeting
        $meeting = Meeting::findOrFail($id);

//        Check if User Exists and Signin
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User Not Found'], 404);
        }
//        Check if User Register for Meeting
        if (!$meeting->users()->where('users.id', $user->id)->first()) {
            return response()->json(['msg' => 'User Not Register for Meeting, Delete Not Successful'], 401);
        };

//        Unregister User From Meeting
        $meeting->users()->detach($user->id);

//        Create another object for response and it has 2 data message and meeting
        $response = [
            'msg' => 'User Unregistered From Meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' => [
                'href' => 'v1/meeting/registration',
                'method' => 'POST',
                'params' => 'user_id, meeting_id'
            ]
        ];
//        Now we use response method which is like simple return things but with this method we able to pass HTTP status code
//        Now we also use json method with response its automatically set Content-Type Header to: application/json as wel as
//        convert array to JSON using json_encode

//        now as we said we can use status code and here we use 200 (ok) Every thing is successful
        return response()->json($response, 200);

    }
}
