<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehiclePromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MarketingDashboardController extends Controller
{
    private const PLATFORMS = ['facebook', 'whatsapp', 'instagram', 'tiktok'];

    public function index(Request $request): View
    {
        abort_unless($request->user()?->is_admin, 403);

        // Aggregate campaign totals without multiplying visits by inquiries.
        $platformResults = DB::table('vehicle_promotions')
            ->select('platform')
            ->selectRaw('COUNT(*) as promotions_count')
            ->selectRaw('SUM((SELECT COUNT(*) FROM vehicle_promotion_visits WHERE vehicle_promotion_visits.vehicle_promotion_id = vehicle_promotions.id)) as visits_count')
            ->selectRaw('SUM((SELECT COUNT(*) FROM inquiries WHERE inquiries.vehicle_promotion_id = vehicle_promotions.id)) as inquiries_count')
            ->groupBy('platform')
            ->get()
            ->keyBy('platform');

        $platformStats = collect(self::PLATFORMS)->map(function (string $platform) use ($platformResults) {
            $stats = $platformResults->get($platform);

            return (object) [
                'platform' => $platform,
                'promotions_count' => (int) ($stats->promotions_count ?? 0),
                'visits_count' => (int) ($stats->visits_count ?? 0),
                'inquiries_count' => (int) ($stats->inquiries_count ?? 0),
            ];
        });

        $totals = (object) [
            'promotions' => $platformStats->sum('promotions_count'),
            'visits' => $platformStats->sum('visits_count'),
            'inquiries' => $platformStats->sum('inquiries_count'),
        ];

        $eligibleVehicles = Vehicle::query()
            ->where('status', 'published')
            ->whereNotNull('marketing_authorized_at')
            ->count();

        $recentPromotions = VehiclePromotion::query()
            ->with(['vehicle', 'promoter'])
            ->withCount(['visits', 'inquiries'])
            ->latest()
            ->paginate(20);

        return view('admin.marketing.index', compact(
            'platformStats',
            'totals',
            'eligibleVehicles',
            'recentPromotions'
        ));
    }
}
