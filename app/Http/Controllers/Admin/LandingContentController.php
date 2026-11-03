<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoLanding;
use App\Support\Demo\DemoState;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingContentController extends Controller
{
    public function __invoke(Request $request): View
    {
        $pages = DemoState::isEmpty() ? [] : DemoLanding::pages();
        $current = collect($pages)->firstWhere('key', $request->query('filter')) ?? ($pages[0] ?? null);
        $query = mb_strtolower(trim((string) $request->query('q', '')));

        $groups = collect($current['groups'] ?? [])
            ->map(function (array $group) use ($query) {
                $group['slots'] = array_values(array_filter($group['slots'], fn (array $slot) => ! $query
                    || str_contains(mb_strtolower($slot['label'].' '.$slot['value']), $query)));

                return $group;
            })
            ->filter(fn (array $group) => count($group['slots']))
            ->values();
        $slots = collect($current['groups'] ?? [])->flatMap(fn (array $group) => $group['slots']);

        return view('admin.landing-content', [
            'pages' => $pages,
            'current' => $current,
            'groups' => $groups,
            'q' => (string) $request->query('q', ''),
            'total' => $slots->count(),
            'changed' => $slots->where('changed', true)->count(),
            'limits' => DemoLanding::imageLimits(),
        ]);
    }
}
