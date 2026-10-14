<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = [
            ['1', '1', 'Suwahono, S.Pd.'],
            ['2', '2', 'Hidayatullah Karismu P, S.Pd.'],
            ['3', '3', 'Siti Aminah, S.Pd.'],
            ['4', '4', 'Siti Nur Farida, S.E.'],
            ['5', '5', 'Suratinem, S.Pd.'],
            ['6', '6', 'Arif Afgani, S.Ag.'],
            ['7', '7', 'Asfandi, S.Pd., M.Pd.'],
            ['8', '8', 'Bektiyono, S.T'],
            ['9', '9', 'Feri Widayanti, S.Pd.'],
            ['10', '10', 'Hanjar Triyono, S.Pd.'],
            ['11', '11', 'Djiyarto, S.Pd.'],
            ['12', '12', 'Nanik Dwi Hariyani, S.Pd.I., M.S.I.'],
            ['13', '13', 'Sri Rokhmaniyati, S.SI, M.Pd.'],
            ['14', '14', 'Diyah Mandarsari, S.Pd.'],
            ['15', '15', 'Lusia Arisandi, S.H.I.'],
            ['16', '16', 'Wahyu Syamrohani, S.Pd.'],
            ['17', '17', 'Dwi Setyo Prihantini, S.Pd.'],
            ['18', '18', 'Qomarudin, S.T.'],
            ['19', '19', 'Sonny Irawan, S.Pd.'],
            ['20', '20', 'Yunita Sari, S.Pd.'],
            ['21', '21', 'Miftahul Cahyaningsih, M.Pd.'],
            ['22', '22', 'Dwi Rohmadi, S.Pd., M.Pd.'],
            ['23', '23', 'Nailu Izzah, S.Pd.I., M.S.I.'],
            ['24', '24', 'Muhammad Arifin, S.Pd.'],
            ['25', '25', 'M. Zakky Fathoni, S.Pd.'],
            ['26', '26', 'Oktareza Adinugraha, S.Sn.'],
            ['27', '27', 'Akhmad Fahrizal, M.Pd.'],
            ['28', '28', 'Zaky Akhmad Solihan, S.Pd.'],
            ['29', '29', 'Aditya Wirawan Rasyid, S.Pd.'],
            ['30', '30', 'Halim Nur Zaky, S.Pd.'],
            ['31', '31', 'Norma Yuni Praptiwi, S.Pd.'],
            ['32', '32', 'Septiana Retno Kurniasari, S.Pd.'],
            ['33', '33', 'Asnah Sari Dewi, S.Pd.'],
            ['34', '34', 'Lisa Prima Sari, S.Pd.'],
            ['35', '35', 'Dinul Ilma Fathur Rozaq, S.Ag.'],
            ['36', '36', 'Aida Nur Asyifa, S.Pd'],
            ['37', '37', 'Ni Putu Savitrya Maheswari, S.Pd.'],
            ['38', '38', 'Salsabilla Azzahra, S.Pd'],
            ['39', '39', 'Annisa Rani Anggayasti, S.Pd.'],
            ['40', '40', "Mu'awana Afifah, S.Pd., Gr."],
            ['41', '41', 'Hugeng Seto Pranowo, S.Kom.'],
            ['42', '42', 'Angger Hafid Prabawa, S.Pd.'],
            ['43', '43', 'Rosalina Ananda Setiawan, S.Pd.'],
            ['44', '44', 'Aji Fajar Setiawan, S.Pd.'],
        ];

        // Akun Admin Khusus
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator / Proktor Utama',
                'email' => 'admin@sekolah.sch.id',
                'password' => Hash::make('admin'),
                'is_admin' => true,
            ]
        );

        foreach ($teachers as [$username, $password, $name]) {
            User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => $name,
                    'email' => "guru{$username}@sekolah.sch.id",
                    'password' => Hash::make($password),
                    'is_admin' => ($username === '1'), // Guru nomor 1 juga default sebagai admin
                ]
            );
        }

        // Pengaturan Awal Asesmen / Ujian
        AppSetting::set('assessment_name', 'Asesmen Nasional Berbasis Komputer (ANBK)', 'Nama Asesmen / Ujian');
        AppSetting::set('academic_year', date('Y').'/'.(date('Y') + 1), 'Tahun Ajaran');
        AppSetting::set('school_name', 'SMA Negeri 1 Indonesia Merdeka', 'Nama Satuan Pendidikan');
        AppSetting::set('headmaster_name', 'Drs. H. Ahmad Dahlan, M.M.', 'Nama Kepala Sekolah / Penanggung Jawab');
        AppSetting::set('headmaster_nip', '196803201995031001', 'NIP Kepala Sekolah');
    }
}
