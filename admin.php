<?php
require_once __DIR__ . '/classes/Order.php';
require_once __DIR__ . '/classes/Kiosk.php';

$kiosks = [
    new Kiosk(
        'KSK-01',
        'Kiosk Perpustakaan UI',
        ['a4' => 88, 'f4' => 65],
        true
    ),
    new Kiosk(
        'KSK-02',
        'Mitra Barokah ITB Ganesha',
        ['a4' => 95, 'f4' => 82],
        true
    ),
    new Kiosk(
        'KSK-03',
        'Kiosk Gelanggang UGM',
        ['a4' => 70, 'f4' => 45],
        true
    ),
    new Kiosk(
        'KSK-04',
        'Kiosk Lab Komputer Rektorat',
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

  <style>
    .kiosk-grid-section {
      margin-bottom: 24px;
    }

    .kiosk-grid-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
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

    .ticket-badge {
      font-family: var(--font-mono);
      font-size: 12.5px;
      font-weight: 900;
      color: var(--primary);
      background: var(--primary-soft);
      padding: 3px 8px;
      border-radius: 6px;
      border: 1px solid var(--peach);
      display: inline-block;
    }

    .pin-badge {
      font-family: var(--font-mono);
      font-size: 13px;
      font-weight: 900;
      background: #FEF3C7;
      color: #92400E;
      padding: 3px 8px;
      border-radius: 6px;
      border: 1px solid #FCD34D;
      letter-spacing: 1.5px;
      display: inline-block;
    }

    .file-icon-mini {
      font-size: 10px;
      font-family: var(--font-mono);
      font-weight: 900;
      background: #F4EBE6;
      color: var(--primary);
      padding: 2px 6px;
      border-radius: 4px;
      border: 1px solid var(--peach);
    }

    .btn-cetak-struk {
      background: #ECFDF5;
      color: #065F46;
      border: 1px solid #A7F3D0;
      padding: 6px 12px;
      font-size: 12px;
      font-weight: 700;
      border-radius: var(--radius-pill);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: all 0.2s ease;
      font-family: inherit;
    }

    .btn-cetak-struk:hover {
      background: #059669;
      color: #FFFFFF;
      border-color: #059669;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
    }

    #receipt-print-area {
      display: none;
    }

    @media print {
      html, body {
        background: #FFFFFF !important;
        margin: 0 !important;
        padding: 0 !important;
      }

      body * {
        visibility: hidden !important;
      }

      #receipt-print-area,
      #receipt-print-area * {
        visibility: visible !important;
      }

      #receipt-print-area {
        display: block !important;
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 58mm !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 2mm 3mm !important;
        background: #FFFFFF !important;
        color: #000000 !important;
        font-family: 'Courier New', Courier, monospace !important;
        font-size: 11px !important;
        line-height: 1.35 !important;
        box-sizing: border-box !important;
      }

      .thermal-header {
        text-align: center;
        margin-bottom: 6px;
      }

      .thermal-title {
        font-weight: 900;
        font-size: 13px;
        letter-spacing: 0.5px;
      }

      .thermal-sub {
        font-size: 9.5px;
        color: #333333;
      }

      .thermal-line {
        border-top: 1px dashed #000000;
        margin: 5px 0;
      }

      .thermal-double-line {
        border-top: 2px dashed #000000;
        margin: 6px 0;
      }

      .thermal-box {
        border: 1px solid #000000;
        padding: 6px;
        text-align: center;
        margin: 6px 0;
      }

      .thermal-box-ticket {
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 1px;
      }

      .thermal-box-pin {
        font-size: 14px;
        font-weight: 900;
        margin-top: 2px;
      }

      .thermal-row {
        display: flex;
        justify-content: space-between;
        margin: 2px 0;
        font-size: 10px;
      }

      .thermal-total {
        font-size: 12px;
        font-weight: 900;
        margin-top: 4px;
      }

      .thermal-footer {
        text-align: center;
        font-size: 9px;
        margin-top: 8px;
        padding-top: 4px;
      }
    }
  </style>
</head>

<body class="admin-body">

  <aside class="admin-topbar">
    <div class="admin-topbar-info">
      <span class="admin-topbar-item">
        <span class="status-dot"></span>
        <span><strong>Admin Server (PHP OOP &amp; SSR Mode)</strong></span>
      </span>
      <span class="admin-topbar-item" style="color: #A89F91;">|</span>
      <span class="admin-topbar-item">
        ⚡ Week 5 &amp; 6 Web Programming Syllabus Demo
      </span>
      <span class="admin-topbar-item" style="color: #A89F91;">|</span>
      <span class="admin-topbar-item" id="serverTime">
        Server Time: <?= date('d M Y, H:i:s') ?> WIB
      </span>
    </div>

    <div class="admin-topbar-links">
      <a href="index.php" title="Buka Halaman Depan">
        <svg class="icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
          <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <span>Lihat Web Beranda</span>
      </a>
      <span style="color: #635A52;">•</span>
      <a href="cetak.php" title="Buka Portal Cetak">
        <svg class="icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="6 9 6 2 18 2 18 9"></polyline>
          <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
          <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        <span>Portal Cetak Pelanggan</span>
      </a>
    </div>
  </aside>

  <header class="admin-header">
    <div class="admin-header-inner">
      <div class="admin-brand-wrap">
        <a href="index.php" class="logo" title="Cepatik Cloud Printing">
          <img src="assets/images/logo.svg" alt="Logo Cepatik">
        </a>
        <span class="admin-badge-panel">
          <span class="status-dot"></span>
          PHP OOP Dashboard
        </span>
      </div>

      <div class="admin-header-actions">
        <div class="operator-profile-badge" title="Akun Operator Aktif">
          <span class="operator-avatar">A</span>
          <div>
            <div style="font-weight: 800; font-size: 12px; color: var(--text-dark);">Dosen / Asisten Praktikum</div>
            <div style="font-size: 10px; color: var(--text-muted);">Week 5-6 Evaluator</div>
          </div>
        </div>

        <a href="admin.html" class="btn btn-cek" style="font-size: 11.5px; padding: 6px 12px;" title="Beralih ke Versi Client-Side JS">
          <span>Mode JS (HTML)</span>
        </a>
      </div>
    </div>
  </header>

  <main class="admin-container">

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
        <div class="admin-metric-value"><?= $totalOrdersCount; ?> Pesanan</div>
        <div class="admin-metric-footer">
          <span style="color: #059669; font-weight: 700;">Instance Array '$orders'</span>
          <span>· Terenkapsulasi</span>
        </div>
      </article>

      <article class="admin-metric-card">
        <div class="admin-metric-top">
          <span class="admin-metric-title">Total Omzet (Kalkulasi)</span>
          <div class="admin-metric-icon-wrap emerald">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <rect x="2" y="5" width="20" height="14" rx="2"></rect>
              <line x1="2" y1="10" x2="22" y2="10"></line>
            </svg>
          </div>
        </div>
        <div class="admin-metric-value">Rp <?= number_format($totalRevenue, 0, ',', '.'); ?></div>
        <div class="admin-metric-footer">
          <span style="color: #059669; font-weight: 700;">✓ $order->calculateTotal()</span>
          <span>· Server-side</span>
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
        <div class="admin-metric-value" style="color: #D97706;"><?= $pendingOrdersCount; ?> Antrean</div>
        <div class="admin-metric-footer">
          <span style="font-weight: 700; color: #4B5563;"><?= $completedOrdersCount; ?> Selesai</span>
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
                <strong style="color: #059669;">
                  ✓ Standby (Siap Cetak)
                </strong>
              <?php else: ?>
                <strong style="color: #DC2626;">
                  ✕ Perawatan Mesin
                </strong>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

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
            Tabel Antrean Pesanan (Server-Side Rendered)
          </h2>
          <p>Setiap baris tabel merepresentasikan 1 instance objek 'Order' yang di-render via PHP echo</p>
        </div>

        <div style="font-size: 11.5px; color: var(--text-muted); font-family: var(--font-mono);">
          <span>Method: <strong>foreach ($orders as $order)</strong></span>
        </div>
      </div>

      <div class="admin-table-container">
        <table class="admin-table" aria-label="Daftar Antrean Pesanan Kiosk">
          <thead>
            <tr>
              <th style="width: 130px;">Kode Tiket</th>
              <th style="width: 140px;">Waktu</th>
              <th>Nama File</th>
              <th>Opsi &amp; Spesifikasi Cetak</th>
              <th style="width: 130px;">Total Biaya</th>
              <th style="width: 170px;">Status Pesanan</th>
              <th style="width: 130px; text-align: center;">Aksi Kasir</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            foreach ($orders as $order): 
                $status = $order->getStatus();
            ?>
              <tr>
                <td>
                  <span class="ticket-badge">
                    <?= $order->getId(); ?>
                  </span>
                </td>

                <td>
                  <span style="font-size: 11.5px; color: var(--text-muted); font-family: var(--font-mono);">
                    <?= $order->getTimestamp(); ?>
                  </span>
                </td>

                <td>
                  <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="file-icon-mini">
                      <?= $order->getFileExtension(); ?>
                    </span>
                    <strong style="font-size: 12.5px; color: var(--text-dark);">
                      <?= htmlspecialchars($order->getFileName()); ?>
                    </strong>
                  </div>
                </td>

                <td>
                  <span style="font-size: 11px; color: #4B5563;">
                    <?= htmlspecialchars($order->getPrintSpecs()); ?>
                  </span>
                </td>

                <td>
                  <strong style="color: var(--primary); font-family: var(--font-mono); font-size: 13.5px;">
                    <?= $order->getFormattedTotal(); ?>
                  </strong>
                </td>

                <td>
                  <?php if ($status === 'Siap Diambil' || $status === 'Siap di Loker'): ?>
                    <span class="badge-status siap">
                      <span class="status-dot"></span> ✓ Siap Diambil
                    </span>
                  <?php elseif ($status === 'Sedang Diproses'): ?>
                    <span class="badge-status proses">
                      <span class="status-dot"></span> ⚡ Sedang Dicetak
                    </span>
                  <?php elseif ($status === 'Selesai'): ?>
                    <span class="badge-status selesai">
                      <span class="status-dot"></span> ✓ Selesai Diambil
                    </span>
                  <?php else: ?>
                    <span class="badge-status menunggu">
                      <span class="status-dot"></span> ⏳ Menunggu Cetak
                    </span>
                  <?php endif; ?>
                </td>

                <td style="text-align: center;">
                  <button type="button" class="btn-cetak-struk" 
                    title="Cetak Struk Kasir Thermal (58mm)"
                    onclick="printThermalReceipt(
                      '<?= addslashes($order->getId()); ?>',
                      '<?= addslashes($order->getFileName()); ?>',
                      '<?= addslashes($order->getPrintSpecs()); ?>',
                      '<?= addslashes($order->getFormattedTotal()); ?>',
                      '<?= addslashes($order->getTimestamp()); ?>'
                    )">
                    <svg class="icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <polyline points="6 9 6 2 18 2 18 9"></polyline>
                      <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                      <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Cetak Struk</span>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>

  <div id="receipt-print-area" aria-hidden="true">
  </div>

  <script>
    function printThermalReceipt(ticketId, fileName, specs, totalFormatted, timestamp) {
      const receiptArea = document.getElementById('receipt-print-area');
      if (!receiptArea) {
        alert('Area cetak struk tidak ditemukan!');
        return;
      }

      receiptArea.innerHTML = `
        <div class="thermal-header">
          <div class="thermal-title">CEPATIK KIOSK</div>
          <div class="thermal-sub">SELF-SERVICE CLOUD PRINTING</div>
          <div class="thermal-sub">PT Cepatik Teknologi Nusantara</div>
          <div class="thermal-sub">Waktu: ${timestamp || 'Hari ini'}</div>
        </div>

        <div class="thermal-double-line"></div>

        <div class="thermal-box">
          <div style="font-size: 9px; font-weight: 700;">KODE TIKET PESANAN:</div>
          <div class="thermal-box-ticket">${ticketId}</div>
          <div style="margin-top: 5px; font-size: 9.5px; color: #222; font-weight: 600;">
            Klaim cetakan langsung di layar mesin Kiosk menggunakan Kode Tiket di atas.
          </div>
        </div>

        <div class="thermal-line"></div>

        <div style="margin: 4px 0;">
          <div style="font-weight: 700; word-break: break-word;">${fileName}</div>
          <div style="font-size: 9.5px; color: #444; margin-top: 2px;">${specs}</div>
        </div>

        <div class="thermal-line"></div>

        <div class="thermal-row">
          <span>Metode Bayar:</span>
          <strong>QRIS Dinamis</strong>
        </div>
        <div class="thermal-row">
          <span>Status Bayar:</span>
          <strong>LUNAS (VERIFIED)</strong>
        </div>
        <div class="thermal-row thermal-total">
          <span>TOTAL BIAYA:</span>
          <span>${totalFormatted}</span>
        </div>

        <div class="thermal-double-line"></div>

        <div class="thermal-footer">
          <div>*** SIMPAN STRUK INI ***</div>
          <div>Gunakan Kode Tiket di atas pada mesin Kiosk.</div>
          <div>File dihapus otomatis setelah pencetakan selesai.</div>
          <div>CS WhatsApp: +62 812-1646-3462</div>
        </div>
      `;

      setTimeout(function () {
        window.print();
      }, 100);
    }
  </script>

</body>

</html>
