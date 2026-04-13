<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur a le plan Premium
        $sub = $user->activeSubscription()->with('plan')->first();
        $isPremium = $user->isOnTrial()
            || ($sub && $sub->plan && strtolower($sub->plan->name) === 'premium');

        if (!$isPremium) {
            abort(403, 'Cette fonctionnalité est réservée aux utilisateurs Premium.');
        }

        $logs = ActivityLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('activite.index', compact('logs'));
    }
}
