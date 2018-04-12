<?php

namespace App\Http\Controllers;

use App\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function __construct()
    {
//        Middleware
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

//        Create Object of Meeting
        $meetings = Meeting::all();

        foreach ($meetings as $meeting) {
//            attach this 2 links
            $meeting->view_meeting = [
                'href' => 'v1/meeting/1' . $meeting->id,
                'method' => 'GET'
            ];
        }

//        Create another object for response and it has 2 data message and meeting
        $response = [
            'msg' => 'List of all meeting',
            'meeting' => $meetings
        ];
//        Now we use response method which is like simple return things but with this method we able to pass HTTP status code
//        Now we also use json method with response its automatically set Content-Type Header to: application/json as wel as
//        convert array to JSON using json_encode

//        now as we said we can use status code and here we use 200 (ok) Every thing is successful
        return response()->json($response, 200);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie',
            'user_id' => 'required',
        ]);
//        Get Input
        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

//        Create Object of Created Meeting
        $meeting = new Meeting([
            'time' => Carbon::createFromFormat('YmdHie', $time),
            'title' => $title,
            'description' => $description
        ]);

        if ($meeting->save()) {
//            attach userID
            $meeting->users()->attach($user_id);
            //            we also pass extra data which is associative array and it has 2 data
//        href of data that created in API way
//        and method that href should pass
            $meeting->view_meeting = [
                'href' => 'v1/meeting/1' . $meeting->id,
                'method' => 'GET'
            ];

            $message = [
                'msg' => 'Meeting Created',
                'meeting' => $meeting
            ];
            return response()->json($message, 201);
        }

//        Create another object for response and it has 2 data message and meeting
        $response = [
            'msg' => 'Meeting Not Created',
            'meeting' => $meeting
        ];
//        Now we use response method which is like simple return things but with this method we able to pass HTTP status code
//        Now we also use json method with response its automatically set Content-Type Header to: application/json as wel as
//        convert array to JSON using json_encode

//        now as we said we can use status code and here we use 201 (created) Every thing is successful and resource is created
        return response()->json($response, 404);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //        Create Object of Meeting
        $meeting = Meeting::with('users')->where('id', $id)->findOrFail();
        //            we also pass extra data which is associative array and it has 2 data
//        href of data that created in API way
//        and method that href should pass
        $meeting->view_meeting = [
            'href' => 'v1/meeting',
            'method' => 'GET'
        ];


//        Create another object for response and it has 2 data message and meeting
        $response = [
            'msg' => 'Meeting Information',
            'meeting' => $meeting
        ];
//        Now we use response method which is like simple return things but with this method we able to pass HTTP status code
//        Now we also use json method with response its automatically set Content-Type Header to: application/json as wel as
//        convert array to JSON using json_encode

//        now as we said we can use status code and here we use 200 (ok) Every thing is successful
        return response()->json($response, 200);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie',
            'user_id' => 'required',
        ]);

        //        Get Input
        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

//        Create Object of Update Meeting
        $meeting = [
            'title' => $title,
            'description' => $description,
            'time' => $time,
            'user_id' => $user_id,
//            we also pass extra data which is associative array and it has 2 data
//        href of data that created in API way
//        and method that href should pass
            'view_meeting' => [
                'href' => 'v1/meeting/1',
                'method' => 'GET'
            ]
        ];

        $meeting = Meeting::with('users')->findOrFail();

        if (!$meeting->users()->where('users.id', $user_id)->first()) {
            return response()->json(['msg' => 'User Not Register for Meeting, Update Not Successful'], 401);
        };

        $meeting->time = Carbon::createFromFormat('YmdHie', $time);
        $meeting->title = $title;
        $meeting->description = $description;

        if (!$meeting->update()) {
            return response()->json(['msg', 'user not register for meeting update not successful'], 401);
        };

        $meeting->time = Carbon::createFromFormat('YmdHie', $time);
        $meeting->title = $title;
        $meeting->description = $description;

        if (!$meeting->update()) {
            return response()->json(['msg' => 'Error during updating'], 404);
        }

        $meeting->view_meeting = [
            'href' => 'v1/meeting/1' . $meeting->id,
            'method' => 'GET'
        ];

//        Create another object for response and it has 2 data message and meeting
        $response = [
            'msg' => 'Meeting Updated',
            'meeting' => $meeting
        ];
//        Now we use response method which is like simple return things but with this method we able to pass HTTP status code
//        Now we also use json method with response its automatically set Content-Type Header to: application/json as wel as
//        convert array to JSON using json_encode

//        now as we said we can use status code and here we use 200 (ok) Every thing is successful
        return response()->json($response, 200);

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
        $users = $meeting->users;
        $meeting->users()->detach();
        if (!$meeting->delete()) {
            foreach ($users as $user) {
                $meeting->users()->attach($user);
            }
            return response()->json(['msg' => 'Delete Failed'], 404);
        }

//      Response message  also it has create associative array with href , method and parameters
        $response = [
            'msg' => 'Meeting Deleted',
            'create' => [
                'href' => 'v1/meeting',
                'method' => 'POST',
//              List all parameters that this link is need for work with because its not Get its post so we declare it
                'params' => 'title, description, time'
            ]
        ];
//        Now we use response method which is like simple return things but with this method we able to pass HTTP status code
//        Now we also use json method with response its automatically set Content-Type Header to: application/json as wel as
//        convert array to JSON using json_encode

//        now as we said we can use status code and here we use 200 (ok) Every thing is successful
        return response()->json($response, 200);

    }
}
