<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function show(User $user)
    {
        return response()->json([
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'avatar'  => $user->avatar,
            'role_id' => $user->role_id,
            'role'    => $user->role ? ['name' => $user->role->name] : null,
        ]);
    }

    public function create()
    {
        $roles = Role::orderByRaw("name = 'admin' ASC")
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact('roles'));
    }
    public function index(Request $request)
    {
        $allowedSorts = ['name', 'email', 'created_at', 'role'];
        $sort = in_array($request->get('sort'), $allowedSorts, true) ? $request->get('sort') : 'created_at';
        $dir  = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $users = User::query()
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.*')
            ->when($request->filled('name'), fn($q) => $q->where('users.name', 'like', '%' . $request->name . '%'))
            ->when($request->filled('email'), fn($q) => $q->where('users.email', 'like', '%' . $request->email . '%'))
            ->when($request->filled('role_id'), function ($q) use ($request) {
                $q->where('roles.id', $request->role_id);
            })
            ->orderBy($sort === 'role' ? 'roles.name' : "users.{$sort}", $dir)
            ->paginate(10)
            ->withQueryString();

        $roles = Role::query()->select('id', 'name')->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles', 'sort', 'dir'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'role_id' => 'required|exists:roles,id',
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data['password'] = bcrypt($data['password']);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')
                ->store('avatars', 'public');
        }

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderByRaw("name = 'admin' ASC")
            ->orderBy('name')
            ->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['remove_avatar'])) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['remove_avatar']);

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', __('admin_users.success_update'));
    }
    
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', __('admin_users.cannot_delete_self'));
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', __('admin_users.success_delete'));
    }
}
