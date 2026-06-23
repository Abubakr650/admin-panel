<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Helpers\Idempotency;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Services\FileStorageService;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    protected FileStorageService $storage;

    public function __construct(FileStorageService $storage)
    {
        $this->storage = $storage;
    }

    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $term = '%' . addcslashes($request->input('search'), '%_\\') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', $term)
                  ->orWhere('full_name', 'LIKE', $term)
                  ->orWhere('email', 'LIKE', $term);
            });
        }

        // Role filter
        if ($request->filled('role') && $request->input('role') !== 'all') {
            $role = $request->input('role');
            if ($role === 'staff') {
                $query->where('role', '!=', 'doctor');
            } else {
                $query->where('role', $role);
            }
        }

        // Archived
        if ($request->input('archived') === 'true') {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            $query->latest();
        }

        $users = $query->paginate(10)->onEachSide(1)->withQueryString();

        // Stats
        $archivedCount = User::onlyTrashed()->count();
        $totalActive   = User::count();
        $doctorCount   = User::where('role', 'doctor')->count();
        $staffCount    = $totalActive - $doctorCount;

        $rolesAvailable = [
            'doctor', 'admin', 'accountant', 'receptionist',
            'pharmacist', 'radiology-staff', 'warehouse-keeper',
        ];

        return view('admin.users.index', compact(
            'users', 'archivedCount', 'totalActive', 'doctorCount', 'staffCount', 'rolesAvailable'
        ));
    }

    public function create()
    {
        $rolesAvailable = [
            'admin', 'accountant', 'receptionist',
            'doctor', 'pharmacist', 'radiology-staff', 'warehouse-keeper',
        ];
        return view('admin.users.create', compact('rolesAvailable'));
    }

    public function store(StoreUserRequest $request)
    {
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('users.index');
        }

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storage->upload($request->file('image'), 'users', 'user');
        }

        $user = User::create($validated);

        if ($request->filled('return_to')) {
            $returnTo = $request->input('return_to');
            $params = $request->except(['_token', 'password', 'password_confirmation', 'image', 'return_to', 'idempotency_key']);
            $params['user_id'] = $user->id;
            
            return redirect($returnTo . '?' . http_build_query($params))
                ->with('success', 'User created and redirected back!');
        }

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    public function show(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $rolesAvailable = [
            'admin', 'accountant', 'receptionist',
            'doctor', 'pharmacist', 'radiology-staff', 'warehouse-keeper',
        ];
        return view('admin.users.edit', compact('user', 'rolesAvailable'));
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('users.index');
        }

        $user = User::findOrFail($id);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image) {
                $this->storage->delete($user->image);
            }
            $validated['image'] = $this->storage->upload($request->file('image'), 'users', 'user');
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Prevent role modification after creation
        unset($validated['role']);

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->hasProtectedRelations()) {
            return back()->with('error', 'Cannot archive user because it has associated records.');
        }

        $user->delete();
        $previousUrl = URL::previous();
        $showRoute   = route('users.show', $user->id);
        if (str_contains($previousUrl, $showRoute)) {
            return redirect()->route('users.index')->with('success', 'User archived successfully!');
        }
        return redirect()->back()->with('success', 'User archived successfully!');
    }

    public function restore(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('users.index', ['archived' => 'true'])->with('success', 'User restored successfully!');
    }
}
