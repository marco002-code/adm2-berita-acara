<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ExamSession;
use App\Models\MinuteOfEvent;
use App\Models\Participant;
use App\Models\Room;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display dashboard overview.
     */
    public function index(): View
    {
        $sessions = ExamSession::query()
            ->withCount('participants')
            ->orderBy('exam_date')
            ->orderBy('start_time')
            ->get();

        $rooms = Room::query()
            ->withCount('participants')
            ->orderBy('name')
            ->get();

        $totalParticipants = Participant::distinct('participant_number')->count('participant_number');
        $totalAbsent = Attendance::count();
        $totalPresent = max(0, $totalParticipants - $totalAbsent);
        $totalMinutes = MinuteOfEvent::count();

        return view('dashboard', compact(
            'sessions',
            'rooms',
            'totalParticipants',
            'totalAbsent',
            'totalPresent',
            'totalMinutes'
        ));
    }
}
