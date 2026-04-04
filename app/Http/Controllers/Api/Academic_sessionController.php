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

    public function toggleStatus(Request $request, $id){
        $session = Academic_session::find($id);

        if(!$session){
            return response()->json(["status"=>"failed", "message"=>"Session not found"], 404);
        }

        // If activating this session, deactivate all others
        if(!$session->is_active){
            Academic_session::where('id', '!=', $id)->update(['is_active' => false]);
        }

        $session->is_active = !$session->is_active;
        $session->save();

        // Return all updated sessions
        $allSessions = Academic_session::get()->map(function($s){
            return [
                "id"=>$s->id,
                "name"=>$s->name,
                "is_active"=>$s->is_active
            ];
        });

        return response()->json([
            "status"=>"success",
            "message"=>"Session status updated",
            "data" => $allSessions
        ]);
    }

}
