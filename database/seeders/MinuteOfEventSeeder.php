<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\ExamSession;
use App\Models\MinuteOfEvent;
use App\Models\Room;
use Illuminate\Database\Seeder;

class MinuteOfEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $headmasterName = AppSetting::get('headmaster_name', 'Suwahono, S.Pd.');
        $headmasterNip = AppSetting::get('headmaster_nip', '960434');

        // Master Ruangan
        $roomLab1 = Room::where('name', 'LAB 1')->orWhere('code', 'LAB-1')->first();
        $roomLab2 = Room::where('name', 'LAB 2')->orWhere('code', 'LAB-2')->first();
        $roomLab3 = Room::where('name', 'LAB 3')->orWhere('code', 'LAB-3')->first();
        $roomMulti = Room::where('name', 'MULTI')->orWhere('code', 'MULTI')->first();

        // Default Proktor per Ruangan (Lab 1 = Ida Alma, Lab 2 = Bektiyono, Lab 3 = Hugeng Seto, Multimedia = Qomarudin)
        $proctors = [
            $roomLab1?->id => ['name' => 'Ida Alma Khairunisa', 'nip' => '1473659'],
            $roomLab2?->id => ['name' => 'Bektiyono', 'nip' => '1109352'],
            $roomLab3?->id => ['name' => 'Hugeng Seto Pranowo', 'nip' => '1589828'],
            $roomMulti?->id => ['name' => 'Qomarudin', 'nip' => '1042001'],
        ];

        // Sesi Ujian Matematika
        $mathS1 = ExamSession::where('subject', 'like', '%Matematika%')->where('name', 'like', '%1%')->first();
        $mathS2 = ExamSession::where('subject', 'like', '%Matematika%')->where('name', 'like', '%2%')->first();

        // Sesi Ujian Bahasa Indonesia
        $indoS1 = ExamSession::where('subject', 'like', '%Indonesia%')->where('name', 'like', '%1%')->first();
        $indoS2 = ExamSession::where('subject', 'like', '%Indonesia%')->where('name', 'like', '%2%')->first();

        // Sesi Ujian Bahasa Inggris
        $ingS1 = ExamSession::where('subject', 'like', '%Inggris%')->where('name', 'like', '%1%')->first();
        $ingS2 = ExamSession::where('subject', 'like', '%Inggris%')->where('name', 'like', '%2%')->first();

        // Sesi Ujian IPA
        $ipaS1 = ExamSession::where('subject', 'like', '%Alam%')->where('name', 'like', '%1%')->first();
        $ipaS2 = ExamSession::where('subject', 'like', '%Alam%')->where('name', 'like', '%2%')->first();

        $schedule = [
            // 1. Bahasa Indonesia (Senin, 12 Oktober 2026)
            [
                'session' => $indoS1,
                'date' => '2026-10-12',
                'supervisors' => [
                    $roomLab1?->id => 'Nanik Dwi Hariyani, S.Pd.I., M.S.I.',
                    $roomLab2?->id => 'Zaki Ahmad Solikhan, S.Pd.',
                    $roomLab3?->id => 'Angger Hafid Prabawa, S.Pd.',
                    $roomMulti?->id => 'Hidayatullah Karismu, S.Pd.',
                ],
            ],
            [
                'session' => $indoS2,
                'date' => '2026-10-12',
                'supervisors' => [
                    $roomLab1?->id => 'Siti Nurifah Farida, S.E.',
                    $roomLab2?->id => 'Dinul Ilma Fathur Rozaq, S.Pd.',
                    $roomLab3?->id => 'Oktareza Adinugraha, S.Sn.',
                    $roomMulti?->id => 'Djiyarto, S.Pd.',
                ],
            ],

            // 2. Matematika (Selasa, 13 Oktober 2026)
            [
                'session' => $mathS1,
                'date' => '2026-10-13',
                'supervisors' => [
                    $roomLab1?->id => 'Diyah Mandarsari, S.Pd.',
                    $roomLab2?->id => 'M. Zakky Fathoni, S.Pd.',
                    $roomLab3?->id => 'Rosalina Ananda Setiawan, S.Pd.',
                    $roomMulti?->id => 'Djiyarto, S.Pd.',
                ],
            ],
            [
                'session' => $mathS2,
                'date' => '2026-10-13',
                'supervisors' => [
                    $roomLab1?->id => 'Ni Putu Savitrya M, S.Pd.',
                    $roomLab2?->id => 'Dinul Ilma Fathur Rozaq, S.Ag.',
                    $roomLab3?->id => 'M. Zakky Fathoni, S.Pd.',
                    $roomMulti?->id => 'Oktareza Adinugraha, S.Sn.',
                ],
            ],

            // 3. Bahasa Inggris (Rabu, 14 Oktober 2026)
            [
                'session' => $ingS1,
                'date' => '2026-10-14',
                'supervisors' => [
                    $roomLab1?->id => 'Wahyu Syamrohani, M.Pd.',
                    $roomLab2?->id => 'Salsabila Azahra, S.Pd.',
                    $roomLab3?->id => 'Zaki Ahmad Solikhan, S.Pd.',
                    $roomMulti?->id => 'Hanjar Triyono, S.Pd.',
                ],
            ],
            [
                'session' => $ingS2,
                'date' => '2026-10-14',
                'supervisors' => [
                    $roomLab1?->id => 'Dwi Rohmadi, M.Pd.',
                    $roomLab2?->id => 'Asfandi, S.Pd., M.Pd.',
                    $roomLab3?->id => 'Dinul Ilma Fathur Rozaq, S.Ag.',
                    $roomMulti?->id => 'Salsabila Azahra, S.Pd.',
                ],
            ],

            // 4. IPA (Kamis, 15 Oktober 2026)
            [
                'session' => $ipaS1,
                'date' => '2026-10-15',
                'supervisors' => [
                    $roomLab1?->id => 'Oktareza Adinugraha, S.Pd.',
                    $roomLab2?->id => 'M. Zakky Fathoni, S.Pd.',
                    $roomLab3?->id => 'Lisa Prima Sari, S.Pd.',
                    $roomMulti?->id => 'Hanjar Triyono, S.Pd.',
                ],
            ],
            [
                'session' => $ipaS2,
                'date' => '2026-10-15',
                'supervisors' => [
                    $roomLab1?->id => 'Nailul Izzah, S.Pd.I., M.S.I.',
                    $roomLab2?->id => "Mu'awanna Afifah, S.Pd.Gr.",
                    $roomLab3?->id => 'Zaki Ahmad Solikhan, S.Pd.',
                    $roomMulti?->id => 'Djiyarto, S.Pd.',
                ],
            ],
        ];

        $totalMinutes = 0;
        foreach ($schedule as $item) {
            $session = $item['session'];
            if (! $session) {
                continue;
            }

            foreach ($item['supervisors'] as $roomId => $supervisorName) {
                if (! $roomId || ! $supervisorName) {
                    continue;
                }

                $proctor = $proctors[$roomId] ?? ['name' => 'Proktor Utama', 'nip' => '-'];

                MinuteOfEvent::updateOrCreate(
                    [
                        'room_id' => $roomId,
                        'exam_session_id' => $session->id,
                    ],
                    [
                        'exam_date' => $item['date'] ?? $session->exam_date,
                        'proctor_name' => $proctor['name'],
                        'proctor_nip' => $proctor['nip'],
                        'supervisor_name' => $supervisorName,
                        'supervisor_nip' => null,
                        'headmaster_name' => $headmasterName,
                        'headmaster_nip' => $headmasterNip,
                        'incident_notes' => 'Pelaksanaan ujian berjalan tertib, aman, dan lancar.',
                        'status' => 'submitted',
                    ]
                );
                $totalMinutes++;
            }
        }

        // Pastikan seluruh data Berita Acara mengikuti plotting proktor per ruangan
        foreach ($proctors as $roomId => $proctor) {
            if ($roomId) {
                MinuteOfEvent::where('room_id', $roomId)->update([
                    'proctor_name' => $proctor['name'],
                    'proctor_nip' => $proctor['nip'],
                ]);
            }
        }

        $this->command?->info("Berhasil membuat {$totalMinutes} data Berita Acara dengan plotting proktor baru.");
    }
}
