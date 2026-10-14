<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - E-Berita Acara & Presensi Ujian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 min-h-screen flex items-center justify-center p-4 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <div class="max-w-md w-full">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white shadow-xl shadow-blue-500/20 mb-4 transform hover:scale-105 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">E-Berita Acara & Presensi</h1>
            <p class="text-xs text-blue-200/80 mt-1">Asesmen Nasional / TKA Kemendikdasmen</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-white/10 relative overflow-hidden">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900">Masuk Akun Pengawas</h2>
                <p class="text-xs text-slate-500 mt-1">Silakan masukkan ID User dan Password yang telah diberikan panitia.</p>
            </div>

            <!-- Error Notification -->
            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    <ul class="space-y-1 font-medium">
                        @foreach($errors->all() as $error)
                            <li>⚠️ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Username / ID User
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" 
                               required autofocus placeholder="Contoh: 1 atau admin"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </span>
                        <input type="password" name="password" id="password" required placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 mr-2">
                        Ingat Saya
                    </label>
                </div>

                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition transform active:scale-95">
                    Masuk ke Sistem &rarr;
                </button>
            </form>

            <!-- Quick Hint Info -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-xs text-slate-500 bg-slate-50 -mx-6 -mb-6 p-4 rounded-b-3xl">
                <p class="font-bold text-slate-700 mb-1">💡 Informasi Akses Akun Guru:</p>
                <p class="text-[11px] leading-relaxed text-slate-500">
                    Gunakan angka urut guru (User: <code class="bg-white px-1.5 py-0.5 rounded border border-slate-200 font-mono text-blue-600">1</code> s/d <code class="bg-white px-1.5 py-0.5 rounded border border-slate-200 font-mono text-blue-600">44</code>) dan Password sesuai nomor user. Untuk admin panel, gunakan user <code class="bg-white px-1.5 py-0.5 rounded border border-slate-200 font-mono text-indigo-600">admin</code> (pass: <code class="bg-white px-1.5 py-0.5 rounded border border-slate-200 font-mono text-indigo-600">admin</code>) atau Guru No. 1.
                </p>
            </div>
        </div>
    </div>

</body>
</html>
