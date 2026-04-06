<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('institution:id,nama');
        $search = trim((string) $request->get('q'));
        $roleFilter = $request->get('peran');
        $institutionFilter = $request->get('institusi_id');
        $roles = array_keys($this->roleLabels());

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                    ->orWhere('surel', 'like', "%{$search}%");
            });
        }

        if ($roleFilter && in_array($roleFilter, $roles, true)) {
            $query->where('peran', $roleFilter);
        }

        if ($institutionFilter) {
            $query->where('institusi_id', $institutionFilter);
        }

        $users = $query->orderBy('nama')->paginate(15)->withQueryString();
        $roleSummary = User::selectRaw('peran, COUNT(*) as total')->groupBy('peran')->pluck('total', 'peran');
        $institutions = Institution::orderBy('nama')->get(['id', 'nama']);

        return view('superadmin.users.index', [
            'users' => $users,
            'roleOptions' => $this->roleLabels(),
            'roleSummary' => $roleSummary,
            'institutions' => $institutions,
            'search' => $search,
            'roleFilter' => $roleFilter,
            'institutionFilter' => $institutionFilter,
        ]);
    }

    public function create()
    {
        return view('superadmin.users.create', [
            'roleOptions' => $this->roleLabels(),
            'institutions' => Institution::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function store(Request $request)
    {
        $roles = array_keys($this->roleLabels());

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'surel' => ['required', 'email', 'max:255', 'unique:pengguna,surel'],
            'peran' => ['required', Rule::in($roles)],
            'nama_institusi' => ['nullable', 'string', 'max:255'],
            'kata_sandi' => ['nullable', 'string', 'min:8', 'max:64'],
        ]);

        $institutionId = null;
        if (!empty($validated['nama_institusi'])) {
            $institution = Institution::firstOrCreate(['nama' => trim($validated['nama_institusi'])]);
            $institutionId = $institution->id;
        }

        $plainPassword = $validated['kata_sandi'] ?? Str::random(12);

        User::create([
            'nama' => $validated['nama'],
            'surel' => $validated['surel'],
            'peran' => $validated['peran'],
            'institusi_id' => $institutionId,
            'kata_sandi' => $plainPassword,
        ]);

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', "Pengguna baru dibuat. Password sementara: {$plainPassword} (mohon segera diganti).");
    }

    public function updateRole(Request $request, User $user)
    {
        $roles = array_keys($this->roleLabels());

        $validated = $request->validate([
            'peran' => ['required', Rule::in($roles)],
        ]);

        if ($user->isSuperAdmin() && !$request->user()->isSuperAdmin()) {
            abort(403, 'Tidak dapat mengubah role super admin lain.');
        }

        if ($user->id === $request->user()->id && $validated['peran'] !== 'superadmin') {
            return back()->with('error', 'Tidak dapat menurunkan role akun Anda sendiri.');
        }

        $user->update(['peran' => $validated['peran']]);

        return back()->with('success', 'Role pengguna diperbarui.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'kata_sandi' => ['nullable', 'string', 'min:8', 'max:64'],
        ]);

        $newPassword = $validated['kata_sandi'] ?? Str::random(12);
        $user->update(['kata_sandi' => $newPassword]);

        return back()->with('success', "Password untuk {$user->surel} disetel ulang. Password sementara: {$newPassword}");
    }

    /**
     * Hapus pengguna (hanya superadmin). Tidak boleh hapus diri sendiri.
     */
    public function destroy(Request $request, User $user)
    {
        $actor = $request->user();
        if (!$actor || !$actor->isSuperAdmin()) {
            abort(403, 'Hanya superadmin yang dapat menghapus pengguna.');
        }

        if ($actor->id === $user->id) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    protected function roleLabels(): array
    {
        return [
            'superadmin' => 'Super Admin (akses penuh)',
            'admin' => 'Admin BAPPPEDA',
            'kesbangpol' => 'Kesbangpol',
            'user' => 'Peneliti',
        ];
    }
}
