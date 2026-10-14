@extends('layouts.app')

@section('title', 'Dashboard Ringkasan Ujian')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-600 to-sky-600 rounded-2xl p-6 sm:p-8 text-white shadow-xl shadow-blue-900/10 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm mb-3">
                Asesmen Nasional / TKA Kemendikdasmen
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Sistem Presensi & E-Berita Acara Ujian
            </h1>
            <p class="mt-2 text-blue-100 text-sm sm:text-base leading-relaxed">
                Dirancang khusus untuk mempermudah proktor & pengawas. Silakan pilih <strong>ruangan ujian Anda di bawah</strong> untuk mengisi Berita Acara & mencatat siswa yang tidak hadir. Siswa yang tidak dicentang otomatis dinyatakan <strong>HADIR</strong>.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="#daftar-ruangan" 
                   class="inline-flex items-center px-5 py-2.5 rounded-xl bg-white text-blue-700 font-bold text-sm shadow-md hover:bg-blue-50 transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Pilih Ruangan Ujian &darr;
                </a>
                <a href="{{ route('minutes.index') }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-800/60 border border-white/20 text-white font-semibold text-sm hover:bg-blue-800/90 transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Semua Berita Acara
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <!-- Total Ruangan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ruangan Ujian</p>
                <p class="text-2xl font-bold text-slate-800">{{ $rooms->count() }} Ruang</p>
            </div>
        </div>

        <!-- Total Peserta -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Peserta Terdaftar</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalParticipants }} Siswa</p>
            </div>
        </div>

        <!-- Berita Acara -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Berita Acara Tersimpan</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $totalMinutes }} Dokumen</p>
            </div>
        </div>
    </div>

    <!-- Daftar Ruangan Ujian (Pilihan Utama Guru & Pengawas) -->
    <div id="daftar-ruangan" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-2">
            <div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 uppercase tracking-wider">
                    Ruang Ujian Aktif
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight mt-1.5">
                    Pilih Ruangan Ujian
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Klik pada kartu ruangan Anda untuk langsung mengisi Berita Acara dan Presensi Ketidakhadiran.
                </p>
            </div>
            <div class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl self-start sm:self-auto">
                {{ $rooms->count() }} Ruangan Siap Digunakan
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-6">
            @forelse($rooms as $room)
                <a href="{{ route('minutes.index', ['room_id' => $room->id]) }}" 
                   class="group relative block bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200/80 hover:border-blue-500 p-6 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-xl bg-blue-100/70 text-blue-700 flex items-center justify-center font-bold text-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-200 shadow-sm">
                            🏛️
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-white group-hover:bg-blue-50 text-slate-700 group-hover:text-blue-700 border border-slate-200 transition">
                            {{ $room->code }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-700 transition">
                            {{ $room->name }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                            {{ $room->description ?: 'Kapasitas '.$room->capacity.' Peserta' }}
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-200/60 flex items-center justify-between text-xs">
                        <div class="flex items-center text-slate-500 font-medium">
                            <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>{{ $room->participants_count ?? 0 }} Peserta</span>
                        </div>
                        <span class="inline-flex items-center font-bold text-blue-600 group-hover:text-blue-700">
                            Masuk Ruang
                            <svg class="w-3.5 h-3.5 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>
            @empty
                <div class="col-span-full p-12 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <p class="text-base font-semibold">Belum ada ruangan ujian yang terdaftar.</p>
                    <p class="text-xs mt-1">Silakan tambahkan ruangan melalui Panel Admin.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
