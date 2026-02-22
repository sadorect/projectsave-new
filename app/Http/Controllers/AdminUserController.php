<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;
use App\Models\AdminAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $roles   = Role::all();
        $perPage = in_array((int) $request->get('per_page', 25), [25, 50, 100])
            ? (int) $request->get('per_page', 25)
            : 25;

        $query = User::with('roles')->latest();

        // Search by name or email
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by user_type
        if ($userType = $request->get('user_type')) {
            if ($userType === 'regular') {
                $query->whereNull('user_type');
            } else {
                $query->where('user_type', $userType);
            }
        }

        // Filter by admin flag
        if ($request->filled('is_admin')) {
            $query->where('is_admin', (bool) $request->get('is_admin'));
        }

        // Filter by verification status
        if ($request->filled('verified')) {
            if ($request->get('verified') === '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->paginate($perPage)->withQueryString();

        // Find active sessions mapped to user IDs (database sessions)
        $activeSessions = [];
        try {
            $sessionTable   = config('session.table', 'sessions');
            $activeSessions = DB::table($sessionTable)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->map(fn($v) => (int) $v)
                ->toArray();
        } catch (\Throwable $e) {
            $activeSessions = [];
        }

        return view('admin.users.index', compact('users', 'roles', 'activeSessions', 'perPage'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Perform a bulk action on selected users.
     *
     * Actions: delete | verify | activate | deactivate
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action'   => ['required', 'in:delete,verify,activate,deactivate'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $action  = $validated['action'];
        $userIds = collect($validated['user_ids']);
        $adminId = Auth::id();

        // Prevent acting on yourself in destructive operations
        if (in_array($action, ['delete', 'deactivate']) && $userIds->contains($adminId)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot perform that action on your own account.');
        }

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            switch ($action) {
                case 'delete':
                    try {
                        AdminAuditLog::create([
                            'admin_user_id' => $adminId,
                            'action'        => 'bulk_delete_user',
                            'target_type'   => 'User',
                            'target_id'     => $user->id,
                            'meta'          => ['email' => $user->email],
                            'ip_address'    => $request->ip(),
                            'user_agent'    => $request->userAgent(),
                        ]);
                    } catch (\Throwable $e) {}
                    $this->safeDeleteUser($user);
                    break;

                case 'verify':
                    if (!$user->hasVerifiedEmail()) {
                        $user->forceFill(['email_verified_at' => now()])->save();
                        try {
                            event(new \Illuminate\Auth\Events\Verified($user));
                            AdminAuditLog::create([
                                'admin_user_id' => $adminId,
                                'action'        => 'bulk_verify_user',
                                'target_type'   => 'User',
                                'target_id'     => $user->id,
                                'meta'          => null,
                                'ip_address'    => $request->ip(),
                                'user_agent'    => $request->userAgent(),
                            ]);
                        } catch (\Throwable $e) {}
                    }
                    break;

                case 'activate':
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active')) {
                        $user->forceFill(['is_active' => true])->save();
                        try {
                            AdminAuditLog::create([
                                'admin_user_id' => $adminId,
                                'action'        => 'bulk_activate_user',
                                'target_type'   => 'User',
                                'target_id'     => $user->id,
                                'meta'          => null,
                                'ip_address'    => $request->ip(),
                                'user_agent'    => $request->userAgent(),
                            ]);
                        } catch (\Throwable $e) {}
                    }
                    break;

                case 'deactivate':
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active')) {
                        $user->forceFill(['is_active' => false])->save();
                        try {
                            AdminAuditLog::create([
                                'admin_user_id' => $adminId,
                                'action'        => 'bulk_deactivate_user',
                                'target_type'   => 'User',
                                'target_id'     => $user->id,
                                'meta'          => null,
                                'ip_address'    => $request->ip(),
                                'user_agent'    => $request->userAgent(),
                            ]);
                        } catch (\Throwable $e) {}
                    }
                    break;
            }
        }

        $count  = $users->count();
        $labels = [
            'delete'     => 'deleted',
            'verify'     => 'verified',
            'activate'   => 'activated',
            'deactivate' => 'deactivated',
        ];

        return redirect()->route('admin.users.index', $request->only(['search', 'user_type', 'is_admin', 'verified', 'per_page']))
            ->with('success', "{$count} user(s) {$labels[$action]} successfully.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        // Audit log
        try {
            AdminAuditLog::create([
                'admin_user_id' => Auth::id(),
                'action' => 'create_user',
                'target_type' => 'User',
                'target_id' => null,
                'meta' => ['email' => $validated['email'], 'name' => $validated['name']],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // don't block
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'is_admin' => 'boolean',
             'roles' => 'array|nullable'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        $user->roles()->sync($request->input('roles', []));
        try {
            AdminAuditLog::create([
                'admin_user_id' => Auth::id(),
                'action' => 'update_user',
                'target_type' => 'User',
                'target_id' => $user->id,
                'meta' => ['changes' => $validated],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // ignore
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    /**
     * Nullify non-cascade FK references before deleting a user,
     * so the delete can never fail due to a constraint violation.
     * After the migration runs these are handled by the DB automatically;
     * this is a belt-and-suspenders guard during the transition.
     */
    private function safeDeleteUser(User $user): void
    {
        DB::table('courses')
            ->where('instructor_id', $user->id)
            ->update(['instructor_id' => null]);

        DB::table('anniversary_wish_logs')
            ->where('sent_by', $user->id)
            ->update(['sent_by' => null]);

        DB::table('partners')
            ->where('user_id', $user->id)
            ->update(['user_id' => null]);

        $user->delete();
    }

    public function destroy(User $user)
    {
        $id = $user->id;
        $this->safeDeleteUser($user);

        try {
            AdminAuditLog::create([
                'admin_user_id' => Auth::id(),
                'action' => 'delete_user',
                'target_type' => 'User',
                'target_id' => $id,
                'meta' => null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // ignore
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    public function show(User $user)
    {
        try {
            AdminAuditLog::create([
                'admin_user_id' => Auth::id(),
                'action' => 'view_user',
                'target_type' => 'User',
                'target_id' => $user->id,
                'meta' => null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // ignore
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Manually verify a user's email (admin action)
     */
    public function verify(Request $request, User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('admin.users.show', $user)->with('info', 'User email is already verified.');
        }

        // Prefer the model method that properly marks the user verified.
        if (method_exists($user, 'markEmailAsVerified')) {
            $user->markEmailAsVerified();
        } else {
            // Fallback: force fill the attribute then save
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        // Fire the verified event so any listeners run (notifications, analytics)
        try {
            event(new Verified($user));
        } catch (\Throwable $e) {
            // Don't block admin flow if listeners fail
        }

        // If this is an AJAX/JSON request, return JSON so the UI can update in-place
        try {
            AdminAuditLog::create([
                'admin_user_id' => Auth::id(),
                'action' => 'verify_user_email',
                'target_type' => 'User',
                'target_id' => $user->id,
                'meta' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // ignore
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User email verified successfully.',
                'email_verified_at' => $user->email_verified_at,
            ]);
        }

        return redirect()->route('admin.users.show', $user)->with('success', 'User email verified successfully.');
    }

    /**
     * Toggle user's active status (admin action)
     */
    public function toggleActive(Request $request, User $user)
    {
        // Some installations may not have an is_active column. Check before updating.
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active')) {
            return redirect()->back()->with('error', "The 'is_active' column is not present in the users table.");
        }

        $old = $user->is_active;
        $user->update(['is_active' => !$user->is_active]);

        try {
            AdminAuditLog::create([
                'admin_user_id' => Auth::id(),
                'action' => 'toggle_user_active',
                'target_type' => 'User',
                'target_id' => $user->id,
                'meta' => ['from' => $old, 'to' => $user->is_active],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // ignore
        }

        return redirect()->back()->with('success', 'User active status updated.');
    }
}
