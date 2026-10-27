

document.addEventListener('DOMContentLoaded', function () {

  const paperCards = document.querySelectorAll('.radio-kertas');
  const colorCards = document.querySelectorAll('.radio-warna');
  const pageInput = document.getElementById('inputHalaman');
  const duplexToggle = document.getElementById('toggleDuplex');

  // Elemen Struk / Receipt
  const strukKertas = document.getElementById('strukKertas');
  const strukWarna = document.getElementById('strukWarna');
  const strukHalaman = document.getElementById('strukHalaman');
  const strukMetode = document.getElementById('strukMetode');
  const strukTotal = document.getElementById('strukTotal');

  // Harga dasar per opsi
  const hargaKertas = {
    'a4_75': { nama: 'A4 Standard (75 GSM)', tambah: 0 },
    'a4_80': { nama: 'A4 Tebal (80 GSM)', tambah: 50 },
    'f4_80': { nama: 'F4 Folio (80 GSM)', tambah: 100 }
  };

  const hargaWarna = {
    'bw': { nama: 'Hitam Putih (B/W)', harga: 350 },
    'color_std': { nama: 'Warna Standar', harga: 1000 },
    'color_full': { nama: 'Full Color Foto', harga: 2000 }
  };

  // Fungsi menghitung total dan memperbarui tampilan struk
  function updateKalkulator() {
    // Ambil radio kertas yang terpilih
    let selectedPaper = 'a4_75';
    paperCards.forEach(card => {
      const radio = card.querySelector('input');
      if (radio.checked) {
        selectedPaper = radio.value;
        card.classList.add('selected');
      } else {
        card.classList.remove('selected');
      }
    });

    // Ambil radio warna yang terpilih
    let selectedColor = 'bw';
    colorCards.forEach(card => {
      const radio = card.querySelector('input');
      if (radio.checked) {
        selectedColor = radio.value;
        card.classList.add('selected');
      } else {
        card.classList.remove('selected');
      }
    });

    // Ambil jumlah halaman (minimal 1)
    let halaman = parseInt(pageInput.value) || 1;
    if (halaman < 1) halaman = 1;

    // Periksa duplex (bolak-balik)
    const isDuplex = duplexToggle.checked;

    // Hitung tarif per lembar
    const biayaDasarWarna = hargaWarna[selectedColor].harga;
    const biayaKertas = hargaKertas[selectedPaper].tambah;
    const tarifPerHalaman = biayaDasarWarna + biayaKertas;

    // Total Biaya
    let subtotal = tarifPerHalaman * halaman;
    // Jika duplex & lebih dari 1 halaman, beri sedikit diskon hemat kertas
    if (isDuplex && halaman > 1) {
      subtotal = Math.round(subtotal * 0.95); // Diskon 5%
    }

    // Perbarui teks di struk
    strukKertas.textContent = hargaKertas[selectedPaper].nama;
    strukWarna.textContent = hargaWarna[selectedColor].nama;
    strukHalaman.textContent = `${halaman} hlm x 1 rangkap`;
    strukMetode.textContent = isDuplex ? 'Bolak-Balik (Duplex)' : '1 Sisi (Simplex)';
    strukTotal.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
  }

  // Pasang event listener untuk radio kartu kertas
  paperCards.forEach(card => {
    card.addEventListener('click', function () {
      const radio = this.querySelector('input');
      radio.checked = true;
      updateKalkulator();
    });
  });

  // Pasang event listener untuk radio kartu warna
  colorCards.forEach(card => {
    card.addEventListener('click', function () {
      const radio = this.querySelector('input');
      radio.checked = true;
      updateKalkulator();
    });
  });

  // Pasang event listener untuk jumlah halaman dan toggle duplex
  if (pageInput) {
    pageInput.addEventListener('input', updateKalkulator);
  }
  if (duplexToggle) {
    duplexToggle.addEventListener('change', updateKalkulator);
  }

  // ==========================================
  // 2. LOGIKA UPLOAD / DROPZONE FILE PREVIEW
  // ==========================================
  const fileInput = document.getElementById('fileInput');
  const dropzoneTitle = document.getElementById('dropzoneTitle');

  if (fileInput && dropzoneTitle) {
    fileInput.addEventListener('change', function () {
      if (this.files && this.files.length > 0) {
        const file = this.files[0];
        const ukuranFormatted = file.size >= 1024 * 1024
          ? (file.size / (1024 * 1024)).toFixed(1) + ' MB'
          : Math.max(1, Math.round(file.size / 1024)) + ' KB';

        dropzoneTitle.textContent = `✓ ${file.name} (${ukuranFormatted}) - Membuka Sistem...`;
        dropzoneTitle.style.color = '#059669';

        try {
          sessionStorage.setItem('cepatik_draft_file_name', file.name);
          sessionStorage.setItem('cepatik_draft_file_size', ukuranFormatted);
        } catch (e) { }

        // Beralih ke halaman sistem cetak dalam 800ms
        setTimeout(function () {
          const isPhp = window.location.pathname.endsWith('.php') || !window.location.pathname.includes('.html');
          window.location.href = isPhp ? 'cetak.php' : 'cetak.html';
        }, 800);
      }
    });
  }

  // ==========================================
  // 3. WIDGET STATUS JAM OPERASIONAL & LIVE CLOCK (REAL-TIME DETIK DEMI DETIK)
  // ==========================================
  function updateOperationalClock() {
    const now = new Date();

    // Kompensasi presisi zona waktu WIB (UTC+7)
    const utcMs = now.getTime() + (now.getTimezoneOffset() * 60000);
    const wibDate = new Date(utcMs + (7 * 3600000));

    const currentHour = wibDate.getHours();
    const jam = String(currentHour).padStart(2, '0');
    const menit = String(wibDate.getMinutes()).padStart(2, '0');
    const detik = String(wibDate.getSeconds()).padStart(2, '0');
    const timeString = `${jam}:${menit}:${detik} WIB`;

    // Deteksi jam operasional Cepatik (08:00 - 22:00 WIB)
    const isOnline = currentHour >= 8 && currentHour < 22;

    // 1. Update Komponen "Live Operational Status Widget" (.op-status-card)
    const opCards = document.querySelectorAll('.op-status-card');
    opCards.forEach(card => {
      // Container status variant
      if (isOnline) {
        card.classList.add('is-online');
        card.classList.remove('is-offline');
      } else {
        card.classList.add('is-offline');
        card.classList.remove('is-online');
      }

      // Baris Header: Sisi kiri status ringkas "• ON-DUTY" / "• OFF-DUTY"
      const dutyText = card.querySelector('.op-duty-text');
      if (dutyText) {
        dutyText.textContent = isOnline ? '• ON-DUTY' : '• OFF-DUTY';
      }

      const dutyDot = card.querySelector('.op-duty-dot');
      if (dutyDot) {
        dutyDot.className = `op-duty-dot ${isOnline ? 'online' : 'offline'}`;
      }

      // Jam Digital Monospace: HH:MM dan :SS lebih ramping / kontras lembut
      const clockHM = card.querySelector('.op-clock-hm');
      const clockSec = card.querySelector('.op-clock-sec');
      if (clockHM) clockHM.textContent = `${jam}:${menit}`;
      if (clockSec) clockSec.textContent = `:${detik}`;

      // Baris Keterangan: teks status ketersediaan di bawah jam
      const cardDesc = card.querySelector('.op-card-desc');
      if (cardDesc) {
        cardDesc.textContent = isOnline ? 'Admin & Kiosk Online' : 'Kiosk Mandiri (CS Istirahat)';
      }

      // Baris Jadwal: 08:00–22:00
      const schedTime = card.querySelector('.op-schedule-time');
      if (schedTime) {
        schedTime.textContent = '08:00–22:00';
      }

      // Baris Bawah: Status Pill Badge memanjang ("● ONLINE" / "● OFFLINE")
      const cardBadge = card.querySelector('.op-card-badge');
      const badgeText = card.querySelector('.op-badge-text');
      if (cardBadge) {
        if (isOnline) {
          cardBadge.className = 'op-card-badge badge-online';
          if (badgeText) badgeText.textContent = '● ONLINE';
        } else {
          cardBadge.className = 'op-card-badge badge-offline';
          if (badgeText) badgeText.textContent = '● OFFLINE';
        }
      }
    });

    // 2. Update Topbar Clock & Status
    const digitalClock = document.getElementById('digitalClock');
    if (digitalClock) {
      digitalClock.textContent = timeString;
    }

    const operationalBadge = document.getElementById('operationalStatusBadge');
    const operationalDot = document.getElementById('operationalDot');
    const operationalStatusText = document.getElementById('operationalStatusText');

    if (operationalDot && operationalStatusText) {
      if (isOnline) {
        operationalDot.className = 'status-indicator-dot online';
        operationalStatusText.textContent = 'ON-DUTY · KIOSK & CS ONLINE';
        if (operationalBadge) {
          operationalBadge.classList.add('is-online');
          operationalBadge.classList.remove('is-offline');
        }
      } else {
        operationalDot.className = 'status-indicator-dot offline';
        operationalStatusText.textContent = 'OFF-DUTY · KIOSK STANDBY';
        if (operationalBadge) {
          operationalBadge.classList.add('is-offline');
          operationalBadge.classList.remove('is-online');
        }
      }
    }

    // 3. Fallback Legacy Elements (jika ada di template lain)
    const heroLiveClock = document.getElementById('heroLiveClock');
    const heroOpBadge = document.getElementById('heroOpBadge');
    const heroOpDesc = document.getElementById('heroOpDesc');
    const heroOpDot = document.getElementById('heroOpDot');
    const heroPill = document.getElementById('heroOperationalPill');

    if (heroLiveClock) {
      heroLiveClock.textContent = timeString;
    }

    if (heroOpBadge && heroOpDot) {
      if (isOnline) {
        heroOpBadge.textContent = 'ON-DUTY · ONLINE';
        heroOpBadge.className = 'hero-op-badge badge-on-duty';
        if (heroOpDesc) heroOpDesc.textContent = 'Admin & Kiosk Online (Buka s/d 22:00)';
        heroOpDot.className = 'hero-op-dot online';
        if (heroPill) {
          heroPill.classList.add('pill-online');
          heroPill.classList.remove('pill-offline');
        }
      } else {
        heroOpBadge.textContent = 'OFF-DUTY · OFFLINE';
        heroOpBadge.className = 'hero-op-badge badge-off-duty';
        if (heroOpDesc) heroOpDesc.textContent = 'Kiosk Mandiri (CS Istirahat)';
        heroOpDot.className = 'hero-op-dot offline';
        if (heroPill) {
          heroPill.classList.add('pill-offline');
          heroPill.classList.remove('pill-online');
        }
      }
    }
  }

  // Jalankan segera dan pasang interval 1000ms
  updateOperationalClock();
  setInterval(updateOperationalClock, 1000);

  // ==========================================
  // 4. LOGIKA QUICK TRACKER REAL-TIME
  // ==========================================
  const formTracker = document.getElementById('formTracker');
  const inputTiket = document.getElementById('inputTiket');
  const trackerResult = document.getElementById('trackerResult');

  if (formTracker) {
    formTracker.addEventListener('submit', function (e) {
      e.preventDefault();
      const kode = inputTiket.value.trim().toUpperCase();
      if (!kode) {
        if (trackerResult) {
          trackerResult.style.display = 'block';
          trackerResult.innerHTML = '<div style="background:#FEF2F2; color:#991B1B; padding:10px 14px; border-radius:8px; font-size:12px; border:1px solid #FECACA;">⚠️ Masukkan kode tiket atau PIN terlebih dahulu (contoh: CPT-8492).</div>';
        }
        return;
      }

      // Cari langsung ke CepatikDB tunggal
      const dataTiket = window.CepatikDB ? window.CepatikDB.findOrderByTicket(kode) : null;

      if (!dataTiket) {
        if (trackerResult) {
          trackerResult.style.display = 'block';
          trackerResult.innerHTML = `
            <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:14px; font-size:12.5px; color:#991B1B; animation:fadeIn 0.3s ease;">
              <div style="font-weight:800; font-size:13px; margin-bottom:4px;">❌ TIKET [${kode}] TIDAK DITEMUKAN</div>
              <p style="margin:0; font-size:12px; color:#7F1D1D;">Pesanan tidak ada di antrean database kiosk. Pastikan kode tiket yang dimasukkan benar.</p>
            </div>
          `;
        }
        return;
      }

      const rawFileName = typeof dataTiket.file === 'object' ? dataTiket.file.nama : dataTiket.file;
      const isSiap = dataTiket.status === 'Siap Diambil' || dataTiket.status === 'Siap di Loker';
      const statusBadgeBg = isSiap ? '#ECFDF5' : (dataTiket.status === 'Sedang Diproses' ? '#EFF6FF' : '#FEF3C7');
      const statusBadgeColor = isSiap ? '#065F46' : (dataTiket.status === 'Sedang Diproses' ? '#1E40AF' : '#92400E');
      const statusBorder = isSiap ? '#A7F3D0' : (dataTiket.status === 'Sedang Diproses' ? '#BFDBFE' : '#FCD34D');

      if (trackerResult) {
        trackerResult.style.display = 'block';
        trackerResult.innerHTML = `
          <div style="background:#ECFDF5; border:1px solid #A7F3D0; border-radius:10px; padding:14px; font-size:12.5px; color:#065F46; box-shadow:0 4px 12px rgba(6,95,70,0.06); animation:fadeIn 0.3s ease;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; gap:8px; flex-wrap:wrap;">
              <span style="font-weight:800; font-family:var(--font-mono); font-size:14px; color:#047857;">✓ KODE TIKET: [${dataTiket.id || dataTiket.tiket}]</span>
              <span style="background:${statusBadgeBg}; color:${statusBadgeColor}; border:1px solid ${statusBorder}; font-size:11px; padding:3px 10px; border-radius:999px; font-weight:800; text-transform:uppercase;">
                ${dataTiket.status === 'Siap di Loker' ? 'Siap Diambil' : dataTiket.status}
              </span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; font-size:12px; margin-bottom:10px; color:#1F2937;">
              <div style="grid-column: span 2;"><strong>Berkas:</strong> ${rawFileName}</div>
              <div style="grid-column: span 2;"><strong>Lokasi Kiosk:</strong> ${dataTiket.kiosk}</div>
              <div><strong>Biaya:</strong> ${window.CepatikDB ? window.CepatikDB.formatRupiah(dataTiket.total) : 'Rp ' + dataTiket.total}</div>
              <div><strong>Waktu:</strong> ${dataTiket.waktu || 'Hari ini'}</div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; font-size:11px; border-top:1px dashed #A7F3D0; padding-top:8px;">
              <span style="color:#059669;">Gunakan Kode Tiket di atas pada layar mesin Kiosk untuk mengambil cetakan</span>
              <button type="button" onclick="document.getElementById('trackerResult').style.display='none'" style="background:none; border:none; color:#065F46; font-size:11px; cursor:pointer; font-weight:700; text-decoration:underline;">Tutup</button>
            </div>
          </div>
        `;
      }
    });
  }

  // 5. Toggle minimize/close kartu status operasional floating
  const btnOpClose = document.getElementById('btnOpClose');
  const floatingOpCard = document.getElementById('floatingOpCard');
  if (btnOpClose && floatingOpCard) {
    btnOpClose.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      floatingOpCard.classList.toggle('is-hidden');
    });
  }

  // Inisialisasi perhitungan kalkulator saat halaman dimuat
  updateKalkulator();
});
