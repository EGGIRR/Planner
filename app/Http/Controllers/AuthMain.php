<?php

namespace App\Http\Controllers;

use App\Models\Desk;
use App\Models\Task;
use App\Models\Card;
use App\Models\DeskList;
use Illuminate\Http\Request;

class AuthMain extends Controller
{
    public function getDesks(){
        return response()->json([Desk::all()]);
    }
    public function getOneDesk($id){
        return response()->json([Desk::all()]);
    }
    public function getTasks(){
        return response()->json([Task::all()]);
    }
    public function getCards(){
        return response()->json([Card::all()]);
    }
    public function getDeskLists(){
        return response()->json([DeskList::all()]);
    }

}
