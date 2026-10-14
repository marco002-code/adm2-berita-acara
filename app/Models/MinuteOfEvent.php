<?php

namespace App\Models;

use Database\Factories\MinuteOfEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinuteOfEvent extends Model
{
    /** @use HasFactory<MinuteOfEventFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'room_id',
        'exam_session_id',
        'exam_date',
        'proctor_name',
        'proctor_nip',
        'supervisor_name',
        'supervisor_nip',
        'headmaster_name',
        'headmaster_nip',
        'incident_notes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exam_date' => 'date',
    ];

    /**
     * Get the room for the minute of event.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the exam session for the minute of event.
     */
    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    /**
     * Get expected participants count in the same room and session.
     */
    public function getExpectedParticipantsCountAttribute(): int
    {
        return Participant::query()
            ->where('room_id', $this->room_id)
            ->where('exam_session_id', $this->exam_session_id)
            ->count();
    }

    /**
     * Get absent participants count (recorded in attendances).
     */
    public function getAbsentParticipantsCountAttribute(): int
    {
        return Attendance::query()
            ->where('room_id', $this->room_id)
            ->where('exam_session_id', $this->exam_session_id)
            ->count();
    }

    /**
     * Get present participants count.
     */
    public function getPresentParticipantsCountAttribute(): int
    {
        return max(0, $this->expected_participants_count - $this->absent_participants_count);
    }
}
