

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
          window.location.href = 'cetak.html';
        }, 800);
      }
    });
  }

  // ==========================================
  // 3. LOGIKA QUICK TRACKER (CEK TIKET VIA LOCALSTORAGE)
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
          trackerResult.innerHTML = '<div style="background:#FEF2F2; color:#991B1B; padding:10px 14px; border-radius:8px; font-size:12px; border:1px solid #FECACA;">⚠️ Masukkan kode tiket terlebih dahulu (contoh: CPT-8492).</div>';
        }
        return;
      }

      // Periksa apakah tiket tersimpan dari sesi aplikasi sebelumnya di localStorage
      let tiketAktif = null;
      try {
        const stored = localStorage.getItem('cepatik_tiket_aktif');
        if (stored) tiketAktif = JSON.parse(stored);
      } catch (err) { }

      const isCocok = tiketAktif && (kode === tiketAktif.tiket || kode === tiketAktif.pin || kode === 'CPT-8492');
      const dataTiket = isCocok && tiketAktif ? tiketAktif : {
        tiket: kode,
        pin: '8492',
        file: { nama: 'Skripsi_BAB_1-5_Final.pdf' },
        kiosk: 'Kiosk Perpustakaan Kampus (Loker Siap Buka)',
        total: 5250
      };

      if (trackerResult) {
        trackerResult.style.display = 'block';
        trackerResult.innerHTML = `
          <div style="background:#ECFDF5; border:1px solid #A7F3D0; border-radius:10px; padding:14px; font-size:12.5px; color:#065F46; box-shadow:0 4px 12px rgba(6,95,70,0.06); animation:fadeIn 0.3s ease;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
              <span style="font-weight:800; font-family:var(--font-mono); font-size:14px; color:#047857;">✓ TIKET [${dataTiket.tiket}] DITEMUKAN</span>
              <span style="background:#059669; color:#fff; font-size:10px; padding:2px 8px; border-radius:4px; font-weight:800;">SIAP DI LOKER</span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; font-size:12px; margin-bottom:10px; color:#1F2937;">
              <div><strong>Berkas:</strong> ${dataTiket.file.nama}</div>
              <div><strong>PIN Loker:</strong> <span style="background:#FEF3C7; color:#92400E; padding:2px 6px; border-radius:4px; font-family:var(--font-mono); font-weight:800;">${dataTiket.pin}</span></div>
              <div style="grid-column: span 2;"><strong>Lokasi:</strong> ${dataTiket.kiosk}</div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; font-size:11px; border-top:1px dashed #A7F3D0; padding-top:8px;">
              <span style="color:#059669;">File terhapus otomatis setelah diambil</span>
              <button type="button" onclick="document.getElementById('trackerResult').style.display='none'" style="background:none; border:none; color:#065F46; font-size:11px; cursor:pointer; font-weight:700; text-decoration:underline;">Tutup</button>
            </div>
          </div>
        `;
      }
    });
  }

  // Inisialisasi perhitungan kalkulator saat halaman dimuat
  updateKalkulator();
});
