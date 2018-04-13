<?php

namespace App\Http\Controllers;

use App\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;


/**
 * Class MeetingController
 * @package App\Http\Controllers
 */
class MeetingController extends Controller
{
    /**
     * MeetingController constructor.
     */
    public function __construct()
    {
//        Protect our Route with JWT middleware also we declare which routes we want to protect
        $this->middleware('jwt.auth', ['only' => ['update', 'store', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

//       Get All Meetings
        $meetings = Meeting::all();

        foreach ($meetings as $meeting) {
//            attach this 2 links to our Meeting
            $meeting->view_meeting = [
                'href' => 'v1/meeting/' . $meeting->id,
                'method' => 'GET'
            ];
        }

//        Create response and it has 2 data message and meeting
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
//        Validation
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie',
        ]);

//        Check User if Exists With JWT ParseToken
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User Not Found'], 404);
        }

//        Get Input
        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $user->id;

//        Create New Meeting
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
                'href' => 'v1/meeting/' . $meeting->id,
                'method' => 'GET'
            ];

            $message = [
                'msg' => 'Meeting Created',
                'meeting' => $meeting
            ];
//        now as we said we can use status code and here we use 201 (created) Every thing is successful and resource is created
            return response()->json($message, 201);
        }

//        Response Message
        $response = [
            'msg' => 'Error during creation'
        ];
//        Now we use response method which is like simple return things but with this method we able to pass HTTP status code
//        Now we also use json method with response its automatically set Content-Type Header to: application/json as wel as
//        convert array to JSON using json_encode

//        now as we said we can use status code and here we use 404 Failed
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
//       Find Meeting With Users it has
        $meeting = Meeting::with('users')->findOrFail($id);

//        we also pass extra data which is associative array and it has 2 data
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
//        Validation
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie',
        ]);

        //        Check User
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User Not Found'], 404);
        }

        //        Get Input
        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $user->id;

//        Get Meeting With Users
        $meeting = Meeting::with('users')->findOrFail($id);

//        Check if User Registered For Meeting
        if (!$meeting->users()->where('users.id', $user_id)->first()) {
            return response()->json(['msg' => 'User Not Register for Meeting, Update Not Successful'], 401);
        };

//        Pass New Data
        $meeting->time = Carbon::createFromFormat('YmdHie', $time);
        $meeting->title = $title;
        $meeting->description = $description;

//        Check If Not Update
        if (!$meeting->update()) {
            return response()->json(['msg' => 'Error during updating'], 404);
        }

        $meeting->view_meeting = [
            'href' => 'v1/meeting/' . $meeting->id,
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        //        Check User
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User Not Found'], 404);
        }
//        Check if User Register For Meeting
        if (!$meeting->users()->where('users.id', $user->id)->first()) {
            return response()->json(['msg' => 'User Not Register for Meeting, Delete Not Successful'], 401);
        };
//        Get Signin User of Meeting
        $users = $meeting->users;
//      Detach it From Meeting
        $meeting->users()->detach();
//        Check if Meeting Not Deleted
        if (!$meeting->delete()) {
//            We attach User Again
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
