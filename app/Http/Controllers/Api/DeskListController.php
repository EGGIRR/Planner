<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeskListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DeskList::all();

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:desk_lists,name',
            'desk_id' => 'numeric',
        ], [
            'name.unique' => 'The name has already been taken.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'desk_id.numeric' => 'The desk_id must be a number.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $created_desk = DeskList::create($request->all());
        return response()->json(["message" => "Desk created!", "data" => $created_desk], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return DeskList::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $deskList = DeskList::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:desk_lists,name',
            'desk_id' => 'numeric',
        ], [
            'name.unique' => 'The name has already been taken.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'desk_id.numeric' => 'The desk_id must be a number.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $deskList->update($request->all());

        return response()->json([
            "message" => "DeskList updated!",
            "data" => $deskList
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $desk_list = DeskList::find($id);

        if (!$desk_list) {
            return response()->json(['message' => 'DeskList not found'], 404);
        }

        $desk_list->delete();

        return response()->json(['message' => 'DeskList deleted']);
    }
}
