<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;

class CalendarData extends Controller
{
    public function jsonReturn(){
      $data= DB::table('calendar')->get();
      return response()->json($data);
    }
}
