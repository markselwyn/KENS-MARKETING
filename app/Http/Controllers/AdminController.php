<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display the Admin Security Hub
     */
    public function securityHub()
    {
        // 1. Bulletproof Security Check: Ignores spaces and capitalization
        $userRole = strtolower(trim(Auth::user()->role));
        
        if ($userRole !== 'admin') {
            abort(403, 'Unauthorized Access. System Admins only.');
        }

        // 2. Fetch all staff members waiting for approval
        $pendingUsers = User::where('is_approved', false)
            ->whereNull('revoked_at')
            ->where('role', 'staff')
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Fetch all active/approved staff members to monitor status
        $approvedStaff = User::where('is_approved', true)
            ->where('role', 'staff')
            ->orderBy('last_seen', 'desc')
            ->get();

        // 4. Fetch staff whose access was explicitly revoked
        $revokedUsers = User::where('is_approved', false)
            ->whereNotNull('revoked_at')
            ->where('role', 'staff')
            ->orderBy('revoked_at', 'desc')
            ->get();

        // 5. Fetch the latest 50 system activities from the Spatie Audit Trail
        $systemLogs = Activity::with('causer') 
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('admin-security', compact('pendingUsers', 'approvedStaff', 'revokedUsers', 'systemLogs'));
    }

    /**
     * Approve a pending staff member
     */
    public function approveStaff($id)
    {
        $userRole = strtolower(trim(Auth::user()->role));
        if ($userRole !== 'admin') abort(403);

        $user = User::where('role', 'staff')
            ->where('is_approved', false)
            ->whereNull('revoked_at')
            ->findOrFail($id);
        $user->is_approved = true;
        $user->revoked_at = null;
        $user->save();

        // Audit Trail
        activity()
            ->causedBy(Auth::user())
            ->log("Admin granted system access to {$user->name} ({$user->email}).");

        return back()->with('success', "Staff account for {$user->name} has been approved.");
    }

    /**
     * Revoke access for an active staff member.
     */
    public function revokeStaff($id)
    {
        $userRole = strtolower(trim(Auth::user()->role));
        if ($userRole !== 'admin') abort(403);

        $user = User::where('role', 'staff')
            ->where('is_approved', true)
            ->findOrFail($id);
        $user->is_approved = false;
        $user->revoked_at = now();
        $user->save();

        // Audit Trail
        activity()
            ->causedBy(Auth::user())
            ->log("Admin revoked system access for {$user->name} ({$user->email}).");

        return back()->with('success', "Access revoked for {$user->name}. User has been moved to Revoked Accounts.");
    }

    /**
     * Restore access for a revoked staff member.
     */
    public function restoreStaff($id)
    {
        $userRole = strtolower(trim(Auth::user()->role));
        if ($userRole !== 'admin') abort(403);

        $user = User::where('role', 'staff')
            ->where('is_approved', false)
            ->whereNotNull('revoked_at')
            ->findOrFail($id);

        $user->is_approved = true;
        $user->revoked_at = null;
        $user->save();

        activity()
            ->causedBy(Auth::user())
            ->log("Admin restored system access for {$user->name} ({$user->email}).");

        return back()->with('success', "Access restored for {$user->name}.");
    }

    /**
     * Permanently delete a revoked staff account.
     */
    public function deleteRevokedStaff($id)
    {
        $userRole = strtolower(trim(Auth::user()->role));
        if ($userRole !== 'admin') abort(403);

        $user = User::where('role', 'staff')
            ->where('is_approved', false)
            ->whereNotNull('revoked_at')
            ->findOrFail($id);
        $name = $user->name;
        $email = $user->email;

        $user->delete();

        activity()
            ->causedBy(Auth::user())
            ->log("Admin permanently deleted revoked account for {$name} ({$email}).");

        return back()->with('success', "Revoked account for {$name} has been permanently deleted.");
    }

    /**
     * Decline and remove a pending staff member
     */
    public function declineStaff($id)
    {
        $userRole = strtolower(trim(Auth::user()->role));
        if ($userRole !== 'admin') abort(403);

        $user = User::findOrFail($id);
        $name = $user->name;
        $email = $user->email;
        
        // Remove the unapproved account completely
        $user->delete();

        // Audit Trail
        activity()
            ->causedBy(Auth::user())
            ->log("Admin declined system access and removed account request for {$name} ({$email}).");

        return back()->with('success', "Account request for {$name} has been declined and removed.");
    }
}
