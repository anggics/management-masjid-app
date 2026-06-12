<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        return view('admin.users', [
            'users' => $query->latest()->paginate(20)->withQueryString(),
            'search' => $search ?? '',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,staff,user'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();

        User::create($data);

        return back()->with('success', 'Pengguna ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,staff,user'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        // Cegah admin menghapus akunnya sendiri.
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna dihapus.');
    }
}
