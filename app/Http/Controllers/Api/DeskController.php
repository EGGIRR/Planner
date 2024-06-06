<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeskController extends Controller
{
    public function index()
    {
        $desks = Desk::select('desks.id','desks.name')
            ->where('user_id', Auth::id())->get();
        return response()->json(['data' => ['desks' => $desks]]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $created_desk = Desk::create($data);
        return response()->json(["message" => "Desk created!", "data" => $created_desk], 201);
    }

    public function update(Request $request, string $id)
    {
        $mydesks = DB::table('desks')
            ->select('desks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mydesks)) {
            return response()->json(["message" => "Desk not found in your desks!"], 404);
        }
        $desk = Desk::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
        ], [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $desk->update($request->all());

        return response()->json(["message" => "Desk updated!", "data" => $desk]);
    }

    public function destroy(string $id)
    {
        $mydesks = DB::table('desks')
            ->select('desks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mydesks)) {
            return response()->json(["message" => "Desk not found in your desks!"], 404);
        }
        $desk = Desk::find($id);
        if (!$desk) {
            return response()->json(['message' => 'Desk not found'], 404);
        }
        $desk->delete();
        return response()->json(['message' => 'Desk deleted']);
    }
}
