<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Proctor;
use Illuminate\Database\Seeder;

class ProctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data 4 Proktor
        $proctors = [
            ['name' => 'Qomarudin', 'nbm' => '1042001'],
            ['name' => 'Ida Alma Khairunisa', 'nbm' => '1473659'],
            ['name' => 'Hugeng Seto Pranowo', 'nbm' => '1589828'],
            ['name' => 'Bektiyono', 'nbm' => '1109352'],
        ];

        foreach ($proctors as $data) {
            Proctor::updateOrCreate(
                ['name' => $data['name']],
                [
                    'nbm' => $data['nbm'],
                    'is_active' => true,
                ]
            );
        }

        // 2. Data Kepala Sekolah
        AppSetting::set('headmaster_name', 'Suwahono, S.Pd.', 'Nama Kepala Sekolah / Penanggung Jawab');
        AppSetting::set('headmaster_nip', '960434', 'NBM / NIP Kepala Sekolah');
    }
}
