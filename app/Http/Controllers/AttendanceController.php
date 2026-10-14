<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ExamSession;
use App\Models\Participant;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display the attendance management form.
     */
    public function index(Request $request): View
    {
        $sessions = ExamSession::query()->orderBy('exam_date')->orderBy('start_time')->get();
        $rooms = Room::query()->orderBy('name')->get();

        $selectedSessionId = (int) $request->input('exam_session_id', $sessions->first()?->id ?? 0);
        $selectedRoomId = (int) $request->input('room_id', $rooms->first()?->id ?? 0);

        $selectedSession = $sessions->firstWhere('id', $selectedSessionId);
        $selectedRoom = $rooms->firstWhere('id', $selectedRoomId);

        $participants = Participant::query()
            ->when($selectedSessionId, fn ($q) => $q->where('exam_session_id', $selectedSessionId))
            ->when($selectedRoomId, fn ($q) => $q->where('room_id', $selectedRoomId))
            ->orderBy('participant_number')
            ->get();

        // Attendances only store students who are absent
        $absentRecords = Attendance::query()
            ->where('exam_session_id', $selectedSessionId)
            ->where('room_id', $selectedRoomId)
            ->get()
            ->keyBy('participant_id');

        $totalParticipants = $participants->count();
        $absentCount = $absentRecords->count();
        $presentCount = max(0, $totalParticipants - $absentCount);

        return view('attendances.index', compact(
            'sessions',
            'rooms',
            'selectedSession',
            'selectedRoom',
            'participants',
            'absentRecords',
            'totalParticipants',
            'absentCount',
            'presentCount'
        ));
    }

    /**
     * Store attendance by recording only absent students.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'exam_session_id' => ['required', 'exists:exam_sessions,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'absent_participants' => ['nullable', 'array'],
            'absent_participants.*' => ['integer', 'exists:participants,id'],
            'reasons' => ['nullable', 'array'],
            'notes' => ['nullable', 'array'],
        ]);

        $sessionId = (int) $validated['exam_session_id'];
        $roomId = (int) $validated['room_id'];
        $absentIds = $request->input('absent_participants', []);

        DB::transaction(function () use ($sessionId, $roomId, $absentIds, $request): void {
            // Remove previous absence records for this session and room
            Attendance::query()
                ->where('exam_session_id', $sessionId)
                ->where('room_id', $roomId)
                ->delete();

            // Insert ONLY the students marked as absent
            foreach ($absentIds as $participantId) {
                Attendance::create([
                    'exam_session_id' => $sessionId,
                    'room_id' => $roomId,
                    'participant_id' => (int) $participantId,
                    'status' => 'tidak_hadir',
                    'reason' => $request->input("reasons.{$participantId}", 'Tanpa Keterangan'),
                    'notes' => $request->input("notes.{$participantId}"),
                ]);
            }
        });

        $absentCount = count($absentIds);

        return redirect()->route('attendances.index', [
            'exam_session_id' => $sessionId,
            'room_id' => $roomId,
        ])->with('success', "Presensi berhasil diperbarui. Hanya {$absentCount} siswa tidak hadir yang dicatat di sistem (siswa lainnya otomatis hadir).");
    }
}
