<?php

namespace App\Http\Controllers;

use App\Models\Desk;

class Test extends Controller
{
    public function get(){
        return response()->json([Desk::all()]);
    }
}
