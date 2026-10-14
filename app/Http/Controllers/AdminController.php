<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Attendance;
use App\Models\ExamSession;
use App\Models\MinuteOfEvent;
use App\Models\Participant;
use App\Models\Proctor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin panel dashboard and settings.
     */
    public function index(Request $request): View
    {
        $settings = [
            'assessment_name' => AppSetting::get('assessment_name', 'Asesmen Nasional Berbasis Komputer (ANBK)'),
            'academic_year' => AppSetting::get('academic_year', date('Y').'/'.(date('Y') + 1)),
            'school_name' => AppSetting::get('school_name', 'SMP MUHAMMADIYAH 1 YOGYAKARTA'),
            'headmaster_name' => AppSetting::get('headmaster_name', 'Suwahono, S.Pd.'),
            'headmaster_nip' => AppSetting::get('headmaster_nip', '960434'),
        ];

        $rooms = Room::query()->withCount('participants')->orderBy('name')->get();
        $sessions = ExamSession::query()->withCount('participants')->orderBy('exam_date')->orderBy('start_time')->get();
        $teachers = User::query()->whereNotNull('username')->orderBy('id')->get();
        $proctors = Proctor::query()->orderBy('name')->get();

        $uniqueStudentsCount = Participant::distinct('participant_number')->count('participant_number');
        $totalAssignmentsCount = Participant::count();

        $viewMode = $request->input('view_mode', $request->filled('filter_session') ? 'all' : 'master');

        if ($viewMode === 'master') {
            $subQuery = Participant::query()
                ->selectRaw('MIN(id) as id')
                ->groupBy('participant_number');

            $participants = Participant::query()
                ->whereIn('id', $subQuery)
                ->with(['room', 'examSession'])
                ->when($request->filled('filter_room'), fn ($q) => $q->where('room_id', $request->input('filter_room')))
                ->when($request->filled('search'), fn ($q) => $q->where(function ($sub) use ($request) {
                    $s = $request->input('search');
                    $sub->where('name', 'like', "%{$s}%")
                        ->orWhere('participant_number', 'like', "%{$s}%")
                        ->orWhere('rombel', 'like', "%{$s}%");
                }))
                ->orderBy('participant_number')
                ->paginate(50)
                ->withQueryString();
        } else {
            $participants = Participant::query()
                ->with(['room', 'examSession'])
                ->when($request->filled('filter_room'), fn ($q) => $q->where('room_id', $request->input('filter_room')))
                ->when($request->filled('filter_session'), fn ($q) => $q->where('exam_session_id', $request->input('filter_session')))
                ->when($request->filled('search'), fn ($q) => $q->where(function ($sub) use ($request) {
                    $s = $request->input('search');
                    $sub->where('name', 'like', "%{$s}%")
                        ->orWhere('participant_number', 'like', "%{$s}%")
                        ->orWhere('rombel', 'like', "%{$s}%");
                }))
                ->orderBy('participant_number')
                ->paginate(50)
                ->withQueryString();
        }

        $participantsCount = $uniqueStudentsCount;

        // 1. Data Berita Acara Terisi
        $minutesQuery = MinuteOfEvent::query()
            ->with(['room', 'examSession'])
            ->when($request->filled('filter_minute_date'), fn ($q) => $q->whereDate('exam_date', $request->input('filter_minute_date')))
            ->when($request->filled('filter_minute_session'), fn ($q) => $q->where('exam_session_id', $request->input('filter_minute_session')))
            ->when($request->filled('filter_minute_room'), fn ($q) => $q->where('room_id', $request->input('filter_minute_room')))
            ->when($request->filled('search_minute'), function ($q) use ($request) {
                $s = $request->input('search_minute');
                $q->where(function ($sub) use ($s) {
                    $sub->where('proctor_name', 'like', "%{$s}%")
                        ->orWhere('supervisor_name', 'like', "%{$s}%")
                        ->orWhere('incident_notes', 'like', "%{$s}%")
                        ->orWhereHas('room', fn ($r) => $r->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"))
                        ->orWhereHas('examSession', fn ($es) => $es->where('name', 'like', "%{$s}%")->orWhere('subject', 'like', "%{$s}%"));
                });
            })
            ->orderByDesc('exam_date')
            ->orderBy('exam_session_id')
            ->orderBy('room_id');

        $filledMinutes = $minutesQuery->paginate(20, ['*'], 'minutes_page')->withQueryString();
        $totalFilledMinutesCount = MinuteOfEvent::count();

        // 2. Data Presensi Terisi (Rekap by Tanggal & Sesi Sesuai Input Berita Acara)
        $minutesForRecapQuery = MinuteOfEvent::query()
            ->with(['room', 'examSession'])
            ->when($request->filled('filter_recap_date'), fn ($q) => $q->whereDate('exam_date', $request->input('filter_recap_date')))
            ->when($request->filled('filter_recap_session'), fn ($q) => $q->where('exam_session_id', $request->input('filter_recap_session')))
            ->orderByDesc('exam_date')
            ->orderBy('exam_session_id');

        $allMinutesForRecap = $minutesForRecapQuery->get();

        $attendanceRecaps = $allMinutesForRecap->groupBy(function ($item) {
            $date = $item->exam_date ? $item->exam_date->format('Y-m-d') : 'unknown';

            return $date.'_'.$item->exam_session_id;
        })->map(function ($minutesGroup) {
            $first = $minutesGroup->first();
            $examDate = $first->exam_date;
            $session = $first->examSession;
            $roomIds = $minutesGroup->pluck('room_id')->unique()->values();

            $totalExpected = Participant::query()
                ->where('exam_session_id', $session?->id)
                ->whereIn('room_id', $roomIds)
                ->count();

            $absentRecords = Attendance::query()
                ->with(['participant', 'room'])
                ->where('exam_session_id', $session?->id)
                ->whereIn('room_id', $roomIds)
                ->get();

            $totalAbsent = $absentRecords->count();
            $totalPresent = max(0, $totalExpected - $totalAbsent);
            $percentage = $totalExpected > 0 ? round(($totalPresent / $totalExpected) * 100, 1) : 100;

            return [
                'exam_date' => $examDate,
                'session' => $session,
                'minutes' => $minutesGroup,
                'room_count' => $roomIds->count(),
                'total_expected' => $totalExpected,
                'total_present' => $totalPresent,
                'total_absent' => $totalAbsent,
                'percentage' => $percentage,
                'absent_records' => $absentRecords,
            ];
        });

        $minuteDates = MinuteOfEvent::query()
            ->select('exam_date')
            ->distinct()
            ->orderByDesc('exam_date')
            ->pluck('exam_date')
            ->map(fn ($d) => is_string($d) ? $d : $d->format('Y-m-d'));

        return view('admin.index', compact(
            'settings',
            'rooms',
            'sessions',
            'teachers',
            'proctors',
            'participants',
            'participantsCount',
            'uniqueStudentsCount',
            'totalAssignmentsCount',
            'viewMode',
            'filledMinutes',
            'totalFilledMinutesCount',
            'attendanceRecaps',
            'minuteDates'
        ));
    }

    /**
     * Update general assessment and school settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assessment_name' => ['required', 'string', 'max:255'],
            'academic_year' => ['required', 'string', 'max:50'],
            'school_name' => ['required', 'string', 'max:255'],
            'headmaster_name' => ['nullable', 'string', 'max:255'],
            'headmaster_nip' => ['nullable', 'string', 'max:50'],
        ]);

        foreach ($validated as $key => $value) {
            AppSetting::set($key, $value);
        }

        return redirect()->route('admin.index', ['tab' => 'settings'])->with('success', 'Pengaturan nama asesmen dan sekolah berhasil diperbarui.');
    }

    /**
     * Store a newly created room.
     */
    public function storeRoom(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:rooms,code'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Room::create($validated);

        return redirect()->route('admin.index', ['tab' => 'rooms'])->with('success', "Ruangan '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Update an existing room.
     */
    public function updateRoom(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:rooms,code,'.$room->id],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $room->update($validated);

        return redirect()->route('admin.index', ['tab' => 'rooms'])->with('success', "Data ruangan '{$room->name}' berhasil diperbarui.");
    }

    /**
     * Delete a room.
     */
    public function destroyRoom(Room $room): RedirectResponse
    {
        $name = $room->name;
        $room->delete();

        return redirect()->route('admin.index', ['tab' => 'rooms'])->with('success', "Ruangan '{$name}' berhasil dihapus.");
    }

    /**
     * Store a newly created exam session.
     */
    public function storeSession(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'exam_date' => ['required', 'date'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        ExamSession::create($validated);

        return redirect()->route('admin.index', ['tab' => 'sessions'])->with('success', "Sesi ujian '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Update an existing exam session.
     */
    public function updateSession(Request $request, ExamSession $session): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'exam_date' => ['required', 'date'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $session->update($validated);

        return redirect()->route('admin.index', ['tab' => 'sessions'])->with('success', "Data sesi '{$session->name}' berhasil diperbarui.");
    }

    /**
     * Delete an exam session.
     */
    public function destroySession(ExamSession $session): RedirectResponse
    {
        $name = $session->name;
        $session->delete();

        return redirect()->route('admin.index', ['tab' => 'sessions'])->with('success', "Sesi ujian '{$name}' berhasil dihapus.");
    }

    /**
     * Bulk generate exam sessions by count.
     */
    public function bulkGenerateSessions(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_count' => ['required', 'integer', 'min:1', 'max:10'],
            'exam_date' => ['required', 'date'],
            'subject' => ['required', 'string', 'max:255'],
        ]);

        $count = (int) $validated['session_count'];
        $date = $validated['exam_date'];
        $subject = $validated['subject'];

        $defaultTimes = [
            1 => ['07:30', '09:30'],
            2 => ['10:30', '12:30'],
            3 => ['13:30', '15:30'],
            4 => ['16:00', '18:00'],
        ];

        for ($i = 1; $i <= $count; $i++) {
            $times = $defaultTimes[$i] ?? [sprintf('%02d:00', 7 + ($i * 2)), sprintf('%02d:00', 9 + ($i * 2))];

            ExamSession::firstOrCreate(
                [
                    'name' => "Sesi {$i}",
                    'exam_date' => $date,
                ],
                [
                    'start_time' => $times[0],
                    'end_time' => $times[1],
                    'subject' => $subject,
                    'description' => 'Dibuat otomatis oleh generator sesi',
                ]
            );
        }

        return redirect()->route('admin.index', ['tab' => 'sessions'])->with('success', "Berhasil membuat/menyesuaikan {$count} sesi ujian.");
    }

    /**
     * Store a newly created proctor.
     */
    public function storeProctor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nbm' => ['nullable', 'string', 'max:50'],
            'nip' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        Proctor::create($validated);

        return redirect()->route('admin.index', ['tab' => 'proctors'])->with('success', "Data proktor '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Update an existing proctor.
     */
    public function updateProctor(Request $request, Proctor $proctor): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nbm' => ['nullable', 'string', 'max:50'],
            'nip' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $proctor->update($validated);

        return redirect()->route('admin.index', ['tab' => 'proctors'])->with('success', "Data proktor '{$proctor->name}' berhasil diperbarui.");
    }

    /**
     * Delete a proctor.
     */
    public function destroyProctor(Proctor $proctor): RedirectResponse
    {
        $name = $proctor->name;
        $proctor->delete();

        return redirect()->route('admin.index', ['tab' => 'proctors'])->with('success', "Data proktor '{$name}' berhasil dihapus.");
    }

    /**
     * Store a newly created participant.
     */
    public function storeParticipant(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'participant_number' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'rombel' => ['nullable', 'string', 'max:50'],
            'room_id' => ['required', 'exists:rooms,id'],
            'exam_session_id' => ['required', 'exists:exam_sessions,id'],
            'school_origin' => ['nullable', 'string', 'max:255'],
        ]);

        Participant::create($validated);

        return redirect()->route('admin.index', [
            'tab' => 'participants',
            'filter_room' => $validated['room_id'],
            'filter_session' => $validated['exam_session_id'],
        ])->with('success', "Peserta '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Update an existing participant.
     */
    public function updateParticipant(Request $request, Participant $participant): RedirectResponse
    {
        $validated = $request->validate([
            'participant_number' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'rombel' => ['nullable', 'string', 'max:50'],
            'room_id' => ['required', 'exists:rooms,id'],
            'exam_session_id' => ['required', 'exists:exam_sessions,id'],
            'school_origin' => ['nullable', 'string', 'max:255'],
        ]);

        $oldNumber = $participant->participant_number;

        $participant->update($validated);

        // Sinkronkan nama, rombel, dan no peserta ke sesi lain yang diikuti siswa ini
        Participant::query()
            ->where('participant_number', $oldNumber)
            ->where('id', '!=', $participant->id)
            ->update([
                'participant_number' => $validated['participant_number'],
                'name' => $validated['name'],
                'rombel' => $validated['rombel'] ?? null,
                'school_origin' => $validated['school_origin'] ?? $participant->school_origin,
            ]);

        return redirect()->route('admin.index', [
            'tab' => 'participants',
            'filter_room' => $validated['room_id'],
            'filter_session' => $validated['exam_session_id'],
        ])->with('success', "Data peserta '{$participant->name}' berhasil diperbarui dan disinkronkan ke seluruh jadwal.");
    }

    /**
     * Delete a participant.
     */
    public function destroyParticipant(Participant $participant): RedirectResponse
    {
        $name = $participant->name;
        $roomId = $participant->room_id;
        $sessionId = $participant->exam_session_id;

        $participant->delete();

        return redirect()->route('admin.index', [
            'tab' => 'participants',
            'filter_room' => $roomId,
            'filter_session' => $sessionId,
        ])->with('success', "Peserta '{$name}' berhasil dihapus.");
    }
}
