<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $staff = User::where('is_staff', true)
            ->latest()
            ->paginate(15);

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(12)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $staff = new User();
        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->password = Hash::make($validated['password']);
        $staff->is_staff = true;
        $staff->is_active = true;
        $staff->save();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff account created successfully.');
    }

    public function suspend(User $staff)
    {
        $this->authorizeStaffMember($staff);

        $staff->is_active = false;
        $staff->save();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff account suspended successfully.');
    }

    public function reactivate(User $staff)
    {
        $this->authorizeStaffMember($staff);

        $staff->is_active = true;
        $staff->save();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff account reactivated successfully.');
    }

    public function remove(User $staff)
    {
        $this->authorizeStaffMember($staff);

        $staff->is_staff = false;
        $staff->save();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff access removed. The account and its history were preserved.');
    }

    public function destroy(User $staff)
    {
        $this->authorizeStaffMember($staff);

        DB::transaction(function () use ($staff): void {
            DB::table('password_reset_tokens')
                ->where('email', $staff->email)
                ->delete();

            $staff->delete();
        });

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff account deleted successfully.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(Auth::user()?->is_admin, 403);
    }

    private function authorizeStaffMember(User $staff): void
    {
        $this->authorizeAdmin();
        abort_unless($staff->is_staff && ! $staff->is_admin && $staff->id !== Auth::id(), 404);
    }
}
