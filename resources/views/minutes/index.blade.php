@extends('layouts.app')

@section('title', 'Berita Acara Pelaksanaan Ujian')

@section('content')
<div class="space-y-6">

    <!-- Filter Sesi & Ruangan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
        <form method="GET" action="{{ route('minutes.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label for="exam_session_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                    Pilih Sesi Ujian
                </label>
                <select name="exam_session_id" id="exam_session_id" onchange="this.form.submit()" 
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @php
                        $groupedSessions = $sessions->groupBy(fn ($s) => $s->exam_date->format('Y-m-d').' - '.$s->subject);
                    @endphp
                    @foreach($groupedSessions as $sessionList)
                        @php
                            $firstSession = $sessionList->first();
                            $dateTitle = \Carbon\Carbon::parse($firstSession->exam_date)->locale('id')->isoFormat('dddd, D MMMM Y');
                        @endphp
                        <optgroup label="📅 {{ $dateTitle }} • {{ $firstSession->subject }}">
                            @foreach($sessionList as $session)
                                <option value="{{ $session->id }}" {{ $selectedSession?->id === $session->id ? 'selected' : '' }}>
                                    {{ $session->name }} ({{ $session->start_time }} - {{ $session->end_time }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="room_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                    Pilih Ruangan Ujian
                </label>
                <select name="room_id" id="room_id" onchange="this.form.submit()" 
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ $selectedRoom?->id === $room->id ? 'selected' : '' }}>
                            {{ $room->name }} ({{ $room->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold shadow-sm transition">
                    Pilih
                </button>
            </div>
        </form>
    </div>

    <!-- Card Besar: ISI KETIDAKHADIRAN -->
    <a href="{{ route('attendances.index', ['exam_session_id' => $selectedSession?->id, 'room_id' => $selectedRoom?->id]) }}" 
       class="group relative block overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-600 p-6 sm:p-7 text-white shadow-lg shadow-blue-600/20 hover:shadow-xl hover:shadow-blue-600/30 transition-all duration-300 transform hover:-translate-y-0.5 border border-white/20">
        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="flex items-start sm:items-center space-x-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/30 shadow-inner group-hover:scale-105 transition-transform">
                    <svg class="w-8 h-8 sm:w-9 sm:h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm mb-2">
                        Presensi • {{ $selectedRoom?->name ?? 'Pilih Ruang' }} • {{ $selectedSession?->name ?? 'Pilih Sesi' }}
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-2">
                        ISI KETIDAKHADIRAN
                        <svg class="w-6 h-6 text-blue-200 group-hover:translate-x-1.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </h2>
                    <p class="text-xs sm:text-sm text-blue-100 mt-1 max-w-xl leading-relaxed">
                        Klik kartu ini untuk mencatat siswa yang <strong>tidak hadir</strong> pada sesi ini. Siswa yang tidak dicentang otomatis tercatat <strong>HADIR</strong>.
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-3 shrink-0">
                <span class="inline-flex items-center px-5 py-3 rounded-xl bg-white text-blue-700 font-extrabold text-sm shadow-md group-hover:bg-blue-50 transition transform group-hover:scale-105">
                    ISI KETIDAKHADIRAN &rarr;
                </span>
            </div>
        </div>
    </a>

    <!-- Attendance Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Ringkasan Kehadiran Otomatis</h2>
                <p class="text-xs text-slate-500">
                    {{ $selectedRoom?->name }} • {{ $selectedSession?->name }} ({{ $selectedSession?->subject }})
                </p>
            </div>
            <div>
                <a href="{{ route('attendances.index', ['exam_session_id' => $selectedSession?->id, 'room_id' => $selectedRoom?->id]) }}" 
                   class="inline-flex items-center text-xs font-bold text-blue-700 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-3.5 py-2 rounded-xl transition shadow-sm">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    ISI KETIDAKHADIRAN
                </a>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-4 text-center">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Seharusnya Hadir</span>
                <span class="text-2xl font-bold text-slate-800 mt-1 block">{{ $totalExpected }}</span>
                <span class="text-[11px] text-slate-400">Total peserta terdaftar</span>
            </div>
            <div class="bg-emerald-50/70 p-4 rounded-xl border border-emerald-100">
                <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block">Jumlah Hadir</span>
                <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ $totalPresent }}</span>
                <span class="text-[11px] text-emerald-500">Dihitung otomatis</span>
            </div>
            <div class="bg-rose-50/70 p-4 rounded-xl border border-rose-100">
                <span class="text-xs font-semibold text-rose-700 uppercase tracking-wider block">Tidak Hadir</span>
                <span class="text-2xl font-bold text-rose-600 mt-1 block">{{ $totalAbsent }}</span>
                <span class="text-[11px] text-rose-500">Terekam di sistem</span>
            </div>
        </div>

        <!-- Daftar Rincian Siswa Tidak Hadir -->
        <div class="mt-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                Daftar Peserta yang Tidak Hadir:
            </h3>
            @if($absentAttendances->isEmpty())
                <div class="p-4 rounded-xl bg-slate-50 text-slate-600 text-xs text-center border border-slate-100">
                    🎉 Seluruh peserta ({{ $totalExpected }} siswa) hadir lengkap! Tidak ada siswa yang tercatat absen.
                </div>
            @else
                <div class="border border-slate-200 rounded-xl overflow-hidden text-xs">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600">
                            <tr>
                                <th class="py-2.5 px-3 w-12 text-center">No</th>
                                <th class="py-2.5 px-3 w-36">No. Peserta</th>
                                <th class="py-2.5 px-3">Nama Siswa</th>
                                <th class="py-2.5 px-3 w-20 text-center">Kelas</th>
                                <th class="py-2.5 px-3 w-32">Alasan</th>
                                <th class="py-2.5 px-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($absentAttendances as $index => $att)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2 px-3 text-center text-slate-500">{{ $index + 1 }}</td>
                                    <td class="py-2 px-3 font-mono font-bold text-slate-700">{{ $att->participant?->participant_number }}</td>
                                    <td class="py-2 px-3 font-semibold text-slate-800">{{ $att->participant?->name }}</td>
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

    <!-- Form Berita Acara -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Formulir Berita Acara Ujian</h2>
                <p class="text-xs text-slate-500">Lengkapi data petugas penandatangan dan catatan pelaksanaan</p>
            </div>
            @if($minute && $minute->exists)
                <a href="{{ route('minutes.print', $minute) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Dokumen Resmi
                </a>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-100 text-slate-400 text-xs font-semibold cursor-not-allowed" title="Simpan formulir berita acara terlebih dahulu untuk mencetak">
                    <svg class="w-4 h-4 mr-1.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Belum Disimpan (Belum Bisa Cetak)
                </span>
            @endif
        </div>

        <form method="POST" action="{{ route('minutes.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="exam_session_id" value="{{ $selectedSession?->id }}">
            <input type="hidden" name="room_id" value="{{ $selectedRoom?->id }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                        Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="exam_date" 
                           value="{{ old('exam_date', $minute?->exam_date?->format('Y-m-d') ?? $selectedSession?->exam_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" 
                           required
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                        Mata Ujian / Materi
                    </label>
                    <input type="text" value="{{ $selectedSession?->subject }}" disabled 
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm text-slate-500 font-medium">
                </div>
            </div>

            <!-- Petugas: Proktor & Pengawas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                <!-- Proktor -->
                <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Data Proktor Ujian</h4>
                        <span class="text-[10px] text-slate-500 font-medium">Bisa pilih dari database</span>
                    </div>

                    @if(isset($proctors) && $proctors->isNotEmpty())
                        <div>
                            <label class="block text-[11px] font-bold text-indigo-700 mb-1">⚡ Pilih Dari Daftar Proktor:</label>
                            <select id="select-registered-proctor" onchange="applyProctor(this)" 
                                    class="w-full rounded-lg border border-indigo-200 bg-white text-xs px-2.5 py-1.5 font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Proktor Terdaftar --</option>
                                @foreach($proctors as $p)
                                    <option value="{{ $p->name }}" data-nbm="{{ $p->nbm ?? $p->nip }}">
                                        {{ $p->name }} (NBM: {{ $p->nbm ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Nama Proktor <span class="text-rose-500">*</span></label>
                        <input type="text" name="proctor_name" id="input_proctor_name"
                               value="{{ old('proctor_name', $minute?->proctor_name) }}" 
                               placeholder="Nama Lengkap & Gelar" required
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">NBM / NIP Proktor</label>
                        <input type="text" name="proctor_nip" id="input_proctor_nip"
                               value="{{ old('proctor_nip', $minute?->proctor_nip) }}" 
                               placeholder="Contoh: 1042001"
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Pengawas -->
                <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 space-y-3">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Data Pengawas Ruang</h4>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Nama Pengawas <span class="text-rose-500">*</span></label>
                        <input type="text" name="supervisor_name" 
                               value="{{ old('supervisor_name', $minute?->supervisor_name ?? Auth::user()->name) }}" 
                               placeholder="Nama Lengkap & Gelar" required
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">NBM / NIP Pengawas (opsional)</label>
                        <input type="text" name="supervisor_nip" 
                               value="{{ old('supervisor_nip', $minute?->supervisor_nip) }}" 
                               placeholder="197xxxxxxxxxxxxxxx atau NBM"
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Penanggung Jawab / Kepala Sekolah -->
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Penanggung Jawab / Kepala Satuan Pendidikan</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Nama Kepala Sekolah / Penanggung Jawab</label>
                        <input type="text" name="headmaster_name" 
                               value="{{ old('headmaster_name', $minute?->headmaster_name ?? \App\Models\AppSetting::get('headmaster_name', 'Suwahono, S.Pd.')) }}" 
                               placeholder="Suwahono, S.Pd."
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">NBM / NIP Kepala Sekolah</label>
                        <input type="text" name="headmaster_nip" 
                               value="{{ old('headmaster_nip', $minute?->headmaster_nip ?? \App\Models\AppSetting::get('headmaster_nip', '960434')) }}" 
                               placeholder="960434"
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Catatan Kejadian Penting -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                    Catatan Kejadian Khusus Selama Pelaksanaan Ujian
                </label>
                <textarea name="incident_notes" rows="3" 
                          placeholder="Contoh: Ujian berjalan tertib dan lancar. Koneksi internet stabil. Tidak ada kendala teknis."
                          class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">{{ old('incident_notes', $minute?->incident_notes ?? 'Pelaksanaan ujian berjalan tertib, aman, dan lancar.') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition transform hover:-translate-y-0.5">
                    Simpan Berita Acara
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function applyProctor(select) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.value) {
        document.getElementById('input_proctor_name').value = selectedOption.value;
        const nbm = selectedOption.getAttribute('data-nbm');
        if (nbm) {
            document.getElementById('input_proctor_nip').value = nbm;
        }
    }
}
</script>
@endpush
