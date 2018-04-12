<?php

namespace App\Http\Controllers;

use App\Meeting;
use App\User;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'meeting_id' => 'required',
            'user_id' => 'required',
        ]);

        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');

        $meeting = Meeting::findOrFail($meeting_id);
        $user = User::findOrFail($user_id);

        $message = [
            'msg' => 'Usre is already Register',
            'user' => $user,
            'meeting' => $meeting,
            'unregistered' => [
                'href' => 'v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE'
            ]
        ];

        if ($meeting->users()->where('users.id', $user->id)->first()) {
            return response()->json($message, 404);
        }

        $user->meetings()->attach($meeting);

//        Create another object for response and it has 2 data message and meeting
        $response = [
            'msg' => 'User Register for Meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' => [
                'href' => 'v1/meeting/registration/1' . $meeting->id,
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
        $meeting = Meeting::findOrFail($id);
        $meeting->users()->detach();

//        Create another object for response and it has 2 data message and meeting
        $response = [
            'msg' => 'User Unregistered for Meeting',
            'meeting' => $meeting,
            'user' => 'tbd',
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
