<?php

namespace App\Http\Controllers;

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
        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');


//        Create Object of Meeting that user want to signup
        $meeting = [
            'title' => 'Title',
            'description' => 'Description',
            'time' => 'Time',
//            we also pass extra data which is associative array and it has 2 data
//        href of data that created in API way
//        and method that href should pass
            'view_meeting' => [
                'href' => 'v1/meeting/1',
                'method' => 'GET'
            ]
        ];
//        Because we want to know which user sign uo for which meetings
        $user = [
            'name' => 'Name'
        ];

//        Create another object for response and it has 2 data message and meeting
        $response = [
            'msg' => 'User Register for Meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' => [
                'href' => 'v1/meeting/registration/1',
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
        //        Create Object of Meeting for Unregistered
        $meeting = [
            'title' => 'Title',
            'description' => 'Description',
            'time' => 'Time',
//            we also pass extra data which is associative array and it has 2 data
//        href of data that created in API way
//        and method that href should pass
            'view_meeting' => [
                'href' => 'v1/meeting/1',
                'method' => 'GET'
            ]
        ];

        $user = [
            'name' => 'Name'
        ];

//        Create another object for response and it has 2 data message and meeting
        $response = [
            'msg' => 'User Unregistered for Meeting',
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
