<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Shared\Enums\UserRole;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna dan staf.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search', '');
        $roleFilter = $request->input('role', '');

        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('assigned_pos', 'like', "%{$search}%");
            });
        }

        if (!empty($roleFilter)) {
            $query->where('role', $roleFilter);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // Statistik Cepat Staf & Pengguna
        $stats = [
            'total' => User::count(),
            'superadmin' => User::where('role', UserRole::SUPERADMIN->value)->count(),
            'sar' => User::where('role', UserRole::ADMIN_SAR->value)->count(),
            'kasir' => User::where('role', UserRole::KASIR->value)->count(),
            'pos' => User::where('role', UserRole::PETUGAS_POS->value)->count(),
            'pendaki' => User::where('role', UserRole::PENDAKI->value)->count(),
        ];

        // Daftar Pos Resmi untuk Assigned Pos
        $posList = [
            'Basecamp',
            'Pos 1 (Tanjakan EOD)',
            'Pos 2 (Batu Gendrung)',
            'Pos 3 (Alam Rimba)',
            'Pos 4 (Samudra Awan)',
            'Puncak Cikuray (2.821 MDPL)',
        ];

        return view('admin.users.index', compact('users', 'stats', 'search', 'roleFilter', 'posList'));
    }

    /**
     * Simpan pengguna / staf baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:25'],
            'assigned_pos' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            return redirect()->route('admin.users.index')
                ->with('success', "Akun staf/pengguna \"{$user->name}\" dengan peran {$user->role->label()} berhasil dibuat!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat akun: ' . $e->getMessage());
        }
    }

    /**
     * Perbarui data staf / pengguna.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:25'],
            'assigned_pos' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return redirect()->route('admin.users.index')
                ->with('success', "Data pengguna \"{$user->name}\" berhasil diperbarui!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus akun staf / pengguna.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        // Cegah menghapus superadmin terakhir
        if ($user->role === UserRole::SUPERADMIN) {
            $superadminCount = User::where('role', UserRole::SUPERADMIN->value)->count();
            if ($superadminCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus akun ini karena merupakan satu-satunya Superadmin di sistem.');
            }
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Akun \"{$userName}\" berhasil dihapus dari sistem.");
    }
}
