<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard GCS Pusat — SIAKAD Terdistribusi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .mono { font-family: 'JetBrains Mono', 'Consolas', monospace; }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Tab Transition */
        .tab-panel { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Checkbox styling */
        .target-checkbox:checked + div {
            border-color: #6366f1; /* Indigo 500 */
            background-color: #e0e7ff; /* Indigo 50 */
        }
        .target-checkbox:checked + div svg {
            color: #4f46e5; /* Indigo 600 */
            opacity: 1;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen pb-20">

    <!-- Header Section -->
    <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-indigo-500/20 rounded-xl flex items-center justify-center border border-indigo-500/30 shadow-inner">
                    <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight">SIAKAD Global Control System</h1>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $user->name }} &bull; GCS Pusat (Master Node)</p>
                </div>
            </div>

            <div class="flex items-center gap-5">
                <span class="hidden sm:inline-flex items-center gap-2 text-[11px] font-bold mono bg-indigo-500/10 text-indigo-400 px-3 py-1.5 rounded-full border border-indigo-500/20 tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                    ALL SYSTEMS NOMINAL
                </span>
                <div class="h-6 w-px bg-slate-700 hidden sm:block"></div>
                <form method="POST" action="/logout" class="m-0">
                    @csrf
                    <button class="text-sm font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-lg transition-colors border border-slate-700 hover:border-slate-600 shadow-sm">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-8">

        <!-- Modern Tab Navigation (Pill-style) -->
        <div class="flex gap-2 overflow-x-auto custom-scrollbar pb-3 mb-6 border-b border-slate-200 snap-x">
            <button onclick="switchTab('monitoring')" id="tab-monitoring" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all bg-slate-900 text-white shadow-sm">Monitoring</button>
            <button onclick="switchTab('mk')" id="tab-mk" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Mata Kuliah</button>
            <button onclick="switchTab('dosen')" id="tab-dosen" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Dosen</button>
            <button onclick="switchTab('ruangan')" id="tab-ruangan" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Ruangan</button>
            <button onclick="switchTab('akun')" id="tab-akun" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Kelola Akun</button>
        </div>

        <!-- ===== PANEL: MONITORING ===== -->
        <div id="panel-monitoring" class="tab-panel space-y-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Status Jaringan Regional
                </h2>
                <button onclick="sinkronSekarang()" class="bg-indigo-600 text-white text-sm font-bold px-5 py-2.5 rounded-lg hover:bg-indigo-700 active:scale-95 transition-all shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Sinkronisasi Global Sekarang
                </button>
            </div>

            <div id="ringkasan-grid" class="grid grid-cols-1 md:grid-cols-3 gap-5"></div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start mt-8">
                <!-- Aktivitas KRS -->
                <section class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                    <div class="bg-slate-50/50 p-5 border-b border-slate-100">
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                            Aktivitas KRS Terbaru (Lintas Node)
                        </h2>
                    </div>
                    <div id="krs-list" class="divide-y divide-slate-100 text-sm max-h-[400px] overflow-y-auto custom-scrollbar"></div>
                </section>

                <!-- Aktivitas Nilai -->
                <section class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                    <div class="bg-slate-50/50 p-5 border-b border-slate-100">
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                            Update Nilai Terbaru (Lintas Node)
                        </h2>
                    </div>
                    <div id="nilai-list" class="divide-y divide-slate-100 text-sm max-h-[400px] overflow-y-auto custom-scrollbar"></div>
                </section>
            </div>
        </div>

        <!-- ===== PANEL: MATA KULIAH ===== -->
        <div id="panel-mk" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5">Distribusi Mata Kuliah Baru</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    <input id="mk-kode" placeholder="Kode MK (mis. TI401)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                    <input id="mk-nama" placeholder="Nama Mata Kuliah" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                    <input id="mk-prodi" placeholder="Kode Prodi (mis. TI)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                    <div class="grid grid-cols-2 gap-4">
                        <input id="mk-sks" type="number" min="1" max="6" placeholder="SKS" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                        <input id="mk-prasyarat" placeholder="Prasyarat (Opsional)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                    </div>
                </div>

                <label class="block text-xs font-bold text-slate-700 mb-3">Distribusi Target Regional</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                    <label class="relative cursor-pointer">
                        <input type="checkbox" value="1" class="mk-target target-checkbox sr-only">
                        <div class="flex items-center justify-between px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 transition-all hover:border-indigo-300">
                            <span class="text-sm font-bold text-slate-700">Regional 1 <span class="block text-[10px] font-medium text-slate-500">Teknik & Ilkom</span></span>
                            <svg class="w-5 h-5 opacity-0 transition-opacity" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="checkbox" value="2" class="mk-target target-checkbox sr-only">
                        <div class="flex items-center justify-between px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 transition-all hover:border-indigo-300">
                            <span class="text-sm font-bold text-slate-700">Regional 2 <span class="block text-[10px] font-medium text-slate-500">Ekonomi & Bisnis</span></span>
                            <svg class="w-5 h-5 opacity-0 transition-opacity" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="checkbox" value="3" class="mk-target target-checkbox sr-only">
                        <div class="flex items-center justify-between px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 transition-all hover:border-indigo-300">
                            <span class="text-sm font-bold text-slate-700">Regional 3 <span class="block text-[10px] font-medium text-slate-500">Kedokteran & Kes.</span></span>
                            <svg class="w-5 h-5 opacity-0 transition-opacity" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        </div>
                    </label>
                </div>
                <button onclick="tambahMataKuliah()" class="bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-800 active:scale-95 transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan & Replikasi ke Node
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Regional 1, 2, 3 Lists -->
                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                    <div class="bg-slate-50/50 p-4 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Data Regional 1</h4>
                    </div>
                    <div id="mk-list-1" class="divide-y divide-slate-100 text-sm max-h-[300px] overflow-y-auto custom-scrollbar"></div>
                </div>
                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                    <div class="bg-slate-50/50 p-4 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Data Regional 2</h4>
                    </div>
                    <div id="mk-list-2" class="divide-y divide-slate-100 text-sm max-h-[300px] overflow-y-auto custom-scrollbar"></div>
                </div>
                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                    <div class="bg-slate-50/50 p-4 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Data Regional 3</h4>
                    </div>
                    <div id="mk-list-3" class="divide-y divide-slate-100 text-sm max-h-[300px] overflow-y-auto custom-scrollbar"></div>
                </div>
            </div>
        </div>

        <!-- ===== PANEL: DOSEN ===== -->
        <div id="panel-dosen" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5">Distribusi Data Dosen</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                    <input id="dosen-nip" placeholder="NIP" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                    <input id="dosen-nama" placeholder="Nama Lengkap Dosen" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                    <input id="dosen-prodi" placeholder="Kode Prodi" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                </div>

                <label class="block text-xs font-bold text-slate-700 mb-3">Distribusi Target Regional</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                    <!-- Reuse the modern checkbox style -->
                    <label class="relative cursor-pointer"><input type="checkbox" value="1" class="dosen-target target-checkbox sr-only"><div class="flex items-center justify-between px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 hover:border-indigo-300"><span class="text-sm font-bold text-slate-700">Regional 1</span><svg class="w-5 h-5 opacity-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></div></label>
                    <label class="relative cursor-pointer"><input type="checkbox" value="2" class="dosen-target target-checkbox sr-only"><div class="flex items-center justify-between px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 hover:border-indigo-300"><span class="text-sm font-bold text-slate-700">Regional 2</span><svg class="w-5 h-5 opacity-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></div></label>
                    <label class="relative cursor-pointer"><input type="checkbox" value="3" class="dosen-target target-checkbox sr-only"><div class="flex items-center justify-between px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 hover:border-indigo-300"><span class="text-sm font-bold text-slate-700">Regional 3</span><svg class="w-5 h-5 opacity-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></div></label>
                </div>
                <button onclick="tambahDosen()" class="bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-800 active:scale-95 transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan & Replikasi ke Node
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Regional Lists -->
                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden"><div class="bg-slate-50/50 p-4 border-b border-slate-100 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-indigo-500"></span><h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Data Regional 1</h4></div><div id="dosen-list-1" class="divide-y divide-slate-100 text-sm max-h-[300px] overflow-y-auto custom-scrollbar"></div></div>
                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden"><div class="bg-slate-50/50 p-4 border-b border-slate-100 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span><h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Data Regional 2</h4></div><div id="dosen-list-2" class="divide-y divide-slate-100 text-sm max-h-[300px] overflow-y-auto custom-scrollbar"></div></div>
                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden"><div class="bg-slate-50/50 p-4 border-b border-slate-100 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-500"></span><h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Data Regional 3</h4></div><div id="dosen-list-3" class="divide-y divide-slate-100 text-sm max-h-[300px] overflow-y-auto custom-scrollbar"></div></div>
            </div>
        </div>

        <!-- ===== PANEL: RUANGAN ===== -->
        <div id="panel-ruangan" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5">Distribusi Data Ruangan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                    <input id="ruangan-id" placeholder="ID Ruangan" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                    <input id="ruangan-nama" placeholder="Nama Ruangan" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                    <input id="ruangan-kapasitas" type="number" min="1" placeholder="Kapasitas" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                </div>

                <label class="block text-xs font-bold text-slate-700 mb-3">Distribusi Target Regional</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                    <label class="relative cursor-pointer"><input type="checkbox" value="1" class="ruangan-target target-checkbox sr-only"><div class="flex items-center justify-between px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 hover:border-indigo-300"><span class="text-sm font-bold text-slate-700">Regional 1</span><svg class="w-5 h-5 opacity-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></div></label>
                    <label class="relative cursor-pointer"><input type="checkbox" value="2" class="ruangan-target target-checkbox sr-only"><div class="flex items-center justify-between px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 hover:border-indigo-300"><span class="text-sm font-bold text-slate-700">Regional 2</span><svg class="w-5 h-5 opacity-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></div></label>
                    <label class="relative cursor-pointer"><input type="checkbox" value="3" class="ruangan-target target-checkbox sr-only"><div class="flex items-center justify-between px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 hover:border-indigo-300"><span class="text-sm font-bold text-slate-700">Regional 3</span><svg class="w-5 h-5 opacity-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></div></label>
                </div>
                <button onclick="tambahRuangan()" class="bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-800 active:scale-95 transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan & Replikasi ke Node
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Regional Lists -->
                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden"><div class="bg-slate-50/50 p-4 border-b border-slate-100 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-indigo-500"></span><h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Data Regional 1</h4></div><div id="ruangan-list-1" class="divide-y divide-slate-100 text-sm max-h-[300px] overflow-y-auto custom-scrollbar"></div></div>
                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden"><div class="bg-slate-50/50 p-4 border-b border-slate-100 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span><h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Data Regional 2</h4></div><div id="ruangan-list-2" class="divide-y divide-slate-100 text-sm max-h-[300px] overflow-y-auto custom-scrollbar"></div></div>
                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden"><div class="bg-slate-50/50 p-4 border-b border-slate-100 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-500"></span><h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Data Regional 3</h4></div><div id="ruangan-list-3" class="divide-y divide-slate-100 text-sm max-h-[300px] overflow-y-auto custom-scrollbar"></div></div>
            </div>
        </div>

        <!-- ===== PANEL: AKUN ===== -->
        <div id="panel-akun" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Pendaftaran Akun Sistem
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <select id="akun-regional" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                        <option value="1">Regional 1 (Teknik & Ilkom)</option>
                        <option value="2">Regional 2 (Ekonomi & Bisnis)</option>
                        <option value="3">Regional 3 (Kedokteran)</option>
                    </select>
                    <select id="akun-role" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                        <option value="mahasiswa">Role: Mahasiswa</option>
                        <option value="dosen">Role: Dosen</option>
                        <option value="baak">Role: BAAK</option>
                    </select>
                    <input id="akun-nama" placeholder="Nama Lengkap" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                    <input id="akun-email" type="email" placeholder="Alamat Email" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                    <input id="akun-password" type="password" placeholder="Password Sistem" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                    <input id="akun-refid" placeholder="NIM / NIP (Kosongkan jika BAAK)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                </div>
                <button onclick="tambahUser()" class="mt-5 w-full sm:w-auto bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-800 active:scale-95 transition-all shadow-sm">
                    Buat Akun Kredensial
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="bg-slate-50/50 p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Direktori Pengguna</h3>
                    <div class="flex items-center gap-3">
                        <select id="akun-filter-regional" onchange="loadUser()" class="bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-[11px] font-bold text-slate-700 shadow-sm">
                            <option value="1">Regional 1</option>
                            <option value="2">Regional 2</option>
                            <option value="3">Regional 3</option>
                        </select>
                        <select id="akun-filter-role" onchange="loadUser()" class="bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-[11px] font-bold text-slate-700 shadow-sm">
                            <option value="">Semua Role</option>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="dosen">Dosen</option>
                            <option value="baak">BAAK</option>
                        </select>
                    </div>
                </div>
                <div id="akun-list" class="divide-y divide-slate-100 text-sm"></div>
            </div>
        </div>

        <!-- Modern Toast Notification -->
        <div id="toast" class="hidden fixed bottom-6 right-6 max-w-sm px-5 py-4 rounded-xl shadow-2xl text-sm font-semibold flex items-start gap-3 transform transition-all z-50 border"></div>
    </main>

    <script>
    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        const icon = isError
            ? '<svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            : '<svg class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

        toast.innerHTML = `${icon} <div class="flex-1">${message}</div>`;
        toast.className = `fixed bottom-6 right-6 max-w-sm lg:max-w-md px-5 py-4 rounded-xl shadow-2xl text-sm font-medium flex items-start gap-3 z-50 border ${isError ? 'bg-red-950 text-red-50 border-red-900' : 'bg-slate-900 text-white border-slate-800'}`;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 5000); // 5 sec for longer messages
    }

    function switchTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-slate-900', 'text-white', 'shadow-sm');
            b.classList.add('text-slate-500', 'hover:bg-slate-100', 'hover:text-slate-700');
        });

        const activePanel = document.getElementById(`panel-${tab}`);
        activePanel.classList.remove('hidden');
        activePanel.style.animation = 'none';
        activePanel.offsetHeight; // trigger reflow
        activePanel.style.animation = null;

        const activeTab = document.getElementById(`tab-${tab}`);
        activeTab.classList.add('bg-slate-900', 'text-white', 'shadow-sm');
        activeTab.classList.remove('text-slate-500', 'hover:bg-slate-100', 'hover:text-slate-700');

        if (tab === 'mk') loadMataKuliah();
        if (tab === 'dosen') loadDosen();
        if (tab === 'ruangan') loadRuangan();
        if (tab === 'akun') loadUser();
    }

    async function loadRingkasan() {
        const res = await fetch('/api/pusat/ringkasan', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        const container = document.getElementById('ringkasan-grid');
        container.innerHTML = '';
        data.forEach(r => {
            const isOnline = r.status === 'online';
            container.innerHTML += `
            <div class="bg-white rounded-2xl border ${isOnline ? 'border-slate-100 shadow-[0_2px_15px_rgb(0,0,0,0.04)]' : 'border-red-200 bg-red-50/30'} p-6 relative overflow-hidden group">
                ${!isOnline ? '<div class="absolute inset-0 bg-red-50/50 z-0"></div>' : ''}
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            ${isOnline ? '<span class="flex h-2.5 w-2.5 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span></span>' : '<span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>'}
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">${r.regional}</h3>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider ${isOnline ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-800 border border-red-200 shadow-sm'}">${isOnline ? 'Node Online' : 'Node Offline'}</span>
                    </div>
                    ${isOnline ? `
                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">MHS Aktif</span>
                                <span class="text-lg font-black text-slate-800">${r.total_mahasiswa}</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas Aktif</span>
                                <span class="text-lg font-black text-slate-800">${r.total_kelas}</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">KRS Sukses</span>
                                <span class="text-lg font-black text-indigo-600">${r.total_krs_sukses}</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nilai Final</span>
                                <span class="text-lg font-black text-emerald-600">${r.total_nilai_final}</span>
                            </div>
                        </div>
                    ` : `<div class="mt-4 p-4 bg-white/60 border border-red-100 rounded-lg"><p class="text-xs font-bold text-red-700 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Koneksi terputus ke Regional API.</p></div>`}
                </div>
            </div>`;
        });
    }

    async function loadKrs() {
        const res = await fetch('/api/pusat/krs', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        const container = document.getElementById('krs-list');
        container.innerHTML = data.length === 0 ? '<p class="p-6 text-center text-sm font-medium text-slate-400">Belum ada aktivitas terekam.</p>' : '';
        data.forEach(k => {
            const statusStyle = {
                'Sukses': 'text-emerald-700 bg-emerald-50 border-emerald-200',
                'Gagal': 'text-red-700 bg-red-50 border-red-200'
            }[k.status] || 'text-slate-700 bg-slate-50 border-slate-200';
            container.innerHTML += `
            <div class="p-4 px-5 flex items-center justify-between hover:bg-slate-50/50 transition-colors">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="mono text-[10px] font-bold bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded border border-indigo-100">NODE ${k.regional}</span>
                        <span class="font-bold text-slate-800 text-sm">${k.nama_mahasiswa}</span>
                    </div>
                    <div class="text-[11px] font-medium text-slate-500 flex items-center gap-1.5"><span class="mono text-slate-400">${k.kode_mk}</span> &bull; ${k.nama_mk}</div>
                </div>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-md border ${statusStyle}">${k.status}</span>
            </div>`;
        });
    }

    async function loadNilai() {
        const res = await fetch('/api/pusat/nilai', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        const container = document.getElementById('nilai-list');
        container.innerHTML = data.length === 0 ? '<p class="p-6 text-center text-sm font-medium text-slate-400">Belum ada aktivitas terekam.</p>' : '';
        data.forEach(n => {
            container.innerHTML += `
            <div class="p-4 px-5 flex items-center justify-between hover:bg-slate-50/50 transition-colors">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="mono text-[10px] font-bold bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded border border-indigo-100">NODE ${n.regional}</span>
                        <span class="font-bold text-slate-800 text-sm">${n.nama_mahasiswa}</span>
                    </div>
                    <div class="text-[11px] font-medium text-slate-500 flex items-center gap-1.5"><span class="mono text-slate-400">${n.kode_mk}</span> &bull; ${n.nama_mk}</div>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-[9px] font-bold uppercase tracking-wider mb-0.5 ${n.is_finalisasi ? 'text-emerald-500' : 'text-amber-500'}">${n.is_finalisasi ? 'Final' : 'Draft'}</span>
                    <span class="text-sm font-black px-2.5 py-0.5 rounded-md border ${n.is_finalisasi ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200 shadow-sm'}">${n.nilai_akhir ?? '-'}</span>
                </div>
            </div>`;
        });
    }

    async function tambahMataKuliah() {
        const targetRegional = Array.from(document.querySelectorAll('.mk-target:checked')).map(el => parseInt(el.value));
        if (targetRegional.length === 0) return showToast('Pilih minimal 1 regional target untuk distribusi.', true);

        const body = {
            kode_mk: document.getElementById('mk-kode').value, nama_mk: document.getElementById('mk-nama').value,
            id_prodi: document.getElementById('mk-prodi').value, sks: parseInt(document.getElementById('mk-sks').value),
            kode_mk_prasyarat: document.getElementById('mk-prasyarat').value || null, target_regional: targetRegional,
        };
        const res = await fetch('/api/pusat/mata-kuliah', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(body) });
        const data = await res.json();
        showToast(data.message || JSON.stringify(data.errors), !res.ok);
        if (res.ok) loadMataKuliah();
    }

    async function loadMataKuliah() {
        const res = await fetch('/api/pusat/mata-kuliah', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        [1, 2, 3].forEach(id => {
            const regional = data[id];
            const container = document.getElementById(`mk-list-${id}`);
            if (!regional || regional.status === 'offline') {
                container.innerHTML = '<div class="p-6 text-center"><div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-500 mb-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><p class="text-xs font-bold text-red-500">NODE OFFLINE</p></div>';
                return;
            }
            container.innerHTML = regional.data.length === 0 ?
                '<p class="p-6 text-center text-sm font-medium text-slate-400">Database Kosong.</p>' :
                regional.data.map(m => `
                <div class="p-3 px-4 hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-center justify-between mb-1">
                        <span class="mono text-[11px] font-bold text-indigo-600 bg-indigo-50 px-1.5 rounded">${m.kode_mk}</span>
                        <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 rounded border border-slate-200">${m.sks} SKS</span>
                    </div>
                    <div class="text-xs font-bold text-slate-800 leading-snug">${m.nama_mk}</div>
                </div>`).join('');
        });
    }

    async function tambahUser() {
        const body = {
            id_regional: parseInt(document.getElementById('akun-regional').value), role: document.getElementById('akun-role').value,
            name: document.getElementById('akun-nama').value, email: document.getElementById('akun-email').value,
            password: document.getElementById('akun-password').value, ref_id: document.getElementById('akun-refid').value || null,
        };
        const res = await fetch('/api/pusat/users', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(body) });
        const data = await res.json();
        showToast(data.message || JSON.stringify(data.errors), !res.ok);
        if (res.ok) loadUser();
    }

    async function loadUser() {
        const idRegional = document.getElementById('akun-filter-regional').value;
        const role = document.getElementById('akun-filter-role').value;
        const url = role ? `/api/pusat/users?regional=${idRegional}&role=${role}` : `/api/pusat/users?regional=${idRegional}`;
        const res = await fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const data = await res.json();

        document.getElementById('akun-list').innerHTML = data.length === 0 ?
            '<p class="p-6 text-center text-sm font-medium text-slate-400">Tidak ada akun dengan filter yang dipilih.</p>' :
            data.map(u => `
            <div class="p-4 px-6 flex items-center justify-between hover:bg-slate-50/50 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 font-bold shrink-0 shadow-sm">${u.name.charAt(0)}</div>
                    <div>
                        <div class="font-bold text-slate-800 text-sm">${u.name}</div>
                        <div class="text-[11px] text-slate-500 font-medium">${u.email}</div>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100">${u.role}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right flex flex-col items-end">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Ref ID / NIP</span>
                    <span class="mono text-xs font-bold text-slate-700 bg-slate-50 px-2 py-1 rounded border border-slate-200">${u.ref_id ?? 'N/A'}</span>
                </div>
            </div>`).join('');
    }

    async function tambahRuangan() {
        const targetRegional = Array.from(document.querySelectorAll('.ruangan-target:checked')).map(el => parseInt(el.value));
        if (targetRegional.length === 0) return showToast('Pilih minimal 1 regional target untuk distribusi.', true);

        const body = { id_ruangan: document.getElementById('ruangan-id').value, nama_ruangan: document.getElementById('ruangan-nama').value, kapasitas: parseInt(document.getElementById('ruangan-kapasitas').value), target_regional: targetRegional };
        const res = await fetch('/api/pusat/ruangan', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(body) });
        const data = await res.json();
        showToast(data.message || JSON.stringify(data.errors), !res.ok);
        if (res.ok) loadRuangan();
    }

    async function loadRuangan() {
        const res = await fetch('/api/pusat/ruangan', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        [1, 2, 3].forEach(id => {
            const regional = data[id];
            const container = document.getElementById(`ruangan-list-${id}`);
            if (!regional || regional.status === 'offline') {
                container.innerHTML = '<div class="p-6 text-center"><div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-500 mb-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><p class="text-xs font-bold text-red-500">NODE OFFLINE</p></div>';
                return;
            }
            container.innerHTML = regional.data.length === 0 ?
                '<p class="p-6 text-center text-sm font-medium text-slate-400">Database Kosong.</p>' :
                regional.data.map(r => `
                <div class="p-3 px-4 hover:bg-slate-50/50 transition-colors flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-800 mb-0.5">${r.nama_ruangan}</div>
                        <div class="mono text-[10px] text-slate-400">${r.id_ruangan}</div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-md border border-slate-200 whitespace-nowrap">Kap: ${r.kapasitas}</span>
                </div>`).join('');
        });
    }

    async function tambahDosen() {
        const targetRegional = Array.from(document.querySelectorAll('.dosen-target:checked')).map(el => parseInt(el.value));
        if (targetRegional.length === 0) return showToast('Pilih minimal 1 regional target untuk distribusi.', true);

        const body = { nip: document.getElementById('dosen-nip').value, nama_dosen: document.getElementById('dosen-nama').value, id_prodi: document.getElementById('dosen-prodi').value, target_regional: targetRegional };
        const res = await fetch('/api/pusat/dosen', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(body) });
        const data = await res.json();
        showToast(data.message || JSON.stringify(data.errors), !res.ok);
        if (res.ok) loadDosen();
    }

    async function loadDosen() {
        const res = await fetch('/api/pusat/dosen', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        [1, 2, 3].forEach(id => {
            const regional = data[id];
            const container = document.getElementById(`dosen-list-${id}`);
            if (!regional || regional.status === 'offline') {
                container.innerHTML = '<div class="p-6 text-center"><div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-500 mb-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><p class="text-xs font-bold text-red-500">NODE OFFLINE</p></div>';
                return;
            }
            container.innerHTML = regional.data.length === 0 ?
                '<p class="p-6 text-center text-sm font-medium text-slate-400">Database Kosong.</p>' :
                regional.data.map(d => `
                <div class="p-3 px-4 hover:bg-slate-50/50 transition-colors">
                    <div class="text-xs font-bold text-slate-800 mb-0.5 truncate">${d.nama_dosen}</div>
                    <div class="mono text-[10px] text-indigo-500 font-bold">${d.nip}</div>
                </div>`).join('');
        });
    }

    async function sinkronSekarang() {
        showToast('Memulai sinkronisasi global antar-node...', false);
        const res = await fetch('/api/pusat/sinkron', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
        const data = await res.json();

        if(res.ok) {
            // HTML styling inside Toast
            const syncDetails = `
                <div class="font-bold text-sm mb-1">${data.message}</div>
                <div class="text-xs space-y-1 mt-2 p-2 bg-slate-800/50 rounded-lg border border-slate-700">
                    <div><span class="text-slate-400">KRS Terkonsolidasi:</span> <span class="font-bold text-white">${data.ringkasan.krs_konsolidasi}</span></div>
                    <div><span class="text-slate-400">Nilai Terkonsolidasi:</span> <span class="font-bold text-white">${data.ringkasan.nilai_konsolidasi}</span></div>
                </div>
            `;
            showToast(syncDetails, false);

            // Reload monitoring data after sync
            loadRingkasan();
            loadKrs();
            loadNilai();
        } else {
            showToast('Gagal melakukan sinkronisasi: ' + (data.message || 'Server Error'), true);
        }
    }

    // Inisialisasi awal
    loadRingkasan();
    loadKrs();
    loadNilai();
    </script>
</body>

</html>
