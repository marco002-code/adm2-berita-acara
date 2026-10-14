@extends('layouts.app')

@section('title', 'Presensi Peserta Ujian (Catat Yang Tidak Hadir)')

@section('content')
<div class="space-y-6">

    <!-- Filter Sesi & Ruangan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
        <form method="GET" action="{{ route('attendances.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
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
                    Tampilkan Data
                </button>
            </div>
        </form>
    </div>

    <!-- Instruction Notice Box -->
    <div class="bg-amber-50/80 border border-amber-200/90 rounded-2xl p-4 sm:p-5 flex items-start space-x-3 text-amber-900 shadow-sm">
        <div class="p-2 bg-amber-100 rounded-xl text-amber-700 shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="text-sm">
            <h3 class="font-bold text-amber-900">Aturan Presensi Otomatis:</h3>
            <p class="mt-1 leading-relaxed text-amber-800">
                Sistem ini menerapkan prinsip efisiensi: <strong>hanya data siswa yang TIDAK HADIR saja yang disimpan di database</strong>. Seluruh siswa yang tidak dicentang secara otomatis dinyatakan <strong>HADIR</strong>. Silakan centang kotak pada baris siswa yang berhalangan hadir dan pilih alasannya.
            </p>
        </div>
    </div>

    <!-- Real-time Attendance Counters & Search -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center space-x-3 sm:space-x-6 text-sm">
            <div>
                <span class="text-slate-500 font-medium text-xs block">Total Peserta:</span>
                <span class="font-bold text-slate-800 text-lg" id="stat-total">{{ $totalParticipants }}</span>
            </div>
            <div class="h-8 w-px bg-slate-200"></div>
            <div>
                <span class="text-slate-500 font-medium text-xs block">Hadir (Otomatis):</span>
                <span class="font-bold text-emerald-600 text-lg" id="stat-present">{{ $presentCount }}</span>
            </div>
            <div class="h-8 w-px bg-slate-200"></div>
            <div>
                <span class="text-slate-500 font-medium text-xs block">Tidak Hadir (Dicentang):</span>
                <span class="font-bold text-rose-600 text-lg" id="stat-absent">{{ $absentCount }}</span>
            </div>
        </div>

        <div class="w-full sm:w-72">
            <input type="text" id="search-participant" placeholder="Cari nama atau nomor..." 
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
        </div>
    </div>

    @if($participants->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-500 shadow-sm">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <h4 class="font-bold text-slate-700">Tidak ada peserta ditemukan</h4>
            <p class="text-xs text-slate-400 mt-1">Belum ada siswa yang dijadwalkan pada sesi dan ruangan ini.</p>
        </div>
    @else
        <!-- Presensi Form -->
        <form method="POST" action="{{ route('attendances.store') }}" id="attendance-form" class="space-y-4">
            @csrf
            <input type="hidden" name="exam_session_id" value="{{ $selectedSession?->id }}">
            <input type="hidden" name="room_id" value="{{ $selectedRoom?->id }}">

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3.5 px-3 text-center w-12">No</th>
                                <th class="py-3.5 px-3 text-center w-14">Pilih</th>
                                <th class="py-3.5 px-4 w-36">No. Peserta</th>
                                <th class="py-3.5 px-4">Nama Siswa</th>
                                <th class="py-3.5 px-3 w-24 text-center">Kelas</th>
                                <th class="py-3.5 px-4 w-36 text-center">Status Kehadiran</th>
                                <th class="py-3.5 px-4 w-44">Alasan Tidak Hadir</th>
                                <th class="py-3.5 px-4 w-48">Keterangan Tambahan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="participant-table-body">
                            @foreach($participants as $idx => $participant)
                                @php
                                    $isAbsent = $absentRecords->has($participant->id);
                                    $currentAbsent = $absentRecords->get($participant->id);
                                @endphp
                                <tr class="participant-row transition {{ $isAbsent ? 'bg-rose-50/60' : 'hover:bg-slate-50/70' }}" 
                                    id="row-{{ $participant->id }}">
                                    <!-- Nomor Urut -->
                                    <td class="py-3 px-3 text-center text-xs font-semibold text-slate-400">
                                        {{ $idx + 1 }}
                                    </td>

                                    <!-- Checkbox Centang Tidak Hadir -->
                                    <td class="py-3 px-3 text-center">
                                        <input type="checkbox" 
                                               name="absent_participants[]" 
                                               value="{{ $participant->id }}" 
                                               id="chk-{{ $participant->id }}"
                                               data-participant-id="{{ $participant->id }}"
                                               class="absent-checkbox w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500 transition cursor-pointer"
                                               {{ $isAbsent ? 'checked' : '' }}>
                                    </td>

                                    <!-- Nomor Peserta -->
                                    <td class="py-3 px-4 font-mono font-bold text-slate-700">
                                        {{ $participant->participant_number }}
                                    </td>

                                    <!-- Nama Peserta -->
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-800 participant-name">{{ $participant->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $participant->school_origin }}</div>
                                    </td>

                                    <!-- Rombel / Kelas -->
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $participant->rombel ?? '-' }}
                                        </span>
                                    </td>

                                    <!-- Status Kehadiran Badge -->
                                    <td class="py-3 px-4 text-center">
                                        <span class="status-badge px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center {{ $isAbsent ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $isAbsent ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                            {{ $isAbsent ? 'Tidak Hadir' : 'Hadir' }}
                                        </span>
                                    </td>

                                    <!-- Alasan Tidak Hadir -->
                                    <td class="py-3 px-4">
                                        <select name="reasons[{{ $participant->id }}]" 
                                                id="reason-{{ $participant->id }}"
                                                class="reason-select w-full rounded-lg border border-slate-200 text-xs px-2.5 py-1.5 text-slate-700 bg-white focus:outline-none focus:ring-1 focus:ring-rose-500 {{ $isAbsent ? '' : 'opacity-40 pointer-events-none' }}">
                                            <option value="Sakit" {{ ($currentAbsent?->reason === 'Sakit') ? 'selected' : '' }}>Sakit</option>
                                            <option value="Izin" {{ ($currentAbsent?->reason === 'Izin') ? 'selected' : '' }}>Izin</option>
                                            <option value="Tanpa Keterangan" {{ ($currentAbsent?->reason === 'Tanpa Keterangan' || !$currentAbsent) ? 'selected' : '' }}>Tanpa Keterangan</option>
                                            <option value="Lainnya" {{ ($currentAbsent?->reason === 'Lainnya') ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                    </td>

                                    <!-- Catatan Tambahan -->
                                    <td class="py-3 px-4">
                                        <input type="text" 
                                               name="notes[{{ $participant->id }}]" 
                                               id="notes-{{ $participant->id }}"
                                               value="{{ $currentAbsent?->notes }}"
                                               placeholder="Opsional (misal: surat sakit)"
                                               class="notes-input w-full rounded-lg border border-slate-200 text-xs px-2.5 py-1.5 text-slate-700 bg-white focus:outline-none focus:ring-1 focus:ring-rose-500 {{ $isAbsent ? '' : 'opacity-40 pointer-events-none' }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                <div class="text-xs text-slate-500">
                    💡 <em>Hanya data siswa dengan kotak centang terpilih yang akan dicatat di database presensi.</em>
                </div>
                <div class="flex items-center space-x-3 w-full sm:w-auto">
                    <a href="{{ route('minutes.index', ['exam_session_id' => $selectedSession?->id, 'room_id' => $selectedRoom?->id]) }}" 
                       class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition">
                        Lanjut ke Berita Acara &rarr;
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition transform hover:-translate-y-0.5">
                        Simpan Presensi
                    </button>
                </div>
            </div>
        </form>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const totalCount = {{ $totalParticipants }};
    const checkboxes = document.querySelectorAll('.absent-checkbox');
    const statAbsent = document.getElementById('stat-absent');
    const statPresent = document.getElementById('stat-present');
    const searchInput = document.getElementById('search-participant');

    function updateCounts() {
        const checkedCount = document.querySelectorAll('.absent-checkbox:checked').length;
        if (statAbsent) statAbsent.textContent = checkedCount;
        if (statPresent) statPresent.textContent = Math.max(0, totalCount - checkedCount);
    }

    checkboxes.forEach(function (chk) {
        chk.addEventListener('change', function () {
            const id = this.getAttribute('data-participant-id');
            const row = document.getElementById('row-' + id);
            const reasonSelect = document.getElementById('reason-' + id);
            const notesInput = document.getElementById('notes-' + id);
            const badge = row.querySelector('.status-badge');

            if (this.checked) {
                row.classList.add('bg-rose-50/60');
                reasonSelect.classList.remove('opacity-40', 'pointer-events-none');
                notesInput.classList.remove('opacity-40', 'pointer-events-none');
                
                badge.className = 'status-badge px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center bg-rose-100 text-rose-700';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-rose-500"></span>Tidak Hadir';
            } else {
                row.classList.remove('bg-rose-50/60');
                reasonSelect.classList.add('opacity-40', 'pointer-events-none');
                notesInput.classList.add('opacity-40', 'pointer-events-none');

                badge.className = 'status-badge px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center bg-emerald-100 text-emerald-700';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-emerald-500"></span>Hadir';
            }

            updateCounts();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('.participant-row');
            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush
