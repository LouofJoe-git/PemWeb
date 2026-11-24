<?php
/**
 * View: resources/views/admin/index.blade.php
 * Modul Anggota B (Damar Ghulamul Abror) - Panel Admin Kiosk & CRUD Database (Laravel 9 + Eloquent ORM).
 * Menerima data dari AdminController ($kiosks, $orders, $editingOrder, $totalRevenue, $totalOrdersCount, dll).
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Panel Admin Kiosk Cepatik - Laravel 9 + Eloquent ORM CRUD & Database">
  <title>Panel Admin Kiosk (Laravel 9 + CRUD + Database) - Cepatik</title>

  <link rel="icon" type="image/svg+xml" href="assets/images/icon.svg">
  <link rel="icon" type="image/png" href="assets/images/icon.png">
  <link rel="shortcut icon" href="favicon.ico">

  <link rel="stylesheet" href="assets/css/style.css">

  <script>
    window.LARAVEL_DB_ORDERS = <?= json_encode($clientOrders ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  </script>

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

    .crud-form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
      gap: 12px;
      margin-top: 14px;
    }

    .crud-field label {
      display: block;
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 4px;
    }

    .crud-field input,
    .crud-field select {
      width: 100%;
      padding: 8px 10px;
      border-radius: 8px;
      border: 1px solid var(--border-color);
      background: #FAF8F5;
      font-size: 12.5px;
      color: var(--text-dark);
    }

    .laravel-flash-banner {
      background: #ECFDF5;
      border: 1px solid #A7F3D0;
      color: #065F46;
      padding: 12px 18px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-size: 13px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: space-between;
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
          Admin Dashboard (Laravel 9 + CRUD + DB)
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
            <div style="font-weight: 800; font-size: 12px; color: var(--text-dark);" id="operatorNameDisplay">Administrator</div>
            <div style="font-size: 10px; color: var(--text-muted);" id="operatorRoleDisplay">Super Admin Kiosk</div>
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

    <?php if (session('success')): ?>
      <div class="laravel-flash-banner" role="alert">
        <span>✓ <?= htmlspecialchars(session('success')); ?></span>
        <span style="font-size: 11px; font-family: var(--font-mono);">Eloquent ORM · SQLite/MySQL Database</span>
      </div>
    <?php endif; ?>

    <!-- METRIK RINGKASAN (4 CARDS) -->
    <section class="admin-metrics-grid" aria-label="Ringkasan Metrik Kiosk">

      <article class="admin-metric-card">
        <div class="admin-metric-top">
          <span class="admin-metric-title">Total Pesanan</span>
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
          <span style="color: #059669; font-weight: 700;">Order::count()</span>
          <span>· Database SQL</span>
        </div>
      </article>

      <article class="admin-metric-card">
        <div class="admin-metric-top">
          <span class="admin-metric-title">Total Omzet</span>
          <div class="admin-metric-icon-wrap emerald">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <rect x="2" y="5" width="20" height="14" rx="2"></rect>
              <line x1="2" y1="10" x2="22" y2="10"></line>
            </svg>
          </div>
        </div>
        <div class="admin-metric-value" id="metricTotalRevenue">Rp <?= number_format($totalRevenue, 0, ',', '.'); ?></div>
        <div class="admin-metric-footer">
          <span style="color: #059669; font-weight: 700;">✓ Terverifikasi QRIS</span>
          <span>· Transaksi Sah</span>
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
          <span style="color: #059669; font-weight: 700;">Kiosk::all()</span>
          <span>· Siap Mencetak</span>
        </div>
      </article>

    </section>

    <!-- STATUS MESIN & KETERSEDIAAN KERTAS KIOSK (ELOQUENT ORM: Kiosk::all() & update()) -->
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
            Status Mesin &amp; Ketersediaan Kertas Kiosk (Database Tabel <code>kiosks</code>)
          </h2>
          <p>Data unit kiosk diambil dari database melalui Eloquent Model <code>App\Models\Kiosk</code> (Modul Anggota B — Damar)</p>
        </div>
        <div style="font-size: 11.5px; color: var(--text-muted); font-family: var(--font-mono);">
          <span>Eloquent: <strong>Kiosk::all()</strong></span>
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
                <span class="kiosk-card-id"><?= htmlspecialchars($kiosk->getKioskId()); ?></span>

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
              <form action="admin.php" method="POST" style="display:inline;">
                <input type="hidden" name="_action" value="update_kiosk">
                <input type="hidden" name="kiosk_id" value="<?= $kiosk->id; ?>">
                <input type="hidden" name="a4_percentage" value="<?= $isOnline ? $a4Pct : 90; ?>">
                <input type="hidden" name="f4_percentage" value="<?= $isOnline ? $f4Pct : 80; ?>">
                <input type="hidden" name="is_online" value="<?= $isOnline ? '0' : '1'; ?>">
                <button type="submit" style="background:none; border:none; cursor:pointer; font-size:11px; font-weight:800; color: <?= $isOnline ? '#059669' : '#DC2626'; ?>;" title="Klik untuk Toggle Status Online/Offline di Database">
                  <?= $isOnline ? '✓ Standby (Klik Toggle)' : '✕ Maintenance (Klik Aktifkan)'; ?>
                </button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- PANEL FORM CRUD ELOQUENT ORM (CREATE & UPDATE PESANAN DI DATABASE) -->
    <section class="admin-card" style="margin-bottom: 24px;">
      <div class="admin-card-head">
        <div class="admin-card-head-title">
          <h2>
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--primary);">
              <path d="M12 20h9"></path>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
            <?= isset($editingOrder) && $editingOrder ? 'Form Edit / Update Data Pesanan [' . htmlspecialchars($editingOrder->ticket_code) . '] (Eloquent Update)' : 'Form Tambah Data Pesanan Baru ke Database (Eloquent Create)'; ?>
          </h2>
          <p>
            <?= isset($editingOrder) && $editingOrder
                ? 'Menggunakan <code>Order::find(' . $editingOrder->id . ')->update([...])</code> untuk memperbarui baris di tabel <code>orders</code>'
                : 'Menggunakan <code>Order::create([...])</code> dengan <code>$fillable</code> untuk menyimpan baris baru ke tabel <code>orders</code>'; ?>
          </p>
        </div>
        <div style="font-size: 11.5px; color: var(--text-muted); font-family: var(--font-mono);">
          <span>Eloquent: <strong><?= isset($editingOrder) && $editingOrder ? 'Order::find($id)->update()' : 'Order::create()'; ?></strong></span>
        </div>
      </div>

      <form action="admin.php" method="POST">
        <input type="hidden" name="_action" value="<?= isset($editingOrder) && $editingOrder ? 'update_order' : 'create_order'; ?>">
        <?php if (isset($editingOrder) && $editingOrder): ?>
          <input type="hidden" name="order_id" value="<?= $editingOrder->id; ?>">
        <?php endif; ?>

        <div class="crud-form-grid">
          <div class="crud-field">
            <label for="crud_file_name">Nama Berkas Dokumen</label>
            <input type="text" id="crud_file_name" name="file_name" required
                   placeholder="Contoh: Skripsi_Bab_1-5.pdf"
                   value="<?= isset($editingOrder) && $editingOrder ? htmlspecialchars($editingOrder->file_name) : ''; ?>">
          </div>

          <div class="crud-field">
            <label for="crud_paper_type">Jenis &amp; Ukuran Kertas</label>
            <select id="crud_paper_type" name="paper_type">
              <?php $curPaper = isset($editingOrder) && $editingOrder ? $editingOrder->paper_type : 'a4_75'; ?>
              <option value="a4_75" <?= $curPaper === 'a4_75' ? 'selected' : ''; ?>>A4 Standard (75 GSM)</option>
              <option value="a4_80" <?= $curPaper === 'a4_80' ? 'selected' : ''; ?>>A4 Tebal (80 GSM)</option>
              <option value="f4_80" <?= $curPaper === 'f4_80' ? 'selected' : ''; ?>>F4 Folio (80 GSM)</option>
            </select>
          </div>

          <div class="crud-field">
            <label for="crud_ink_mode">Mode Tinta</label>
            <select id="crud_ink_mode" name="ink_mode">
              <?php $curInk = isset($editingOrder) && $editingOrder ? $editingOrder->ink_mode : 'bw'; ?>
              <option value="bw" <?= $curInk === 'bw' ? 'selected' : ''; ?>>Hitam Putih (BW)</option>
              <option value="color_std" <?= $curInk === 'color_std' ? 'selected' : ''; ?>>Warna Standar</option>
              <option value="color_full" <?= $curInk === 'color_full' ? 'selected' : ''; ?>>Full Color Foto</option>
            </select>
          </div>

          <div class="crud-field">
            <label for="crud_pages">Jumlah Halaman</label>
            <input type="number" id="crud_pages" name="pages" min="1" max="1000" required
                   value="<?= isset($editingOrder) && $editingOrder ? (int) $editingOrder->pages : 15; ?>">
          </div>

          <div class="crud-field">
            <label for="crud_finishing">Opsi Jilid / Finishing</label>
            <select id="crud_finishing" name="finishing">
              <?php $curFin = isset($editingOrder) && $editingOrder ? $editingOrder->finishing : 'none'; ?>
              <option value="none" <?= $curFin === 'none' ? 'selected' : ''; ?>>Tanpa Jilid</option>
              <option value="steples" <?= $curFin === 'steples' ? 'selected' : ''; ?>>Jilid Steples Sudut</option>
              <option value="lakban" <?= $curFin === 'lakban' ? 'selected' : ''; ?>>Jilid Lakban Mika</option>
            </select>
          </div>

          <div class="crud-field">
            <label for="crud_kiosk_name">Lokasi Mesin Kiosk</label>
            <select id="crud_kiosk_name" name="kiosk_name">
              <?php $curKiosk = isset($editingOrder) && $editingOrder ? $editingOrder->kiosk_name : 'Kiosk Perpustakaan Pusat UMM (Lt. 1)'; ?>
              <option value="Kiosk Perpustakaan Pusat UMM (Lt. 1)" <?= $curKiosk === 'Kiosk Perpustakaan Pusat UMM (Lt. 1)' ? 'selected' : ''; ?>>Kiosk Perpustakaan Pusat UMM (Lt. 1)</option>
              <option value="Kiosk GKB 1 UMM" <?= $curKiosk === 'Kiosk GKB 1 UMM' ? 'selected' : ''; ?>>Kiosk GKB 1 UMM</option>
              <option value="Kiosk GKB 2 (Lobby Lt. 1)" <?= $curKiosk === 'Kiosk GKB 2 (Lobby Lt. 1)' ? 'selected' : ''; ?>>Kiosk GKB 2 (Lobby Lt. 1)</option>
              <option value="Kiosk Lab Informatika & Komputer (Lt. 6)" <?= $curKiosk === 'Kiosk Lab Informatika & Komputer (Lt. 6)' ? 'selected' : ''; ?>>Kiosk Lab Informatika &amp; Komputer (Lt. 6)</option>
              <option value="Kiosk Kantin Teknik" <?= $curKiosk === 'Kiosk Kantin Teknik' ? 'selected' : ''; ?>>Kiosk Kantin Teknik</option>
            </select>
          </div>

          <div class="crud-field">
            <label for="crud_status">Status Pesanan</label>
            <select id="crud_status" name="status">
              <?php $curStatus = isset($editingOrder) && $editingOrder ? $editingOrder->status : 'Menunggu Cetak'; ?>
              <option value="Menunggu Cetak" <?= $curStatus === 'Menunggu Cetak' ? 'selected' : ''; ?>>Menunggu Cetak</option>
              <option value="Sedang Diproses" <?= $curStatus === 'Sedang Diproses' ? 'selected' : ''; ?>>Sedang Diproses</option>
              <option value="Siap Diambil" <?= $curStatus === 'Siap Diambil' ? 'selected' : ''; ?>>Siap Diambil</option>
              <option value="Selesai" <?= $curStatus === 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
            </select>
          </div>
        </div>

        <div style="margin-top: 14px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
          <label style="font-size: 12.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
            <input type="checkbox" name="is_duplex" value="1" <?= (!isset($editingOrder) || !$editingOrder || $editingOrder->is_duplex) ? 'checked' : ''; ?>>
            <span>Cetak Bolak-Balik (Duplex - Hemat Kertas Diskon 5%)</span>
          </label>

          <div style="display: flex; gap: 8px;">
            <?php if (isset($editingOrder) && $editingOrder): ?>
              <a href="admin.php" class="btn btn-cek" style="font-size: 12px; padding: 8px 14px; text-decoration: none;">Batal Edit</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-utama" style="font-size: 12.5px; padding: 8px 18px;">
              <span><?= isset($editingOrder) && $editingOrder ? '💾 Simpan Perubahan (Eloquent Update)' : '+ Tambah Pesanan ke Database (Eloquent Create)'; ?></span>
            </button>
          </div>
        </div>
      </form>
    </section>

    <!-- TABEL ANTREAN PESANAN REAL-TIME & ELOQUENT ORM -->
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
            Tabel Antrean Pesanan (Database Tabel <code>orders</code> &amp; Real-Time Sync)
          </h2>
          <p>Daftar antrean pesanan dari database menggunakan <code>App\Models\Order</code> &amp; tersinkronisasi dengan CepatikDB</p>
        </div>

        <div style="font-size: 11.5px; color: var(--text-muted); font-family: var(--font-mono);">
          <span>Eloquent: <strong>Order::all()</strong></span>
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
              <th style="width: 150px; text-align: center;">Aksi CRUD</th>
            </tr>
          </thead>
          <tbody id="adminOrderTableBody">
            <?php foreach ($orders as $order): 
                $status = $order->getStatus();
            ?>
              <tr>
                <td>
                  <span class="badge-ticket"><?= htmlspecialchars($order->getId()); ?></span>
                </td>
                <td>
                  <div style="font-weight: 700; color: var(--text-dark);"><?= htmlspecialchars($order->getTimestamp()); ?></div>
                  <div style="font-size: 10.5px; color: #059669; font-weight: 600; margin-top: 2px;">📍 <?= htmlspecialchars($order->kiosk_name); ?></div>
                </td>
                <td>
                  <div class="admin-file-cell">
                    <div class="admin-file-icon"><?= htmlspecialchars($order->getFileExtension()); ?></div>
                    <div class="admin-file-meta">
                      <h4><?= htmlspecialchars($order->getFileName()); ?></h4>
                      <p><?= $order->getPages(); ?> Hlm · <?= $order->copies ?: 1; ?>x Rangkap</p>
                    </div>
                  </div>
                </td>
                <td>
                  <span style="font-size: 11px; color: #4B5563;"><?= htmlspecialchars($order->getPrintSpecs()); ?></span>
                </td>
                <td>
                  <div style="font-weight: 900; color: var(--text-dark); font-size: 14px;"><?= htmlspecialchars($order->getFormattedTotal()); ?></div>
                </td>
                <td>
                  <span class="badge-status siap">
                    <span class="status-dot"></span>
                    <span><?= htmlspecialchars($status); ?></span>
                  </span>
                </td>
                <td style="text-align: center;">
                  <div style="display:flex; gap:4px; justify-content:center; align-items:center;">
                    <a href="admin.php?edit_id=<?= $order->id; ?>" class="btn-action" style="text-decoration:none; font-size:11px; padding:4px 8px;" title="Edit Data via Eloquent Order::find()">Edit</a>
                    <button type="button" class="btn-action btn-action-print" onclick="printThermalReceipt('<?= addslashes($order->getId()); ?>', '<?= addslashes($order->getFileName()); ?>', '<?= addslashes($order->getPrintSpecs()); ?>', '<?= addslashes($order->getFormattedTotal()); ?>', '<?= addslashes($order->getTimestamp()); ?>')">
                      <span>Struk</span>
                    </button>
                    <form action="admin.php" method="POST" style="display:inline;" onsubmit="return confirm('Hapus pesanan <?= addslashes($order->getId()); ?> dari database?');">
                      <input type="hidden" name="_action" value="delete_order">
                      <input type="hidden" name="order_id" value="<?= $order->id; ?>">
                      <button type="submit" class="btn-action btn-action-delete" title="Hapus dari Database (Eloquent delete)">✕</button>
                    </form>
                  </div>
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
