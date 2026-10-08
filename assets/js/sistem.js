/**
 * SISTEM.JS - CEPATIK WEB APPLICATION
 * Logika wizard alur pemesanan cetak mandiri 5-langkah:
 * 1. Unggah Dokumen & Validasi Berkas
 * 2. Konfigurasi Kertas, Warna, Duplex, & Penjilidan
 * 3. Pemilihan Mesin Kiosk & Loker Mandiri
 * 4. Pembayaran Instan QRIS & Timer Countdown
 * 5. Penerbitan Kode Tiket Kiosk & PIN Loker
 */

document.addEventListener('DOMContentLoaded', function () {
  // State Data Pesanan Aktif (Awalnya Kosong)
  const pesanan = {
    file: {
      nama: 'Belum dipilih',
      ukuran: '0 MB',
      halaman: 0,
      ekstensi: ''
    },
    kertas: { nama: 'A4 Standard (75 GSM)', tambah: 0 },
    warna: { nama: 'Hitam Putih', harga: 350 },
    duplex: false,
    rangkap: 1,
    jilid: { nama: 'Tanpa Jilid', harga: 0 },
    kiosk: 'Kiosk Perpustakaan UI (Loker Blok A)',
    total: 0,
    tiket: '',
    pin: ''
  };

  let langkahSaatIni = 1;
  let fileSudahDipilih = false; // Awalnya kosong
  let qrisTimerInterval = null;

  // Elemen DOM Wizard
  const wizardSteps = document.querySelectorAll('.wizard-step');
  const stepPanels = document.querySelectorAll('.step-panel');
  const ringkasFile = document.getElementById('ringkasFile');
  const ringkasHalaman = document.getElementById('ringkasHalaman');
  const ringkasKertas = document.getElementById('ringkasKertas');
  const ringkasWarna = document.getElementById('ringkasWarna');
  const ringkasDuplex = document.getElementById('ringkasDuplex');
  const ringkasJilid = document.getElementById('ringkasJilid');
  const ringkasKiosk = document.getElementById('ringkasKiosk');
  const ringkasTotal = document.getElementById('ringkasTotal');
  const inputAppFile = document.getElementById('inputAppFile');
  const boxUploadArea = document.getElementById('boxUploadArea');
  const previewUpload = document.getElementById('previewUpload');
  const previewNamaFile = document.getElementById('previewNamaFile');
  const previewInfoFile = document.getElementById('previewInfoFile');
  const btnHapusBerkas = document.getElementById('btnHapusBerkas');
  const inputRangkap = document.getElementById('inputRangkap');
  const toggleDuplexApp = document.getElementById('toggleDuplexApp');

  // =========================================================================
  // 1. FUNGSI PEMROSESAN BERKAS (UPLOAD & DRAG-AND-DROP)
  // =========================================================================
  function prosesBerkas(file) {
    if (!file) return;

    const fileExt = file.name.split('.').pop().toUpperCase() || 'DOC';
    let ukuranFormatted = '';
    if (file.size >= 1024 * 1024) {
      ukuranFormatted = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
    } else {
      ukuranFormatted = Math.max(1, Math.round(file.size / 1024)) + ' KB';
    }

    // Simulasi hitung estimasi halaman otomatis berdasarkan ukuran file
    let estimasiHalaman = 12;
    if (file.size > 0) {
      estimasiHalaman = Math.max(4, Math.min(80, Math.floor(file.size / 60000) || 10));
    }

    pesanan.file.nama = file.name;
    pesanan.file.ukuran = ukuranFormatted;
    pesanan.file.halaman = estimasiHalaman;
    pesanan.file.ekstensi = fileExt;
    fileSudahDipilih = true;

    // Tampilkan Kotak Preview Berkas Terpilih
    if (previewNamaFile) previewNamaFile.textContent = file.name;
    if (previewInfoFile) {
      previewInfoFile.textContent = `${estimasiHalaman} Halaman · ${ukuranFormatted} · Format ${fileExt} · Siap Cetak`;
    }
    const fileBadge = document.querySelector('.file-badge-icon');
    if (fileBadge) {
      fileBadge.textContent = fileExt.slice(0, 3);
    }
    if (previewUpload) {
      previewUpload.style.display = 'flex';
    }

    hitungTotal();
  }

  // Event Listener: Input File Klasik
  if (inputAppFile) {
    inputAppFile.addEventListener('change', function () {
      if (this.files && this.files.length > 0) {
        prosesBerkas(this.files[0]);
      }
    });
  }

  // Event Listener: Tombol Hapus Berkas
  if (btnHapusBerkas) {
    btnHapusBerkas.addEventListener('click', function (e) {
      e.stopPropagation();
      e.preventDefault();
      fileSudahDipilih = false;
      pesanan.file = { nama: 'Belum dipilih', ukuran: '0 MB', halaman: 0, ekstensi: '' };
      if (inputAppFile) inputAppFile.value = '';
      if (previewUpload) previewUpload.style.display = 'none';
      hitungTotal();
    });
  }

  // Event Listener: Drag and Drop Berkas
  if (boxUploadArea) {
    ['dragenter', 'dragover'].forEach(eventName => {
      boxUploadArea.addEventListener(eventName, function (e) {
        e.preventDefault();
        e.stopPropagation();
        boxUploadArea.classList.add('drag-over');
      }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      boxUploadArea.addEventListener(eventName, function (e) {
        e.preventDefault();
        e.stopPropagation();
        boxUploadArea.classList.remove('drag-over');
      }, false);
    });

    boxUploadArea.addEventListener('drop', function (e) {
      const dt = e.dataTransfer;
      if (dt && dt.files && dt.files.length > 0) {
        prosesBerkas(dt.files[0]);
      }
    }, false);
  }

  // Cek apakah ada draft berkas yang dikirim dari landing page
  try {
    const draftName = sessionStorage.getItem('cepatik_draft_file_name');
    const draftSize = sessionStorage.getItem('cepatik_draft_file_size');
    if (draftName) {
      prosesBerkas({ name: draftName, size: 2.4 * 1024 * 1024 });
      sessionStorage.removeItem('cepatik_draft_file_name');
      sessionStorage.removeItem('cepatik_draft_file_size');
    }
  } catch (e) {
    // SessionStorage opsional
  }

  // =========================================================================
  // 2. FUNGSI PERPINDAHAN LANGKAH WIZARD
  // =========================================================================
  window.pindahLangkah = function (tujuan) {
    if (tujuan < 1 || tujuan > 5) return;

    // Validasi: Wajib unggah berkas sebelum melanjutkan ke Langkah 2
    if (tujuan > 1 && !fileSudahDipilih && langkahSaatIni === 1) {
      alert('Silakan pilih atau unggah berkas dokumen terlebih dahulu sebelum melanjutkan.');
      if (boxUploadArea) {
        boxUploadArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
        boxUploadArea.classList.add('drag-over');
        setTimeout(() => boxUploadArea.classList.remove('drag-over'), 800);
      }
      return;
    }

    langkahSaatIni = tujuan;

    // Perbarui Navigasi Step Tracker
    wizardSteps.forEach((step, index) => {
      const stepIndex = index + 1;
      step.classList.remove('active', 'completed');
      if (stepIndex === langkahSaatIni) {
        step.classList.add('active');
      } else if (stepIndex < langkahSaatIni) {
        step.classList.add('completed');
      }
    });

    // Perbarui Panel Konten Aktif
    stepPanels.forEach((panel) => {
      panel.classList.remove('active-panel');
      if (parseInt(panel.dataset.step) === langkahSaatIni) {
        panel.classList.add('active-panel');
      }
    });

    // Kontrol Timer QRIS
    if (langkahSaatIni === 4) {
      mulaiTimerQRIS(15 * 60); // 15 menit
    } else if (qrisTimerInterval) {
      clearInterval(qrisTimerInterval);
    }

    // Scroll halus ke atas formulir
    window.scrollTo({ top: 120, behavior: 'smooth' });
  };

  // =========================================================================
  // 3. COUNTDOWN TIMER QRIS (LANGKAH 4)
  // =========================================================================
  function mulaiTimerQRIS(durasiDetik) {
    const timerElement = document.getElementById('qrisTimerText');
    if (!timerElement) return;

    if (qrisTimerInterval) clearInterval(qrisTimerInterval);

    let sisaWaktu = durasiDetik;
    qrisTimerInterval = setInterval(function () {
      const menit = Math.floor(sisaWaktu / 60);
      const detik = sisaWaktu % 60;
      const formatMenit = menit < 10 ? '0' + menit : menit;
      const formatDetik = detik < 10 ? '0' + detik : detik;

      timerElement.textContent = `⏱ Sisa Waktu Bayar: ${formatMenit}:${formatDetik} Menit`;

      if (sisaWaktu <= 0) {
        clearInterval(qrisTimerInterval);
        timerElement.textContent = '⚠️ Waktu Pembayaran Habis (Silakan Refresh)';
      }
      sisaWaktu--;
    }, 1000);
  }

  // =========================================================================
  // 4. PERHITUNGAN TOTAL HARGA REAL-TIME
  // =========================================================================
  function hitungTotal() {
    if (!fileSudahDipilih || pesanan.file.halaman === 0) {
      pesanan.total = 0;
      if (ringkasFile) ringkasFile.textContent = 'Belum dipilih';
      if (ringkasHalaman) ringkasHalaman.textContent = '0 lembar';
      if (ringkasTotal) ringkasTotal.textContent = 'Rp 0';
      const qrisTotalDisplay = document.getElementById('qrisTotalDisplay');
      if (qrisTotalDisplay) qrisTotalDisplay.textContent = 'Rp 0';
      return;
    }

    const biayaLembar = pesanan.warna.harga + pesanan.kertas.tambah;
    let totalHalaman = pesanan.file.halaman * pesanan.rangkap;

    let subtotalCetak = biayaLembar * totalHalaman;
    if (pesanan.duplex && pesanan.file.halaman > 1) {
      subtotalCetak = Math.round(subtotalCetak * 0.95); // Diskon hemat kertas 5%
    }

    const totalAkhir = subtotalCetak + pesanan.jilid.harga;
    pesanan.total = totalAkhir;

    // Perbarui Sidebar Ringkasan Pesanan
    if (ringkasFile) ringkasFile.textContent = pesanan.file.nama;
    if (ringkasHalaman) ringkasHalaman.textContent = `${pesanan.file.halaman} lbr x ${pesanan.rangkap} rangkap`;
    if (ringkasKertas) ringkasKertas.textContent = pesanan.kertas.nama;
    if (ringkasWarna) ringkasWarna.textContent = pesanan.warna.nama;
    if (ringkasDuplex) ringkasDuplex.textContent = pesanan.duplex ? 'Bolak-balik (Duplex)' : '1 Sisi (Simplex)';
    if (ringkasJilid) ringkasJilid.textContent = pesanan.jilid.nama;
    if (ringkasKiosk) ringkasKiosk.textContent = pesanan.kiosk;
    if (ringkasTotal) ringkasTotal.textContent = 'Rp ' + totalAkhir.toLocaleString('id-ID');

    // Perbarui Total di Panel QRIS
    const qrisTotalDisplay = document.getElementById('qrisTotalDisplay');
    if (qrisTotalDisplay) {
      qrisTotalDisplay.textContent = 'Rp ' + totalAkhir.toLocaleString('id-ID');
    }
  }

  // =========================================================================
  // 5. EVENT LISTENERS OPSI FORMULIR (LANGKAH 2 & 3)
  // =========================================================================
  // Pilihan Kertas
  const opsiKertas = document.querySelectorAll('.opsi-app-kertas');
  opsiKertas.forEach(card => {
    card.addEventListener('click', function () {
      opsiKertas.forEach(c => c.classList.remove('selected'));
      this.classList.add('selected');
      const input = this.querySelector('input');
      if (input) input.checked = true;

      const jenis = input ? input.value : 'a4_75';
      if (jenis === 'a4_75') pesanan.kertas = { nama: 'A4 Standard (75 GSM)', tambah: 0 };
      if (jenis === 'a4_80') pesanan.kertas = { nama: 'A4 Tebal (80 GSM)', tambah: 50 };
      if (jenis === 'f4_80') pesanan.kertas = { nama: 'F4 Folio (80 GSM)', tambah: 100 };

      hitungTotal();
    });
  });

  // Pilihan Warna Tinta
  const opsiWarna = document.querySelectorAll('.opsi-app-warna');
  opsiWarna.forEach(card => {
    card.addEventListener('click', function () {
      opsiWarna.forEach(c => c.classList.remove('selected'));
      this.classList.add('selected');
      const input = this.querySelector('input');
      if (input) input.checked = true;

      const mode = input ? input.value : 'bw';
      if (mode === 'bw') pesanan.warna = { nama: 'Hitam Putih', harga: 350 };
      if (mode === 'color_std') pesanan.warna = { nama: 'Warna Standar', harga: 1000 };
      if (mode === 'color_full') pesanan.warna = { nama: 'Full Color Foto', harga: 2000 };

      hitungTotal();
    });
  });

  // Pilihan Finishing / Penjilidan
  const opsiJilid = document.querySelectorAll('.opsi-app-jilid');
  opsiJilid.forEach(card => {
    card.addEventListener('click', function () {
      opsiJilid.forEach(c => c.classList.remove('selected'));
      this.classList.add('selected');
      const input = this.querySelector('input');
      if (input) input.checked = true;

      const jenis = input ? input.value : 'none';
      if (jenis === 'none') pesanan.jilid = { nama: 'Tanpa Jilid', harga: 0 };
      if (jenis === 'steples') pesanan.jilid = { nama: 'Steples Sudut', harga: 500 };
      if (jenis === 'lakban') pesanan.jilid = { nama: 'Jilid Lakban Mika', harga: 3500 };

      hitungTotal();
    });
  });

  // Input Rangkap Salinan
  if (inputRangkap) {
    inputRangkap.addEventListener('input', function () {
      pesanan.rangkap = Math.max(1, parseInt(this.value) || 1);
      hitungTotal();
    });
  }

  // Toggle Duplex (Bolak-Balik)
  if (toggleDuplexApp) {
    toggleDuplexApp.addEventListener('change', function () {
      pesanan.duplex = this.checked;
      hitungTotal();
    });
  }

  // Pemilihan Kiosk & Loker
  const opsiKiosk = document.querySelectorAll('.opsi-app-kiosk');
  opsiKiosk.forEach(card => {
    card.addEventListener('click', function () {
      opsiKiosk.forEach(c => c.classList.remove('selected'));
      this.classList.add('selected');
      const input = this.querySelector('input');
      if (input) input.checked = true;

      pesanan.kiosk = input ? input.value : 'Kiosk Perpustakaan UI (Loker Blok A)';
      hitungTotal();
    });
  });

  // =========================================================================
  // 6. KONFIRMASI PEMBAYARAN QRIS (LANGKAH 4 -> 5)
  // =========================================================================
  const btnBayarQRIS = document.getElementById('btnBayarQRIS');
  if (btnBayarQRIS) {
    btnBayarQRIS.addEventListener('click', function () {
      btnBayarQRIS.innerHTML = '<span>⏳ Memverifikasi Pembayaran...</span>';
      btnBayarQRIS.disabled = true;

      setTimeout(function () {
        // Buat Kode Tiket Acak Unik & PIN 4 Digit
        const randomNum = Math.floor(1000 + Math.random() * 9000);
        pesanan.tiket = 'CPT-' + randomNum;
        pesanan.pin = randomNum.toString();

        // Tampilkan Data ke Panel Hasil Tiket
        const displayTiket = document.getElementById('displayTiket');
        const displayPIN = document.getElementById('displayPIN');
        const displayLokasiSukses = document.getElementById('displayLokasiSukses');

        if (displayTiket) displayTiket.textContent = pesanan.tiket;
        if (displayPIN) displayPIN.textContent = pesanan.pin;
        if (displayLokasiSukses) displayLokasiSukses.textContent = pesanan.kiosk;

        // Simpan ke localStorage agar bisa dilacak di tracker index.html
        try {
          localStorage.setItem('cepatik_tiket_aktif', JSON.stringify(pesanan));
          let riwayat = JSON.parse(localStorage.getItem('cepatik_riwayat_tiket') || '[]');
          riwayat.unshift(pesanan);
          localStorage.setItem('cepatik_riwayat_tiket', JSON.stringify(riwayat.slice(0, 5)));
        } catch (e) {
          console.warn('LocalStorage tidak dapat diakses:', e);
        }

        btnBayarQRIS.innerHTML = '<svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>✓ Pembayaran Berhasil</span>';
        btnBayarQRIS.disabled = false;

        // Beralih ke Langkah 5 (Tiket Siap)
        pindahLangkah(5);
      }, 700);
    });
  }

  // Inisialisasi awal kalkulasi harga (Rp 0 saat awal kosong)
  hitungTotal();
});
