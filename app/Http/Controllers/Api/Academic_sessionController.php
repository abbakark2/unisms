<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Academic_session;
use Illuminate\Http\Request;

class Academic_sessionController extends Controller
{
    public function Index(){
        $academic_sessions = Academic_session::get();

        if(!$academic_sessions) return response()->json(["status"=>"failed"]);

        $res = $academic_sessions->map(function($academicSession){

        return["id"=>$academicSession->id,
            "name"=>$academicSession->name,
            "is_active"=>$academicSession->is_active];
        });

        return response()->json(["status"=>"success","data"=>$res]);
    }

}
