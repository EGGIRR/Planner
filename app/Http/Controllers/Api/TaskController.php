<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function complete(string $id)
    {
        $mytasks = DB::table('tasks')
            ->join('desks', 'tasks.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mytasks)) {
            return response()->json(["message" => "Task not found in your tasks!"], 404);
        }

        $task = Task::find($id);

        if (!$task) {
            return response()->json(["message" => "Task not found!"], 404);
        }

        if ($task->complete) {
            return response()->json(["message" => "Task already completed!"]);
        }

        $task->completed = true;
        $task->expired = $task->date < Carbon::now();
        $task->save();

        return response()->json(["message" => "Task completed!", "data" => $task]);
    }

    public function index()
    {
        $tasks = DB::table('tasks')
            ->join('desks', 'tasks.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->get();
        return response()->json(['data' => ['tasks' => $tasks]]);
    }
    public function image(string $id)
    {
        $mytasks = DB::table('tasks')
            ->join('desks', 'tasks.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mytasks)) {
            return response()->json(["message" => "Task not found in your tasks!"], 404);
        }
        $task = Task::find($id);

        $img = $task->img;
        $filePath = storage_path('app/public/'. $img);
        return response()->file($filePath);
    }

    public function uncompletedTasks()
    {
        $task = DB::table('tasks')
            ->join('desks', 'tasks.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->where('tasks.complete', false)
            ->get();
        return response()->json(['data' => ['tasks' => $task]]);

    }
    public function completedTasks()
    {
        $task = DB::table('tasks')
            ->join('desks', 'tasks.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->where('tasks.complete', true)
            ->get();
        return response()->json(['data' => ['tasks' => $task]]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'img' => 'image',
            'desk_id' => 'numeric|exists:desks,id',
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'date.required' => 'The date field is required.',
            'date.date' => 'The date must be a date.',
            'desk_id.numeric' => 'The card_id must be a number.',
            'desk_id.exists' => 'The desk_id does not exist or does not belong to the current user.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $cards = DB::table('desks')
            ->select('desks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->where('desks.id', $request->input('desk_id'))
            ->first();

        if (!$cards) {
            return response()->json(["message" => "Desk is not yours!"], 403);
        }
        if ($request->hasFile('img')) {
            $avatar = $request->file('img');
            $path = $avatar->store('img', 'public');
            $taskData = $request->except('img');
            $taskData['img'] = $path;
            $created_task = Task::create($taskData);
        }else{
            $created_task = Task::create($request->all());
        }
        return response()->json(["message" => "Task created!", "data" => $created_task], 201);
    }

    public function update(Request $request, string $id)
    {
        $mytasks = DB::table('tasks')
            ->join('desks', 'tasks.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mytasks)) {
            return response()->json(["message" => "Task not found in your tasks!"], 404);
        }
        $task = Task::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'img' => 'image',
            'date' => 'date',
            'desk_id' => 'numeric|exists:desks,id',
        ], [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'desk_id.numeric' => 'The desk_id must be a number.',
            'desk_id.exists' => 'The desk_id does not exist.'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $task->update($request->all());

        return response()->json(["message" => "Task updated!", "data" => $task]);
    }
    public function updateImg(Request $request,string $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'img.required' => 'The avatar field is required.',
            'img.image' => 'The avatar must be an image.',
            'img.mimes' => 'The avatar must be a file of type: jpeg, png, jpg, gif.',
            'img.max' => 'The avatar may not be greater than 2048 kilobytes.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('img')) {
            $avatar = $request->file('img');
            $path = $avatar->store('img', 'public');
            $task->img = $path;
            $task->save();

            return response()->json([
                "message" => "img updated!",
                "data" => $task
            ]);
        } else {
            return response()->json(['message' => 'img not provided'], 400);
        }
    }
    public function destroy(string $id)
    {
        $mytasks = DB::table('tasks')
            ->join('desks', 'tasks.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mytasks)) {
            return response()->json(["message" => "Task not found in your tasks!"], 404);
        }
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
