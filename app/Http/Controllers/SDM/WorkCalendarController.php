<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\WorkCalendarEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkCalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->filled('month')
            ? Carbon::parse($request->month . '-01')->startOfMonth()
            : now()->startOfMonth();

        $calendarStart = $month->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $events = WorkCalendarEvent::whereDate('date_from', '<=', $calendarEnd)
            ->whereDate('date_to', '>=', $calendarStart)
            ->orderBy('date_from')
            ->get();

        $days = collect();
        $cursor = $calendarStart->copy();
        while ($cursor->lte($calendarEnd)) {
            $dayEvents = $events->filter(fn ($event) => $event->date_from->lte($cursor) && $event->date_to->gte($cursor))->values();
            $days->push([
                'date' => $cursor->copy(),
                'in_month' => $cursor->isSameMonth($month),
                'is_weekend' => $cursor->isWeekend(),
                'events' => $dayEvents,
            ]);
            $cursor->addDay();
        }

        $upcomingEvents = WorkCalendarEvent::whereDate('date_to', '>=', now()->toDateString())
            ->orderBy('date_from')
            ->take(8)
            ->get();

        return view('pages.sdm.calendar.index', compact('month', 'days', 'events', 'upcomingEvents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:holiday,collective_leave'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        WorkCalendarEvent::create($data);

        return back()->with('success', 'Kalender hari libur/cuti bersama berhasil ditambahkan.');
    }

    public function destroy(WorkCalendarEvent $calendar)
    {
        $calendar->delete();

        return back()->with('success', 'Data kalender berhasil dihapus.');
    }
}
