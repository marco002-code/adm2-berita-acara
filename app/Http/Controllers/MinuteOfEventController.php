<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Attendance;
use App\Models\ExamSession;
use App\Models\MinuteOfEvent;
use App\Models\Participant;
use App\Models\Proctor;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MinuteOfEventController extends Controller
{
    /**
     * Display the minute of event form and summary.
     */
    public function index(Request $request): View
    {
        $sessions = ExamSession::query()->orderBy('exam_date')->orderBy('start_time')->get();
        $rooms = Room::query()->orderBy('name')->get();

        $selectedSessionId = (int) $request->input('exam_session_id', $sessions->first()?->id ?? 0);
        $selectedRoomId = (int) $request->input('room_id', $rooms->first()?->id ?? 0);

        $selectedSession = $sessions->firstWhere('id', $selectedSessionId);
        $selectedRoom = $rooms->firstWhere('id', $selectedRoomId);

        $minute = MinuteOfEvent::query()
            ->where('exam_session_id', $selectedSessionId)
            ->where('room_id', $selectedRoomId)
            ->first();

        if (! $minute) {
            $minute = new MinuteOfEvent([
                'proctor_name' => auth()->user()?->name ?? '',
                'headmaster_name' => AppSetting::get('headmaster_name', 'Suwahono, S.Pd.'),
                'headmaster_nip' => AppSetting::get('headmaster_nip', '960434'),
                'exam_date' => $selectedSession?->exam_date ?? now()->toDateString(),
            ]);
        }

        $totalExpected = Participant::query()
            ->where('exam_session_id', $selectedSessionId)
            ->where('room_id', $selectedRoomId)
            ->count();

        // Get absent students only
        $absentAttendances = Attendance::query()
            ->with('participant')
            ->where('exam_session_id', $selectedSessionId)
            ->where('room_id', $selectedRoomId)
            ->get();

        $totalAbsent = $absentAttendances->count();
        $totalPresent = max(0, $totalExpected - $totalAbsent);

        $proctors = Proctor::query()->where('is_active', true)->orderBy('name')->get();

        return view('minutes.index', compact(
            'sessions',
            'rooms',
            'selectedSession',
            'selectedRoom',
            'minute',
            'totalExpected',
            'totalAbsent',
            'totalPresent',
            'absentAttendances',
            'proctors'
        ));
    }

    /**
     * Store or update the minute of event.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'exam_session_id' => ['required', 'exists:exam_sessions,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'exam_date' => ['required', 'date'],
            'proctor_name' => ['required', 'string', 'max:255'],
            'proctor_nip' => ['nullable', 'string', 'max:50'],
            'supervisor_name' => ['required', 'string', 'max:255'],
            'supervisor_nip' => ['nullable', 'string', 'max:50'],
            'headmaster_name' => ['nullable', 'string', 'max:255'],
            'headmaster_nip' => ['nullable', 'string', 'max:50'],
            'incident_notes' => ['nullable', 'string'],
        ]);

        $minute = MinuteOfEvent::updateOrCreate(
            [
                'exam_session_id' => $validated['exam_session_id'],
                'room_id' => $validated['room_id'],
            ],
            [
                'exam_date' => $validated['exam_date'],
                'proctor_name' => $validated['proctor_name'],
                'proctor_nip' => $validated['proctor_nip'],
                'supervisor_name' => $validated['supervisor_name'],
                'supervisor_nip' => $validated['supervisor_nip'],
                'headmaster_name' => $validated['headmaster_name'],
                'headmaster_nip' => $validated['headmaster_nip'],
                'incident_notes' => $validated['incident_notes'],
                'status' => 'submitted',
            ]
        );

        return redirect()->route('minutes.index', [
            'exam_session_id' => $minute->exam_session_id,
            'room_id' => $minute->room_id,
        ])->with('success', 'Berita Acara Ujian berhasil disimpan.');
    }

    /**
     * Display printable view for the minute of event.
     */
    public function print(MinuteOfEvent $minute): View
    {
        $minute->load(['room', 'examSession']);

        $totalExpected = Participant::query()
            ->where('exam_session_id', $minute->exam_session_id)
            ->where('room_id', $minute->room_id)
            ->count();

        $absentAttendances = Attendance::query()
            ->with('participant')
            ->where('exam_session_id', $minute->exam_session_id)
            ->where('room_id', $minute->room_id)
            ->get();

        $totalAbsent = $absentAttendances->count();
        $totalPresent = max(0, $totalExpected - $totalAbsent);

        $presentParticipants = Participant::query()
            ->where('exam_session_id', $minute->exam_session_id)
            ->where('room_id', $minute->room_id)
            ->whereNotIn('id', $absentAttendances->pluck('participant_id'))
            ->orderBy('participant_number')
            ->get();

        return view('minutes.print', compact(
            'minute',
            'totalExpected',
            'totalAbsent',
            'totalPresent',
            'absentAttendances',
            'presentParticipants'
        ));
    }
}
