<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Dosen — SIAKAD Terdistribusi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .mono { font-family: 'JetBrains Mono', 'Consolas', monospace; }

        /* Custom Scrollbar for tables */
        .custom-scrollbar::-webkit-scrollbar { height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>

<body class="bg-slate-50 min-h-screen pb-20">

    <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-indigo-500/20 rounded-xl flex items-center justify-center border border-indigo-500/30 shadow-inner">
                    <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight">Portal Dosen</h1>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $user->name }} &bull; {{ $namaRegional }}</p>
                </div>
            </div>

            <div class="flex items-center gap-5">
                <span class="hidden sm:inline-flex items-center gap-2 text-[11px] font-bold mono bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-full border border-emerald-500/20 tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    SINKRONISASI AKTIF
                </span>
                <div class="h-6 w-px bg-slate-700 hidden sm:block"></div>
                <form method="POST" action="/logout" class="m-0">
                    @csrf
                    <button class="text-sm font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-lg transition-colors border border-slate-700 hover:border-slate-600">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-8 space-y-10">

        <section>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Kelas yang Diampu
                </h2>
            </div>
            <div id="kelas-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5"></div>
        </section>

        <section id="detail-kelas" class="hidden space-y-6 animate-[fadeIn_0.3s_ease-in-out]">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-slate-200 border-dashed">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight" id="detail-title">Detail Kelas</h2>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">Mode Pengelolaan</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                    <div class="bg-slate-50/50 p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Penilaian Mahasiswa
                        </h3>
                    </div>
                    <div class="p-2">
                        <div id="nilai-form" class="divide-y divide-slate-50 max-h-[400px] overflow-y-auto custom-scrollbar pr-2"></div>
                    </div>
                    <div class="p-5 bg-slate-50 border-t border-slate-100">
                        <button onclick="finalisasiNilai()" class="w-full bg-red-600 text-white text-sm font-bold py-2.5 rounded-lg hover:bg-red-700 active:scale-[0.98] transition-all shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Finalisasi Nilai Kelas
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                    <div class="bg-slate-50/50 p-5 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Input Presensi Baru
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Tanggal Pertemuan</label>
                                <input type="date" id="presensi-tanggal" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-900 transition-all font-medium text-slate-700">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Pertemuan Ke-</label>
                                <input type="number" id="presensi-pertemuan" min="1" max="16" placeholder="1-16" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-900 transition-all font-medium text-slate-700">
                            </div>
                        </div>
                        <div class="border border-slate-100 rounded-xl overflow-hidden mb-5">
                            <div id="presensi-form" class="divide-y divide-slate-50 max-h-[250px] overflow-y-auto custom-scrollbar"></div>
                        </div>
                        <button onclick="simpanPresensi()" class="w-full bg-slate-900 text-white text-sm font-bold py-2.5 rounded-lg hover:bg-slate-800 active:scale-[0.98] transition-all shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Simpan Data Presensi
                        </button>
                    </div>
                </div>

            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden mt-6">
                <div class="bg-slate-50/50 p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Rekapitulasi Kehadiran
                    </h3>
                    <button onclick="loadRekapPresensi()" class="text-[11px] font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Muat Ulang
                    </button>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-bold">Mahasiswa</th>
                                <th scope="col" class="px-4 py-4 font-bold text-center">Hadir</th>
                                <th scope="col" class="px-4 py-4 font-bold text-center">Izin</th>
                                <th scope="col" class="px-4 py-4 font-bold text-center">Sakit</th>
                                <th scope="col" class="px-4 py-4 font-bold text-center">Alpa</th>
                                <th scope="col" class="px-4 py-4 font-bold text-center">Total</th>
                                <th scope="col" class="px-6 py-4 font-bold text-right">% Hadir</th>
                            </tr>
                        </thead>
                        <tbody id="rekap-presensi-body" class="divide-y divide-slate-100"></tbody>
                    </table>
                    <p id="rekap-empty" class="text-sm font-medium text-slate-400 text-center py-10 hidden border-t border-slate-100">Belum ada data presensi yang tercatat.</p>
                </div>
            </div>
        </section>

        <div id="toast" class="hidden fixed bottom-6 right-6 max-w-sm px-5 py-4 rounded-xl shadow-2xl text-sm font-semibold flex items-center gap-3 transform transition-all z-50 border"></div>
    </main>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
    const API = '/api/dosen';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let currentKelasId = null;
    let peserta = [];

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

    async function loadKelas() {
        const res = await fetch(`${API}/kelas`, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        });
        const kelasList = await res.json();
        const container = document.getElementById('kelas-list');
        container.innerHTML = '';

        kelasList.forEach(k => {
            container.innerHTML += `
                <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-col justify-between cursor-pointer transition-all hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:border-indigo-300 group h-full" onclick="bukaKelas(${k.id_kelas}, '${k.mata_kuliah.nama_mk}')">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="mono text-xs font-bold px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">${k.mata_kuliah.kode_mk}</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-indigo-700 transition-colors mb-2">${k.mata_kuliah.nama_mk}</h3>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div class="text-xs text-slate-500 font-medium flex flex-col gap-1">
                            <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg> ${k.ruangan.nama_ruangan}</span>
                            <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg> ${k.sisa_kuota}/${k.kuota} peserta</span>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white text-slate-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                </div>`;
        });
    }

    async function bukaKelas(idKelas, namaMk) {
        currentKelasId = idKelas;
        const detailSection = document.getElementById('detail-kelas');
        detailSection.classList.remove('hidden');

        // Animasi trigger ulang
        detailSection.style.animation = 'none';
        detailSection.offsetHeight; // trigger reflow
        detailSection.style.animation = null;

        document.getElementById('detail-title').textContent = `${namaMk}`;

        // Scroll pelan ke area detail
        setTimeout(() => {
            detailSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);

        const res = await fetch(`${API}/kelas/${idKelas}/peserta`, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        });
        peserta = await res.json();

        // === Render form Nilai ===
        const form = document.getElementById('nilai-form');
        form.innerHTML = '';
        peserta.forEach(p => {
            const nilaiValue = p.nilai_akhir !== null ? p.nilai_akhir : '';
            const isLocked = p.is_finalisasi;
            const statusBadge = p.nilai_akhir === null ?
                '<span class="text-[10px] uppercase font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">Kosong</span>' :
                isLocked ?
                '<span class="text-[10px] uppercase font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Final</span>' :
                '<span class="text-[10px] uppercase font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">Draft</span>';

            form.innerHTML += `
            <div class="flex items-center justify-between p-3.5 hover:bg-slate-50 transition-colors group">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-slate-800">${p.mahasiswa.nama_mahasiswa}</span>
                    <span class="mono text-xs text-slate-400">${p.nim}</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex flex-col items-end gap-1">
                        ${statusBadge}
                        <div class="flex items-center gap-1.5 mt-1">
                            <input type="number" min="0" max="100" placeholder="0"
                                id="nilai-${p.nim}" value="${nilaiValue}" ${isLocked ? 'disabled' : ''}
                                class="bg-white border border-slate-300 rounded-md px-2 py-1 text-sm font-bold text-center w-16 focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-900 transition-all ${isLocked ? 'bg-slate-100 text-slate-400 border-slate-200 shadow-inner' : 'shadow-sm'}">
                            <button onclick="simpanNilai('${p.nim}')" ${isLocked ? 'disabled' : ''}
                                class="text-xs font-bold px-3 py-1.5 rounded-md transition-all shadow-sm ${isLocked ? 'bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200' : 'bg-slate-900 text-white hover:bg-slate-800 active:scale-95'}">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;
        });

        // === Render form Presensi ===
        const presensiForm = document.getElementById('presensi-form');
        presensiForm.innerHTML = '';
        peserta.forEach(p => {
            presensiForm.innerHTML += `
            <div class="flex items-center justify-between p-3.5 hover:bg-slate-50/50 transition-colors">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-slate-800">${p.mahasiswa.nama_mahasiswa}</span>
                    <span class="mono text-[11px] text-slate-400">${p.nim}</span>
                </div>
                <div class="w-28 relative">
                    <select id="presensi-${p.nim}" class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm transition-all cursor-pointer">
                        <option value="Hadir">Hadir</option>
                        <option value="Izin">Izin</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Alpa">Alpa</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>`;
        });

        loadRekapPresensi();
    }

    async function simpanNilai(nim) {
        const nilai = document.getElementById(`nilai-${nim}`).value;
        if (!nilai) return showToast('Isi nilai terlebih dahulu.', true);

        const res = await fetch(`${API}/nilai`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                id_kelas: currentKelasId,
                nim,
                nilai_akhir: parseFloat(nilai)
            }),
        });
        const data = await res.json();
        showToast(data.message, !res.ok);
    }

    async function finalisasiNilai() {
        if (!confirm('Yakin ingin memfinalisasi nilai kelas ini? Setelah difinalisasi, nilai akan terkunci secara permanen.')) return;
        const res = await fetch(`${API}/kelas/${currentKelasId}/finalisasi`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
        });
        const data = await res.json();
        showToast(data.message, !res.ok);
        // Refresh detail to lock inputs
        if(res.ok) bukaKelas(currentKelasId, document.getElementById('detail-title').textContent);
    }

    async function simpanPresensi() {
        const tanggal = document.getElementById('presensi-tanggal').value;
        const pertemuan = document.getElementById('presensi-pertemuan').value;
        if (!tanggal || !pertemuan) return showToast('Pilih tanggal dan isi nomor pertemuan terlebih dahulu.', true);

        const kehadiran = peserta.map(p => ({
            nim: p.nim,
            status: document.getElementById(`presensi-${p.nim}`).value,
        }));

        const res = await fetch(`${API}/presensi`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                id_kelas: currentKelasId,
                tanggal_pertemuan: tanggal,
                pertemuan_ke: parseInt(pertemuan),
                kehadiran,
            }),
        });
        const data = await res.json();
        showToast(data.message, !res.ok);

        if (res.ok) loadRekapPresensi();
    }

    async function loadRekapPresensi() {
        const res = await fetch(`${API}/kelas/${currentKelasId}/rekap-presensi`, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) return showToast('Gagal memuat rekap presensi.', true);
        const rekap = await res.json();

        const tbody = document.getElementById('rekap-presensi-body');
        const empty = document.getElementById('rekap-empty');
        tbody.innerHTML = '';

        if (Object.keys(rekap).length === 0) {
            empty.classList.remove('hidden');
            return;
        }
        empty.classList.add('hidden');

        peserta.forEach(p => {
            const rows = rekap[p.nim] || [];
            const count = { Hadir: 0, Izin: 0, Sakit: 0, Alpa: 0 };

            rows.forEach(r => { count[r.status] = r.jumlah; });

            const total = count.Hadir + count.Izin + count.Sakit + count.Alpa;
            const persenHadir = total > 0 ? Math.round((count.Hadir / total) * 100) : 0;

            // Badge persentase
            const persenBadge = persenHadir >= 75
                ? `<span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-1 rounded-md border border-emerald-200">${persenHadir}%</span>`
                : persenHadir >= 50
                ? `<span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-1 rounded-md border border-amber-200">${persenHadir}%</span>`
                : `<span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-1 rounded-md border border-red-200">${persenHadir}%</span>`;

            tbody.innerHTML += `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="font-semibold text-slate-800">${p.mahasiswa.nama_mahasiswa}</div>
                    <div class="mono text-[11px] text-slate-400 mt-0.5">${p.nim}</div>
                </td>
                <td class="px-4 py-4 text-center font-semibold text-emerald-600">${count.Hadir > 0 ? count.Hadir : '-'}</td>
                <td class="px-4 py-4 text-center font-medium text-slate-500">${count.Izin > 0 ? count.Izin : '-'}</td>
                <td class="px-4 py-4 text-center font-medium text-slate-500">${count.Sakit > 0 ? count.Sakit : '-'}</td>
                <td class="px-4 py-4 text-center font-semibold text-red-500">${count.Alpa > 0 ? count.Alpa : '-'}</td>
                <td class="px-4 py-4 text-center font-bold text-slate-600 bg-slate-50/50">${total}</td>
                <td class="px-6 py-4 text-right">${persenBadge}</td>
            </tr>`;
        });
    }

    loadKelas();
    </script>
</body>

</html>
