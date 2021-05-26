<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function store(Request $request){

        $event = new Event();
        $event->name = $request->name;
        $event->from = $request->from;
        $event->to = $request->to;
        $event->day = implode(",",$request->day);
        $event->save();

        return  Event::orderBy('id', 'desc')->first();

    }

}
