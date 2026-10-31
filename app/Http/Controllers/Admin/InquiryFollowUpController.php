<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\InquiryFollowUp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InquiryFollowUpController extends Controller
{

public function index(Request $request, Inquiry $inquiry)
{
    $user = $request->user();

    abort_unless($user?->is_staff, 403);

    if (! $user->is_admin) {
        abort_unless(
            (int) $inquiry->assigned_to_user_id === (int) $user->id,
            403
        );
    }

    $inquiry->load('vehicle');

    $followUps = $inquiry->followUps()
        ->with('staff')
        ->latest()
        ->paginate(10);

    return view(
        'admin.inquiries.follow-ups',
        compact('inquiry', 'followUps')
    );
}

    public function store(Request $request, Inquiry $inquiry)
    {
        $user = $request->user();

        // Only HM CARS staff can record follow-ups.
        abort_unless($user?->is_staff, 403);

        // Ordinary staff can only update assigned leads.
        if (! $user->is_admin) {
            abort_unless(
                (int) $inquiry->assigned_to_user_id === (int) $user->id,
                403
            );
        }

        $validated = $request->validate([
            'contact_method' => [
                'required',
                Rule::in([
                    'Phone Call',
                    'WhatsApp',
                    'SMS',
                    'Email',
                    'In Person',
                ]),
            ],
            'notes' => [
                'required',
                'string',
                'max:5000',
            ],
            'next_follow_up_at' => [
                'nullable',
                'date',
            ],
        ]);

        $followUp = new InquiryFollowUp();
        $followUp->inquiry_id = $inquiry->id;
        $followUp->user_id = $user->id;
        $followUp->contact_method = $validated['contact_method'];
        $followUp->notes = $validated['notes'];
        $followUp->next_follow_up_at =
            $validated['next_follow_up_at'] ?? null;

        $followUp->save();

        
return redirect()
    ->route('admin.inquiries.follow-ups.index', $inquiry)
    ->with('success', 'Follow-up recorded successfully.');

    }
}
