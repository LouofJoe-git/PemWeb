<?php
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';

$calc_halaman = 15;
$calc_warna = 'bw';
$calc_kertas = 'a4_75';
$calc_duplex = false;
$calc_rangkap = 1;

$estimasi_awal = hitung_estimasi_cetak($calc_halaman, $calc_warna, $calc_kertas, $calc_rangkap, $calc_duplex);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($site_info['deskripsi']) ?>">
  <title><?= htmlspecialchars($site_info['nama']) ?> - <?= htmlspecialchars($site_info['tagline']) ?></title>
  
  <link rel="icon" type="image/svg+xml" href="assets/images/icon.svg">
  <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAIhSURBVHhe7Zu/S8NAFIAzOjq66ejYRXDTzeYU/BN0U3MFndRRQRER1EFwFAVnQZAiYhUEXUQXUaRDBYeig0UEf7W9py+h1bzQQGlj7d178C3vLrl33yWlBJ5lhQQ4IgaOGAZpzzQtWL8jYnRvFQMSA51Kih2QAnRDSTupRuPddM/lQFtKijd6oW4oaS/AUE+Lb/NqzB6lE3VGJcT6z8l7j732Jx+kv9c7fUccBQf1R0mRsfBdoAMmYeGvIk2ahKVkfIImTcLy/jAEB0yh/gJWpwD2tqMD70/XrIH6C8Aiowy8P12zBlgAC2ABLIAF0GRNsAAWQEuubzRaQHF5EvK7W5W5uYR8oRAdeH+65i+wPlpzGFULwEXePz7/LVgfrTkMFtCsAlIPaXh+ew3ktReAm+46WYPW/VloP1yE+5cn37j2Ajbvzt3Nl1hKH/vGtReAJ952MF8WcPqY8Y1rLwC5ymVh7jYFyexNYMwIAWGwgEYJ+Ly+gMLu1p+B69EaGioAi6JzowTXozWwABbAAlgAC6iyDhbAAlgAC2ABNBlGpa/CxZXqvsbWCq5Ha3DriPqrsG6wABbAAlgAC6BJk2ABLIAFsAAWQJMmgZ1i0zRpEtgvFKdJk7BgfLCVJk3C6xqTIkMHTABbBUttc2a+Bo6YLjdPKmlvBCZojJLirLx5DPwtwOZiOlFHcPMw0tfhE1AKt4HasXP0Ih1w24N/P/bf8QXf4i9hq+va5AAAAABJRU5ErkJggg==">
  <link rel="icon" type="image/png" href="assets/images/icon.png">
  <link rel="shortcut icon" href="favicon.ico">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="db/database.js" defer></script>
  <script src="assets/js/script.js" defer></script>
</head>

<body>

  <aside class="topbar">
    <div class="topbar-container">
      <div class="topbar-item topbar-clock">
        <svg class="icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
        <span id="digitalClock">--:--:-- WIB</span>
      </div>
      <div class="topbar-divider">•</div>
      <div class="topbar-item topbar-status is-online" id="operationalStatusBadge">
        <span class="status-indicator-dot online" id="operationalDot"></span>
        <span class="status-text" id="operationalStatusText">ON-DUTY · KIOSK &amp; CS ONLINE</span>
      </div>
      <div class="topbar-divider">•</div>
      <div class="topbar-item topbar-info">
        <span>Jam Operasional: 07.00 - 23.00 WIB (Kiosk Siap Cetak di <?= (int)$site_info['kampus_mitra_count'] ?>+ Kampus)</span>
      </div>
    </div>
  </aside>

  <header class="header-area">
    <nav class="navbar">
      <a href="#hero" class="logo" aria-label="Beranda <?= htmlspecialchars($site_info['nama']) ?>">
        <img src="assets/images/logo.svg" alt="Logo <?= htmlspecialchars($site_info['nama']) ?>">
      </a>

      <ul class="menu">
        <li><a href="#hero" class="aktif">Beranda</a></li>
        <li><a href="#kalkulator">Kalkulator Tarif</a></li>
        <li><a href="#keunggulan">Keunggulan</a></li>
        <li><a href="#lokasi">Lokasi Kiosk</a></li>
        <li><a href="#testimoni">Testimoni</a></li>
        <li><a href="#faq">Bantuan &amp; FAQ</a></li>
      </ul>

      <div class="tombol-grup">
        <a href="#tracker" class="btn btn-cek">
          <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2.2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <span>Cek Pesanan</span>
        </a>
        <a href="cetak.php" class="btn btn-cetak">
          <span>Mulai Cetak</span>
          <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2.5">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </a>
      </div>
    </nav>
  </header>

  <main>

    <section class="hero" id="hero">
      <div class="hero-glow"></div>
      <div class="container hero-container-wide">

        <!-- Header Hero: Terpusat, Luas, dan Berisi Live Operational Status Widget -->
        <header class="hero-header-center">
          <div class="hero-top-badges">
            <div class="hero-tag">
              <span class="hero-tag-star">★</span>
              <span>Solusi Cetak Mandiri &amp; Cepat untuk Mahasiswa</span>
            </div>
          </div>

          <h1 class="hero-title">
            Cetak Dokumen Dari Mana Saja, <span>Ambil Mandiri Kapan Saja.</span>
          </h1>

          <p class="hero-desc">
            Upload dokumen dari HP atau laptop, bayar instan via QRIS otomatis, dan ambil langsung di mesin kiosk pintar tanpa perlu antre di meja operator.
          </p>
        </header>

        <!-- Dua Kartu Aksi Utama: Upload Dropzone & Quick Ticket Tracker -->
        <div class="hero-actions-grid">
          
          <!-- Kartu 1: Dropzone Unggah Dokumen Utama -->
          <article class="hero-action-card hero-dropzone-card">
            <div class="action-card-header">
              <div class="action-card-header-left">
                <div class="action-card-icon upload-icon-gradient">
                  <svg class="icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                    <path d="M12 12v9"></path>
                    <path d="m16 16-4-4-4 4"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="action-card-title">Unggah Dokumen Instan</h3>
                  <p class="action-card-subtitle">Mulai <?= format_rupiah($site_info['tarif_mulai'] ?? 350) ?> / lembar · Privasi 100%</p>
                </div>
              </div>
              <span class="badge-action-pill">Mulai Cetak</span>
            </div>

            <div id="dropzone">
              <label class="dropzone-label" for="fileInput">
                <input type="file" id="fileInput" style="display: none;"
                  accept=".pdf,.docx,.doc,.pptx,.ppt,.jpg,.jpeg,.png">
                <div class="dropzone-content">
                  <div class="dropzone-icon-box">
                    <svg class="icon" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                      <path d="M12 12v9"></path>
                      <path d="m16 16-4-4-4 4"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="dropzone-title" id="dropzoneTitle">Tarik &amp; Lepas Berkas atau Klik untuk Upload</h3>
                    <p class="dropzone-subtitle">Mendukung format PDF, Word (DOCX), PowerPoint, &amp; Gambar</p>
                  </div>
                  <div class="dropzone-badges">
                    <span>⚡ Auto-Count Halaman</span>
                    <span>🔒 Enkripsi TLS 256-Bit</span>
                    <span>🗑 Hapus Otomatis <?= (int)$site_info['retensi_jam'] ?> Jam</span>
                  </div>
                </div>
              </label>
            </div>
          </article>

          <!-- Kartu 2: Lacak Status Antrean / Tiket Pesanan -->
          <article class="hero-action-card hero-tracker-card">
            <div class="action-card-header">
              <div class="action-card-header-left">
                <div class="action-card-icon tracker-icon-gradient">
                  <svg class="icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                  </svg>
                </div>
                <div>
                  <h3 class="action-card-title">Lacak Antrean &amp; Tiket</h3>
                  <p class="action-card-subtitle">Pantau progres cetak &amp; ambil dengan kode tiket</p>
                </div>
              </div>
              <span class="badge-action-pill badge-live">Live Sync</span>
            </div>

            <div class="tracker-box" id="tracker">
              <form class="tracker-form" id="formTracker">
                <div class="tracker-input-group">
                  <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2">
                    <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path>
                    <line x1="8" y1="7" x2="16" y2="7"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                    <line x1="8" y1="17" x2="13" y2="17"></line>
                  </svg>
                  <input type="text" id="inputTiket" placeholder="Kode tiket (cth: CPT-8492)" maxlength="12">
                </div>
                <button type="submit" class="btn-tracker">
                  <span>Cek Status</span>
                  <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </button>
              </form>

              <!-- Tombol Cepat Contoh Tiket Demo -->
              <div class="tracker-quick-suggestions">
                <span>Coba tiket:</span>
                <button type="button" class="btn-sample-code" onclick="document.getElementById('inputTiket').value='CPT-8492'; document.getElementById('formTracker').dispatchEvent(new Event('submit'));">CPT-8492</button>
                <button type="button" class="btn-sample-code" onclick="document.getElementById('inputTiket').value='CPT-8493'; document.getElementById('formTracker').dispatchEvent(new Event('submit'));">CPT-8493</button>
                <button type="button" class="btn-sample-code" onclick="document.getElementById('inputTiket').value='CPT-8494'; document.getElementById('formTracker').dispatchEvent(new Event('submit'));">CPT-8494</button>
              </div>

              <!-- Hasil Cek Tiket Real-time -->
              <div id="trackerResult" style="display: none; margin-top: 12px;"></div>
            </div>
          </article>

        </div>

        <!-- Trust Badges Seimbang Melintang di Bawah -->
        <ul class="trust-list-wide">
          <li class="trust-item">
            <svg class="icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              <path d="m9 12 2 2 4-4"></path>
            </svg>
            <div class="trust-text">
              <strong>Privasi Terjamin 100%</strong>
              <span>File otomatis dihapus aman <?= (int)$site_info['retensi_jam'] ?> jam</span>
            </div>
          </li>
          <li class="trust-item">
            <svg class="icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <div class="trust-text">
              <strong>QRIS Dinamis Instan</strong>
              <span>Semua Bank &amp; Dompet Digital</span>
            </div>
          </li>
          <li class="trust-item">
            <svg class="icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
            </svg>
            <div class="trust-text">
              <strong>Spooling Laserjet <?= (int)$site_info['kecepatan_ppm'] ?> ppm</strong>
              <span>Cetak presisi tinggi dalam 12 detik</span>
            </div>
          </li>
        </ul>

      </div>
    </section>

    <section class="status-strip">
      <div class="container status-strip-content">
        <div class="status-left">
          <span class="status-dot"></span>
          <span><?= (int)$site_info['kiosk_aktif_count'] ?> Mitra Kiosk <?= htmlspecialchars($site_info['nama']) ?> Online &amp; Siap Cetak</span>
        </div>
        <div class="status-right">
          <div>⚡ Kecepatan: <strong><?= (int)$site_info['kecepatan_ppm'] ?> Halaman / Menit</strong></div>
          <div>🔒 Ambil Mandiri via <strong>PIN / QR</strong></div>
        </div>
      </div>
    </section>

    <section class="kalkulator-section" id="kalkulator">
      <div class="container">
        <div class="kalkulator-card">
          <header>
            <span class="section-badge">Hitung Transparan</span>
            <h2 class="section-title">Kalkulator Simulasi Biaya Cetak</h2>
            <p class="section-subtitle">Estimasi biaya cetak akurat secara real-time tanpa ada biaya tersembunyi.</p>
          </header>

          <div class="kalkulator-layout">
            <form id="calcForm" onsubmit="return false;">
              <div class="form-group-title">1. Ukuran &amp; Jenis Kertas</div>
              <div class="radio-pilihan-grid">
                <?php foreach ($pilihan_kertas as $kertas): 
                  $is_selected = ($kertas['id'] === $calc_kertas);
                ?>
                  <div class="radio-card radio-kertas <?= $is_selected ? 'selected' : '' ?>">
                    <input type="radio" name="paper" value="<?= htmlspecialchars($kertas['id']) ?>" <?= $is_selected ? 'checked' : '' ?>>
                    <span class="radio-label"><?= htmlspecialchars($kertas['nama_alt'] ?? $kertas['nama']) ?></span>
                    <span class="radio-desc"><?= htmlspecialchars($kertas['gsm']) ?></span>
                    <span class="radio-price"><?= htmlspecialchars($kertas['label_harga']) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="form-group-title">2. Mode Warna Tinta</div>
              <div class="radio-pilihan-grid">
                <?php foreach ($pilihan_warna as $warna): 
                  $is_selected = ($warna['id'] === $calc_warna);
                ?>
                  <div class="radio-card radio-warna <?= $is_selected ? 'selected' : '' ?>">
                    <input type="radio" name="color" value="<?= htmlspecialchars($warna['id']) ?>" <?= $is_selected ? 'checked' : '' ?>>
                    <span class="radio-label"><?= htmlspecialchars($warna['nama']) ?></span>
                    <span class="radio-desc"><?= htmlspecialchars($warna['deskripsi_singkat']) ?></span>
                    <span class="radio-price"><?= htmlspecialchars($warna['label_harga']) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="opsi-halaman-row">
                <div class="input-box">
                  <label for="inputHalaman">Jumlah Halaman</label>
                  <div class="input-number-wrap">
                    <input type="number" id="inputHalaman" value="<?= (int)$calc_halaman ?>" min="1" max="500">
                    <span style="font-size: 12px; color: var(--text-muted);">Lembar</span>
                  </div>
                </div>

                <div class="input-box duplex-toggle-box">
                  <div>
                    <label for="toggleDuplex" style="margin-bottom: 2px;">Cetak Bolak-Balik</label>
                    <span style="font-size: 11px; color: #059669; font-weight:600;">Diskon hemat kertas 5%</span>
                  </div>
                  <label class="switch">
                    <input type="checkbox" id="toggleDuplex" <?= $calc_duplex ? 'checked' : '' ?>>
                    <span class="slider"></span>
                  </label>
                </div>
              </div>
            </form>

            <aside class="struk-preview">
              <div class="struk-head">
                <div>
                  <span
                    style="font-size: 10px; font-family: var(--font-mono); color: var(--primary); font-weight: 800; text-transform: uppercase;">Estimasi
                    Realtime</span>
                  <h3>Rincian Tiket Cetak</h3>
                </div>
                <svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)"
                  stroke-width="2">
                  <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path>
                  <line x1="8" y1="7" x2="16" y2="7"></line>
                  <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
              </div>

              <dl class="struk-list">
                <div class="struk-row">
                  <dt>Jenis Kertas</dt>
                  <dd id="strukKertas"><?= htmlspecialchars($estimasi_awal['nama_kertas']) ?></dd>
                </div>
                <div class="struk-row">
                  <dt>Warna Tinta</dt>
                  <dd id="strukWarna"><?= htmlspecialchars($estimasi_awal['nama_warna']) ?></dd>
                </div>
                <div class="struk-row">
                  <dt>Volume</dt>
                  <dd id="strukHalaman"><?= $estimasi_awal['halaman'] ?> hlm x <?= $estimasi_awal['rangkap'] ?> rangkap</dd>
                </div>
                <div class="struk-row">
                  <dt>Metode Sisi</dt>
                  <dd id="strukMetode"><?= htmlspecialchars($estimasi_awal['metode_sisi']) ?></dd>
                </div>
                <div class="struk-row">
                  <dt>Cloud Spooling</dt>
                  <dd style="color: #059669; font-weight: 800;">GRATIS</dd>
                </div>
              </dl>

              <div class="struk-footer">
                <div class="total-row">
                  <div>
                    <span
                      style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); font-family: var(--font-mono);">Total
                      Estimasi:</span>
                    <div class="total-tax">Termasuk PPN 11%</div>
                  </div>
                  <div class="total-nominal" id="strukTotal"><?= $estimasi_awal['total_formatted'] ?></div>
                </div>

                <a href="cetak.php" class="btn-upload-struk">
                  <span>Lanjutkan Pemesanan di Aplikasi Cetak</span>
                  <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                    <path d="M12 12v9"></path>
                    <path d="m16 16-4-4-4 4"></path>
                  </svg>
                </a>
              </div>
            </aside>
          </div>
        </div>
      </div>
    </section>

    <section class="keunggulan-section" id="keunggulan">
      <div class="container">
        <header class="keunggulan-header">
          <span class="section-badge">Keamanan &amp; Performa</span>
          <h2 class="section-title">Mengapa Memilih <?= htmlspecialchars($site_info['nama']) ?>?</h2>
          <p class="section-subtitle">Teknologi cetak mandiri modern yang dirancang untuk kenyamanan mahasiswa.</p>
        </header>

        <div class="keunggulan-grid">
          <?php foreach ($daftar_keunggulan as $item): ?>
            <article class="kartu-keunggulan">
              <div>
                <div class="keunggulan-icon <?= htmlspecialchars($item['icon_class']) ?>">
                  <?php if ($item['svg_type'] === 'shield'): ?>
                    <svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                      <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                  <?php elseif ($item['svg_type'] === 'printer'): ?>
                    <svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="6 9 6 2 18 2 18 9"></polyline>
                      <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                      <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                  <?php else: ?>
                    <svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                  <?php endif; ?>
                </div>
                <h3><?= htmlspecialchars($item['judul']) ?></h3>
                <p><?= htmlspecialchars($item['deskripsi']) ?></p>
              </div>
              <div class="keunggulan-tag" <?= !empty($item['tag_style']) ? 'style="' . $item['tag_style'] . '"' : '' ?>>
                <?= htmlspecialchars($item['tag']) ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="lokasi-section" id="lokasi">
      <div class="container">
        <div class="lokasi-card-wrap">
          <div class="lokasi-top">
            <div>
              <span class="section-badge">Jaringan Kampus</span>
              <h2 class="section-title">Titik Kiosk Terdekat</h2>
              <p class="section-subtitle" style="margin-bottom:0;">Pilih lokasi kiosk yang paling dekat dengan fakultas atau lokasimu.</p>
            </div>

            <div class="search-kiosk">
              <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF"
                stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <input type="text" placeholder="Cari kampus atau gedung...">
            </div>
          </div>

          <div class="lokasi-grid">
            <?php foreach ($daftar_kiosk as $kiosk): ?>
              <article class="kartu-kiosk">
                <div class="kiosk-status-head">
                  <?= render_kiosk_status_badge($kiosk['status']) ?>
                  <span class="kiosk-distance"><?= htmlspecialchars($kiosk['jarak']) ?></span>
                </div>
                <h4><?= htmlspecialchars($kiosk['nama']) ?></h4>
                <address><?= htmlspecialchars($kiosk['lokasi']) ?></address>
                <div class="kiosk-spec-grid">
                  <div>Kertas A4: <?= render_stok_kertas_label($kiosk['stok_a4']) ?></div>
                  <div>Kertas F4: <?= render_stok_kertas_label($kiosk['stok_f4']) ?></div>
                  <div>Buka: <strong><?= htmlspecialchars($kiosk['jam_buka']) ?></strong></div>
                  <div>Status: <strong style="color: #059669;">Siap Cetak</strong></div>
                </div>
                <div class="kiosk-actions">
                  <a href="#dropzone" class="btn-pilih-kiosk">Pilih Lokasi Ini</a>
                  <a href="<?= htmlspecialchars($kiosk['maps_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn-maps"
                    title="Buka Maps">
                    <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                      stroke-width="2">
                      <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                    </svg>
                  </a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION TESTIMONI PEMBELIAN TERBARU (MAHASISWA) -->
    <section class="testimoni-section" id="testimoni">
      <div class="container">
        <header class="testimoni-header">
          <span class="section-badge">Pengalaman Mahasiswa</span>
          <h2 class="section-title">Dipercaya Ribuan Mahasiswa di Berbagai Kampus</h2>
          <p class="section-subtitle">Lihat apa kata mereka yang sudah merasakan praktisnya cetak tanpa antre untuk tugas harian, laporan praktikum, hingga naskah skripsi.</p>
        </header>

        <div class="testimoni-grid">
          <!-- Kartu Testimoni 1 -->
          <article class="testimoni-card">
            <div class="testimoni-card-glow"></div>
            <div class="testimoni-card-quote-mark">“</div>
            <div>
              <div class="testimoni-top">
                <span class="badge-verified-ticket">
                  <svg class="icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  Tiket CPT-8492 Terverifikasi
                </span>
                <div class="testimoni-stars" title="Rating 5/5">★★★★★</div>
              </div>
              <p class="testimoni-text">
                "Beneran penyelamat waktu malam sebelum sidang skripsi! Jam 11 malam upload dari kosan, bayar QRIS, langsung meluncur ke perpus pusat ambil cetakan pakai kode tiket. Hasil cetak laserjetnya tajem banget dan gak lecek sama sekali."
              </p>
            </div>
            <div>
              <div class="testimoni-order-spec">
                <span>📄 Cetak: 64 Hlm Skripsi · Duplex A4 80g · Kiosk UI Depok</span>
              </div>
              <div class="testimoni-student">
                <div class="testimoni-avatar" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);">
                  FR
                </div>
                <div class="testimoni-author">
                  <h4>Fikri Ramadhan</h4>
                  <p>Teknik Informatika · Universitas Indonesia</p>
                </div>
              </div>
            </div>
          </article>

          <!-- Kartu Testimoni 2 -->
          <article class="testimoni-card">
            <div class="testimoni-card-glow"></div>
            <div class="testimoni-card-quote-mark">“</div>
            <div>
              <div class="testimoni-top">
                <span class="badge-verified-ticket">
                  <svg class="icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  Tiket CPT-8493 Terverifikasi
                </span>
                <div class="testimoni-stars" title="Rating 5/5">★★★★★</div>
              </div>
              <p class="testimoni-text">
                "Udah gak pernah mau antre di fotokopian depan kampus lagi semenjak ada <?= htmlspecialchars($site_info['nama']) ?>. Format warna diagram praktikum keluar tajam sesuai file aslinya, auto-count halamannya juga akurat gak perlu repot hitung manual."
              </p>
            </div>
            <div>
              <div class="testimoni-order-spec">
                <span>🎨 Cetak: 18 Hlm Jurnal · Full Color Foto · Kiosk ITB Ganesha</span>
              </div>
              <div class="testimoni-student">
                <div class="testimoni-avatar" style="background: linear-gradient(135deg, #EC4899 0%, #BE185D 100%);">
                  NP
                </div>
                <div class="testimoni-author">
                  <h4>Nabila Putri Anggraini</h4>
                  <p>Sekolah Farmasi · Institut Teknologi Bandung</p>
                </div>
              </div>
            </div>
          </article>

          <!-- Kartu Testimoni 3 -->
          <article class="testimoni-card">
            <div class="testimoni-card-glow"></div>
            <div class="testimoni-card-quote-mark">“</div>
            <div>
              <div class="testimoni-top">
                <span class="badge-verified-ticket">
                  <svg class="icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  Tiket CPT-8494 Terverifikasi
                </span>
                <div class="testimoni-stars" title="Rating 5/5">★★★★★</div>
              </div>
              <p class="testimoni-text">
                "Fitur tracking tiketnya keren banget, ada progress bar real-time dan konfirmasi kode tiket pesanan. Tinggal jalan santai ke Gelanggang, masukkan kode tiket di layar kiosk, dokumen sudah tercetak rapi. Sangat recommended buat mahasiswa aktif!"
              </p>
            </div>
            <div>
              <div class="testimoni-order-spec">
                <span>📌 Cetak: 42 Hlm Makalah · A4 75g + Jilid Staples · UGM</span>
              </div>
              <div class="testimoni-student">
                <div class="testimoni-avatar" style="background: linear-gradient(135deg, #10B981 0%, #047857 100%);">
                  DA
                </div>
                <div class="testimoni-author">
                  <h4>Dimas Arya Wicaksana</h4>
                  <p>Fakultas Ekonomika &amp; Bisnis · Universitas Gadjah Mada</p>
                </div>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- SECTION BANTUAN & FAQ 2-KOLOM (SISI KIRI STICKY) -->
    <section class="faq-section" id="faq">
      <div class="container faq-grid">
        <!-- Kolom Kiri: Sticky Header & CS Contact CTA -->
        <aside class="faq-sticky-col">
          <div class="faq-sticky-content">
            <span class="section-badge">Pusat Bantuan &amp; Edukasi</span>
            <h2 class="section-title">Kerap Ditanyakan seputar Kiosk <?= htmlspecialchars($site_info['nama']) ?></h2>
            <p class="faq-desc">
              Punya pertanyaan tentang cara cetak dokumen, keamanan enkripsi file, atau mekanisme pengambilan di kiosk? Temukan jawaban ringkas di sini.
            </p>

            <div class="faq-cs-card">
              <div class="faq-cs-icon">💬</div>
              <div class="faq-cs-info">
                <strong>Masih ada kendala mencetak?</strong>
                <span>Customer Support siaga respon cepat</span>
              </div>
            </div>

            <a href="<?= htmlspecialchars($site_info['kontak_wa_link']) ?>" target="_blank" rel="noopener noreferrer" class="btn-faq-cs">
              <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
              <span>Hubungi CS WhatsApp</span>
            </a>
          </div>
        </aside>

        <!-- Kolom Kanan: Daftar Pertanyaan Akordeon (<details>/<summary>) -->
        <div class="faq-accordion-col">
          <details class="faq-item" open>
            <summary class="faq-question">
              <span>Bagaimana cara mencetak dan mengambil dokumen di Kiosk?</span>
              <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </summary>
            <div class="faq-answer">
              <p>Cukup 3 langkah mudah: (1) Unggah berkas dokumenmu di halaman web ini, (2) Tentukan opsi cetak (warna / hitam putih, jenis kertas, bolak-balik) lalu selesaikan pembayaran via QRIS, (3) Kamu akan mendapatkan <strong>Kode Tiket Pesanan</strong> (cth: CPT-8492). Datang ke kiosk terdekat, masukkan kode tiket pada layar mesin, dan dokumenmu langsung tercetak dan siap diambil!</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">
              <span>Apakah berkas dokumen saya aman dan terjaga kerahasiaannya?</span>
              <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </summary>
            <div class="faq-answer">
              <p>Sangat aman! <?= htmlspecialchars($site_info['nama']) ?> menerapkan enkripsi transmisi TLS 256-bit kelas enterprise. Berkas hanya dikirim ke sistem spooler printer pada saat pencetakan dan <strong>otomatis terhapus permanen dari sistem lokal kiosk dalam <?= (int)$site_info['retensi_jam'] ?> jam</strong> setelah berkas selesai dicetak dan diambil di kiosk.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">
              <span>Bagaimana jika terjadi paper jam atau kendala mekanis pada printer?</span>
              <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </summary>
            <div class="faq-answer">
              <p>Setiap unit Kiosk <?= htmlspecialchars($site_info['nama']) ?> dilengkapi sensor presisi pemantau kertas dan toner. Jika terdeteksi gangguan mekanis, antrean cetakmu akan dialihkan secara otomatis ke unit kiosk terdekat atau kamu bisa menekan tombol <strong>Chat Admin</strong> untuk refund instan via QRIS.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">
              <span>Berapa lama batas waktu pengambilan dokumen di Kiosk?</span>
              <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </summary>
            <div class="faq-answer">
              <p>Dokumen dapat diambil kapan saja dengan memasukkan Kode Tiket pada mesin Kiosk yang Anda pilih selama jam operasional kiosk.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">
              <span>Metode pembayaran apa saja yang didukung?</span>
              <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </summary>
            <div class="faq-answer">
              <p>Kami mendukung seluruh dompet digital (GoPay, OVO, DANA, ShopeePay, LinkAja) serta Mobile Banking bank mana pun di Indonesia melalui QRIS Standar Bank Indonesia dengan verifikasi otomatis detik itu juga.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">
              <span>Apakah menyediakan ukuran kertas F4 (Folio) dan opsi Jilid?</span>
              <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </summary>
            <div class="faq-answer">
              <p>Tentu saja! Selain A4 75 GSM dan 80 GSM untuk tugas &amp; skripsi, kami juga menyediakan kertas F4 Folio 80 GSM serta opsi finishing jilid staples sudut atau jilid lakban rapi yang dapat dipilih langsung saat checkout.</p>
            </div>
          </details>
        </div>
      </div>
    </section>

    <section class="cta-section" id="cta">
      <div class="container">
        <div class="cta-banner">
          <div class="cta-text">
            <span
              style="font-size: 11px; font-family: var(--font-mono); color: var(--peach); font-weight: 800; text-transform: uppercase;">Cetak
              Mandiri Tanpa Antre</span>
            <h2>Siap Cetak Dokumen Kamu Sekarang?</h2>
            <p>Upload berkasmu sekarang juga, bayar mudah via QRIS, dan ambil di mesin kiosk pintar <?= htmlspecialchars($site_info['nama']) ?> kapan pun kamu sempat.</p>
            <div class="cta-btn-group">
              <a href="cetak.php" class="btn btn-cetak">
                <span>Buka Sistem Cetak</span>
                <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2.5">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </a>
              <a href="<?= htmlspecialchars($site_info['kontak_wa_link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-wa">
                <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Hubungi CS WhatsApp</span>
              </a>
            </div>
          </div>

          <aside class="cta-badge-card">
            <div class="cta-qr-icon">
              <svg class="icon" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
            </div>
            <strong style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Scan di Kiosk</strong>
            <p style="font-size: 11px; color: #D6CEC8; margin-top: 4px;">Ambil cetakan langsung di mesin Kiosk dengan kode tiket
            </p>
            <div class="cta-badge-code">CPT-2026</div>
          </aside>
        </div>
      </div>
    </section>

  </main>

  <aside class="floating-help" id="floatingHelp">
    <!-- Tombol Floating Chat Admin -->
    <a href="<?= htmlspecialchars($site_info['kontak_wa_link']) ?>" target="_blank" rel="noopener noreferrer" class="btn-floating"
      title="Chat Admin Cepatik" aria-label="Chat Admin Customer Service">
      <span class="floating-icon-wrap">
        <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
          <path d="M8 10h.01M12 10h.01M16 10h.01"></path>
        </svg>
      </span>
      <span class="floating-label">Chat Admin</span>
      <span class="floating-pulse-dot" title="CS Online"></span>
    </a>

    <!-- Live Operational Status Widget (Di Bagian Bawah Tombol Chat Admin) -->
    <div class="floating-op-card" id="floatingOpCard">
      <div class="op-status-card is-online" id="opStatusCard">
        <!-- Baris Header -->
        <div class="op-card-header">
          <div class="op-header-left">
            <span class="op-duty-text" id="opDutyText">• ON-DUTY</span>
          </div>
          <div class="op-header-right">
            <span class="op-tz-label">WIB · UTC+7</span>
            <button type="button" class="btn-op-close" id="btnOpClose" title="Sembunyikan status" aria-label="Tutup">×</button>
          </div>
        </div>

        <!-- Jam Digital Monospace: Format HH:MM:SS (Detik :SS Ramping & Lembut) -->
        <div class="op-card-clock" id="opCardClock">
          <span class="op-clock-hm" id="opClockHM">--:--</span><span class="op-clock-sec" id="opClockSec">:--</span>
        </div>

        <!-- Baris Keterangan -->
        <div class="op-card-desc" id="opCardDesc">Admin &amp; Kiosk Online</div>

        <!-- Baris Jadwal Operasional -->
        <div class="op-card-schedule">
          <span class="op-schedule-label">JAM BUKA</span>
          <span class="op-schedule-time" id="opScheduleTime">08:00–22:00</span>
        </div>

        <!-- Baris Bawah: Status Pill Badge Memanjang -->
        <div class="op-card-badge badge-online" id="opCardBadge">
          <span class="op-badge-text" id="opBadgeText">● ONLINE</span>
        </div>
      </div>
    </div>
  </aside>

  <footer class="footer">
    <div class="container footer-content">
      <div class="footer-brand">
        <img src="assets/images/logo.svg" alt="<?= htmlspecialchars($site_info['nama']) ?> Logo">
        <span><?= htmlspecialchars($site_info['nama']) ?> Cloud Printing</span>
        <span style="font-size: 11px; color: var(--text-muted); margin-left: 8px;">© <?= (int)$site_info['tahun'] ?> <?= htmlspecialchars($site_info['perusahaan']) ?></span>
      </div>

      <nav class="footer-links">
        <a href="login.html">ꗃ Panel Admin</a>
        <a href="#testimoni">Testimoni</a>
        <a href="#faq">Bantuan &amp; FAQ</a>
        <a href="#lokasi">Lokasi Kiosk</a>
        <a href="#">Privasi</a>
      </nav>
    </div>
  </footer>

</body>

</html>
