<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SIAKAD Terdistribusi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Menambahkan font Inter untuk tipografi yang lebih bersih -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Dekorasi Background Aksen -->
    <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-br from-slate-900 to-slate-800 transform -skew-y-6 origin-top-left -z-10 shadow-lg"></div>

    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-slate-100 w-full max-w-md p-8 transition-all relative z-10">

        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-900 text-white mb-4 shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">SIAKAD</h1>

            @php
            $regionalInfo = [
            1 => ['nama' => 'Regional 1', 'fakultas' => 'Fakultas Teknik & Ilmu Komputer'],
            2 => ['nama' => 'Regional 2', 'fakultas' => 'Fakultas Ekonomi dan Bisnis'],
            3 => ['nama' => 'Regional 3', 'fakultas' => 'Fakultas Kedokteran dan Ilmu Kesehatan'],
            'pusat' => ['nama' => 'GCS Pusat', 'fakultas' => 'Biro Administrasi Akademik dan Kemahasiswaan'],
            ];
            $current = $regionalInfo[session('current_regional', 1)] ?? $regionalInfo[1];
            @endphp

            <!-- Muted foreground untuk hierarki teks yang lebih baik -->
            <p class="text-sm text-slate-500 mt-2 font-medium">{{ $current['nama'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $current['fakultas'] }}</p>
        </div>

        <!-- Alert Error -->
        @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 text-sm px-4 py-3 rounded-md mb-6 flex items-start" role="alert">
            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <!-- Form Section -->
        <form method="POST" action="/login" class="space-y-5">
            @csrf
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Email</label>
                <input type="email" name="email" required placeholder="nama@email.com"
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-900">
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-900">
            </div>

            <button type="submit"
                class="w-full bg-slate-900 text-white text-sm font-semibold py-2.5 rounded-lg hover:bg-slate-800 active:bg-slate-950 transition-all shadow-md hover:shadow-lg flex justify-center items-center gap-2 mt-2">
                Masuk
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </form>
    </div>
</body>
</html>
