<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desk;
use App\Models\DeskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Desk::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:desks,name',
            'user_id' => 'numeric',
        ], [
            'name.unique' => 'The name has already been taken.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'user_id.numeric' => 'The user_id must be a number.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $created_desk = Desk::create($request->all());
        return response()->json(["message" => "Desk created!", "data" => $created_desk], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Desk::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $desk = Desk::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:desks,name',
            'user_id' => 'numeric',
        ], [
            'name.unique' => 'The name has already been taken.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'user_id.numeric' => 'The user_id must be a number.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $desk->update($request->all());

        return response()->json([
            "message" => "Desk updated!",
            "data" => $desk
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $desk = Desk::find($id);

        if (!$desk) {
            return response()->json(['message' => 'Desk not found'], 404);
        }

        $desk->delete();

        return response()->json(['message' => 'Desk deleted']);
    }
}
