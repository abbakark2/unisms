<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function Dashboard()
    {
        /** @App App\Models\User */
        $user = Auth::user();
        return response()->json([$user], 200);
    }
}
