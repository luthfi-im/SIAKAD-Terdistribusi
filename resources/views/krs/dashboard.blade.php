<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIAKAD Terdistribusi — Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Kombinasi font Sans untuk keterbacaan & Mono untuk kode MK/SKS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .mono { font-family: 'JetBrains Mono', 'Consolas', monospace; }
    </style>
</head>

<body class="bg-slate-50 min-h-screen pb-16">

    <!-- Header Section -->
    <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center border border-slate-700 shadow-inner">
                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21l9-5-9-5-9 5 9 5z"></path></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight">SIAKAD Terdistribusi</h1>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $user->name }} &bull; {{ $namaRegional }}</p>
                </div>
            </div>

            <div class="flex items-center gap-5">
                <span id="regional-badge" class="hidden sm:inline-flex items-center gap-2 text-[11px] font-bold mono bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-full border border-emerald-500/20 tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    NODE ONLINE
                </span>
                <div class="h-6 w-px bg-slate-700 hidden sm:block"></div>
                <form method="POST" action="/logout" class="m-0">
                    @csrf
                    <button class="text-sm font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-lg transition-colors border border-slate-700 hover:border-slate-600">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-8 space-y-8">

        <!-- Info Mahasiswa Card -->
        <section id="mhs-info" class="hidden bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8 relative overflow-hidden">
            <!-- Aksen background geometris -->
            <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-br from-slate-50 to-slate-100 rounded-bl-full -z-0"></div>

            <h2 class="text-xs font-bold text-slate-400 mb-5 uppercase tracking-widest relative z-10">Data Akademik Mahasiswa</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm relative z-10">
                <div class="space-y-1.5">
                    <span class="text-slate-500 font-medium text-xs">Nama Lengkap</span>
                    <span id="mhs-nama" class="block font-bold text-slate-900 text-base"></span>
                </div>
                <div class="space-y-1.5">
                    <span class="text-slate-500 font-medium text-xs">Program Studi</span>
                    <span id="mhs-prodi" class="block font-bold text-slate-900 text-base"></span>
                </div>
                <div class="space-y-1.5">
                    <span class="text-slate-500 font-medium text-xs">IPS Terakhir</span>
                    <div class="inline-flex items-center justify-center bg-slate-100 text-slate-800 px-3 py-1 rounded-md mono font-bold border border-slate-200">
                        <span id="mhs-ips"></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kelas Tersedia Section -->
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Kelas Tersedia
                </h2>
            </div>
            <div id="kelas-list" class="grid gap-4"></div>
        </section>

        <!-- Kolom Dua (KRS & KHS) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <!-- Riwayat KRS -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Riwayat Pengajuan
                    </h2>
                </div>
                <p id="krs-empty" class="hidden p-8 text-sm font-medium text-slate-400 text-center bg-white rounded-2xl border-2 border-dashed border-slate-200">Belum ada pengajuan KRS.</p>
                <div id="krs-list" class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_15px_rgb(0,0,0,0.02)] overflow-hidden"></div>
            </section>

            <!-- KHS -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Kartu Hasil Studi
                    </h2>
                    <span id="ipk-badge" class="text-xs font-bold px-3 py-1.5 rounded-lg bg-slate-900 text-white shadow-sm mono tracking-wide"></span>
                </div>
                <p id="nilai-empty" class="hidden p-8 text-sm font-medium text-slate-400 text-center bg-white rounded-2xl border-2 border-dashed border-slate-200">Belum ada nilai yang tercatat.</p>
                <div id="nilai-list" class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_15px_rgb(0,0,0,0.02)] divide-y divide-slate-50 overflow-hidden"></div>
            </section>
        </div>

        <!-- Modern Toast Notification -->
        <div id="toast" class="hidden fixed bottom-6 right-6 max-w-sm px-5 py-4 rounded-xl shadow-2xl text-sm font-semibold flex items-center gap-3 transform transition-all z-50 border"></div>
    </main>

    <script>
        const API = '/api/krs';

        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            // Menambahkan ikon pada toast agar lebih informatif
            const icon = isError
                ? '<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                : '<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

            toast.innerHTML = `${icon} <span>${message}</span>`;
            toast.className = `fixed bottom-6 right-6 max-w-sm px-5 py-4 rounded-xl shadow-2xl text-sm font-semibold flex items-center gap-3 z-50 border ${isError ? 'bg-red-950 text-red-50 border-red-900' : 'bg-slate-900 text-white border-slate-800'}`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }

        async function loadDashboard() {
            const res = await fetch(`${API}/dashboard`, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) {
                showToast('Gagal memuat data. Silakan login ulang.', true);
                return;
            }
            const data = await res.json();
            renderMahasiswa(data.mahasiswa);
            renderKelas(data.kelas_tersedia);
            renderKrs(data.krs_saya);
        }

        function renderMahasiswa(m) {
            document.getElementById('mhs-info').classList.remove('hidden');
            document.getElementById('mhs-nama').textContent = m.nama_mahasiswa;
            document.getElementById('mhs-prodi').textContent = m.id_prodi;
            document.getElementById('mhs-ips').textContent = m.ips_terakhir;
        }

        function renderKelas(kelasList) {
            const container = document.getElementById('kelas-list');
            container.innerHTML = '';
            kelasList.forEach(k => {
                const penuh = k.sisa_kuota <= 0;
                // Penyesuaian variasi warna kuota
                const kuotaColor = penuh ? 'text-red-700 bg-red-50 border-red-200' : k.sisa_kuota <= 5 ?
                    'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200';

                const prasyarat = k.mata_kuliah.kode_mk_prasyarat ?
                    `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 uppercase tracking-widest">Syarat: ${k.mata_kuliah.kode_mk_prasyarat}</span>` : '';

                container.innerHTML += `
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col md:flex-row md:items-center justify-between gap-5 transition-all hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:border-slate-200 group">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="mono text-xs font-bold px-2.5 py-1 rounded-md bg-slate-100 text-slate-600">${k.mata_kuliah.kode_mk}</span>
                            <h3 class="font-bold text-slate-900 text-base group-hover:text-slate-700 transition-colors">${k.mata_kuliah.nama_mk}</h3>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-500 font-medium">
                            <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> ${k.dosen.nama_dosen}</span>
                            <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg> ${k.ruangan.nama_ruangan}</span>
                            <span class="font-bold text-slate-700 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded">${k.mata_kuliah.sks} SKS</span>
                            ${prasyarat}
                        </div>
                    </div>
                    <div class="flex items-center justify-between md:justify-end gap-5 border-t border-slate-100 md:border-t-0 pt-4 md:pt-0 mt-2 md:mt-0 w-full md:w-auto">
                        <div class="text-right">
                            <span class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Sisa Kuota</span>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-md border ${kuotaColor} mono inline-block">${k.sisa_kuota} / ${k.kuota}</span>
                        </div>
                        <button
                            onclick="ambilKelas(${k.id_kelas})"
                            ${penuh ? 'disabled' : ''}
                            class="text-sm font-bold px-5 py-2.5 rounded-lg transition-all shadow-sm flex items-center gap-2 ${penuh ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-slate-900 text-white hover:bg-slate-800 hover:shadow-md active:scale-95'}">
                            ${penuh ? 'Penuh' : 'Ambil <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>'}
                        </button>
                    </div>
                </div>`;
            });
        }

        function renderKrs(krsList) {
            const container = document.getElementById('krs-list');
            const empty = document.getElementById('krs-empty');
            if (krsList.length === 0) {
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');
            container.innerHTML = '';

            const grouped = {};
            krsList.forEach(k => {
                const sem = k.kelas.mata_kuliah.semester;
                if (!grouped[sem]) grouped[sem] = [];
                grouped[sem].push(k);
            });

            const semesterUrut = Object.keys(grouped).sort((a, b) => a - b);

            semesterUrut.forEach(sem => {
                container.innerHTML += `
                <div class="px-5 py-2.5 bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Semester ${sem}
                </div>`;

                [...grouped[sem]].reverse().forEach(k => {
                    const statusStyle = {
                        'Sukses': 'text-emerald-700 bg-emerald-50 border-emerald-200',
                        'Gagal': 'text-red-700 bg-red-50 border-red-200',
                        'Antre': 'text-amber-700 bg-amber-50 border-amber-200',
                    }[k.status] || 'text-slate-700 bg-slate-50 border-slate-200';

                    container.innerHTML += `
                    <div class="p-5 flex items-center justify-between text-sm border-b border-slate-50 last:border-b-0 hover:bg-slate-50/50 transition-colors">
                        <div>
                            <div class="mono text-xs font-bold text-slate-400 mb-0.5">${k.kelas.mata_kuliah.kode_mk}</div>
                            <div class="font-semibold text-slate-900">${k.kelas.mata_kuliah.nama_mk}</div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-md border ${statusStyle}">${k.status}</span>
                    </div>`;
                });
            });
        }

        async function ambilKelas(idKelas) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const res = await fetch(`${API}/ambil`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ id_kelas: idKelas }),
            });
            const data = await res.json();
            if (res.status === 202) {
                showToast(data.message);
                setTimeout(loadDashboard, 1500);
            } else {
                showToast(data.message, true);
            }
        }

        async function loadNilai() {
            const res = await fetch('/api/krs/nilai', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            document.getElementById('ipk-badge').textContent = `IPK: ${data.ipk} (${data.total_sks_lulus} SKS)`;

            const container = document.getElementById('nilai-list');
            const empty = document.getElementById('nilai-empty');
            if (data.nilai.length === 0) {
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');
            container.innerHTML = '';
            data.nilai.forEach(n => {
                const statusBadge = n.is_finalisasi ?
                    `<span class="text-xs font-bold px-3 py-1 rounded-md border border-emerald-200 bg-emerald-50 text-emerald-700 shadow-sm">${n.grade}</span>` :
                    `<span class="text-xs font-bold px-2.5 py-1 rounded-md border border-slate-200 bg-slate-50 text-slate-500">Belum Final</span>`;

                container.innerHTML += `
                <div class="p-5 flex items-center justify-between text-sm hover:bg-slate-50/50 transition-colors">
                    <div>
                        <div class="mono text-xs font-bold text-slate-400 mb-0.5">${n.kode_mk} <span class="font-medium text-slate-300 ml-1">&bull; ${n.sks} SKS</span></div>
                        <div class="font-semibold text-slate-900">${n.nama_mk}</div>
                    </div>
                    ${statusBadge}
                </div>`;
            });
        }

        loadDashboard();
        loadNilai();
    </script>
</body>

</html>
