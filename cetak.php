<?php
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Portal Aplikasi Cetak Mandiri <?= htmlspecialchars($site_info['nama']) ?> - Upload, Konfigurasi, & Ambil di Kiosk.">
  <title>Cetak Dokumen Mandiri - <?= htmlspecialchars($site_info['nama']) ?></title>

  <link rel="icon" type="image/svg+xml" href="assets/images/icon.svg">
  <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAIhSURBVHhe7Zu/S8NAFIAzOjq66ejYRXDTzeYU/BN0U3MFndRRQRER1EFwFAVnQZAiYhUEXUQXUaRDBYeig0UEf7W9py+h1bzQQGlj7d178C3vLrl33yWlBJ5lhQQ4IgaOGAZpzzQtWL8jYnRvFQMSA51Kih2QAnRDSTupRuPddM/lQFtKijd6oW4oaS/AUE+Lb/NqzB6lE3VGJcT6z8l7j732Jx+kv9c7fUccBQf1R0mRsfBdoAMmYeGvIk2ahKVkfIImTcLy/jAEB0yh/gJWpwD2tqMD70/XrIH6C8Aiowy8P12zBlgAC2ABLIAF0GRNsAAWQEuubzRaQHF5EvK7W5W5uYR8oRAdeH+65i+wPlpzGFULwEXePz7/LVgfrTkMFtCsAlIPaXh+ew3ktReAm+46WYPW/VloP1yE+5cn37j2Ajbvzt3Nl1hKH/vGtReAJ952MF8WcPqY8Y1rLwC5ymVh7jYFyexNYMwIAWGwgEYJ+Ly+gMLu1p+B69EaGioAi6JzowTXozWwABbAAlgAC6iyDhbAAlgAC2ABNBlGpa/CxZXqvsbWCq5Ha3DriPqrsG6wABbAAlgAC6BJk2ABLIAFsAAWQJMmgZ1i0zRpEtgvFKdJk7BgfLCVJk3C6xqTIkMHTABbBUttc2a+Bo6YLjdPKmlvBCZojJLirLx5DPwtwOZiOlFHcPMw0tfhE1AKt4HasXP0Ih1w24N/P/bf8QXf4i9hq+va5AAAAABJRU5ErkJggg==">
  <link rel="icon" type="image/png" href="assets/images/icon.png">
  <link rel="shortcut icon" href="favicon.ico">

  <link rel="stylesheet" href="assets/css/style.css">

  <!-- File JavaScript Khusus Sistem Pemesanan -->
  <script src="assets/js/sistem.js" defer></script>
</head>

<body class="app-body">

  <header class="app-navbar">
    <div class="app-navbar-inner">
      <div style="display: flex; align-items: center; gap: 14px;">
        <a href="index.php" class="logo" title="Kembali ke Beranda">
          <img src="assets/images/logo.svg" alt="Logo <?= htmlspecialchars($site_info['nama']) ?>">
        </a>
        <span style="font-size: 11px; background: #ECFDF5; color: #065F46; padding: 4px 10px; border-radius: 999px; font-weight: 700; border: 1px solid #A7F3D0;">
          ● Kiosk Online (<?= (int)$site_info['kiosk_aktif_count'] ?> Titik)
        </span>
      </div>

      <a href="index.php" class="btn-back-home">
        <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Kembali ke Beranda</span>
      </a>
    </div>
  </header>

  <div class="app-wrapper">

    <nav class="wizard-progress" aria-label="Alur Langkah Pemesanan">
      <div class="wizard-step active" id="stepIndicator1">
        <span class="step-num">1</span>
        <span>Unggah Berkas</span>
      </div>
      <div class="wizard-step" id="stepIndicator2">
        <span class="step-num">2</span>
        <span>Atur Cetakan</span>
      </div>
      <div class="wizard-step" id="stepIndicator3">
        <span class="step-num">3</span>
        <span>Pilih Loker</span>
      </div>
      <div class="wizard-step" id="stepIndicator4">
        <span class="step-num">4</span>
        <span>Bayar QRIS</span>
      </div>
      <div class="wizard-step" id="stepIndicator5">
        <span class="step-num">5</span>
        <span>Tiket Selesai</span>
      </div>
    </nav>

    <div class="app-grid-layout">

      <main class="app-main-panel">

        <section class="step-panel active-panel" data-step="1">
          <header class="panel-header">
            <h2>Langkah 1: Unggah Dokumen Kamu</h2>
            <p>Pilih file skripsi, tugas, atau makalah yang ingin kamu cetak di mesin kiosk.</p>
          </header>

          <label for="inputAppFile" class="dropzone-label" style="max-width: 100%;" id="boxUploadArea">
            <input type="file" id="inputAppFile" style="display: none;" accept=".pdf,.docx,.doc,.pptx,.jpg,.png">
            <div class="dropzone-content">
              <div class="dropzone-icon-box">
                <svg class="icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                  <path d="M12 12v9"></path>
                  <path d="m16 16-4-4-4 4"></path>
                </svg>
              </div>
              <h3 class="dropzone-title">Klik atau Tarik Berkas ke Sini</h3>
              <p class="dropzone-subtitle">Mendukung format PDF, Word (.docx), PowerPoint (.pptx), atau Gambar</p>
              <div class="dropzone-badges">
                <span>🔒 Enkripsi TLS 256-Bit</span>
                <span>⚡ Auto-Deteksi Halaman</span>
              </div>
            </div>
          </label>

          <div class="uploaded-preview-box" id="previewUpload" style="display: none;">
            <div class="file-preview-left">
              <div class="file-badge-icon">PDF</div>
              <div class="file-badge-meta">
                <h4 id="previewNamaFile">Nama_Dokumen.pdf</h4>
                <p id="previewInfoFile">0 Halaman · 0 MB · Siap Cetak</p>
              </div>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
              <span style="font-size: 11px; background: #ECFDF5; color: #065F46; padding: 4px 10px; border-radius: 6px; font-weight: 800;">
                ✓ Terverifikasi
              </span>
              <button type="button" id="btnHapusBerkas" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer;">
                ✕ Hapus
              </button>
            </div>
          </div>

          <div class="wizard-actions">
            <div></div>
            <button type="button" class="btn-wizard-next" onclick="pindahLangkah(2)">
              <span>Lanjut: Atur Cetakan</span>
              <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
          </div>
        </section>

        <section class="step-panel" data-step="2">
          <header class="panel-header">
            <h2>Langkah 2: Opsi Cetak &amp; Finishing</h2>
            <p>Pilih spesifikasi kertas, warna tinta, dan kebutuhan penjilidan.</p>
          </header>

          <!-- Pilihan Kertas dari Array PHP -->
          <div class="form-group-title">1. Jenis Kertas</div>
          <div class="radio-pilihan-grid">
            <?php foreach ($pilihan_kertas as $kertas): ?>
              <div class="radio-card opsi-app-kertas <?= $kertas['default'] ? 'selected' : '' ?>">
                <input type="radio" name="app_paper" value="<?= htmlspecialchars($kertas['id']) ?>" <?= $kertas['default'] ? 'checked' : '' ?>>
                <span class="radio-label"><?= htmlspecialchars($kertas['nama']) ?></span>
                <span class="radio-desc"><?= htmlspecialchars($kertas['gsm']) ?></span>
                <span class="radio-price"><?= htmlspecialchars($kertas['label_harga']) ?></span>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="form-group-title">2. Mode Warna Tinta</div>
          <div class="radio-pilihan-grid">
            <?php foreach ($pilihan_warna as $warna): ?>
              <div class="radio-card opsi-app-warna <?= $warna['default'] ? 'selected' : '' ?>">
                <input type="radio" name="app_color" value="<?= htmlspecialchars($warna['id']) ?>" <?= $warna['default'] ? 'checked' : '' ?>>
                <span class="radio-label"><?= htmlspecialchars($warna['nama_pendek'] ?? $warna['nama']) ?></span>
                <span class="radio-desc"><?= htmlspecialchars($warna['deskripsi']) ?></span>
                <span class="radio-price"><?= htmlspecialchars($warna['label_harga_per_lbr']) ?></span>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Pilihan Halaman, Sisi & Rangkap -->
          <div class="opsi-halaman-row" style="margin-bottom: 22px; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));">
            <div class="input-box">
              <label for="inputAppHalaman">Jumlah Halaman (Lembar)</label>
              <div class="input-number-wrap">
                <input type="number" id="inputAppHalaman" value="12" min="1" max="500">
                <span style="font-size: 12px; color: var(--text-muted);">Hlm</span>
              </div>
            </div>

            <div class="input-box">
              <label for="inputRangkap">Jumlah Rangkap (Salinan)</label>
              <div class="input-number-wrap">
                <input type="number" id="inputRangkap" value="1" min="1" max="20">
                <span style="font-size: 12px; color: var(--text-muted);">Set</span>
              </div>
            </div>

            <div class="input-box duplex-toggle-box">
              <div>
                <label for="toggleDuplexApp" style="margin-bottom: 2px;">Cetak Bolak-Balik</label>
                <span style="font-size: 11px; color: #059669; font-weight:600;">Diskon hemat kertas 5%</span>
              </div>
              <label class="switch">
                <input type="checkbox" id="toggleDuplexApp">
                <span class="slider"></span>
              </label>
            </div>
          </div>

          <div class="form-group-title">3. Penjilidan &amp; Finishing</div>
          <div class="radio-pilihan-grid">
            <?php foreach ($pilihan_jilid as $jilid): ?>
              <div class="radio-card opsi-app-jilid <?= $jilid['default'] ? 'selected' : '' ?>">
                <input type="radio" name="app_jilid" value="<?= htmlspecialchars($jilid['id']) ?>" <?= $jilid['default'] ? 'checked' : '' ?>>
                <span class="radio-label"><?= htmlspecialchars($jilid['nama']) ?></span>
                <span class="radio-desc"><?= htmlspecialchars($jilid['deskripsi']) ?></span>
                <span class="radio-price"><?= htmlspecialchars($jilid['label_harga']) ?></span>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="wizard-actions">
            <button type="button" class="btn-wizard-prev" onclick="pindahLangkah(1)">
              <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
              <span>Kembali</span>
            </button>
            <button type="button" class="btn-wizard-next" onclick="pindahLangkah(3)">
              <span>Lanjut: Pilih Loker</span>
              <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
          </div>
        </section>

        <section class="step-panel" data-step="3">
          <header class="panel-header">
            <h2>Langkah 3: Pilih Titik Kiosk &amp; Loker Pengambilan</h2>
            <p>Pilih mesin printer kiosk terdekat yang paling nyaman untuk kamu ambil hasilnya.</p>
          </header>

          <div style="display: flex; flex-direction: column; gap: 14px;">
            <?php foreach ($daftar_kiosk as $kiosk): ?>
              <div class="radio-card opsi-app-kiosk <?= $kiosk['default'] ? 'selected' : '' ?>" style="padding: 18px;">
                <input type="radio" name="app_kiosk" value="<?= htmlspecialchars($kiosk['nama_lengkap_app']) ?>" <?= $kiosk['default'] ? 'checked' : '' ?>>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                  <div>
                    <span class="radio-label" style="font-size: 14px;"><?= htmlspecialchars($kiosk['nama_lengkap_app']) ?></span>
                    <span class="radio-desc" style="font-size: 12px;"><?= htmlspecialchars($kiosk['lokasi']) ?></span>
                  </div>
                  <?php if ($kiosk['loker_tersedia'] > 0): ?>
                    <span style="font-size: 11px; background: #ECFDF5; color: #065F46; padding: 2px 8px; border-radius: 6px; font-weight: 700;">
                      <?= (int)$kiosk['loker_tersedia'] ?> Loker Siap
                    </span>
                  <?php else: ?>
                    <span style="font-size: 11px; background: #FEF2F2; color: #991B1B; padding: 2px 8px; border-radius: 6px; font-weight: 700;">
                      Loker Penuh
                    </span>
                  <?php endif; ?>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 8px; font-family: var(--font-mono);">
                  Status: <?= htmlspecialchars($kiosk['jam_buka']) === '24 Jam' ? 'Online 24 Jam' : 'Buka ' . htmlspecialchars($kiosk['jam_buka']) ?> • Stok Kertas: <?= $kiosk['stok_a4'] > 500 ? 'Penuh' : 'Menipis' ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="wizard-actions">
            <button type="button" class="btn-wizard-prev" onclick="pindahLangkah(2)">
              <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
              <span>Kembali</span>
            </button>
            <button type="button" class="btn-wizard-next" onclick="pindahLangkah(4)">
              <span>Lanjut: Bayar QRIS</span>
              <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
          </div>
        </section>

        <section class="step-panel" data-step="4">
          <header class="panel-header">
            <h2>Langkah 4: Pembayaran Instan via QRIS</h2>
            <p>Scan kode QR menggunakan m-Banking atau e-Wallet apa saja (GoPay, OVO, Dana, ShopeePay).</p>
          </header>

          <div class="qris-card">
            <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-dark);">
              QRIS STANDAR PEMBAYARAN NASIONAL
            </div>

            <div class="qris-image-wrap">
              <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="#2D2825" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
                <path d="M10 7h4v4h-4z"></path>
              </svg>
              <span style="font-size: 10px; font-family: var(--font-mono); color: var(--text-muted); margin-top: 4px;">NMID: ID1020261948</span>
            </div>

            <div style="font-size: 12px; color: var(--text-muted);">Total Tagihan:</div>
            <div style="font-size: 28px; font-weight: 900; color: var(--primary); font-family: var(--font-main);" id="qrisTotalDisplay">
              Rp 0
            </div>

            <span class="qris-timer-pill" id="qrisTimerText">⏱ Sisa Waktu Bayar: 15:00 Menit</span>
          </div>

          <div class="wizard-actions">
            <button type="button" class="btn-wizard-prev" onclick="pindahLangkah(3)">
              <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
              <span>Kembali</span>
            </button>
            <button type="button" class="btn-wizard-next" id="btnBayarQRIS" style="background-color: #059669;">
              <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Simulasikan Bayar Berhasil</span>
            </button>
          </div>
        </section>

        <section class="step-panel" data-step="5">
          <div class="ticket-success-box">
            <div style="width: 52px; height: 52px; background: #ECFDF5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
              <svg class="icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>

            <h2 style="font-size: 22px; font-weight: 900; color: #065F46; margin-bottom: 4px;">Pembayaran Sukses &amp; Dokumen Dicetak!</h2>
            <p style="font-size: 13px; color: var(--text-muted); max-width: 480px; margin: 0 auto;">
              Dokumenmu sedang di-spooling dan otomatis masuk ke loker mandiri dalam 12 detik.
            </p>

            <div class="ticket-code-display">
              <span style="font-size: 11px; text-transform: uppercase; font-family: var(--font-mono); font-weight: 800; color: var(--text-muted); letter-spacing: 1px;">KODE TIKET KIOSK:</span>
              <div class="ticket-big-code" id="displayTiket">CPT-8492</div>
              <div class="ticket-pin-info">
                PIN Loker Mandiri: <span style="background: var(--primary-soft); color: var(--primary); padding: 2px 10px; border-radius: 6px;" id="displayPIN">8492</span>
              </div>
            </div>

            <div style="background: #FAFAF8; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 14px; text-align: left; font-size: 12.5px; margin-bottom: 24px;">
              <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span style="color: var(--text-muted);">Lokasi Pengambilan:</span>
                <strong id="displayLokasiSukses">Kiosk Perpustakaan UI (Loker Blok A)</strong>
              </div>
              <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span style="color: var(--text-muted);">Batas Simpan Loker:</span>
                <strong style="color: #D97706;"><?= (int)$site_info['retensi_jam'] ?> Jam (s.d. 19:45 WIB)</strong>
              </div>
              <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">Keamanan Data:</span>
                <strong style="color: #059669;">File Terhapus Permanen Otomatis</strong>
              </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
              <button type="button" class="btn-wizard-next" onclick="pindahLangkah(1)">
                <span>Cetak Dokumen Lain</span>
              </button>
              <a href="index.php" class="btn-back-home" style="padding: 11px 22px;">
                <span>Kembali ke Beranda</span>
              </a>
            </div>
          </div>
        </section>

      </main>

      <aside class="app-sidebar-panel">
        <header style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 14px; border-bottom: 1px dashed var(--border-color); margin-bottom: 14px;">
          <h3 style="font-size: 15px; font-weight: 800; color: var(--text-dark);">Ringkasan Pesanan</h3>
          <span style="font-size: 11px; background: var(--primary-soft); color: var(--primary); font-weight: 800; padding: 2px 8px; border-radius: 6px; font-family: var(--font-mono);">LIVE</span>
        </header>

        <dl class="struk-list" style="font-size: 12px; gap: 8px;">
          <div class="struk-row">
            <dt>Berkas:</dt>
            <dd id="ringkasFile" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Belum dipilih</dd>
          </div>
          <div class="struk-row">
            <dt>Volume:</dt>
            <dd id="ringkasHalaman">0 lembar</dd>
          </div>
          <div class="struk-row">
            <dt>Kertas:</dt>
            <dd id="ringkasKertas"><?= htmlspecialchars($pilihan_kertas['a4_75']['deskripsi_lengkap']) ?></dd>
          </div>
          <div class="struk-row">
            <dt>Warna:</dt>
            <dd id="ringkasWarna"><?= htmlspecialchars($pilihan_warna['bw']['nama']) ?></dd>
          </div>
          <div class="struk-row">
            <dt>Metode:</dt>
            <dd id="ringkasDuplex">1 Sisi (Simplex)</dd>
          </div>
          <div class="struk-row">
            <dt>Jilid:</dt>
            <dd id="ringkasJilid"><?= htmlspecialchars($pilihan_jilid['none']['nama']) ?></dd>
          </div>
          <div class="struk-row">
            <dt>Loker:</dt>
            <dd id="ringkasKiosk" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Perpustakaan UI</dd>
          </div>
        </dl>

        <div style="margin-top: 16px; padding-top: 14px; border-top: 1px dashed var(--border-color);">
          <div class="total-row">
            <div>
              <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); font-family: var(--font-mono);">Total Biaya:</span>
              <div class="total-tax">Termasuk PPN 11%</div>
            </div>
            <div class="total-nominal" id="ringkasTotal">Rp 0</div>
          </div>
        </div>
      </aside>

    </div>

  </div>

</body>

</html>
