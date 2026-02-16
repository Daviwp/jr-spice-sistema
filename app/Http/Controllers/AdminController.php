<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    /**
     * Display the users management page.
     */
    public function usersIndex(Request $request)
    {
        $query = User::latest();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        return \Inertia\Inertia::render('Admin/Users/Index', [
            'users' => $query->get(),
            'filters' => $request->only(['search', 'status']),
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count()
        ]);
    }

    /**
     * Display the user creation page.
     */
    /**
     * Display the user creation page.
     */
    /**
     * Display the user creation page.
     */
    public function usersCreate()
    {
        return \Inertia\Inertia::render('Admin/Users/Create');
    }

    /**
     * Display the settings page.
     */
    public function settingsIndex()
    {
        $settings = \App\Models\Setting::all()->mapWithKeys(function ($item) {
            return [$item->key => \App\Models\Setting::get($item->key)];
        });

        return \Inertia\Inertia::render('Admin/Settings/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Update system settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            \App\Models\Setting::set($key, $value);
        }

        return back()->with('success', __('Settings updated successfully.'));
    }

    /**
     * Store a new master admin or user.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_master' => ['boolean'],
            'is_active' => ['boolean'],
            'allowed_pages' => ['nullable', 'array'],
            'tenant_id' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => $request->boolean('is_active', true),
            'tenant_id' => $request->input('tenant_id'),
            'allowed_pages' => $request->input('allowed_pages', []),
        ]);

        $user->is_master = $request->boolean('is_master', false);
        $user->save();

        return redirect()->route('admin.users.index')->with('success', __('User created successfully.'));
    }

    /**
     * Display the user edit page.
     */
    public function usersEdit(User $user)
    {
        return \Inertia\Inertia::render('Admin/Users/Edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified user.
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'is_master' => ['boolean'],
            'is_active' => ['boolean'],
            'allowed_pages' => ['nullable', 'array'],
        ]);

        if ($user->id === $request->user()->id) {
            if (!$request->boolean('is_master') || !$request->boolean('is_active')) {
                return back()->with('error', __('You cannot remove your own master status or deactivate yourself.'));
            }
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'allowed_pages' => $request->input('allowed_pages', []),
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->fill($userData);
        $user->is_master = $request->boolean('is_master');
        $user->is_active = $request->boolean('is_active');
        $user->save();

        return redirect()->route('admin.users.index')->with('success', __('User updated successfully.'));
    }

    /**
     * Get Power BI Pages (API)
     */
    public function getPowerBiPages(\App\Services\PowerBiService $powerBiService)
    {
        try {
            return response()->json($powerBiService->getReportPages());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Toggle user active status.
     */
    public function toggleUserStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('You cannot deactivate yourself.'));
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', __('User status updated.'));
    }

    /**
     * Bulk update users status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'is_active' => 'required|boolean',
        ]);

        $ids = $request->ids;
        $currentUserExcluded = false;

        if (!$request->is_active && in_array(auth()->id(), $ids)) {
            $ids = array_filter($ids, fn($id) => $id != auth()->id());
            $currentUserExcluded = true;
        }

        if (empty($ids)) {
            return back()->with('error', __('No changes allowed for your own account.'));
        }

        User::whereIn('id', $ids)->update([
            'is_active' => $request->is_active
        ]);

        $message = __('Users updated successfully.');
        if ($currentUserExcluded) {
            $message .= ' ' . __('Your account was not deactivated.');
        }

        return back()->with('success', $message);
    }

    /**
     * Remove the specified user.
     */
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('You cannot delete yourself.'));
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('User deleted successfully.'));
    }
}
