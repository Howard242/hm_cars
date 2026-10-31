<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\InquiryFollowUp;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        $user = request()->user();

        abort_unless($user && ($user->is_staff || $user->is_admin), 403);

        /*
        |--------------------------------------------------------------------------
        | INQUIRIES
        |--------------------------------------------------------------------------
        | Admins see all inquiries.
        | Staff see only inquiries assigned to them.
        */

        $inquiryQuery = Inquiry::with([
            'vehicle',
            'assignedStaff',
        ])->latest();

        if (! $user->is_admin) {
            $inquiryQuery->where(
                'assigned_to_user_id',
                $user->id
            );
        }

        $inquiries = $inquiryQuery->paginate(15);

        /*
        |--------------------------------------------------------------------------
        | STAFF LIST
        |--------------------------------------------------------------------------
        | Only administrators need the staff assignment list.
        */

        $staff = $user->is_admin
            ? User::where('is_staff', true)
                ->where('is_admin', false)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | FOLLOW-UP REMINDERS
        |--------------------------------------------------------------------------
        | Retrieve the latest follow-up record for each inquiry.
        | Staff see reminders only for their assigned inquiries.
        */

        $reminders = InquiryFollowUp::query()
            ->with(['inquiry.vehicle', 'staff'])
            ->whereHas('inquiry', function ($query) use ($user) {

                if (! $user->is_admin) {
                    $query->where(
                        'assigned_to_user_id',
                        $user->id
                    );
                }

            })
            ->whereNotNull('next_follow_up_at')
            ->whereIn('id', function ($query) {

                $query->selectRaw('MAX(id)')
                    ->from('inquiry_follow_ups')
                    ->groupBy('inquiry_id');

            })
            ->orderBy('next_follow_up_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | OVERDUE FOLLOW-UPS
        |--------------------------------------------------------------------------
        | Follow-ups scheduled before the current time.
        */

        $overdueReminders = $reminders->filter(
            fn ($followUp) =>
                $followUp->next_follow_up_at->isPast()
        );

        /*
        |--------------------------------------------------------------------------
        | UPCOMING FOLLOW-UPS
        |--------------------------------------------------------------------------
        | Follow-ups scheduled for the current or future time.
        */

        $upcomingReminders = $reminders->filter(
            fn ($followUp) =>
                $followUp->next_follow_up_at->greaterThanOrEqualTo(now())
        );

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD VIEWS
        |--------------------------------------------------------------------------
        | Pass inquiries and reminders to the correct dashboard.
        */

        if ($user->is_admin) {

            $soldVehicleIds = Inquiry::query()
                ->select('vehicle_id')
                ->whereNotNull('vehicle_id')
                ->whereRaw('LOWER(status) = ?', ['sold'])
                ->distinct();

            $totalVehicles = Vehicle::count();
            $soldVehicles = Vehicle::whereIn(
                'id',
                clone $soldVehicleIds
            )->count();
            $availableVehicles = Vehicle::query()
                ->where('status', 'published')
                ->whereNotIn('id', clone $soldVehicleIds)
                ->count();
            $recentVehicleCount = Vehicle::query()
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            $latestFollowUpIds = InquiryFollowUp::query()
                ->selectRaw('MAX(id) as latest_id')
                ->groupBy('inquiry_id');

            $inquiriesWithFutureFollowUp = InquiryFollowUp::query()
                ->joinSub(
                    $latestFollowUpIds,
                    'latest_follow_up_ids',
                    fn ($join) => $join->on(
                        'inquiry_follow_ups.id',
                        '=',
                        'latest_follow_up_ids.latest_id'
                    )
                )
                ->whereNotNull('inquiry_follow_ups.next_follow_up_at')
                ->where('inquiry_follow_ups.next_follow_up_at', '>', now())
                ->select('inquiry_follow_ups.inquiry_id');

            $totalInquiries = Inquiry::count();
            $newInquiries = Inquiry::query()
                ->whereRaw('LOWER(status) = ?', ['new'])
                ->count();
            $awaitingFollowUp = Inquiry::query()
                ->whereRaw('LOWER(status) <> ?', ['sold'])
                ->whereNotIn('id', $inquiriesWithFutureFollowUp)
                ->count();

            $recentInquiries = Inquiry::with('vehicle')
                ->latest()
                ->take(6)
                ->get();

            $recentVehicles = Vehicle::with('primaryImage')
                ->withCount([
                    'inquiries as sold_inquiries_count' => fn ($query) =>
                        $query->whereRaw('LOWER(status) = ?', ['sold']),
                ])
                ->latest()
                ->take(6)
                ->get();

            return view('admin.dashboard', compact(
                'inquiries',
                'staff',
                'overdueReminders',
                'upcomingReminders',
                'totalVehicles',
                'availableVehicles',
                'soldVehicles',
                'recentVehicleCount',
                'totalInquiries',
                'newInquiries',
                'awaitingFollowUp',
                'recentInquiries',
                'recentVehicles'
            ));

        }

        return view('admin.staff-dashboard', compact(
            'inquiries',
            'overdueReminders',
            'upcomingReminders'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE INQUIRY STATUS
    |--------------------------------------------------------------------------
    */

    public function updateInquiryStatus(
        Request $request,
        Inquiry $inquiry
    ) {
        $user = $request->user();

        abort_unless($user?->is_staff, 403);

        // Staff can update only inquiries assigned to them.
        if (! $user->is_admin) {

            abort_unless(
                (int) $inquiry->assigned_to_user_id === (int) $user->id,
                403
            );

        }

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'New',
                    'Contacted',
                    'Viewing Scheduled',
                    'Negotiating',
                    'Sold',
                ]),
            ],
        ]);

        $inquiry->update($validated);

        return redirect()
            ->route('admin.dashboard')
            ->with(
                'success',
                'Inquiry status updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGN INQUIRY TO STAFF
    |--------------------------------------------------------------------------
    */

    public function assignInquiry(
        Request $request,
        Inquiry $inquiry
    ) {
        // Only administrators can assign inquiries.
        abort_unless(
            $request->user()?->is_admin,
            403
        );

        $validated = $request->validate([
            'assigned_to_user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(
                    fn ($query) =>
                        $query->where('is_staff', true)
                ),
            ],
        ]);

        $inquiry->assigned_to_user_id =
            $validated['assigned_to_user_id'] ?? null;

        $inquiry->save();

        return redirect()
            ->route('admin.dashboard')
            ->with(
                'success',
                'Inquiry assignment updated successfully.'
            );
    }
}
