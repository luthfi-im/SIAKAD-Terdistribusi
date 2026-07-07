<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard BAAK — SIAKAD Terdistribusi</title>
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
        .tab-panel { animation: fadeIn 0.2s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen pb-20">

    <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-amber-500/20 rounded-xl flex items-center justify-center border border-amber-500/30 shadow-inner">
                    <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight">Portal BAAK</h1>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $user->name }} &bull; {{ $namaRegional }}</p>
                </div>
            </div>

            <div class="flex items-center gap-5">
                <span class="hidden sm:inline-flex items-center gap-2 text-[11px] font-bold mono bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-full border border-emerald-500/20 tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    SISTEM PUSAT AKTIF
                </span>
                <div class="h-6 w-px bg-slate-700 hidden sm:block"></div>
                <form method="POST" action="/logout" class="m-0">
                    @csrf
                    <button class="text-sm font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-lg transition-colors border border-slate-700 hover:border-slate-600">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-8">

        <div class="flex gap-2 overflow-x-auto custom-scrollbar pb-3 mb-6 border-b border-slate-200 snap-x">
            <button onclick="switchTab('mk')" id="tab-mk" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all bg-slate-900 text-white shadow-sm">Mata Kuliah</button>
            <button onclick="switchTab('bukakelas')" id="tab-bukakelas" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Buka Kelas</button>
            <button onclick="switchTab('dosen')" id="tab-dosen" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Dosen</button>
            <button onclick="switchTab('ruangan')" id="tab-ruangan" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Ruangan</button>
            <button onclick="switchTab('kalender')" id="tab-kalender" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Kalender Akademik</button>
            <button onclick="switchTab('revisi')" id="tab-revisi" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Revisi Nilai</button>
            <button onclick="switchTab('keuangan')" id="tab-keuangan" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Status Keuangan</button>
            <button onclick="switchTab('pddikti')" id="tab-pddikti" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Ekspor PDDIKTI</button>
            <button onclick="switchTab('audit')" id="tab-audit" class="tab-btn snap-start whitespace-nowrap px-4 py-2 text-sm font-bold rounded-lg transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700">Audit Log</button>
        </div>

        <section id="panel-mk" class="tab-panel space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Mata Kuliah Baru
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input id="mk-kode" placeholder="Kode MK (mis. TI401)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                    <input id="mk-nama" placeholder="Nama Mata Kuliah" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                    <input id="mk-prodi" placeholder="Kode Prodi (mis. TI)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                    <div class="grid grid-cols-2 gap-4">
                        <input id="mk-sks" type="number" min="1" max="6" placeholder="SKS" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                        <input id="mk-semester" type="number" min="1" max="8" placeholder="Semester (1-8)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                    </div>
                    <input id="mk-prasyarat" placeholder="Kode MK Prasyarat (opsional)" class="w-full md:col-span-2 bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                </div>
                <button onclick="tambahMataKuliah()" class="mt-5 w-full sm:w-auto bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-800 active:scale-95 transition-all shadow-sm">
                    Simpan Mata Kuliah
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="bg-slate-50/50 p-5 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Daftar Mata Kuliah</h3>
                </div>
                <div id="mk-list" class="divide-y divide-slate-100 text-sm"></div>
            </div>
        </section>

        <section id="panel-dosen" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Registrasi Dosen Baru
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input id="dosen-nip" placeholder="NIP (mis. D004)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all font-medium">
                    <input id="dosen-nama" placeholder="Nama Dosen" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all font-medium">
                    <input id="dosen-prodi" placeholder="Kode Prodi (mis. TI)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all font-medium">
                </div>
                <button onclick="tambahDosen()" class="mt-5 w-full sm:w-auto bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-800 active:scale-95 transition-all shadow-sm">
                    Tambahkan Dosen
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="bg-slate-50/50 p-5 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Direktori Dosen</h3>
                </div>
                <div id="dosen-list" class="divide-y divide-slate-100"></div>
            </div>
        </section>

        <section id="panel-ruangan" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Fasilitas Ruangan Baru
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input id="ruangan-id" placeholder="ID Ruangan (mis. GD-A-103)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all font-medium">
                    <input id="ruangan-nama" placeholder="Nama Ruangan" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all font-medium">
                    <input id="ruangan-kapasitas" type="number" min="1" placeholder="Kapasitas Mahasiswa" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all font-medium">
                </div>
                <button onclick="tambahRuangan()" class="mt-5 w-full sm:w-auto bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-800 active:scale-95 transition-all shadow-sm">
                    Simpan Ruangan
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="bg-slate-50/50 p-5 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Daftar Ruangan</h3>
                </div>
                <div id="ruangan-list" class="divide-y divide-slate-100"></div>
            </div>
        </section>

        <section id="panel-bukakelas" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Alokasi Kelas Baru
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    <select id="bk-mk" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium"></select>
                    <select id="bk-dosen" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium"></select>
                    <select id="bk-ruangan" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium"></select>
                    <div class="grid grid-cols-3 gap-4">
                        <select id="bk-semester" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium">
                            <option value="1">Ganjil</option>
                            <option value="2">Genap</option>
                        </select>
                        <input id="bk-tahun" type="number" placeholder="Tahun (2025)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium">
                        <input id="bk-kuota" type="number" min="1" placeholder="Kuota" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium">
                    </div>
                </div>
                <button onclick="bukaKelas()" class="w-full sm:w-auto bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-800 active:scale-95 transition-all shadow-sm">
                    Alokasikan Kelas
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="bg-slate-50/50 p-5 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Kelas Berjalan Terpusat</h3>
                </div>
                <div id="kelas-lengkap-list" class="divide-y divide-slate-100 text-sm"></div>
            </div>
        </section>

        <section id="panel-kalender" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Setup Periode Akademik
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input id="kalender-semester" placeholder="Semester (mis. Ganjil)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium">
                    <input id="kalender-tahun-ajaran" placeholder="Tahun Ajaran (mis. 2025/2026)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium">
                </div>
                <button onclick="tambahKalender()" class="mt-5 w-full sm:w-auto bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-800 active:scale-95 transition-all shadow-sm">
                    Tambah Periode
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-5 gap-3">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Manajemen Periode</h3>
                    <span class="text-xs text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-lg font-medium">Hanya 1 periode aktif yang direkomendasikan</span>
                </div>
                <div id="kalender-list" class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden"></div>
            </div>
        </section>

        <section id="panel-revisi" class="tab-panel hidden space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden h-full flex flex-col">
                    <div class="bg-slate-50/50 p-5 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Referensi Kelas</h3>
                        <p class="text-[11px] text-slate-400 mt-1">Klik kelas untuk autofill ID.</p>
                    </div>
                    <div id="kelas-referensi" class="p-3 text-sm divide-y divide-slate-50 max-h-[350px] overflow-y-auto custom-scrollbar flex-1"></div>
                </div>

                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Otorisasi Revisi Nilai
                    </h3>
                    <p class="text-[11px] text-slate-500 font-medium mb-5 bg-slate-50 p-3 rounded-lg border border-slate-100">Aksi ini memiliki hak akses level tertinggi dan akan otomatis tercatat ke dalam Audit Log sistem pusat.</p>
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">ID Kelas Target</label>
                            <input id="revisi-kelas" type="number" placeholder="Contoh: 15" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Nomor Induk Mahasiswa (NIM)</label>
                            <input id="revisi-nim" placeholder="Masukkan NIM" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Nilai Akhir (0-100)</label>
                            <input id="revisi-nilai" type="number" min="0" max="100" placeholder="Nilai Baru" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium">
                        </div>
                    </div>
                    <button onclick="revisiNilai()" class="mt-6 w-full bg-amber-600 text-white text-sm font-bold px-6 py-3 rounded-lg hover:bg-amber-700 active:scale-[0.98] transition-all shadow-sm flex items-center justify-center gap-2">
                        Simpan Revisi Permanen
                    </button>
                </div>
            </div>
        </section>

        <section id="panel-keuangan" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="bg-slate-50/50 p-6 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Clearance UKT Mahasiswa</h3>
                    <p class="text-xs text-slate-400 font-medium">Mahasiswa dengan status "Belum Lunas" akan diblokir dari modul KRS secara otomatis.</p>
                </div>
                <div id="keuangan-list" class="divide-y divide-slate-100 text-sm"></div>
            </div>
        </section>

        <section id="panel-pddikti" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Sinkronisasi Neo Feeder PDDIKTI
                </h3>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-5">
                    <input id="pddikti-tahun" type="number" value="2025" placeholder="Tahun Akademik" class="w-full sm:w-48 bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 font-bold text-slate-700">
                    <button onclick="eksporPddikti()" class="w-full sm:w-auto bg-slate-900 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-800 active:scale-95 transition-all shadow-sm flex items-center justify-center gap-2">
                        Generate Payload JSON
                    </button>
                </div>
                <div class="relative bg-slate-900 rounded-xl p-4 overflow-hidden shadow-inner">
                    <div class="absolute top-0 left-0 w-full h-8 bg-slate-800 border-b border-slate-700 flex items-center px-4">
                        <span class="text-[10px] mono text-slate-400">output.json</span>
                    </div>
                    <pre id="pddikti-result" class="pt-8 text-xs mono text-emerald-400 overflow-x-auto max-h-96 overflow-y-auto custom-scrollbar"></pre>
                </div>
            </div>
        </section>

        <section id="panel-audit" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="bg-slate-50/50 p-5 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Sistem Audit Trail</h3>
                </div>
                <div id="audit-list" class="divide-y divide-slate-100 text-sm"></div>
            </div>
        </section>

        <div id="toast" class="hidden fixed bottom-6 right-6 max-w-sm px-5 py-4 rounded-xl shadow-2xl text-sm font-semibold flex items-center gap-3 transform transition-all z-50 border"></div>
    </main>

    <script>
    const API = '/api/baak';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        const icon = isError
            ? '<svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            : '<svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

        toast.innerHTML = `${icon} <span>${message}</span>`;
        toast.className = `fixed bottom-6 right-6 max-w-sm px-5 py-4 rounded-xl shadow-2xl text-sm font-semibold flex items-start gap-3 z-50 border ${isError ? 'bg-red-950 text-red-50 border-red-900' : 'bg-slate-900 text-white border-slate-800'}`;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 4000);
    }

    function switchTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));

        // Reset tab styles
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-slate-900', 'text-white', 'shadow-sm');
            b.classList.add('text-slate-500', 'hover:bg-slate-100', 'hover:text-slate-700');
        });

        // Active tab style
        const activeTab = document.getElementById(`tab-${tab}`);
        activeTab.classList.add('bg-slate-900', 'text-white', 'shadow-sm');
        activeTab.classList.remove('text-slate-500', 'hover:bg-slate-100', 'hover:text-slate-700');

        // Show panel with quick reflow for animation
        const panel = document.getElementById(`panel-${tab}`);
        panel.classList.remove('hidden');
        panel.style.animation = 'none';
        panel.offsetHeight; // trigger reflow
        panel.style.animation = null;

        if (tab === 'audit') loadAuditLog();
        if (tab === 'revisi') loadKelasReferensi();
        if (tab === 'kalender') loadKalender();
        if (tab === 'dosen') loadDosen();
        if (tab === 'ruangan') loadRuangan();
        if (tab === 'keuangan') loadStatusKeuangan();
        if (tab === 'bukakelas') {
            loadFormBukaKelas();
            loadKelasLengkap();
        }
    }

    async function tambahMataKuliah() {
        const body = {
            kode_mk: document.getElementById('mk-kode').value,
            nama_mk: document.getElementById('mk-nama').value,
            id_prodi: document.getElementById('mk-prodi').value,
            sks: parseInt(document.getElementById('mk-sks').value),
            semester: parseInt(document.getElementById('mk-semester').value),
            kode_mk_prasyarat: document.getElementById('mk-prasyarat').value || null,
        };
        const res = await fetch(`${API}/mata-kuliah`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        showToast(data.message || JSON.stringify(data.errors), !res.ok);
        loadMataKuliahRegional();
    }

    async function loadMataKuliahRegional() {
        const res = await fetch(`${API}/mata-kuliah`, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        const grouped = {};
        data.forEach(m => {
            if (!grouped[m.semester]) grouped[m.semester] = [];
            grouped[m.semester].push(m);
        });

        let html = '';
        Object.keys(grouped).sort((a, b) => a - b).forEach(sem => {
            html += `<div class="px-5 py-2 bg-slate-50 border-y border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest sticky top-0">Semester ${sem}</div>`;
            grouped[sem].forEach(m => {
                html += `
                <div class="p-4 px-6 flex items-center justify-between hover:bg-slate-50/50 transition-colors">
                    <div>
                        <div class="mono text-xs font-bold text-slate-400 mb-0.5">${m.kode_mk}</div>
                        <div class="font-semibold text-slate-800">${m.nama_mk}</div>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded bg-slate-100 text-slate-600 border border-slate-200">${m.sks} SKS</span>
                </div>`;
            });
        });

        document.getElementById('mk-list').innerHTML = html || '<p class="p-6 text-slate-400 text-sm font-medium text-center">Belum ada mata kuliah yang terdaftar.</p>';
    }

    async function loadFormBukaKelas() {
        const [mkRes, dosenRes, ruanganRes] = await Promise.all([
            fetch(`${API}/mata-kuliah`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } }),
            fetch(`${API}/dosen`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } }),
            fetch(`${API}/ruangan`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
        ]);
        const mkList = await mkRes.json();
        const dosenList = await dosenRes.json();
        const ruanganList = await ruanganRes.json();

        document.getElementById('bk-mk').innerHTML = mkList.map(m => `<option value="${m.kode_mk}">${m.kode_mk} - ${m.nama_mk}</option>`).join('');
        document.getElementById('bk-dosen').innerHTML = dosenList.filter(d => !d.is_deleted).map(d => `<option value="${d.nip}">${d.nama_dosen}</option>`).join('');
        document.getElementById('bk-ruangan').innerHTML = ruanganList.map(r => `<option value="${r.id_ruangan}">${r.nama_ruangan} (Kap: ${r.kapasitas})</option>`).join('');
    }

    async function bukaKelas() {
        const body = {
            kode_mk: document.getElementById('bk-mk').value,
            nip_dosen: document.getElementById('bk-dosen').value,
            id_ruangan: document.getElementById('bk-ruangan').value,
            semester: parseInt(document.getElementById('bk-semester').value),
            tahun_akademik: parseInt(document.getElementById('bk-tahun').value),
            kuota: parseInt(document.getElementById('bk-kuota').value),
        };
        const res = await fetch(`${API}/kelas`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        showToast(data.message || JSON.stringify(data.errors), !res.ok);
        loadKelasLengkap();
    }

    async function loadKelasLengkap() {
        const res = await fetch(`${API}/kelas-lengkap`, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        document.getElementById('kelas-lengkap-list').innerHTML = data.map(k => `
        <div class="p-5 px-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/50 transition-colors">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="mono text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">#${k.id_kelas}</span>
                    <span class="mono text-xs font-bold text-indigo-500">${k.mata_kuliah.kode_mk}</span>
                </div>
                <div class="font-bold text-slate-800 text-sm mb-1">${k.mata_kuliah.nama_mk}</div>
                <div class="text-[11px] font-medium text-slate-500 flex items-center gap-3">
                    <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> ${k.dosen.nama_dosen}</span>
                    <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg> ${k.ruangan.nama_ruangan}</span>
                </div>
            </div>
            <div class="flex items-center gap-4 text-right">
                <div class="text-xs font-bold text-slate-600">${k.semester == 1 ? 'Ganjil' : 'Genap'} ${k.tahun_akademik}</div>
                <div class="text-xs font-bold px-3 py-1.5 rounded-lg border ${k.sisa_kuota === 0 ? 'bg-red-50 text-red-700 border-red-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'} mono flex items-center gap-1.5">
                    <span class="text-[10px] uppercase text-slate-400">Kuota</span> ${k.sisa_kuota}/${k.kuota}
                </div>
            </div>
        </div>
        `).join('');
    }

    async function loadDosen() {
        const res = await fetch(`${API}/dosen`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const list = await res.json();
        const container = document.getElementById('dosen-list');
        container.innerHTML = '';

        list.forEach(d => {
            const isDel = d.is_deleted;
            container.innerHTML += `
            <div class="flex items-center justify-between p-5 px-6 hover:bg-slate-50/50 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 font-bold shrink-0">
                        ${d.nama_dosen.charAt(0)}
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 text-sm flex items-center gap-2">
                            ${d.nama_dosen}
                            ${isDel ? '<span class="text-[9px] px-1.5 py-0.5 bg-slate-200 text-slate-500 rounded uppercase">Nonaktif</span>' : '<span class="text-[9px] px-1.5 py-0.5 bg-emerald-100 text-emerald-700 rounded uppercase">Aktif</span>'}
                        </div>
                        <div class="mono text-[11px] text-slate-400 mt-0.5">NIP: ${d.nip}</div>
                        <div class="text-[11px] font-medium text-slate-500 mt-1">Prodi ${d.id_prodi} &bull; Regional ${d.id_regional}</div>
                    </div>
                </div>
                <button onclick="toggleDosen('${d.nip}', ${!isDel})" class="text-[11px] font-bold px-4 py-2 rounded-lg transition-all border ${isDel ? 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' : 'border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-red-600 hover:border-red-200'}">
                    ${isDel ? 'Aktifkan Kembali' : 'Non-aktifkan'}
                </button>
            </div>`;
        });
    }

    async function tambahDosen() {
        const body = { nip: document.getElementById('dosen-nip').value, nama_dosen: document.getElementById('dosen-nama').value, id_prodi: document.getElementById('dosen-prodi').value };
        const res = await fetch(`${API}/dosen`, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(body) });
        const data = await res.json();
        showToast(data.message || JSON.stringify(data.errors), !res.ok);
        if (res.ok) {
            document.getElementById('dosen-nip').value = '';
            document.getElementById('dosen-nama').value = '';
            document.getElementById('dosen-prodi').value = '';
            loadDosen();
        }
    }

    async function toggleDosen(nip, isDeletedBaru) {
        const res = await fetch(`${API}/dosen/${nip}/toggle`, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ is_deleted: isDeletedBaru }) });
        const data = await res.json();
        showToast(data.message, !res.ok);
        loadDosen();
    }

    async function loadRuangan() {
        const res = await fetch(`${API}/ruangan`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const list = await res.json();
        const container = document.getElementById('ruangan-list');
        container.innerHTML = '';
        list.forEach(r => {
            container.innerHTML += `
            <div class="flex items-center justify-between p-5 px-6 hover:bg-slate-50/50 transition-colors">
                <div>
                    <div class="font-bold text-slate-800 text-sm mb-0.5">${r.nama_ruangan}</div>
                    <div class="text-[11px] font-medium text-slate-500"><span class="mono mr-2">${r.id_ruangan}</span> &bull; Regional ${r.id_regional}</div>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-[10px] uppercase font-bold text-slate-400 mb-1">Kapasitas</span>
                    <span class="text-xs font-bold px-3 py-1 rounded-md bg-slate-100 text-slate-700 mono border border-slate-200">${r.kapasitas} Mahasiswa</span>
                </div>
            </div>`;
        });
    }

    async function tambahRuangan() {
        const body = { id_ruangan: document.getElementById('ruangan-id').value, nama_ruangan: document.getElementById('ruangan-nama').value, kapasitas: parseInt(document.getElementById('ruangan-kapasitas').value) };
        const res = await fetch(`${API}/ruangan`, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(body) });
        const data = await res.json();
        showToast(data.message || JSON.stringify(data.errors), !res.ok);
        if (res.ok) {
            document.getElementById('ruangan-id').value = '';
            document.getElementById('ruangan-nama').value = '';
            document.getElementById('ruangan-kapasitas').value = '';
            loadRuangan();
        }
    }

    async function loadKalender() {
        const res = await fetch(`${API}/kalender`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const list = await res.json();
        const container = document.getElementById('kalender-list');
        container.innerHTML = '';
        list.forEach(k => {
            container.innerHTML += `
            <div class="flex items-center justify-between p-5 px-6 hover:bg-slate-50/50 transition-colors">
                <div>
                    <div class="font-bold text-slate-800 text-sm mb-1 flex items-center gap-2">
                        Semester ${k.semester}
                        ${k.status_aktif ? '<span class="flex h-2 w-2 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span>' : ''}
                    </div>
                    <div class="text-[11px] font-medium text-slate-500">Tahun Ajaran ${k.tahun_ajaran}</div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-md border ${k.status_aktif ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-500 border-slate-200'}">${k.status_aktif ? 'Aktif Saat Ini' : 'Arsip'}</span>
                    <button onclick="toggleKalender(${k.id}, ${!k.status_aktif})" class="text-[11px] font-bold px-3 py-1.5 rounded-lg transition-all border ${k.status_aktif ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}">
                        ${k.status_aktif ? 'Tutup Periode' : 'Aktifkan'}
                    </button>
                </div>
            </div>`;
        });
    }

    async function toggleKalender(id, statusBaru) {
        const res = await fetch(`${API}/kalender/${id}/toggle`, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ status_aktif: statusBaru }) });
        const data = await res.json();
        showToast(data.message, !res.ok);
        loadKalender();
    }

    async function tambahKalender() {
        const semester = document.getElementById('kalender-semester').value;
        const tahunAjaran = document.getElementById('kalender-tahun-ajaran').value;
        if (!semester || !tahunAjaran) return showToast('Isi semester dan tahun ajaran dulu.', true);
        const res = await fetch(`${API}/kalender`, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ semester, tahun_ajaran: tahunAjaran }) });
        const data = await res.json();
        showToast(data.message, !res.ok);
        if (res.ok) {
            document.getElementById('kalender-semester').value = '';
            document.getElementById('kalender-tahun-ajaran').value = '';
            loadKalender();
        }
    }

    async function loadKelasReferensi() {
        const res = await fetch(`${API}/kelas`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const kelasList = await res.json();
        const container = document.getElementById('kelas-referensi');
        container.innerHTML = '';
        kelasList.forEach(k => {
            container.innerHTML += `
        <div class="flex justify-between items-center py-2.5 px-3 hover:bg-indigo-50 rounded-lg cursor-pointer transition-colors group border border-transparent hover:border-indigo-100"
            onclick="document.getElementById('revisi-kelas').value = ${k.id_kelas}; showToast('ID Kelas ${k.id_kelas} terpilih.');">
            <div>
                <div class="font-bold text-slate-800 text-xs group-hover:text-indigo-800 transition-colors">${k.nama_mk}</div>
                <div class="mono text-[10px] text-slate-400 mt-0.5">ID: ${k.id_kelas} &bull; ${k.kode_mk}</div>
            </div>
            <span class="text-[10px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded group-hover:bg-indigo-100 group-hover:text-indigo-700">${k.dosen}</span>
        </div>`;
        });
    }

    async function revisiNilai() {
        const body = { id_kelas: parseInt(document.getElementById('revisi-kelas').value), nim: document.getElementById('revisi-nim').value, nilai_akhir: parseFloat(document.getElementById('revisi-nilai').value) };
        const res = await fetch(`${API}/nilai/revisi`, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(body) });
        const data = await res.json();
        showToast(data.message, !res.ok);
    }

    async function loadStatusKeuangan() {
        const res = await fetch(`${API}/status-keuangan`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        document.getElementById('keuangan-list').innerHTML = data.map(m => `
        <div class="p-5 px-6 flex items-center justify-between hover:bg-slate-50/50 transition-colors">
            <div>
                <div class="font-bold text-slate-800 text-sm">${m.nama_mahasiswa}</div>
                <div class="mono text-[11px] text-slate-400 mt-0.5">${m.nim}</div>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider border ${m.status === 'LUNAS' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200'}">${m.status}</span>
                <button onclick="toggleKeuangan('${m.nim}', '${m.status === 'LUNAS' ? 'BELUM LUNAS' : 'LUNAS'}')" class="text-[11px] font-bold bg-white border border-slate-200 text-slate-600 px-3 py-1.5 rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                    Toggle Status
                </button>
            </div>
        </div>
        `).join('');
    }

    async function toggleKeuangan(nim, statusBaru) {
        const res = await fetch(`${API}/status-keuangan/toggle`, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ nim, status: statusBaru }) });
        const data = await res.json();
        showToast(data.message, !res.ok);
        loadStatusKeuangan();
    }

    async function eksporPddikti() {
        const tahun = document.getElementById('pddikti-tahun').value;
        showToast('Memproses JSON dari server...', false);
        const res = await fetch(`${API}/ekspor-pddikti?tahun_akademik=${tahun}`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        document.getElementById('pddikti-result').textContent = JSON.stringify(data, null, 2);
    }

    async function loadAuditLog() {
        const res = await fetch(`${API}/audit-log`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const logs = await res.json();
        const container = document.getElementById('audit-list');
        container.innerHTML = '';
        if (logs.length === 0) {
            container.innerHTML = '<p class="p-8 text-center text-slate-400 font-medium text-sm">Tidak ada catatan audit yang tersedia.</p>';
            return;
        }
        logs.forEach(l => {
            container.innerHTML += `
            <div class="p-5 px-6 flex flex-col md:flex-row md:items-center gap-4 hover:bg-slate-50/50 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-slate-800 text-sm leading-snug">${l.aktivitas}</p>
                    <div class="flex items-center gap-2 mt-1.5 text-[11px] font-medium text-slate-500">
                        <span class="bg-slate-100 px-2 py-0.5 rounded uppercase tracking-wider text-slate-600">${l.role_user}</span>
                        <span>&bull;</span>
                        <span class="mono">${l.user_id}</span>
                    </div>
                </div>
                <div class="text-[11px] font-medium text-slate-400 bg-white border border-slate-100 px-2.5 py-1 rounded-md shadow-sm whitespace-nowrap">
                    ${l.created_at}
                </div>
            </div>`;
        });
    }

    // Inisialisasi awal
    loadMataKuliahRegional();
    </script>
</body>

</html>
