<?php
require_once __DIR__ . '/classes/Order.php';
require_once __DIR__ . '/classes/Kiosk.php';

$kiosks = [
    new Kiosk(
        'KSK-01',
        'Kiosk Perpustakaan Pusat UMM (Lt. 1)',
        ['a4' => 88, 'f4' => 65],
        true
    ),
    new Kiosk(
        'KSK-02',
        'Kiosk GKB 1 UMM',
        ['a4' => 95, 'f4' => 82],
        true
    ),
    new Kiosk(
        'KSK-03',
        'Kiosk GKB 2 (Lobby Lt. 1)',
        ['a4' => 70, 'f4' => 45],
        true
    ),
    new Kiosk(
        'KSK-04',
        'Kiosk Lab Informatika & Komputer (Lt. 6)',
        ['a4' => 18, 'f4' => 20],
        false 
    ),
];

$orders = [
    new Order(
        'CPT-8492',
        'Skripsi_BAB_1-5_Final.pdf',
        'a4_80',       // A4 Tebal 80 GSM
        'bw',          // Hitam Putih
        48,            // 48 Halaman
        true,          // Duplex (Bolak-balik) -> Diskon 5%
        'lakban',      // Jilid Lakban Mika
        'Siap Diambil',
        '10 menit yang lalu'
    ),
    new Order(
        'CPT-8493',
        'Jurnal_Farmasi_Klinis.pdf',
        'a4_75',       // A4 Standard
        'color_full',  // Full Color Foto
        18,
        false,         // Simplex (1 Sisi)
        'steples',     // Jilid Steples Sudut
        'Sedang Diproses',
        '25 menit yang lalu'
    ),
    new Order(
        'CPT-8494',
        'Laporan_Praktikum_Fisika.docx',
        'a4_75',
        'color_std',   // Warna Standar
        14,
        true,          // Duplex
        'steples',
        'Menunggu Cetak',
        '40 menit yang lalu'
    ),
    new Order(
        'CPT-8495',
        'Makalah_Ekonomi_Makro.pdf',
        'a4_75',
        'bw',
        26,
        true,
        'none',        // Tanpa Jilid
        'Siap Diambil',
        '1 jam yang lalu'
    ),
    new Order(
        'CPT-8496',
        'Poster_Seminar_Nasional.png',
        'f4_80',       // F4 Folio 80 GSM
        'color_full',
        4,
        false,
        'none',
        'Menunggu Cetak',
        '2 jam yang lalu'
    ),
    new Order(
        'CPT-8497',
        'Buku_Panduan_Himpunan.pdf',
        'a4_80',
        'bw',
        85,
        true,
        'lakban',
        'Selesai',
        '3 jam yang lalu'
    ),
];

$totalOrdersCount = count($orders);
$totalRevenue = 0;
$pendingOrdersCount = 0;
$completedOrdersCount = 0;

foreach ($orders as $ord) {
    $totalRevenue += $ord->calculateTotal();

    if ($ord->getStatus() === 'Menunggu Cetak' || $ord->getStatus() === 'Sedang Diproses') {
        $pendingOrdersCount++;
    } else {
        $completedOrdersCount++;
    }
}

$onlineKiosksCount = 0;
foreach ($kiosks as $k) {
    if ($k->isOnline()) {
        $onlineKiosksCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Panel Admin Kiosk Cepatik - PHP OOP & Server-Side Rendering Dashboard">
  <title>Panel Admin Kiosk (PHP OOP &amp; SSR) - Cepatik</title>

  <link rel="icon" type="image/svg+xml" href="assets/images/icon.svg">
  <link rel="icon" type="image/png" href="assets/images/icon.png">
  <link rel="shortcut icon" href="favicon.ico">

  <link rel="stylesheet" href="assets/css/style.css">

  <!-- Database Layer & Script Logika Admin Real-Time -->
  <script src="db/database.js" defer></script>
  <script src="assets/js/admin.js" defer></script>

  <style>
    .kiosk-grid-section {
      margin-bottom: 24px;
    }

    .kiosk-grid-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 16px;
      margin-top: 12px;
    }

    .kiosk-ssr-card {
      background: #FFFFFF;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 16px;
      box-shadow: var(--shadow-sm);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 12px;
      transition: all 0.2s ease;
    }

    .kiosk-ssr-card:hover {
      border-color: var(--peach);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .kiosk-ssr-card.is-offline {
      opacity: 0.85;
      background: #FDFBF7;
    }

    .kiosk-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }

    .kiosk-card-id {
      font-size: 11px;
      font-family: var(--font-mono);
      font-weight: 800;
      color: var(--text-muted);
      background: #F4F1EB;
      padding: 2px 6px;
      border-radius: 4px;
    }

    .kiosk-badge-status {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 11px;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 999px;
    }

    .kiosk-badge-status.status-online {
      background: #ECFDF5;
      color: #065F46;
      border: 1px solid #A7F3D0;
    }

    .kiosk-badge-status.status-offline {
      background: #FEF2F2;
      color: #991B1B;
      border: 1px solid #FECACA;
    }

    .kiosk-location-title {
      font-size: 13.5px;
      font-weight: 800;
      color: var(--text-dark);
      margin: 4px 0 0;
    }

    .kiosk-hardware-mini {
      display: flex;
      flex-direction: column;
      gap: 8px;
      padding: 8px 0;
      border-top: 1px solid #F1ECE6;
      border-bottom: 1px solid #F1ECE6;
    }

    .kiosk-bar-item {
      font-size: 11px;
      font-family: var(--font-mono);
    }

    .kiosk-bar-label {
      display: flex;
      justify-content: space-between;
      margin-bottom: 2px;
    }

    .kiosk-bar-track {
      width: 100%;
      height: 5px;
      background: #EBE6DF;
      border-radius: 999px;
      overflow: hidden;
    }

    .kiosk-bar-fill {
      height: 100%;
      border-radius: 999px;
      transition: width 0.3s ease;
    }

    .fill-emerald { background: #10B981; }
    .fill-amber { background: #F59E0B; }
    .fill-rose { background: #EF4444; }

    .kiosk-footer-info {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 11px;
      color: var(--text-muted);
    }
  </style>
</head>

<body class="admin-body">

  <!-- 1. TOPBAR ADMIN -->
  <aside class="admin-topbar">
    <div class="admin-topbar-info">
      <span class="admin-topbar-item">
        <span class="status-dot"></span>
        <span id="activeKioskTopbar"><strong>Kiosk Perpustakaan Pusat UMM (Lt. 1)</strong> (Online 24/7)</span>
      </span>
      <span class="admin-topbar-item" style="color: #A89F91;">|</span>
      <span class="admin-topbar-item" id="liveClock">
        Server Time: <?= date('d M Y, H:i:s') ?> WIB
      </span>
    </div>

    <div class="admin-topbar-links">
      <a href="index.php" title="Kembali ke Landing Page Cepatik">
        <svg class="icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
          <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <span>Lihat Web Beranda</span>
      </a>
      <span style="color: #635A52;">•</span>
      <a href="cetak.php" title="Buka Portal Cetak Pelanggan">
        <svg class="icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="6 9 6 2 18 2 18 9"></polyline>
          <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
          <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        <span>Portal Cetak Siswa</span>
      </a>
    </div>
  </aside>

  <!-- 2. HEADER PANEL ADMIN & PROFIL OPERATOR AKTIF -->
  <header class="admin-header">
    <div class="admin-header-inner">
      <div class="admin-brand-wrap">
        <a href="index.php" class="logo" title="Cepatik Cloud Printing">
          <img src="assets/images/logo.svg" alt="Logo Cepatik">
        </a>
        <span class="admin-badge-panel">
          <span class="status-dot"></span>
          PHP OOP &amp; SSR Dashboard
        </span>
      </div>

      <!-- Profil Operator & Pilihan Lokasi Kiosk -->
      <div class="admin-header-actions">

        <!-- Filter Lokasi Kiosk -->
        <div class="kiosk-filter-select-wrap" id="kioskSwitcherWrap">
          <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted);">Titik Kiosk:</span>
          <select id="selectKioskAdminFilter" class="kiosk-filter-select" title="Saring antrean berdasarkan lokasi kiosk">
            <option value="all">Semua Lokasi Kiosk</option>
            <option value="umm_perpus">📍 Kiosk Perpustakaan Pusat UMM (Lt. 1)</option>
            <option value="umm_gkb1">📍 Kiosk GKB 1 UMM</option>
            <option value="umm_gkb2">📍 Kiosk GKB 2 (Lobby Lt. 1)</option>
            <option value="umm_lab_it">📍 Kiosk Lab Komputer</option>
            <option value="umm_kantin_teknik">📍 Kiosk Kantin Teknik</option>
          </select>
        </div>

        <!-- Badge Profil Operator Aktif -->
        <div class="operator-profile-badge" title="Akun Operator Aktif">
          <span class="operator-avatar" id="operatorAvatar">A</span>
          <div>
            <div style="font-weight: 800; font-size: 12px; color: var(--text-dark);" id="operatorNameDisplay">Admin Pusat</div>
            <div style="font-size: 10px; color: var(--text-muted);" id="operatorRoleDisplay">Super Admin</div>
          </div>
        </div>

        <!-- Tombol Logout -->
        <button type="button" class="btn-logout" onclick="logoutAdmin()" title="Keluar dari sesi admin">
          <svg class="icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span>Keluar</span>
        </button>

      </div>
    </div>
  </header>

  <!-- 3. KONTEN UTAMA -->
  <main class="admin-container">

    <!-- METRIK RINGKASAN (4 CARDS) -->
    <section class="admin-metrics-grid" aria-label="Ringkasan Metrik Kiosk">

      <article class="admin-metric-card">
        <div class="admin-metric-top">
          <span class="admin-metric-title">Total Pesanan (OOP)</span>
          <div class="admin-metric-icon-wrap coral">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="16" y1="13" x2="8" y2="13"></line>
              <line x1="16" y1="17" x2="8" y2="17"></line>
              <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
          </div>
        </div>
        <div class="admin-metric-value" id="metricTotalOrders"><?= $totalOrdersCount; ?> Pesanan</div>
        <div class="admin-metric-footer">
          <span style="color: #059669; font-weight: 700;">Instance Array '$orders'</span>
          <span>· Terenkapsulasi</span>
        </div>
      </article>

      <article class="admin-metric-card">
        <div class="admin-metric-top">
          <span class="admin-metric-title">Total Omzet (QRIS)</span>
          <div class="admin-metric-icon-wrap emerald">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <rect x="2" y="5" width="20" height="14" rx="2"></rect>
              <line x1="2" y1="10" x2="22" y2="10"></line>
            </svg>
          </div>
        </div>
        <div class="admin-metric-value" id="metricTotalRevenue">Rp <?= number_format($totalRevenue, 0, ',', '.'); ?></div>
        <div class="admin-metric-footer">
          <span style="color: #059669; font-weight: 700;">✓ $order->calculateTotal()</span>
          <span>· Server-side &amp; Sync</span>
        </div>
      </article>

      <article class="admin-metric-card">
        <div class="admin-metric-top">
          <span class="admin-metric-title">Perlu Diproses</span>
          <div class="admin-metric-icon-wrap amber">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </div>
        </div>
        <div class="admin-metric-value" id="metricPendingOrders" style="color: #D97706;"><?= $pendingOrdersCount; ?> Antrean</div>
        <div class="admin-metric-footer">
          <span id="metricCompletedOrders" style="font-weight: 700; color: #4B5563;"><?= $completedOrdersCount; ?> Selesai</span>
          <span>· Antrean Aktif</span>
        </div>
      </article>

      <article class="admin-metric-card">
        <div class="admin-metric-top">
          <span class="admin-metric-title">Unit Kiosk Aktif</span>
          <div class="admin-metric-icon-wrap indigo">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <polyline points="6 9 6 2 18 2 18 9"></polyline>
              <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
              <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
          </div>
        </div>
        <div class="admin-metric-value" style="color: #2563EB;">
          <?= $onlineKiosksCount; ?> / <?= count($kiosks); ?> Kiosk
        </div>
        <div class="admin-metric-footer">
          <span style="color: #059669; font-weight: 700;">$kiosk->isOnline()</span>
          <span>· Siap Mencetak</span>
        </div>
      </article>

    </section>

    <!-- STATUS MESIN & KETERSEDIAAN KERTAS KIOSK (PHP OOP SSR) -->
    <section class="admin-card kiosk-grid-section">
      <div class="admin-card-head">
        <div class="admin-card-head-title">
          <h2>
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--primary);">
              <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
              <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
              <line x1="6" y1="6" x2="6.01" y2="6"></line>
              <line x1="6" y1="18" x2="6.01" y2="18"></line>
            </svg>
            Status Mesin &amp; Ketersediaan Kertas Kiosk
          </h2>
          <p>Dirender secara Server-Side Rendering (SSR) dari array objek 'classes/Kiosk.php'</p>
        </div>
        <div style="font-size: 11.5px; color: var(--text-muted); font-family: var(--font-mono);">
          <span>Perulangan: <strong>foreach ($kiosks as $kiosk)</strong></span>
        </div>
      </div>

      <div class="kiosk-grid-cards">
        <?php 
        foreach ($kiosks as $kiosk): 
            $a4Pct = $kiosk->getA4Percentage();
            $f4Pct = $kiosk->getF4Percentage();
            $isOnline = $kiosk->isOnline();
        ?>
          <article class="kiosk-ssr-card <?= $isOnline ? '' : 'is-offline'; ?>">
            <div>
              <div class="kiosk-card-header">
                <span class="kiosk-card-id"><?= $kiosk->getKioskId(); ?></span>

                <?php if ($isOnline): ?>
                  <span class="kiosk-badge-status status-online">
                    <span class="status-dot"></span> Online
                  </span>
                <?php else: ?>
                  <span class="kiosk-badge-status status-offline">
                    <span class="status-dot" style="background:#DC2626; box-shadow:none;"></span> Offline
                  </span>
                <?php endif; ?>
              </div>

              <h3 class="kiosk-location-title"><?= htmlspecialchars($kiosk->getLocationName()); ?></h3>
            </div>

            <div class="kiosk-hardware-mini">
              <div class="kiosk-bar-item">
                <div class="kiosk-bar-label">
                  <span>Tray A4:</span>
                  <strong><?= $a4Pct; ?>%</strong>
                </div>
                <div class="kiosk-bar-track">
                  <div class="kiosk-bar-fill <?= $a4Pct > 50 ? 'fill-emerald' : ($a4Pct > 20 ? 'fill-amber' : 'fill-rose'); ?>" 
                       style="width: <?= $a4Pct; ?>%;"></div>
                </div>
              </div>

              <div class="kiosk-bar-item">
                <div class="kiosk-bar-label">
                  <span>Tray F4:</span>
                  <strong><?= $f4Pct; ?>%</strong>
                </div>
                <div class="kiosk-bar-track">
                  <div class="kiosk-bar-fill <?= $f4Pct > 50 ? 'fill-emerald' : ($f4Pct > 20 ? 'fill-amber' : 'fill-rose'); ?>" 
                       style="width: <?= $f4Pct; ?>%;"></div>
                </div>
              </div>
            </div>

            <div class="kiosk-footer-info">
              <span>Status Operasional:</span>
              <?php if ($isOnline): ?>
                <strong style="color: #059669;">✓ Standby (Siap Cetak)</strong>
              <?php else: ?>
                <strong style="color: #DC2626;">✕ Perawatan Mesin</strong>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- TABEL ANTREAN PESANAN REAL-TIME & SSR -->
    <section class="admin-card">
      <div class="admin-card-head">
        <div class="admin-card-head-title">
          <h2>
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--primary);">
              <line x1="8" y1="6" x2="21" y2="6"></line>
              <line x1="8" y1="12" x2="21" y2="12"></line>
              <line x1="8" y1="18" x2="21" y2="18"></line>
              <line x1="3" y1="6" x2="3.01" y2="6"></line>
              <line x1="3" y1="12" x2="3.01" y2="12"></line>
              <line x1="3" y1="18" x2="3.01" y2="18"></line>
            </svg>
            Tabel Antrean Pesanan (Server-Side Rendered &amp; Live Sync)
          </h2>
          <p>Dirender dari instance objek 'Order' &amp; tersinkronisasi real-time dengan CepatikDB</p>
        </div>

        <div style="font-size: 11.5px; color: var(--text-muted); font-family: var(--font-mono);">
          <span>Method: <strong>foreach ($orders as $order)</strong></span>
        </div>
      </div>

      <!-- Toolbar Filter & Search -->
      <div class="admin-toolbar">
        <div class="admin-filter-group" role="tablist" aria-label="Filter status pesanan">
          <button type="button" class="admin-filter-btn active" data-filter="Semua">
            Semua <span class="admin-filter-count"><?= $totalOrdersCount; ?></span>
          </button>
          <button type="button" class="admin-filter-btn" data-filter="Menunggu Cetak">
            Menunggu Cetak <span class="admin-filter-count">0</span>
          </button>
          <button type="button" class="admin-filter-btn" data-filter="Sedang Diproses">
            Sedang Diproses <span class="admin-filter-count">0</span>
          </button>
          <button type="button" class="admin-filter-btn" data-filter="Siap Diambil">
            Siap Diambil <span class="admin-filter-count">0</span>
          </button>
          <button type="button" class="admin-filter-btn" data-filter="Selesai">
            Selesai <span class="admin-filter-count">0</span>
          </button>
        </div>

        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
          <div class="admin-search-wrap">
            <svg class="icon admin-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="searchOrderInput" class="admin-search-input" placeholder="Cari ID Tiket atau Nama File..." autocomplete="off">
          </div>

          <button type="button" class="btn btn-cek" onclick="handlePurgeFiles()" title="Pemusnahan otomatis file dokumen pesanan selesai demi privasi mahasiswa (Zero Data Retention)" style="font-size: 12px; padding: 7px 14px; white-space: nowrap;">
            <svg class="icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
            <span>Retensi Berkas</span>
          </button>
        </div>
      </div>

      <!-- Tabel Antrean -->
      <div class="admin-table-container">
        <table class="admin-table" aria-label="Daftar Antrean Pesanan Kiosk">
          <thead>
            <tr>
              <th style="width: 130px;">Kode Tiket</th>
              <th style="width: 160px;">Waktu &amp; Lokasi</th>
              <th>Nama File</th>
              <th>Opsi Cetak</th>
              <th style="width: 130px;">Total Biaya</th>
              <th style="width: 170px;">Status Pesanan</th>
              <th style="width: 120px; text-align: center;">Aksi</th>
            </tr>
          </thead>
          <tbody id="adminOrderTableBody">
            <?php foreach ($orders as $order): 
                $status = $order->getStatus();
            ?>
              <tr>
                <td>
                  <span class="badge-ticket"><?= $order->getId(); ?></span>
                </td>
                <td>
                  <div style="font-weight: 700; color: var(--text-dark);"><?= $order->getTimestamp(); ?></div>
                  <div style="font-size: 10.5px; color: #059669; font-weight: 600; margin-top: 2px;">📍 Kiosk Perpustakaan Pusat UMM</div>
                </td>
                <td>
                  <div class="admin-file-cell">
                    <div class="admin-file-icon"><?= $order->getFileExtension(); ?></div>
                    <div class="admin-file-meta">
                      <h4><?= htmlspecialchars($order->getFileName()); ?></h4>
                      <p><?= $order->getPages(); ?> Hlm · 1x Rangkap</p>
                    </div>
                  </div>
                </td>
                <td>
                  <span style="font-size: 11px; color: #4B5563;"><?= htmlspecialchars($order->getPrintSpecs()); ?></span>
                </td>
                <td>
                  <div style="font-weight: 900; color: var(--text-dark); font-size: 14px;"><?= $order->getFormattedTotal(); ?></div>
                </td>
                <td>
                  <span class="badge-status siap">
                    <span class="status-dot"></span>
                    <span><?= htmlspecialchars($status); ?></span>
                  </span>
                </td>
                <td style="text-align: center;">
                  <button type="button" class="btn-action btn-action-print" onclick="printThermalReceipt('<?= addslashes($order->getId()); ?>', '<?= addslashes($order->getFileName()); ?>', '<?= addslashes($order->getPrintSpecs()); ?>', '<?= addslashes($order->getFormattedTotal()); ?>', '<?= addslashes($order->getTimestamp()); ?>')">
                    <span>Struk</span>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- Empty State -->
        <div id="emptyOrderState" class="admin-empty-state" style="display: none;">
          <div class="admin-empty-icon">
            <svg class="icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
          </div>
          <h3>Tidak Ada Antrean Pesanan</h3>
          <p>Belum ada pesanan yang sesuai dengan filter atau lokasi kiosk aktif saat ini.</p>
        </div>
      </div>
    </section>

  </main>

  <!-- 4. KONTAINER KHUSUS STRUK KASIR / THERMAL (#receipt-print-area) -->
  <div id="receipt-print-area" aria-hidden="true"></div>

  <!-- Toast Notifikasi Ringan -->
  <div id="adminToast" class="admin-toast" role="alert"></div>

  <script>
    function printThermalReceipt(ticketId, fileName, specs, totalFormatted, timestamp) {
      if (typeof cetakStruk === 'function') {
        cetakStruk(ticketId);
      } else {
        window.print();
      }
    }
  </script>

</body>

</html>
