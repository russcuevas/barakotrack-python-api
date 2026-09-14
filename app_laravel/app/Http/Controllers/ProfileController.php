<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            $referer = $request->headers->get('referer', '');
            if (str_contains($referer, '/admin')) {
                $user = User::where('role', 'admin')->first() ?: User::first();
            } else {
                $user = User::where('role', 'student')->first() ?: User::first();
            }
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password.min' => 'New password must be at least 6 characters long.',
            'current_password.required' => 'Please enter your current password.',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors([
                'current_password' => 'The provided current password does not match your account password.'
            ])->with('error', 'Incorrect current password provided. Please try again.');
        }

        // Prevent setting the exact same password
        if (Hash::check($request->new_password, $user->password)) {
            return redirect()->back()->withErrors([
                'new_password' => 'Your new password cannot be the same as your current password.'
            ])->with('warning', 'New password cannot be identical to current password.');
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password successfully updated! Your account is now secured with your new password.');
    }
}
