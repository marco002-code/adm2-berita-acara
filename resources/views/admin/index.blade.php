@extends('layouts.app')

@section('title', 'Panel Admin - Pengaturan Asesmen, Sesi & Ruangan')

@section('content')
<div class="space-y-6">

    <!-- Header Panel Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">Panel Administrator</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">{{ Auth::user()->name }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Pengaturan Sistem Ujian</h1>
            <p class="text-xs text-slate-500">Konfigurasi nama asesmen, jumlah & jadwal sesi, serta master ruangan ujian.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 text-xs font-semibold shadow-sm transition">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Tabs Navigation -->
    @php
        $activeTab = request('tab', 'settings');
    @endphp
    <div class="flex border-b border-slate-200 space-x-2 sm:space-x-6 overflow-x-auto text-sm font-semibold">
        <a href="{{ route('admin.index', ['tab' => 'settings']) }}" 
           class="py-3 px-2 border-b-2 whitespace-nowrap transition {{ $activeTab === 'settings' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            ⚙️ 1. Nama Asesmen & Sekolah
        </a>
        <a href="{{ route('admin.index', ['tab' => 'sessions']) }}" 
           class="py-3 px-2 border-b-2 whitespace-nowrap transition {{ $activeTab === 'sessions' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            ⏰ 2. Jumlah & Jadwal Sesi ({{ $sessions->count() }})
        </a>
        <a href="{{ route('admin.index', ['tab' => 'rooms']) }}" 
           class="py-3 px-2 border-b-2 whitespace-nowrap transition {{ $activeTab === 'rooms' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            🏛️ 3. Nama Ruangan ({{ $rooms->count() }})
        </a>
        <a href="{{ route('admin.index', ['tab' => 'proctors']) }}" 
           class="py-3 px-2 border-b-2 whitespace-nowrap transition {{ $activeTab === 'proctors' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            👨‍💻 4. Data Proktor ({{ $proctors->count() }})
        </a>
        <a href="{{ route('admin.index', ['tab' => 'teachers']) }}" 
           class="py-3 px-2 border-b-2 whitespace-nowrap transition {{ $activeTab === 'teachers' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            👥 5. Akun Guru ({{ $teachers->count() }})
        </a>
        <a href="{{ route('admin.index', ['tab' => 'participants']) }}" 
           class="py-3 px-2 border-b-2 whitespace-nowrap transition {{ $activeTab === 'participants' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            🎓 6. Data Peserta ({{ $participantsCount }})
        </a>
        <a href="{{ route('admin.index', ['tab' => 'minutes']) }}" 
           class="py-3 px-2 border-b-2 whitespace-nowrap transition {{ $activeTab === 'minutes' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            📋 7. Berita Acara Terisi ({{ $totalFilledMinutesCount }})
        </a>
        <a href="{{ route('admin.index', ['tab' => 'attendances']) }}" 
           class="py-3 px-2 border-b-2 whitespace-nowrap transition {{ $activeTab === 'attendances' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            📊 8. Rekap Presensi Terisi ({{ $attendanceRecaps->count() }})
        </a>
    </div>

    <!-- TAB 1: PENGATURAN NAMA ASESMEN & SEKOLAH -->
    @if($activeTab === 'settings')
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm max-w-3xl">
            <div class="mb-6">
                <h2 class="text-base font-bold text-slate-800">Identitas Asesmen / Ujian</h2>
                <p class="text-xs text-slate-500">Data ini akan otomatis tercantum pada kop Berita Acara & Laporan resmi.</p>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Nama Asesmen / Ujian <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="assessment_name" value="{{ old('assessment_name', $settings['assessment_name']) }}" required
                           placeholder="Contoh: Asesmen Nasional Berbasis Komputer (ANBK)"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Tahun Ajaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="academic_year" value="{{ old('academic_year', $settings['academic_year']) }}" required
                               placeholder="2026/2027"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Nama Sekolah / Satuan Pendidikan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="school_name" value="{{ old('school_name', $settings['school_name']) }}" required
                               placeholder="SMA Negeri 1 Indonesia Merdeka"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Penanggung Jawab / Kepala Sekolah Default</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Nama Kepala Sekolah</label>
                            <input type="text" name="headmaster_name" value="{{ old('headmaster_name', $settings['headmaster_name']) }}"
                                   placeholder="Drs. H. Ahmad Dahlan, M.M."
                                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">NIP Kepala Sekolah</label>
                            <input type="text" name="headmaster_nip" value="{{ old('headmaster_nip', $settings['headmaster_nip']) }}"
                                   placeholder="1968xxxxxxxxxxxxxx"
                                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition">
                        Simpan Pengaturan Asesmen
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 2: PENGATURAN JUMLAH & JADWAL SESI -->
    @if($activeTab === 'sessions')
        <div class="space-y-6">
            <!-- Generator Otomatis Jumlah Sesi -->
            <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center space-x-2 text-indigo-900 font-bold text-sm mb-1">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Generator Cepat Jumlah Sesi</span>
                </div>
                <p class="text-xs text-indigo-700 mb-4">Buat sesi ujian sekaligus berdasarkan jumlah sesi yang Anda tentukan.</p>

                <form method="POST" action="{{ route('admin.sessions.generate') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-indigo-800 mb-1">Jumlah Sesi</label>
                        <select name="session_count" class="w-full rounded-xl border border-indigo-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                            <option value="1">1 Sesi</option>
                            <option value="2">2 Sesi</option>
                            <option value="3" selected>3 Sesi (Standar ANBK)</option>
                            <option value="4">4 Sesi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-indigo-800 mb-1">Tanggal Pelaksanaan</label>
                        <input type="date" name="exam_date" value="{{ date('Y-m-d') }}" required
                               class="w-full rounded-xl border border-indigo-200 bg-white px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-indigo-800 mb-1">Mata Uji</label>
                        <input type="text" name="subject" value="Literasi Membaca & Survei Karakter" required
                               class="w-full rounded-xl border border-indigo-200 bg-white px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow transition">
                            Generate Sesi Otomatis &rarr;
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Tambah Sesi Manual -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm h-fit">
                    <h3 class="text-sm font-bold text-slate-800 mb-3">Tambah Sesi Baru Manual</h3>
                    <form method="POST" action="{{ route('admin.sessions.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Nama Sesi (misal: Sesi 1)</label>
                            <input type="text" name="name" required placeholder="Sesi 1" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal</label>
                            <input type="date" name="exam_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Mulai</label>
                                <input type="text" name="start_time" value="07:30" required placeholder="07:30" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Selesai</label>
                                <input type="text" name="end_time" value="09:30" required placeholder="09:30" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Mata Uji</label>
                            <input type="text" name="subject" required placeholder="Literasi Membaca" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Keterangan</label>
                            <input type="text" name="description" placeholder="ANBK Hari ke-1" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow transition mt-2">
                            + Tambah Sesi
                        </button>
                    </form>
                </div>

                <!-- Tabel Daftar Sesi -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 mb-3">Daftar Sesi Ujian Tersedia ({{ $sessions->count() }})</h3>
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase tracking-wider">
                                <tr>
                                    <th class="py-2.5 px-3">Nama Sesi</th>
                                    <th class="py-2.5 px-3">Tanggal & Waktu</th>
                                    <th class="py-2.5 px-3">Mata Uji</th>
                                    <th class="py-2.5 px-3 text-center">Peserta</th>
                                    <th class="py-2.5 px-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($sessions as $session)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-2 px-3 font-bold text-slate-800">{{ $session->name }}</td>
                                        <td class="py-2 px-3">
                                            <div>{{ $session->exam_date->format('d/m/Y') }}</div>
                                            <span class="text-slate-400 font-mono">{{ $session->start_time }} - {{ $session->end_time }}</span>
                                        </td>
                                        <td class="py-2 px-3 text-slate-700">{{ $session->subject }}</td>
                                        <td class="py-2 px-3 text-center font-bold text-slate-600">{{ $session->participants_count }}</td>
                                        <td class="py-2 px-3 text-right">
                                            <form method="POST" action="{{ route('admin.sessions.destroy', $session) }}" onsubmit="return confirm('Hapus sesi ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-slate-400">Belum ada sesi ujian.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 3: PENGATURAN NAMA RUANGAN -->
    @if($activeTab === 'rooms')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Tambah Ruangan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm h-fit">
                <h3 class="text-sm font-bold text-slate-800 mb-3">Tambah Ruangan Baru</h3>
                <form method="POST" action="{{ route('admin.rooms.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Nama Ruangan <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Laboratorium Komputer 3" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Kode Ruangan <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" required placeholder="LAB-03" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Kapasitas Peserta (Siswa)</label>
                        <input type="number" name="capacity" value="30" min="1" required class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Keterangan / Lokasi</label>
                        <input type="text" name="description" placeholder="Gedung C Lantai 2" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="w-full py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow transition mt-2">
                        + Tambah Ruangan
                    </button>
                </form>
            </div>

            <!-- Tabel Daftar Ruangan -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 mb-3">Daftar Ruangan Ujian ({{ $rooms->count() }})</h3>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase tracking-wider">
                            <tr>
                                <th class="py-2.5 px-3">Kode</th>
                                <th class="py-2.5 px-3">Nama Ruangan</th>
                                <th class="py-2.5 px-3">Kapasitas</th>
                                <th class="py-2.5 px-3">Lokasi / Keterangan</th>
                                <th class="py-2.5 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rooms as $room)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2.5 px-3 font-mono font-bold text-blue-600">{{ $room->code }}</td>
                                    <td class="py-2.5 px-3 font-bold text-slate-800">{{ $room->name }}</td>
                                    <td class="py-2.5 px-3">{{ $room->capacity }} Kursi</td>
                                    <td class="py-2.5 px-3 text-slate-500">{{ $room->description ?? '-' }}</td>
                                    <td class="py-2.5 px-3 text-right">
                                        <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('Hapus ruangan ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-slate-400">Belum ada ruangan ujian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 4: DATA PROKTOR -->
    @if($activeTab === 'proctors')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Tambah Proktor -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm h-fit">
                <h3 class="text-sm font-bold text-slate-800 mb-1">Tambah Data Proktor</h3>
                <p class="text-xs text-slate-500 mb-3">Data proktor dapat dipilih saat pembuatan Berita Acara.</p>
                <form method="POST" action="{{ route('admin.proctors.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Nama Proktor <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Qomarudin" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">NBM (Nomor Baku Muhammadiyah)</label>
                        <input type="text" name="nbm" placeholder="Contoh: 1042001" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">NIP (jika ada)</label>
                        <input type="text" name="nip" placeholder="198xxxxxxxxxxxxxxx" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">No. HP / WhatsApp (opsional)</label>
                        <input type="text" name="phone" placeholder="08xxxxxxxxxx" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="w-full py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow transition mt-2">
                        + Tambah Proktor
                    </button>
                </form>
            </div>

            <!-- Tabel Daftar Proktor -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Daftar Proktor Ujian ({{ $proctors->count() }})</h3>
                        <p class="text-xs text-slate-500">Data proktor yang aktif untuk penugasan ruangan & sesi.</p>
                    </div>
                </div>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase tracking-wider">
                            <tr>
                                <th class="py-2.5 px-3 w-12 text-center">No</th>
                                <th class="py-2.5 px-3">Nama Proktor</th>
                                <th class="py-2.5 px-3">NBM / NIP</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($proctors as $index => $proctor)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono">{{ $index + 1 }}</td>
                                    <td class="py-2.5 px-3 font-bold text-slate-800">{{ $proctor->name }}</td>
                                    <td class="py-2.5 px-3 font-mono text-slate-600">
                                        {{ $proctor->nbm ? 'NBM: ' . $proctor->nbm : ($proctor->nip ? 'NIP: ' . $proctor->nip : '-') }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">Aktif</span>
                                    </td>
                                    <td class="py-2.5 px-3 text-right">
                                        <form method="POST" action="{{ route('admin.proctors.destroy', $proctor) }}" onsubmit="return confirm('Hapus data proktor ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-slate-400">Belum ada data proktor.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 5: DAFTAR AKUN GURU (44 GURU) -->
    @if($activeTab === 'teachers')
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Daftar Akun Guru & Pengawas ({{ $teachers->count() }} Guru)</h3>
                    <p class="text-xs text-slate-500">Gunakan ID User dan Password di bawah untuk login ke sistem.</p>
                </div>
            </div>

            <div class="border border-slate-200 rounded-xl overflow-hidden max-h-[600px] overflow-y-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase tracking-wider sticky top-0">
                        <tr>
                            <th class="py-2.5 px-3 w-16 text-center">No. ID</th>
                            <th class="py-2.5 px-3 w-28">Username</th>
                            <th class="py-2.5 px-3 w-28">Password</th>
                            <th class="py-2.5 px-4">Nama Lengkap Guru</th>
                            <th class="py-2.5 px-3 text-center">Hak Akses</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($teachers as $index => $teacher)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 px-3 text-center text-slate-400 font-mono">{{ $index + 1 }}</td>
                                <td class="py-2 px-3 font-mono font-bold text-blue-600 bg-blue-50/40">{{ $teacher->username }}</td>
                                <td class="py-2 px-3 font-mono text-slate-600 bg-slate-50/60">{{ $teacher->username }}</td>
                                <td class="py-2 px-4 font-semibold text-slate-800">{{ $teacher->name }}</td>
                                <td class="py-2 px-3 text-center">
                                    @if($teacher->is_admin)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700">Admin / Proktor</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">Pengawas</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 6: DATA PESERTA UJIAN -->
    @if($activeTab === 'participants')
        <div class="space-y-4">
            <!-- Mode Selector & Stats Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <span>👥 Pengelolaan Data Peserta Ujian</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $viewMode === 'master' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $viewMode === 'master' ? 'Mode Master Siswa' : 'Mode Penugasan Sesi' }}
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if($viewMode === 'master')
                            Menampilkan data induk siswa unik (<strong>{{ $uniqueStudentsCount }} siswa</strong>), bebas duplikasi.
                        @else
                            Menampilkan penugasan siswa per sesi & mata ujian (total <strong>{{ $totalAssignmentsCount }} jadwal</strong>).
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl shrink-0">
                    <a href="{{ route('admin.index', ['tab' => 'participants', 'view_mode' => 'master']) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $viewMode === 'master' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                        📋 Master Siswa ({{ $uniqueStudentsCount }})
                    </a>
                    <a href="{{ route('admin.index', ['tab' => 'participants', 'view_mode' => 'all']) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $viewMode !== 'master' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                        📅 Jadwal Per Sesi ({{ $totalAssignmentsCount }})
                    </a>
                </div>
            </div>

            <!-- Filter & Search Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <form method="GET" action="{{ route('admin.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <input type="hidden" name="tab" value="participants">
                    <input type="hidden" name="view_mode" value="{{ $viewMode }}">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Filter Ruangan
                        </label>
                        <select name="filter_room" onchange="this.form.submit()" 
                                class="w-full rounded-xl border border-slate-200 text-xs px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Ruangan</option>
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}" {{ request('filter_room') == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }} ({{ $r->participants_count }} siswa)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Filter Sesi Ujian
                        </label>
                        <select name="filter_session" onchange="this.form.submit()" 
                                class="w-full rounded-xl border border-slate-200 text-xs px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Sesi Ujian</option>
                            @php
                                $groupedSessions = $sessions->groupBy(fn ($s) => $s->exam_date->format('Y-m-d').' - '.$s->subject);
                            @endphp
                            @foreach($groupedSessions as $sessionList)
                                @php
                                    $firstSession = $sessionList->first();
                                    $dateTitle = \Carbon\Carbon::parse($firstSession->exam_date)->locale('id')->isoFormat('D MMM');
                                @endphp
                                <optgroup label="📅 {{ $dateTitle }} • {{ $firstSession->subject }}">
                                    @foreach($sessionList as $s)
                                        <option value="{{ $s->id }}" {{ request('filter_session') == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }} ({{ $s->participants_count }} siswa)
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Cari Siswa
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, nomor, kelas..." 
                                class="w-full rounded-xl border border-slate-200 text-xs px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-900 transition">
                            Terapkan Filter
                        </button>
                        <a href="{{ route('admin.index', ['tab' => 'participants', 'view_mode' => $viewMode]) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs hover:bg-slate-50 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tabel Peserta -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">
                            {{ $viewMode === 'master' ? 'Data Induk Siswa Terdaftar' : 'Daftar Penugasan Peserta Sesi Ujian' }}
                        </h3>
                        <p class="text-xs text-slate-500">
                            Menampilkan {{ $participants->total() }} data {{ $viewMode === 'master' ? 'siswa unik' : 'penugasan jadwal' }}.
                        </p>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden overflow-x-auto text-xs">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase tracking-wider">
                            <tr>
                                <th class="py-2.5 px-3 w-12 text-center">No</th>
                                <th class="py-2.5 px-3 w-36">No. Peserta</th>
                                <th class="py-2.5 px-4">Nama Siswa</th>
                                <th class="py-2.5 px-3 w-20 text-center">Kelas</th>
                                <th class="py-2.5 px-3 w-28">Ruangan</th>
                                <th class="py-2.5 px-3 w-48">{{ $viewMode === 'master' ? 'Sesi Pokok' : 'Sesi & Mata Uji' }}</th>
                                <th class="py-2.5 px-3 w-24 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($participants as $idx => $p)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2 px-3 text-center text-slate-400 font-mono">
                                        {{ $participants->firstItem() + $idx }}
                                    </td>
                                    <td class="py-2 px-3 font-mono font-bold text-slate-700">
                                        {{ $p->participant_number }}
                                    </td>
                                    <td class="py-2 px-4 font-semibold text-slate-800">
                                        {{ $p->name }}
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                            {{ $p->rombel ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">
                                            {{ $p->room?->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">
                                            {{ $p->examSession?->name ?? '-' }}
                                        </span>
                                        @if($viewMode === 'master')
                                            <span class="block text-[10px] text-slate-400 mt-0.5">Semua hari ujian</span>
                                        @else
                                            <div class="text-[10px] text-slate-500 mt-0.5 font-medium">
                                                📅 {{ \Carbon\Carbon::parse($p->examSession?->exam_date)->locale('id')->isoFormat('D MMM') }} • {{ $p->examSession?->subject }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <button type="button" 
                                                onclick="editParticipant({{ $p->id }}, '{{ addslashes($p->participant_number) }}', '{{ addslashes($p->name) }}', '{{ addslashes($p->rombel ?? '') }}', {{ $p->room_id }}, {{ $p->exam_session_id }})"
                                                class="px-2.5 py-1 text-[11px] font-semibold text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition">
                                            Ubah
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400">
                                        Tidak ada data peserta yang cocok dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($participants->hasPages())
                    <div class="mt-4">
                        {{ $participants->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Edit Peserta -->
        <div id="modal-edit-participant" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-sm">Edit Data Peserta</h3>
                    <button onclick="closeParticipantModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>
                <form id="form-edit-participant" method="POST" action="" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Nomor Peserta</label>
                        <input type="text" name="participant_number" id="edit_p_number" required 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Nama Siswa</label>
                        <input type="text" name="name" id="edit_p_name" required 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Kelas / Rombel</label>
                        <input type="text" name="rombel" id="edit_p_rombel" 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Ruangan</label>
                            <select name="room_id" id="edit_p_room" required class="w-full rounded-lg border border-slate-200 px-3 py-2">
                                @foreach($rooms as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Sesi</label>
                            <select name="exam_session_id" id="edit_p_session" required class="w-full rounded-lg border border-slate-200 px-3 py-2">
                                @foreach($sessions as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 pt-3">
                        <button type="button" onclick="closeParticipantModal()" class="px-3 py-2 rounded-lg border border-slate-200 text-slate-600 font-semibold">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        @push('scripts')
        <script>
            function editParticipant(id, number, name, rombel, roomId, sessionId) {
                document.getElementById('edit_p_number').value = number;
                document.getElementById('edit_p_name').value = name;
                document.getElementById('edit_p_rombel').value = rombel;
                document.getElementById('edit_p_room').value = roomId;
                document.getElementById('edit_p_session').value = sessionId;
                document.getElementById('form-edit-participant').action = '{{ url("admin/participants") }}/' + id;
                document.getElementById('modal-edit-participant').classList.remove('hidden');
            }
            function closeParticipantModal() {
                document.getElementById('modal-edit-participant').classList.add('hidden');
            }
        </script>
        @endpush
    @endif

    <!-- TAB 7: DAFTAR BERITA ACARA TERISI -->
    @if($activeTab === 'minutes')
        <div class="space-y-6">
            <!-- Header & Filter -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-4">
                    <div>
                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 mb-1">
                            📋 Berita Acara Resmi
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">Daftar Berita Acara Ujian Terisi</h2>
                        <p class="text-xs text-slate-500">Berita Acara yang telah disimpan dan diselesaikan oleh pengawas & proktor di ruang ujian.</p>
                    </div>
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-bold">
                            Total: {{ $totalFilledMinutesCount }} Dokumen
                        </span>
                    </div>
                </div>

                <!-- Form Filter -->
                <form method="GET" action="{{ route('admin.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mt-4 items-end">
                    <input type="hidden" name="tab" value="minutes">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
                        <select name="filter_minute_date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Tanggal</option>
                            @foreach($minuteDates as $date)
                                <option value="{{ $date }}" {{ request('filter_minute_date') === $date ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Sesi</label>
                        <select name="filter_minute_session" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Sesi</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}" {{ request('filter_minute_session') == $session->id ? 'selected' : '' }}>
                                    {{ $session->name }} ({{ $session->subject }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Ruangan</label>
                        <select name="filter_minute_room" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Ruangan</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ request('filter_minute_room') == $room->id ? 'selected' : '' }}>
                                    {{ $room->name }} ({{ $room->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Petugas / Catatan</label>
                        <input type="text" name="search_minute" value="{{ request('search_minute') }}" placeholder="Nama pengawas, proktor..."
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="submit" class="w-full px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition">
                            Terapkan Filter
                        </button>
                        @if(request()->hasAny(['filter_minute_date', 'filter_minute_session', 'filter_minute_room', 'search_minute']))
                            <a href="{{ route('admin.index', ['tab' => 'minutes']) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition" title="Reset filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Berita Acara -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-700 uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4 w-48">Tanggal & Sesi</th>
                                <th class="py-3 px-4 w-40">Ruangan</th>
                                <th class="py-3 px-4">Pengawas & Proktor</th>
                                <th class="py-3 px-4 text-center w-36">Kehadiran Siswa</th>
                                <th class="py-3 px-4">Catatan Kejadian</th>
                                <th class="py-3 px-4 w-28 text-center">Status</th>
                                <th class="py-3 px-4 w-36 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($filledMinutes as $index => $minute)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-4 text-center font-medium text-slate-400">
                                        {{ $filledMinutes->firstItem() + $index }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900">
                                            {{ \Carbon\Carbon::parse($minute->exam_date)->locale('id')->isoFormat('D MMM Y') }}
                                        </div>
                                        <div class="text-[11px] text-blue-600 font-semibold mt-0.5">
                                            {{ $minute->examSession?->name }} • {{ $minute->examSession?->subject }}
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            {{ $minute->examSession?->start_time }} - {{ $minute->examSession?->end_time }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800">{{ $minute->room?->name }}</div>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700">
                                            {{ $minute->room?->code }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 space-y-1">
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Pengawas:</span>
                                            <span class="font-semibold text-slate-800">{{ $minute->supervisor_name }}</span>
                                            @if($minute->supervisor_nip)
                                                <span class="text-[10px] text-slate-400">({{ $minute->supervisor_nip }})</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Proktor:</span>
                                            <span class="font-semibold text-slate-800">{{ $minute->proctor_name }}</span>
                                            @if($minute->proctor_nip)
                                                <span class="text-[10px] text-slate-400">({{ $minute->proctor_nip }})</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex items-center justify-center space-x-1.5 text-[11px]">
                                            <span class="px-2 py-0.5 rounded-md font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Jumlah Hadir">
                                                {{ $minute->present_participants_count }} Hadir
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md font-bold {{ $minute->absent_participants_count > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-50 text-slate-400 border border-slate-200' }}" title="Tidak Hadir">
                                                {{ $minute->absent_participants_count }} Absen
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 mt-1 block">
                                            Total: {{ $minute->expected_participants_count }} Siswa
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 max-w-xs">
                                        @if($minute->incident_notes)
                                            <span class="italic line-clamp-2" title="{{ $minute->incident_notes }}">
                                                "{{ $minute->incident_notes }}"
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic">Tidak ada catatan kejadian khusus.</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 inline-flex items-center">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Terisi
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex items-center justify-center space-x-1.5">
                                            <a href="{{ route('minutes.print', $minute) }}" target="_blank"
                                               class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] shadow-sm transition" title="Cetak Berita Acara">
                                                Cetak
                                            </a>
                                            <a href="{{ route('minutes.index', ['exam_session_id' => $minute->exam_session_id, 'room_id' => $minute->room_id]) }}"
                                               class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-[11px] border border-blue-200 transition" title="Lihat / Edit Berita Acara">
                                                Buka
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-12 text-center text-slate-400">
                                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="font-bold text-slate-700 text-sm">Belum ada Berita Acara yang tersimpan.</p>
                                        <p class="text-xs text-slate-500 mt-1">Berita Acara akan otomatis muncul di sini setelah proktor/pengawas menyimpan formulir di ruangan masing-masing.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($filledMinutes->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50">
                        {{ $filledMinutes->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- TAB 8: REKAP PRESENSI TERISI BY TANGGAL & SESI -->
    @if($activeTab === 'attendances')
        <div class="space-y-6">
            <!-- Header & Filter -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-4">
                    <div>
                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 mb-1">
                            📊 Rekapitulasi Presensi
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">Daftar Presensi Terisi (Rekap by Tanggal & Sesi)</h2>
                        <p class="text-xs text-slate-500">Tampilan rekapitulasi kehadiran berdasarkan data dan input Berita Acara yang telah masuk dari ruang-ruang ujian.</p>
                    </div>
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-bold">
                            {{ $attendanceRecaps->count() }} Sesi Terdata
                        </span>
                    </div>
                </div>

                <!-- Form Filter -->
                <form method="GET" action="{{ route('admin.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 items-end">
                    <input type="hidden" name="tab" value="attendances">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Tanggal Ujian</label>
                        <select name="filter_recap_date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Tanggal</option>
                            @foreach($minuteDates as $date)
                                <option value="{{ $date }}" {{ request('filter_recap_date') === $date ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Sesi Ujian</label>
                        <select name="filter_recap_session" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Sesi Ujian</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}" {{ request('filter_recap_session') == $session->id ? 'selected' : '' }}>
                                    {{ $session->name }} ({{ $session->subject }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="submit" class="w-full px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition">
                            Tampilkan Rekap
                        </button>
                        @if(request()->hasAny(['filter_recap_date', 'filter_recap_session']))
                            <a href="{{ route('admin.index', ['tab' => 'attendances']) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition" title="Reset filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Rekap Cards by Tanggal & Sesi -->
            <div class="space-y-6">
                @forelse($attendanceRecaps as $key => $recap)
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                        <!-- Header Tanggal & Sesi -->
                        <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center space-x-2 mb-1.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm">
                                        📅 {{ \Carbon\Carbon::parse($recap['exam_date'])->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-500/30 text-blue-200 border border-blue-400/30">
                                        {{ $recap['room_count'] }} Ruang Terisi Berita Acara
                                    </span>
                                </div>
                                <h3 class="text-xl font-black tracking-tight text-white flex items-center gap-2">
                                    {{ $recap['session']?->name ?? 'Sesi Ujian' }} • {{ $recap['session']?->subject ?? '-' }}
                                </h3>
                                <p class="text-xs text-slate-300 mt-0.5">
                                    Waktu: {{ $recap['session']?->start_time ?? '-' }} - {{ $recap['session']?->end_time ?? '-' }} WIB
                                </p>
                            </div>

                            <!-- Stat Rings / Pills -->
                            <div class="grid grid-cols-4 gap-2 text-center text-xs">
                                <div class="bg-white/10 backdrop-blur-sm px-3 py-2 rounded-xl border border-white/10">
                                    <span class="text-[10px] text-slate-300 block uppercase font-semibold">Total</span>
                                    <span class="text-lg font-black text-white block">{{ $recap['total_expected'] }}</span>
                                </div>
                                <div class="bg-emerald-500/20 backdrop-blur-sm px-3 py-2 rounded-xl border border-emerald-400/30">
                                    <span class="text-[10px] text-emerald-200 block uppercase font-semibold">Hadir</span>
                                    <span class="text-lg font-black text-emerald-300 block">{{ $recap['total_present'] }}</span>
                                </div>
                                <div class="bg-rose-500/20 backdrop-blur-sm px-3 py-2 rounded-xl border border-rose-400/30">
                                    <span class="text-[10px] text-rose-200 block uppercase font-semibold">Tidak Hadir</span>
                                    <span class="text-lg font-black text-rose-300 block">{{ $recap['total_absent'] }}</span>
                                </div>
                                <div class="bg-blue-500/20 backdrop-blur-sm px-3 py-2 rounded-xl border border-blue-400/30">
                                    <span class="text-[10px] text-blue-200 block uppercase font-semibold">Kehadiran</span>
                                    <span class="text-lg font-black text-blue-300 block">{{ $recap['percentage'] }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Ruangan-ruangan yang telah mengisi Berita Acara -->
                        <div class="p-5 sm:p-6 border-b border-slate-100 bg-slate-50/50">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
                                Status Pengisian per Ruangan:
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($recap['minutes'] as $min)
                                    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                                        <div>
                                            <div class="flex items-center space-x-1.5">
                                                <span class="font-bold text-xs text-slate-900">{{ $min->room?->name }}</span>
                                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">{{ $min->room?->code }}</span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 mt-0.5">
                                                Pengawas: <span class="font-semibold text-slate-700">{{ $min->supervisor_name }}</span>
                                            </p>
                                            <div class="flex items-center space-x-2 text-[10px] text-slate-600 mt-1">
                                                <span class="text-emerald-600 font-bold">{{ $min->present_participants_count }} Hadir</span>
                                                <span>•</span>
                                                <span class="{{ $min->absent_participants_count > 0 ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                                    {{ $min->absent_participants_count }} Absen
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col space-y-1">
                                            <a href="{{ route('attendances.index', ['exam_session_id' => $min->exam_session_id, 'room_id' => $min->room_id]) }}" 
                                               class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-[10px] text-center transition">
                                                Presensi
                                            </a>
                                            <a href="{{ route('minutes.index', ['exam_session_id' => $min->exam_session_id, 'room_id' => $min->room_id]) }}" 
                                               class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[10px] text-center transition">
                                                Berita Acara
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Daftar Rincian Siswa yang Tidak Hadir -->
                        <div class="p-5 sm:p-6">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center justify-between">
                                <span>Rincian Siswa Tidak Hadir pada Sesi Ini:</span>
                                <span class="text-[11px] font-semibold text-rose-600">{{ $recap['total_absent'] }} Siswa Terdata</span>
                            </h4>

                            @if($recap['absent_records']->isEmpty())
                                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs text-center font-medium">
                                    🎉 Seluruh peserta ({{ $recap['total_expected'] }} siswa) pada tanggal dan sesi ini hadir lengkap! Tidak ada siswa yang tercatat absen.
                                </div>
                            @else
                                <div class="border border-slate-200 rounded-xl overflow-hidden text-xs">
                                    <table class="w-full text-left">
                                        <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600">
                                            <tr>
                                                <th class="py-2.5 px-3 w-12 text-center">No</th>
                                                <th class="py-2.5 px-3 w-36">No. Peserta</th>
                                                <th class="py-2.5 px-3">Nama Siswa</th>
                                                <th class="py-2.5 px-3 w-28">Ruangan</th>
                                                <th class="py-2.5 px-3 w-20 text-center">Kelas</th>
                                                <th class="py-2.5 px-3 w-32">Alasan</th>
                                                <th class="py-2.5 px-3">Catatan / Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($recap['absent_records'] as $aIndex => $att)
                                                <tr class="hover:bg-slate-50">
                                                    <td class="py-2 px-3 text-center text-slate-400">{{ $aIndex + 1 }}</td>
                                                    <td class="py-2 px-3 font-mono font-bold text-slate-800">{{ $att->participant?->participant_number }}</td>
                                                    <td class="py-2 px-3 font-semibold text-slate-900">{{ $att->participant?->name }}</td>
                                                    <td class="py-2 px-3 text-slate-700">
                                                        <span class="font-medium">{{ $att->room?->name }}</span>
                                                        <span class="text-[10px] font-mono text-slate-400">({{ $att->room?->code }})</span>
                                                    </td>
                                                    <td class="py-2 px-3 text-center">
                                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700">
                                                            {{ $att->participant?->rombel ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-100 text-rose-700">
                                                            {{ $att->reason }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 px-3 text-slate-600">{{ $att->notes ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center text-slate-400 shadow-sm">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <p class="font-bold text-slate-700 text-base">Belum ada Rekap Presensi yang terisi.</p>
                        <p class="text-xs text-slate-500 mt-1">Data rekapitulasi by tanggal & sesi akan otomatis terhimpun setelah pengawas/proktor mengisi Berita Acara di ruang ujian.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

</div>
@endsection
