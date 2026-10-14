<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ExamSession;
use App\Models\Participant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ujian Matematika (12 Oktober 2026 - 11 Siswa Tidak Hadir)
        $mathAbsentList = [
            'P0100320017' => ['name' => 'IQBAL RADITYA ARYASATYA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320247' => ['name' => 'WAHYU SEKAR KIRANA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320185' => ['name' => 'WILDHAN NUR ARIFIN', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320007' => ['name' => 'ARKAN ARROYYAN', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320235' => ['name' => 'LATHIIFA FARAA AZZAHRA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320053' => ['name' => 'MUHAMMAD HASBY NAUFAL PRATAMA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320001' => ['name' => 'ABIGAIL XAVIOLA NUGRAHA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320086' => ['name' => 'REYHAN ALFATIR ARSYA PRATISTA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320184' => ['name' => 'WAWA RAHMADANI', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320080' => ['name' => 'KIANNO LUCKY ALEANDA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320144' => ['name' => 'MUHAMMAD RAYYAN KHAIRUL AKRAM', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
        ];

        // 2. Ujian Bahasa Indonesia (13 Oktober 2026 - 14 Siswa Tidak Hadir)
        $indoAbsentList = [
            'P0100320229' => ['name' => 'HANA DANENDRA KASYARA HABIBULLAH', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320185' => ['name' => 'WILDHAN NUR ARIFIN', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320003' => ['name' => 'AIESHA HASNA HABIB', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320007' => ['name' => 'ARKAN ARROYYAN', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320001' => ['name' => 'ABIGAIL XAVIOLA NUGRAHA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320086' => ['name' => 'REYHAN ALFATIR ARSYA PRATISTA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320026' => ['name' => 'NAURA ABEL NIJANANDA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320184' => ['name' => 'WAWA RAHMADANI', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320010' => ['name' => 'CAHYA AULIA WILDA PUTRI', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320240' => ['name' => 'MUHAMMAD TIAN PUTRA ARYARETA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320144' => ['name' => 'MUHAMMAD RAYYAN KHAIRUL AKRAM', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320152' => ['name' => 'SITI ALISYA ALEXANDRA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320040' => ['name' => 'CHAILA AFRILLIA NAFISA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320228' => ['name' => 'FADEL ABBAS AL FAHREZI', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
        ];

        // Sesi Matematika & Bahasa Indonesia
        $mathSessionIds = ExamSession::query()->where('subject', 'like', '%Matematika%')->pluck('id');
        $indoSessionIds = ExamSession::query()->where('subject', 'like', '%Indonesia%')->pluck('id');

        $mathCount = $this->seedAbsences($mathAbsentList, $mathSessionIds);
        $indoCount = $this->seedAbsences($indoAbsentList, $indoSessionIds);

        $this->command?->info("Berhasil mencatat {$mathCount} siswa tidak hadir Matematika dan {$indoCount} siswa tidak hadir Bhs. Indonesia.");
    }

    /**
     * Helper to seed absences for given participant numbers and session IDs.
     *
     * @param  array<string, array{name: string, reason: string, notes: string}>  $absentList
     * @param  Collection<int, int>  $sessionIds
     */
    private function seedAbsences(array $absentList, $sessionIds): int
    {
        $count = 0;
        foreach ($absentList as $nis => $info) {
            $participant = Participant::query()
                ->where('participant_number', $nis)
                ->whereIn('exam_session_id', $sessionIds)
                ->first();

            if ($participant) {
                Attendance::updateOrCreate(
                    [
                        'exam_session_id' => $participant->exam_session_id,
                        'participant_id' => $participant->id,
                    ],
                    [
                        'room_id' => $participant->room_id,
                        'status' => 'tidak_hadir',
                        'reason' => $info['reason'],
                        'notes' => $info['notes'],
                    ]
                );
                $count++;
            }
        }

        return $count;
    }
}
