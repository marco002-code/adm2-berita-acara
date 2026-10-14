<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara - {{ $minute->room->name }} - {{ $minute->examSession->name }}</title>
    @vite(['resources/css/app.css'])
        @media print {
            .no-print { display: none !important; }
            body { 
                background: white !important; 
                color: black !important;
                font-size: 11pt !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 15mm 12mm 15mm;
            }
            .page-break { page-break-after: always; }
        }
        .qr-code-box img {
            display: block !important;
            margin: 0 auto !important;
        }
        .qr-code-box canvas {
            margin: 0 auto !important;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-serif min-h-screen p-4 sm:p-8 print:p-0">

    <!-- Action Bar (Hidden when printed) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <a href="{{ route('minutes.index', ['exam_session_id' => $minute->exam_session_id, 'room_id' => $minute->room_id]) }}" 
           class="inline-flex items-center text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali ke Formulir
        </a>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" 
                    class="px-5 py-2 rounded-lg bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm shadow transition">
                🖨️ Cetak Dokumen (Print)
            </button>
        </div>
    </div>

    <!-- Official Document Paper -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 shadow-md print:shadow-none border border-slate-200 print:border-none min-h-[297mm]">

        <!-- Kop Dokumen Resmi -->
        <div class="text-center border-b-2 border-black pb-3 mb-5">
            <h1 class="text-base sm:text-lg font-bold uppercase tracking-wider">BERITA ACARA PELAKSANAAN</h1>
            <h2 class="text-sm sm:text-base font-bold uppercase tracking-wide">{{ \App\Models\AppSetting::get('assessment_name', 'TPM TKA-TKAD TAHAP 1 - KOTA YOGYAKARTA') }}</h2>
            <h3 class="text-xs sm:text-sm font-bold uppercase">{{ \App\Models\AppSetting::get('school_name', 'SMP MUHAMMADIYAH 1 YOGYAKARTA') }}</h3>
            @php
                $academicYearSetting = \App\Models\AppSetting::get('academic_year', '2026/2027');
                $academicYearDisplay = str_starts_with(strtoupper($academicYearSetting), 'TAHUN') ? $academicYearSetting : 'TAHUN AJARAN ' . $academicYearSetting;
            @endphp
            <p class="text-xs font-semibold mt-0.5 uppercase tracking-wide">{{ $academicYearDisplay }}</p>
        </div>

        <!-- Narasi Pembuka -->
        <div class="text-xs sm:text-sm leading-relaxed mb-4 text-justify">
            <p>
                Pada hari ini, <strong>{{ \Carbon\Carbon::parse($minute->exam_date)->locale('id')->isoFormat('dddd') }}</strong> 
                tanggal <strong>{{ \Carbon\Carbon::parse($minute->exam_date)->locale('id')->isoFormat('D MMMM Y') }}</strong>, 
                telah diselenggarakan <strong>{{ \App\Models\AppSetting::get('assessment_name', 'TPM TKA-TKAD TAHAP 1 - KOTA YOGYAKARTA') }}</strong> bertempat di <strong>{{ \App\Models\AppSetting::get('school_name', 'SMP MUHAMMADIYAH 1 YOGYAKARTA') }}</strong> dengan rincian pelaksanaan sebagai berikut:
            </p>
        </div>

        <!-- Rincian Pelaksanaan -->
        <table class="w-full text-xs sm:text-sm mb-5 border-collapse">
            <tbody>
                <tr class="align-top">
                    <td class="w-48 py-1 font-semibold">Ruangan Ujian</td>
                    <td class="w-4 py-1">:</td>
                    <td class="py-1">{{ $minute->room->name }} (Kode: {{ $minute->room->code }})</td>
                </tr>
                <tr class="align-top">
                    <td class="py-1 font-semibold">Sesi Pelaksanaan</td>
                    <td class="py-1">:</td>
                    <td class="py-1">{{ $minute->examSession->name }} (Pukul {{ $minute->examSession->start_time }} s.d. {{ $minute->examSession->end_time }})</td>
                </tr>
                <tr class="align-top">
                    <td class="py-1 font-semibold">Materi / Mata Uji</td>
                    <td class="py-1">:</td>
                    <td class="py-1">{{ $minute->examSession->subject }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Tabel Statistik Kehadiran -->
        <div class="mb-5">
            <h4 class="text-xs sm:text-sm font-bold uppercase mb-2">I. Data Kehadiran Peserta</h4>
            <table class="w-full text-xs sm:text-sm border border-black border-collapse text-center">
                <thead>
                    <tr class="bg-slate-100 print:bg-slate-100 border-b border-black font-bold">
                        <th class="border-r border-black py-2 px-3">Jumlah Seharusnya Hadir</th>
                        <th class="border-r border-black py-2 px-3">Jumlah Hadir</th>
                        <th class="py-2 px-3">Jumlah Tidak Hadir</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="font-bold text-base">
                        <td class="border-r border-black py-2 px-3">{{ $totalExpected }} Orang</td>
                        <td class="border-r border-black py-2 px-3">{{ $totalPresent }} Orang</td>
                        <td class="py-2 px-3">{{ $totalAbsent }} Orang</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Rincian Peserta Tidak Hadir -->
        <div class="mb-5">
            <h4 class="text-xs sm:text-sm font-bold uppercase mb-2">II. Rincian Peserta Tidak Hadir</h4>
            @if($absentAttendances->isEmpty())
                <p class="text-xs sm:text-sm italic pl-2">- Seluruh peserta hadir lengkap (Nihil) -</p>
            @else
                <table class="w-full text-xs sm:text-sm border border-black border-collapse">
                    <thead>
                        <tr class="bg-slate-100 print:bg-slate-100 border-b border-black font-bold text-left">
                            <th class="border-r border-black py-1.5 px-2 w-10 text-center">No</th>
                            <th class="border-r border-black py-1.5 px-3 w-36">Nomor Peserta</th>
                            <th class="border-r border-black py-1.5 px-3">Nama Siswa</th>
                            <th class="border-r border-black py-1.5 px-2 w-20 text-center">Kelas</th>
                            <th class="border-r border-black py-1.5 px-3 w-32">Keterangan / Alasan</th>
                            <th class="py-1.5 px-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absentAttendances as $idx => $att)
                            <tr class="border-b border-black">
                                <td class="border-r border-black py-1 px-2 text-center">{{ $idx + 1 }}</td>
                                <td class="border-r border-black py-1 px-3 font-mono">{{ $att->participant?->participant_number }}</td>
                                <td class="border-r border-black py-1 px-3">{{ $att->participant?->name }}</td>
                                <td class="border-r border-black py-1 px-2 text-center">{{ $att->participant?->rombel ?? '-' }}</td>
                                <td class="border-r border-black py-1 px-3">{{ $att->reason }}</td>
                                <td class="py-1 px-3">{{ $att->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Catatan Pelaksanaan / Insiden -->
        <div class="mb-6">
            <h4 class="text-xs sm:text-sm font-bold uppercase mb-1">III. Catatan Khusus Pelaksanaan Ujian</h4>
            <div class="p-3 border border-black rounded text-xs sm:text-sm min-h-[48px] leading-relaxed">
                {{ $minute->incident_notes ?? 'Pelaksanaan ujian berjalan tertib, lancar, dan aman.' }}
            </div>
        </div>

        <!-- Narasi Penutup -->
        <div class="text-xs sm:text-sm leading-relaxed mb-4 text-justify">
            <p>
                Demikian Berita Acara ini dibuat dengan sesungguhnya dan ditandatangani secara sah oleh Pengawas, Proktor Ruang, dan Kepala Sekolah untuk dipergunakan sebagaimana mestinya.
            </p>
        </div>

        <!-- Titimangsa / Tempat & Tanggal -->
        <div class="flex justify-end text-xs sm:text-sm mb-3 pr-2">
            <p>Yogyakarta, {{ \Carbon\Carbon::parse($minute->exam_date)->locale('id')->isoFormat('D MMMM Y') }}</p>
        </div>

        <!-- Bagian Tanda Tangan QR Otomatis -->
        @php
            $headmasterName = $minute->headmaster_name ?: \App\Models\AppSetting::get('headmaster_name', 'Suwahono, S.Pd.');
            $headmasterNbm = $minute->headmaster_nip ?: \App\Models\AppSetting::get('headmaster_nip', '960434');

            $formatNbm = function(?string $nip): string {
                $trimmed = trim((string) $nip);
                if ($trimmed === '' || $trimmed === '-') {
                    return 'NBM. -';
                }
                if (str_starts_with(strtoupper($trimmed), 'NBM') || str_starts_with(strtoupper($trimmed), 'NIP')) {
                    return $trimmed;
                }
                return 'NBM. ' . $trimmed;
            };
        @endphp

        <div class="grid grid-cols-3 gap-4 text-xs sm:text-sm text-center mb-6">
            <!-- 1. Pengawas -->
            <div class="flex flex-col items-center">
                <p class="font-bold">Pengawas,</p>
                <div class="my-2 p-1.5 bg-white border border-slate-300 rounded-lg inline-block shadow-sm print:shadow-none print:border-black">
                    <div id="qr-supervisor" class="qr-code-box w-[88px] h-[88px] flex items-center justify-center"></div>
                </div>
                <p class="font-bold underline uppercase text-xs sm:text-sm tracking-wide">{{ $minute->supervisor_name }}</p>
                <p class="text-[11px] sm:text-xs text-slate-700 font-medium mt-0.5">{{ $formatNbm($minute->supervisor_nip) }}</p>
            </div>

            <!-- 2. Proktor Ruang -->
            <div class="flex flex-col items-center">
                <p class="font-bold">Proktor Ruang,</p>
                <div class="my-2 p-1.5 bg-white border border-slate-300 rounded-lg inline-block shadow-sm print:shadow-none print:border-black">
                    <div id="qr-proctor" class="qr-code-box w-[88px] h-[88px] flex items-center justify-center"></div>
                </div>
                <p class="font-bold underline uppercase text-xs sm:text-sm tracking-wide">{{ $minute->proctor_name }}</p>
                <p class="text-[11px] sm:text-xs text-slate-700 font-medium mt-0.5">{{ $formatNbm($minute->proctor_nip) }}</p>
            </div>

            <!-- 3. Kepala Sekolah -->
            <div class="flex flex-col items-center">
                <p class="font-bold">Kepala Sekolah,</p>
                <div class="my-2 p-1.5 bg-white border border-slate-300 rounded-lg inline-block shadow-sm print:shadow-none print:border-black">
                    <div id="qr-headmaster" class="qr-code-box w-[88px] h-[88px] flex items-center justify-center"></div>
                </div>
                <p class="font-bold underline uppercase text-xs sm:text-sm tracking-wide">{{ $headmasterName }}</p>
                <p class="text-[11px] sm:text-xs text-slate-700 font-medium mt-0.5">{{ $formatNbm($headmasterNbm) }}</p>
            </div>
        </div>

    </div>

    <!-- Script Pembuat QR Code Otomatis -->
    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    <script>
        function generateSignatureQRCodes() {
            if (typeof QRCode === 'undefined') {
                console.warn('QRCode library not ready yet.');
                return;
            }

            const schoolName = @json(\App\Models\AppSetting::get('school_name', 'SMP MUHAMMADIYAH 1 YOGYAKARTA'));
            const assessmentName = @json(\App\Models\AppSetting::get('assessment_name', 'TPM TKA-TKAD TAHAP 1 - KOTA YOGYAKARTA'));
            const examDate = @json(\Carbon\Carbon::parse($minute->exam_date)->locale('id')->isoFormat('D MMMM Y'));
            const roomName = @json($minute->room->name);
            const sessionName = @json($minute->examSession->name);

            const signers = [
                {
                    id: 'qr-supervisor',
                    role: 'Pengawas',
                    name: @json($minute->supervisor_name),
                    nbm: @json($minute->supervisor_nip ?: '-')
                },
                {
                    id: 'qr-proctor',
                    role: 'Proktor Ruang',
                    name: @json($minute->proctor_name),
                    nbm: @json($minute->proctor_nip ?: '-')
                },
                {
                    id: 'qr-headmaster',
                    role: 'Kepala Sekolah',
                    name: @json($headmasterName),
                    nbm: @json($headmasterNbm ?: '-')
                }
            ];

            signers.forEach(function(s) {
                const container = document.getElementById(s.id);
                if (!container) return;
                container.innerHTML = '';

                const payload = [
                    'TANDA TANGAN ELEKTRONIK SAH',
                    'Jabatan: ' + s.role,
                    'Nama: ' + s.name,
                    'NBM: ' + s.nbm,
                    'Satuan Pendidikan: ' + schoolName,
                    'Agenda: ' + assessmentName,
                    'Tanggal: ' + examDate,
                    'Ruang: ' + roomName + ' | Sesi: ' + sessionName
                ].join('\n');

                try {
                    new QRCode(container, {
                        text: payload,
                        width: 88,
                        height: 88,
                        colorDark: '#000000',
                        colorLight: '#ffffff',
                        correctLevel: QRCode.CorrectLevel.M
                    });
                } catch (err) {
                    console.error('Error generating QR for ' + s.id, err);
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', generateSignatureQRCodes);
        } else {
            generateSignatureQRCodes();
        }
    </script>
</body>
</html>
