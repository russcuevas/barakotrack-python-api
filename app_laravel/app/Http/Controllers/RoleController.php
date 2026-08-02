<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function switchRole(Request $request)
    {
        $role = $request->input('role', 'student');
        if (!in_array($role, ['student', 'admin'])) {
            $role = 'student';
        }

        session(['active_role' => $role]);

        return redirect()->back()->with('success', "Switched active view to " . strtoupper($role) . " mode.");
    }
}
