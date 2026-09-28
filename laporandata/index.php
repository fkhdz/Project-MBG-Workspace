<?php
include "../config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!$koneksi_db) {
    die("<div style='padding:2rem; font-family:sans-serif;'><h3 style='color:red;'>Koneksi Gagal!</h3></div>");
}

// Statistik
$total_penerima       = (int)(mysqli_fetch_row(mysqli_query($koneksi_db, "SELECT COUNT(*) FROM PENERIMA"))[0] ?? 0);
$total_mitra          = (int)(mysqli_fetch_row(mysqli_query($koneksi_db, "SELECT COUNT(*) FROM MITRA"))[0] ?? 0);
$total_paket          = (int)(mysqli_fetch_row(mysqli_query($koneksi_db, "SELECT COALESCE(SUM(kuantitas),0) FROM PAKETBANTUAN"))[0] ?? 0);
$distribusi_selesai   = (int)(mysqli_fetch_row(mysqli_query($koneksi_db, "SELECT COUNT(*) FROM DISTRIBUSI WHERE status_pengiriman IN ('Selesai','Diterima','Terkirim')"))[0] ?? 0);
$distribusi_proses    = (int)(mysqli_fetch_row(mysqli_query($koneksi_db, "SELECT COUNT(*) FROM DISTRIBUSI WHERE status_pengiriman NOT IN ('Selesai','Diterima','Terkirim')"))[0] ?? 0);

// Data chart
$data_chart = mysqli_query($koneksi_db, "
    SELECT DATE_FORMAT(tanggal_kirim, '%Y-%m') AS bulan, COUNT(*) AS total
    FROM DISTRIBUSI
    GROUP BY bulan
    ORDER BY bulan ASC
    LIMIT 12
");
$bulan = [];
$jumlah_distribusi = [];
while ($row = mysqli_fetch_assoc($data_chart)) {
    $bulan[] = date('M Y', strtotime($row['bulan']));
    $jumlah_distribusi[] = (int)$row['total'];
}

$data_kategori = mysqli_query($koneksi_db, "
    SELECT kategori_penerima, COUNT(*) AS total
    FROM PENERIMA
    GROUP BY kategori_penerima
");
$label_kategori = [];
$total_kategori = [];
while ($row = mysqli_fetch_assoc($data_kategori)) {
    $label_kategori[] = $row['kategori_penerima'] ?: 'Lainnya';
    $total_kategori[] = (int)$row['total'];
}

$query_stok = mysqli_query($koneksi_db, "SELECT * FROM ITEM ORDER BY stok_gudang ASC LIMIT 5");

$current_page = 'laporan';
$page_title   = 'Laporan & Analitik';

$extra_head = '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-7xl mx-auto w-full">

        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="../index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Laporan Data</span>
        </nav>

        <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Pusat Analitik & Laporan</h2>
            <p class="text-slate-500 mt-1.5 text-sm">Ringkasan metrik statistik dan performa program bantuan sosial.</p>
        </div>

        <!-- Statistik Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-soft border border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="size-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">groups</span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Penerima</p>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight"><?= number_format($total_penerima) ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-soft border border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="size-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">handshake</span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mitra</p>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight"><?= number_format($total_mitra) ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-soft border border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="size-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stok Paket</p>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight"><?= number_format($total_paket) ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-soft border border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="size-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Selesai</p>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight"><?= number_format($distribusi_selesai) ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-soft border border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="size-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">pending</span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Proses</p>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight"><?= number_format($distribusi_proses) ?></p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-soft border border-slate-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tren Distribusi Bulanan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Periode 12 bulan terakhir</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-400">show_chart</span>
                </div>
                <div class="relative h-[280px] w-full"><canvas id="chartDistribusi"></canvas></div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-soft border border-slate-100 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Demografi Penerima</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Berdasarkan kategori</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-400">donut_large</span>
                </div>
                <div class="relative flex-1 w-full flex justify-center items-center min-h-[220px]"><canvas id="chartKategori"></canvas></div>
            </div>
        </div>

        <!-- Tabel Bawah -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Stok Terendah -->
            <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-rose-500 text-[20px]">warning</span>
                        <h3 class="text-sm font-bold text-slate-900">Peringatan Stok Terendah</h3>
                    </div>
                    <a href="../item/index.php" class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors">Kelola Item &rarr;</a>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tersedia</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        <?php if (mysqli_num_rows($query_stok) > 0): 
                            mysqli_data_seek($query_stok, 0);
                            while ($item = mysqli_fetch_assoc($query_stok)):
                                $stok = (int)$item['stok_gudang'];
                                if ($stok == 0) {
                                    $status_badge = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-500/20">
                                        <span class="material-symbols-outlined text-[12px]">block</span> Habis</span>';
                                } elseif ($stok < 50) {
                                    $status_badge = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-500/20">
                                        <span class="material-symbols-outlined text-[12px]">warning</span> Menipis</span>';
                                } else {
                                    $status_badge = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-500/20">
                                        <span class="material-symbols-outlined text-[12px]">check_circle</span> Aman</span>';
                                }
                        ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3 text-sm">
                                    <p class="font-semibold text-slate-800"><?= htmlspecialchars($item['nama_item']) ?></p>
                                    <p class="text-xs text-slate-400 mt-0.5">Satuan: <?= htmlspecialchars($item['satuan']) ?></p>
                                </td>
                                <td class="px-5 py-3 text-sm font-bold text-slate-700"><?= number_format($stok) ?></td>
                                <td class="px-5 py-3 text-right"><?= $status_badge ?></td>
                            </tr>
                        <?php endwhile;
                        else: ?>
                            <tr><td colspan="3" class="px-5 py-12 text-center text-sm text-slate-500">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-3xl text-slate-300">category</span>
                                    <p class="mt-2">Belum ada data barang di gudang.</p>
                                </div>
                            </td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Distribusi Terbaru -->
            <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary-600 text-[20px]">history</span>
                        <h3 class="text-sm font-bold text-slate-900">Aktivitas Distribusi Terbaru</h3>
                    </div>
                    <a href="../distribusi/index.php" class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors">Lihat Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Pengiriman</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        <?php
                        $latest = mysqli_query($koneksi_db, "
                            SELECT d.distribusi_id, m.nama_mitra, r.nama_lengkap, d.tanggal_kirim, d.status_pengiriman
                            FROM DISTRIBUSI d
                            LEFT JOIN MITRA m ON d.mitra_id = m.mitra_id
                            LEFT JOIN PENERIMA r ON d.penerima_id = r.penerima_id
                            ORDER BY d.tanggal_kirim DESC, d.distribusi_id DESC
                            LIMIT 5
                        ");
                        if (mysqli_num_rows($latest) > 0):
                            while ($row = mysqli_fetch_assoc($latest)):
                                $status = strtolower($row['status_pengiriman']);
                                if (strpos($status,'selesai')!==false || strpos($status,'terkirim')!==false || strpos($status,'diterima')!==false) {
                                    $badge = 'bg-emerald-50 text-emerald-700 ring-emerald-500/20';
                                    $icon = 'check_circle';
                                } elseif (strpos($status,'proses')!==false || strpos($status,'kirim')!==false || strpos($status,'pending')!==false) {
                                    $badge = 'bg-amber-50 text-amber-700 ring-amber-500/20';
                                    $icon = 'local_shipping';
                                } elseif (strpos($status,'gagal')!==false || strpos($status,'batal')!==false) {
                                    $badge = 'bg-rose-50 text-rose-700 ring-rose-500/20';
                                    $icon = 'cancel';
                                } else {
                                    $badge = 'bg-slate-50 text-slate-700 ring-slate-500/20';
                                    $icon = 'info';
                                }
                        ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3 text-sm">
                                    <p class="font-semibold text-slate-800">Ke: <?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></p>
                                    <p class="text-xs text-slate-500 mt-0.5">Via: <?= htmlspecialchars($row['nama_mitra'] ?? '-') ?> <span class="text-slate-400">#<?= $row['distribusi_id'] ?></span></p>
                                </td>
                                <td class="px-5 py-3 text-sm text-slate-600"><?= $row['tanggal_kirim'] ? date('d M Y', strtotime($row['tanggal_kirim'])) : '-' ?></td>
                                <td class="px-5 py-3 text-right">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset <?= $badge ?>">
                                        <span class="material-symbols-outlined text-[12px]"><?= $icon ?></span>
                                        <?= htmlspecialchars($row['status_pengiriman']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else: ?>
                            <tr><td colspan="3" class="px-5 py-12 text-center text-sm text-slate-500">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-3xl text-slate-300">local_shipping</span>
                                    <p class="mt-2">Belum ada data distribusi terbaru.</p>
                                </div>
                            </td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>
</div>

<script>
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#64748b';
Chart.defaults.scale.grid.color = '#f1f5f9';

const ctxLine = document.getElementById('chartDistribusi').getContext('2d');
new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: <?= json_encode($bulan) ?>,
        datasets: [{
            label: 'Jumlah Distribusi',
            data: <?= json_encode($jumlah_distribusi) ?>,
            borderColor: '#0ea5e9',
            backgroundColor: 'rgba(14, 165, 233, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#0ea5e9',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 12,
                titleFont: { size: 13 },
                bodyFont: { size: 14, weight: 'bold' }
            }
        },
        scales: {
            y: { beginAtZero: true, border: { dash: [4, 4] } },
            x: { grid: { display: false } }
        }
    }
});

const ctxPie = document.getElementById('chartKategori').getContext('2d');
new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($label_kategori) ?>,
        datasets: [{
            data: <?= json_encode($total_kategori) ?>,
            backgroundColor: [
                '#0ea5e9',
                '#8b5cf6',
                '#f59e0b',
                '#10b981',
                '#f43f5e'
            ],
            borderWidth: 2,
            borderColor: '#ffffff',
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { usePointStyle: true, padding: 16, font: { size: 12 } }
            },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 10
            }
        },
        cutout: '70%',
        layout: { padding: { bottom: 10 } }
    }
});
</script>
</body>
</html>