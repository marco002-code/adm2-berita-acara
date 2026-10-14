<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\ExamSession;
use App\Models\Participant;
use App\Models\Room;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Update App Settings identitas asesmen & sekolah
        AppSetting::set('assessment_name', 'TPM TKA-TKAD TAHAP 1 - KOTA YOGYAKARTA', 'Nama Asesmen / Ujian');
        AppSetting::set('school_name', 'SMP MUHAMMADIYAH 1 YOGYAKARTA', 'Nama Sekolah');
        AppSetting::set('academic_year', '2026/2027', 'Tahun Pelajaran');

        // 2. Siapkan Master Ruangan
        $rooms = [
            'LAB 1' => Room::firstOrCreate(
                ['code' => 'LAB-1'],
                ['name' => 'LAB 1', 'capacity' => 45, 'description' => 'Laboratorium Komputer 1']
            ),
            'LAB 2' => Room::firstOrCreate(
                ['code' => 'LAB-2'],
                ['name' => 'LAB 2', 'capacity' => 45, 'description' => 'Laboratorium Komputer 2']
            ),
            'LAB 3' => Room::firstOrCreate(
                ['code' => 'LAB-3'],
                ['name' => 'LAB 3', 'capacity' => 45, 'description' => 'Laboratorium Komputer 3']
            ),
            'MULTI' => Room::firstOrCreate(
                ['code' => 'MULTI'],
                ['name' => 'MULTI', 'capacity' => 35, 'description' => 'Ruang Multimedia']
            ),
        ];

        // 3. Konfigurasi Jadwal Pelaksanaan Ujian (TKA 75 Menit & TKAD 90 Menit)
        $schedule = [
            [
                'date' => '2026-10-12',
                'subject' => '01 Matematika TKA',
                'description' => 'TKA (75 Menit) - Matematika',
                'sessions' => [
                    ['name' => 'SESI 1', 'start' => '07:30', 'end' => '08:45'],
                    ['name' => 'SESI 2', 'start' => '09:15', 'end' => '10:30'],
                    ['name' => 'SESI 3', 'start' => '11:00', 'end' => '12:15'],
                ],
            ],
            [
                'date' => '2026-10-13',
                'subject' => '02 Bhs. Indonesia TKA',
                'description' => 'TKA (75 Menit) - Bahasa Indonesia',
                'sessions' => [
                    ['name' => 'SESI 1', 'start' => '07:30', 'end' => '08:45'],
                    ['name' => 'SESI 2', 'start' => '09:15', 'end' => '10:30'],
                    ['name' => 'SESI 3', 'start' => '11:00', 'end' => '12:15'],
                ],
            ],
            [
                'date' => '2026-10-14',
                'subject' => '03 Ilmu Pengetahuan Alam TKAD',
                'description' => 'TKAD (90 Menit) - Ilmu Pengetahuan Alam',
                'sessions' => [
                    ['name' => 'SESI 1', 'start' => '07:30', 'end' => '09:00'],
                    ['name' => 'SESI 2', 'start' => '09:30', 'end' => '11:00'],
                ],
            ],
            [
                'date' => '2026-10-15',
                'subject' => '04 Bhs. Inggris TKAD',
                'description' => 'TKAD (90 Menit) - Bahasa Inggris',
                'sessions' => [
                    ['name' => 'SESI 1', 'start' => '07:30', 'end' => '09:00'],
                    ['name' => 'SESI 2', 'start' => '09:30', 'end' => '11:00'],
                ],
            ],
        ];

        // Buat semua objek sesi ujian di database
        $createdSessions = [];
        foreach ($schedule as $day) {
            foreach ($day['sessions'] as $s) {
                $createdSessions[$day['date']][$s['name']] = ExamSession::updateOrCreate(
                    [
                        'name' => $s['name'],
                        'exam_date' => $day['date'],
                        'subject' => $day['subject'],
                    ],
                    [
                        'start_time' => $s['start'],
                        'end_time' => $s['end'],
                        'description' => $day['description'],
                    ]
                );
            }
        }

        // 4. Baca CSV pembagian sesi
        $csvPath = database_path('data/pembagian_sesi.csv');
        if (! file_exists($csvPath)) {
            $this->command?->error("File CSV tidak ditemukan di: {$csvPath}");

            return;
        }

        $file = fopen($csvPath, 'r');
        fgetcsv($file); // Skip header: NO,NOMOR TES,NAMA SISWA,RBL,SESI,RUANG

        $studentsData = [];
        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 6 || empty($row[1])) {
                continue;
            }

            $studentsData[] = [
                'number' => trim($row[1]),
                'name' => trim($row[2]),
                'rombel' => trim($row[3]),
                'session_key' => strtoupper(trim($row[4])), // 'SESI 1' or 'SESI 2'
                'room_key' => trim($row[5]), // 'LAB 1', 'LAB 2', 'LAB 3', 'MULTI'
            ];
        }
        fclose($file);

        // 5. Masukkan peserta ke masing-masing jadwal ujian harian (12 - 15 Oktober 2026)
        $totalCreated = 0;
        foreach ($createdSessions as $date => $sessionMap) {
            foreach ($studentsData as $student) {
                $sessionKey = $student['session_key'];
                $room = $rooms[$student['room_key']] ?? null;
                $session = $sessionMap[$sessionKey] ?? null;

                if ($room && $session) {
                    Participant::updateOrCreate(
                        [
                            'participant_number' => $student['number'],
                            'exam_session_id' => $session->id,
                        ],
                        [
                            'room_id' => $room->id,
                            'name' => $student['name'],
                            'rombel' => $student['rombel'],
                            'school_origin' => 'SMP MUHAMMADIYAH 1 YOGYAKARTA',
                        ]
                    );
                    $totalCreated++;
                }
            }
        }

        $uniqueStudents = count($studentsData);
        $this->command?->info("Berhasil mengimpor {$uniqueStudents} siswa riil ke seluruh 4 hari jadwal pelaksanaan (Total penugasan: {$totalCreated} peserta).");
    }
}
