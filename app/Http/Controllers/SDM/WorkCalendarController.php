<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Imports\WorkCalendarEventsImport;
use App\Models\WorkCalendarEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class WorkCalendarController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $month = $request->filled('month')
            ? Carbon::parse($request->month.'-01')->startOfMonth()
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

        $monthEvents = WorkCalendarEvent::whereDate('date_from', '<=', $month->copy()->endOfMonth())
            ->whereDate('date_to', '>=', $month->copy()->startOfMonth())
            ->orderBy('date_from')
            ->orderBy('date_to')
            ->get();

        return view('pages.sdm.calendar.index', [
            'month' => $month,
            'days' => $days,
            'events' => $events,
            'monthEvents' => $monthEvents,
            'typeOptions' => WorkCalendarEvent::typeOptions(),
            'previousMonth' => $month->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $month->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedEvent($request);

        WorkCalendarEvent::create($data);

        return back()->with('success', 'Agenda kalender berhasil ditambahkan.');
    }

    public function update(Request $request, WorkCalendarEvent $calendar)
    {
        $calendar->update($this->validatedEvent($request));

        return back()->with('success', 'Agenda kalender berhasil diperbarui.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'agenda_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $import = new WorkCalendarEventsImport;

        try {
            DB::transaction(fn () => Excel::import($import, $request->file('agenda_file')));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withErrors(['agenda_file' => 'File Excel tidak dapat diproses. Pastikan format kolom A-E dan data mulai baris 6 sudah sesuai.'])
                ->withInput();
        }

        $summary = $import->summary();

        return redirect()
            ->route('sdm.calendar.index', ['month' => $request->month ?: now()->format('Y-m')])
            ->with(
                'success',
                "Import agenda selesai: {$summary['created']} ditambahkan, {$summary['updated']} diperbarui, dan {$summary['skipped']} baris kosong dilewati."
            );
    }

    public function destroy(WorkCalendarEvent $calendar)
    {
        $calendar->delete();

        return back()->with('success', 'Agenda kalender berhasil dihapus.');
    }

    private function validatedEvent(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(WorkCalendarEvent::typeOptions()))],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_non_working' => ['nullable', 'boolean'],
        ]);
        $data['is_non_working'] = $request->boolean('is_non_working');

        return $data;
    }
}
