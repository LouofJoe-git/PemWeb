<?php
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cepatik - Portal Perkuliahan PemWeb (Week 2 - Week 7)</title>
  <link rel="icon" type="image/svg+xml" href="Week7/assets/images/icon.svg">
  <style>
    :root {
      --primary: #06b6d4;
      --primary-hover: #0891b2;
      --primary-glow: rgba(6, 182, 212, 0.25);
      --bg-dark: #090d16;
      --card-bg: rgba(17, 24, 39, 0.85);
      --border-color: rgba(255, 255, 255, 0.08);
      --text-main: #f3f4f6;
      --text-muted: #9ca3af;
      --success: #10b981;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    body {
      background: radial-gradient(circle at 50% 0%, #162438 0%, var(--bg-dark) 70%);
      color: var(--text-main);
      min-height: 100vh;
      padding: 40px 20px;
    }

    .container {
      max-width: 1100px;
      margin: 0 auto;
    }

    header {
      text-align: center;
      margin-bottom: 36px;
    }

    .badge-hub {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(6, 182, 212, 0.12);
      border: 1px solid rgba(6, 182, 212, 0.35);
      color: var(--primary);
      padding: 6px 16px;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 16px;
    }

    .badge-hub span.dot {
      width: 8px;
      height: 8px;
      background: var(--primary);
      border-radius: 50%;
      box-shadow: 0 0 10px var(--primary);
    }

    h1 {
      font-size: 32px;
      font-weight: 800;
      letter-spacing: -0.5px;
      margin-bottom: 10px;
    }

    h1 span {
      background: linear-gradient(135deg, #38bdf8 0%, var(--primary) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    p.subtitle {
      color: var(--text-muted);
      font-size: 15px;
      max-width: 650px;
      margin: 0 auto;
      line-height: 1.6;
    }

    .team-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 20px 24px;
      margin-bottom: 36px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      backdrop-filter: blur(12px);
    }

    .team-info h3 {
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--primary);
      margin-bottom: 8px;
    }

    .members-list {
      display: flex;
      flex-wrap: wrap;
      gap: 24px;
    }

    .member-item {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, rgba(6, 182, 212, 0.3), rgba(59, 130, 246, 0.3));
      border: 1px solid var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: var(--primary);
      font-size: 15px;
    }

    .member-item div strong {
      display: block;
      font-size: 14px;
      color: var(--text-main);
    }

    .member-item div span {
      font-size: 12px;
      color: var(--text-muted);
    }

    .grid-weeks {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 20px;
      margin-bottom: 36px;
    }

    .card-week {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 24px;
      transition: all 0.25s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      backdrop-filter: blur(8px);
    }

    .card-week:hover {
      transform: translateY(-4px);
      border-color: rgba(6, 182, 212, 0.4);
      box-shadow: 0 12px 24px -10px var(--primary-glow);
    }

    .card-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .pill-week {
      background: rgba(6, 182, 212, 0.15);
      color: var(--primary);
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 700;
    }

    .status-ok {
      color: var(--success);
      font-size: 12px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .card-week h2 {
      font-size: 18px;
      margin-bottom: 8px;
      color: #fff;
    }

    .card-week p {
      font-size: 13px;
      color: var(--text-muted);
      line-height: 1.5;
      margin-bottom: 20px;
    }

    .tech-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-bottom: 20px;
    }

    .tag {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.08);
      font-size: 11px;
      color: #cbd5e1;
      padding: 3px 8px;
      border-radius: 4px;
    }

    .btn-launch {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
      color: #fff;
      text-decoration: none;
      padding: 11px 16px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      transition: opacity 0.2s;
    }

    .btn-launch:hover {
      opacity: 0.92;
    }

    .card-doc {
      background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(17, 24, 39, 0.8) 100%);
      border: 1px dashed rgba(6, 182, 212, 0.4);
      border-radius: 16px;
      padding: 24px;
      text-align: center;
    }

    .card-doc h3 {
      font-size: 18px;
      margin-bottom: 8px;
    }

    .card-doc p {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 16px;
    }

    .doc-links {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 10px;
    }

    .btn-doc {
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.12);
      color: #e2e8f0;
      text-decoration: none;
      padding: 8px 14px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
      transition: all 0.2s;
    }

    .btn-doc:hover {
      background: var(--primary);
      color: #fff;
      border-color: var(--primary);
    }

    footer {
      text-align: center;
      margin-top: 40px;
      color: var(--text-muted);
      font-size: 12px;
    }
  </style>
</head>

<body>

  <div class="container">

    <!-- Header -->
    <header>
      <div class="badge-hub">
        <span class="dot"></span>
        <span>Portal Evaluasi Praktikum Pemrograman Web</span>
      </div>
      <h1>Cepatik — <span>Cloud Printing Kiosk</span></h1>
      <p class="subtitle">Platform cetak mandiri terdistribusi untuk ekosistem kampus. Dirancang secara berurutan mulai dari fondasi web statis hingga arsitektur modern PHP MVC.</p>
    </header>

    <!-- Info Kelompok -->
    <div class="team-card">
      <div class="team-info">
        <h3>Identitas Tim Pengembang</h3>
        <div class="members-list">
          <div class="member-item">
            <div class="avatar">A</div>
            <div>
              <strong>Zahra Zusseta</strong>
              <span>GitHub: @LouOfJoe-Git (Anggota A)</span>
            </div>
          </div>
          <div class="member-item">
            <div class="avatar">B</div>
            <div>
              <strong>Damar Abror</strong>
              <span>GitHub: @DamarAbror-Git (Anggota B)</span>
            </div>
          </div>
        </div>
      </div>
      <div>
        <a href="Week7/test_suite.php" target="_blank" class="btn-doc" style="background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.4); color: #34d399;">
          ✓ Jalankan 60 Unit Tests
        </a>
      </div>
    </div>

    <!-- Grid Weeks 2 - 7 -->
    <div class="grid-weeks">

      <!-- Week 2 -->
      <div class="card-week">
        <div>
          <div class="card-head">
            <span class="pill-week">WEEK 02</span>
            <span class="status-ok">● Selesai (100%)</span>
          </div>
          <h2>HTML5 &amp; CSS3 Styling</h2>
          <p>Fondasi semantik landing page, form wizard cetak, tabel monitoring admin, tata letak Grid &amp; Flexbox responsif, serta skema tema Glassmorphism.</p>
          <div class="tech-tags">
            <span class="tag">Semantic HTML5</span>
            <span class="tag">CSS Grid</span>
            <span class="tag">Flexbox</span>
            <span class="tag">Responsive</span>
          </div>
        </div>
        <a href="Week2/index.html" class="btn-launch">Buka Proyek Week 2 &rarr;</a>
      </div>

      <!-- Week 3 -->
      <div class="card-week">
        <div>
          <div class="card-head">
            <span class="pill-week">WEEK 03</span>
            <span class="status-ok">● Selesai (100%)</span>
          </div>
          <h2>JavaScript, DOM &amp; Events</h2>
          <p>Kalkulator estimasi biaya real-time, drag-and-drop berkas dokumen, validasi ukuran &amp; format file, serta alur navigasi multi-step wizard 5 langkah.</p>
          <div class="tech-tags">
            <span class="tag">Vanilla JS</span>
            <span class="tag">DOM Traversal</span>
            <span class="tag">Drag &amp; Drop</span>
            <span class="tag">Event Listeners</span>
          </div>
        </div>
        <a href="Week3/index.html" class="btn-launch">Buka Proyek Week 3 &rarr;</a>
      </div>

      <!-- Week 4 -->
      <div class="card-week">
        <div>
          <div class="card-head">
            <span class="pill-week">WEEK 04</span>
            <span class="status-ok">● Selesai (100%)</span>
          </div>
          <h2>API, JSON &amp; Storage Layer</h2>
          <p>Dataset master JSON antrean &amp; kiosk, modul CepatikDB berbasis localStorage, event bus multi-tab, timer QRIS, dan kebijakan privasi Zero-Retention.</p>
          <div class="tech-tags">
            <span class="tag">JSON Dataset</span>
            <span class="tag">Web Storage API</span>
            <span class="tag">Event Bus Pub/Sub</span>
            <span class="tag">Zero-Retention</span>
          </div>
        </div>
        <a href="Week4/index.html" class="btn-launch">Buka Proyek Week 4 &rarr;</a>
      </div>

      <!-- Week 5 -->
      <div class="card-week">
        <div>
          <div class="card-head">
            <span class="pill-week">WEEK 05</span>
            <span class="status-ok">● Selesai (100%)</span>
          </div>
          <h2>PHP Prosedural &amp; Bisnis</h2>
          <p>Master array multidimensi konfigurasi tarif di sisi server, fungsi matematika hitung_estimasi_cetak(), diskon duplex 5%, dan Server-Side Rendering.</p>
          <div class="tech-tags">
            <span class="tag">PHP Procedural</span>
            <span class="tag">Multidim Arrays</span>
            <span class="tag">Math Functions</span>
            <span class="tag">SSR</span>
          </div>
        </div>
        <a href="Week5/index.php" class="btn-launch">Buka Proyek Week 5 &rarr;</a>
      </div>

      <!-- Week 6 -->
      <div class="card-week">
        <div>
          <div class="card-head">
            <span class="pill-week">WEEK 06</span>
            <span class="status-ok">● Selesai (100%)</span>
          </div>
          <h2>PHP OOP &amp; Enkapsulasi</h2>
          <p>Pemodelan Class Kiosk &amp; Class Order, enkapsulasi properti privat, type-hinted constructor, method bisnis calculateTotal(), dan instansiasi objek.</p>
          <div class="tech-tags">
            <span class="tag">PHP OOP</span>
            <span class="tag">Encapsulation</span>
            <span class="tag">Constructor &amp; Getters</span>
            <span class="tag">Business Methods</span>
          </div>
        </div>
        <a href="Week6/index.php" class="btn-launch">Buka Proyek Week 6 &rarr;</a>
      </div>

      <!-- Week 7 -->
      <div class="card-week" style="border-color: rgba(6, 182, 212, 0.5);">
        <div>
          <div class="card-head">
            <span class="pill-week" style="background: var(--primary); color: #fff;">WEEK 07 (LENGKAP)</span>
            <span class="status-ok">● Selesai (100%)</span>
          </div>
          <h2>Arsitektur PHP MVC</h2>
          <p>Arsitektur lengkap Model-View-Controller: Front Controller router (?page=...), BaseController, Models (Tarif, Kiosk, Order), dan View Templates modular.</p>
          <div class="tech-tags">
            <span class="tag">Front Controller</span>
            <span class="tag">MVC Architecture</span>
            <span class="tag">View Templating</span>
            <span class="tag">60 Unit Tests</span>
          </div>
        </div>
        <a href="Week7/index.php" class="btn-launch" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);">Buka Proyek Week 7 (MVC) &rarr;</a>
      </div>

    </div>

    <!-- Dokumentasi Laporan -->
    <div class="card-doc">
      <h3>Dokumentasi Resmi Laporan Mingguan</h3>
      <p>Akses rincian laporan progres, bukti kode, dan rekaman kontribusi Anggota A dan B untuk setiap minggu:</p>
      <div class="doc-links">
        <a href="docs/weekly-progress/week-02.md" target="_blank" class="btn-doc">Laporan Week 02</a>
        <a href="docs/weekly-progress/week-03.md" target="_blank" class="btn-doc">Laporan Week 03</a>
        <a href="docs/weekly-progress/week-04.md" target="_blank" class="btn-doc">Laporan Week 04</a>
        <a href="docs/weekly-progress/week-05.md" target="_blank" class="btn-doc">Laporan Week 05</a>
        <a href="docs/weekly-progress/week-06.md" target="_blank" class="btn-doc">Laporan Week 06</a>
        <a href="docs/weekly-progress/week-07.md" target="_blank" class="btn-doc">Laporan Week 07</a>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      &copy; 2026 Cepatik Cloud Printing Kiosk &middot; Zahra Zusseta &amp; Damar Abror &middot; Praktikum Pemrograman Web
    </footer>

  </div>

</body>

</html>
