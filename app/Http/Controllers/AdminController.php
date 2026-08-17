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
    public function securityHub(Request $request)
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

        $auditModules = [
            'Account' => 'Account',
            'Authentication' => 'Authentication',
            'Sales' => 'Sales',
            'Inventory' => 'Inventory',
            'DSS Insights' => 'DSS Insights',
            'Reports' => 'Reports',
            'Settings' => 'Settings',
            'legacy' => 'General / Legacy',
        ];

        $auditFilters = [
            'search' => trim((string) $request->query('search', '')),
            'role' => in_array($request->query('role'), ['admin', 'staff', 'system'], true)
                ? $request->query('role')
                : '',
            'module' => array_key_exists((string) $request->query('module'), $auditModules)
                ? (string) $request->query('module')
                : '',
            'period' => in_array($request->query('period'), ['today', '7_days', '30_days'], true)
                ? $request->query('period')
                : '',
        ];

        $activityQuery = Activity::with('causer');

        if ($auditFilters['search'] !== '') {
            $search = $auditFilters['search'];
            $activityQuery->where(function ($query) use ($search) {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhere('properties->actor_name', 'like', "%{$search}%")
                    ->orWhere('properties->module', 'like', "%{$search}%")
                    ->orWhereHasMorph('causer', [User::class], function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($auditFilters['role'] !== '') {
            $role = $auditFilters['role'];
            $activityQuery->where(function ($query) use ($role) {
                $query->where('properties->actor_role', $role);

                if ($role === 'system') {
                    $query->orWhereNull('causer_id');
                } else {
                    $query->orWhereHasMorph('causer', [User::class], function ($userQuery) use ($role) {
                        $userQuery->whereRaw('LOWER(TRIM(role)) = ?', [$role]);
                    });
                }
            });
        }

        if ($auditFilters['module'] !== '') {
            if ($auditFilters['module'] === 'legacy') {
                $activityQuery->whereNull('properties->module');
            } else {
                $activityQuery->where('properties->module', $auditFilters['module']);
            }
        }

        if ($auditFilters['period'] !== '') {
            $from = match ($auditFilters['period']) {
                'today' => now()->startOfDay(),
                '7_days' => now()->subDays(7),
                '30_days' => now()->subDays(30),
            };
            $activityQuery->where('created_at', '>=', $from);
        }

        // 5. Fetch the latest 50 activities that match the selected filters.
        $systemLogs = $activityQuery
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        $hasAuditFilters = collect($auditFilters)->contains(fn ($value) => $value !== '');

        return view('admin-security', compact(
            'pendingUsers',
            'approvedStaff',
            'revokedUsers',
            'systemLogs',
            'auditFilters',
            'auditModules',
            'hasAuditFilters'
        ));
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
