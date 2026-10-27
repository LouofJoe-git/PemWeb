/**
 * DATABASE.JS - CEPATIK SINGLE SOURCE OF TRUTH (DB LAYER)
 * Terletak di folder /db/ untuk memusatkan seluruh data antrean,
 * lokasi kiosk, sinkronisasi real-time via localStorage ('cepatik_orders'),
 * kebijakan Zero-Retention (otomatis menghapus berkas saat pesanan selesai),
 * dan pub/sub event antar-halaman (index.html, cetak.html, admin.html).
 */

(function (window) {
  'use strict';

  // Seed Data Awal Antrean (Bersumber dari db/antrean.json)
  const SEED_ANTREAN = [
    {
      id: 'CPT-8492',
      waktu: '11:45 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Skripsi_BAB_1-5_Final.pdf',
        ukuran: '3.8 MB',
        halaman: 24,
        ekstensi: 'PDF',
        terhapus: false
      },
      kertas: 'A4 Standard (75 GSM)',
      tinta: 'Hitam Putih',
      duplex: true,
      rangkap: 1,
      jilid: 'Jilid Spiral Kawat',
      total: 13400,
      pin: '8492',
      kiosk: 'Kiosk Perpustakaan UI',
      status: 'Siap Diambil'
    },
    {
      id: 'CPT-3021',
      waktu: '12:05 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Laporan_Praktikum_Fisika.docx',
        ukuran: '1.4 MB',
        halaman: 14,
        ekstensi: 'DOCX',
        terhapus: false
      },
      kertas: 'A4 Standard (75 GSM)',
      tinta: 'Hitam Putih',
      duplex: false,
      rangkap: 2,
      jilid: 'Tanpa Jilid',
      total: 9800,
      pin: '3021',
      kiosk: 'Kiosk Lab Komputer TI',
      status: 'Sedang Diproses'
    },
    {
      id: 'CPT-5519',
      waktu: '12:18 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Poster_Dies_Natalis_2026.png',
        ukuran: '5.2 MB',
        halaman: 1,
        ekstensi: 'PNG',
        terhapus: false
      },
      kertas: 'A4 Tebal (80 GSM)',
      tinta: 'Full Color Foto',
      duplex: false,
      rangkap: 3,
      jilid: 'Tanpa Jilid',
      total: 6150,
      pin: '5519',
      kiosk: 'Kiosk Gedung Rektorat',
      status: 'Menunggu Cetak'
    },
    {
      id: 'CPT-9182',
      waktu: '10:12 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Jurnal_Ilmiah_TI_Revisi.pdf',
        ukuran: '850 KB',
        halaman: 8,
        ekstensi: 'PDF',
        terhapus: true,
        statusRetensi: 'Dihapus Otomatis (Retensi Selesai)',
        waktuDihapus: '10:15 WIB'
      },
      kertas: 'F4 Folio (80 GSM)',
      tinta: 'Warna Standar',
      duplex: true,
      rangkap: 1,
      jilid: 'Tanpa Jilid',
      total: 8350,
      pin: '9182',
      kiosk: 'Kiosk Gelanggang UGM',
      status: 'Selesai'
    },
    {
      id: 'CPT-4473',
      waktu: '12:20 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Modul_Algoritma_Pemrograman.pdf',
        ukuran: '4.6 MB',
        halaman: 32,
        ekstensi: 'PDF',
        terhapus: false
      },
      kertas: 'A4 Standard (75 GSM)',
      tinta: 'Hitam Putih',
      duplex: true,
      rangkap: 1,
      jilid: 'Jilid Softcover Mika',
      total: 16200,
      pin: '4473',
      kiosk: 'Kiosk Perpustakaan UI',
      status: 'Menunggu Cetak'
    }
  ];

  // Daftar Lokasi Kiosk
  const SEED_KIOSK = [
    {
      id: 'ui_perpus',
      nama: 'Kiosk Perpustakaan UI',
      nama_lengkap: 'Kiosk Perpustakaan UI',
      lokasi: 'Gedung Perpustakaan Pusat UI Depok, Lt. 1',
      status: 'online'
    },
    {
      id: 'itb_ganesha',
      nama: 'Mitra Barokah ITB',
      nama_lengkap: 'Mitra Barokah ITB',
      lokasi: 'Jl. Ganesha No. 10 (Samping Gerbang Utama ITB)',
      status: 'online'
    },
    {
      id: 'ugm_gelanggang',
      nama: 'Kiosk Gelanggang UGM',
      nama_lengkap: 'Kiosk Gelanggang UGM',
      lokasi: 'Student Center UGM, Sayap Barat Lt. Dasar',
      status: 'online'
    },
    {
      id: 'lab_komputer',
      nama: 'Kiosk Lab Komputer TI',
      nama_lengkap: 'Kiosk Lab Komputer TI',
      lokasi: 'Gedung Lab Komputer & Informatika Lt. 2',
      status: 'online'
    },
    {
      id: 'gedung_rektorat',
      nama: 'Kiosk Gedung Rektorat',
      nama_lengkap: 'Kiosk Gedung Rektorat',
      lokasi: 'Lobby Utama Gedung Rektorat Kampus',
      status: 'online'
    }
  ];

  const STORAGE_KEY = 'cepatik_orders';
  const EVENT_NAME = 'cepatik:db_updated';

  // Objek Utama CepatikDB
  const CepatikDB = {
    /**
     * Mengambil seluruh antrean pesanan dari database localStorage.
     * Jika kosong, otomatis inisialisasi dari SEED_ANTREAN.
     */
    getOrders: function () {
      try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) {
          this.saveOrders(SEED_ANTREAN);
          return [...SEED_ANTREAN];
        }
        const parsed = JSON.parse(raw);
        if (Array.isArray(parsed) && parsed.length > 0) {
          return parsed;
        }
        return parsed || [];
      } catch (e) {
        console.error('CepatikDB: Gagal membaca data antrean:', e);
        return [...SEED_ANTREAN];
      }
    },

    /**
     * Menyimpan seluruh array pesanan ke localStorage dan memancarkan event pembaruan
     */
    saveOrders: function (orders) {
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(orders));
        window.dispatchEvent(new CustomEvent(EVENT_NAME, { detail: { orders } }));
      } catch (e) {
        console.error('CepatikDB: Gagal menyimpan data antrean:', e);
      }
    },

    /**
     * Menambahkan pesanan baru ke posisi terdepan antrean
     */
    addOrder: function (order) {
      const orders = this.getOrders();
      orders.unshift(order);
      this.saveOrders(orders);
      return order;
    },

    /**
     * Memperbarui status pesanan berdasarkan ID Tiket secara real-time.
     * KEBIJAKAN ZERO-RETENTION:
     * Jika status diubah menjadi 'Selesai', berkas dokumen otomatis dimusnahkan/dihapus
     * demi keamanan privasi dokumen mahasiswa.
     */
    updateOrderStatus: function (orderId, newStatus) {
      const orders = this.getOrders();
      const idx = orders.findIndex(o => (o.id || o.tiket) === orderId);
      if (idx !== -1) {
        orders[idx].status = newStatus;

        // Eksekusi pemusnahan berkas otomatis jika pesanan Selesai
        if (newStatus === 'Selesai') {
          const nowWib = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
          if (typeof orders[idx].file === 'object') {
            orders[idx].file.terhapus = true;
            orders[idx].file.statusRetensi = 'Dihapus Otomatis (Retensi Selesai)';
            orders[idx].file.waktuDihapus = nowWib;
          } else {
            orders[idx].file = {
              nama: orders[idx].file,
              terhapus: true,
              statusRetensi: 'Dihapus Otomatis (Retensi Selesai)',
              waktuDihapus: nowWib
            };
          }

          // Bersihkan cache file draft di sessionStorage
          try {
            sessionStorage.removeItem('cepatik_draft_file_name');
            sessionStorage.removeItem('cepatik_draft_file_size');
          } catch (err) {}
        }

        this.saveOrders(orders);
        return orders[idx];
      }
      return null;
    },

    /**
     * Memusnahkan file dokumen pada semua pesanan yang telah selesai
     * (Zero Data Retention Enforcement)
     */
    purgeCompletedFiles: function () {
      const orders = this.getOrders();
      let purgedCount = 0;
      const nowWib = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

      orders.forEach(o => {
        if (o.status === 'Selesai') {
          if (typeof o.file === 'object') {
            if (!o.file.terhapus) {
              o.file.terhapus = true;
              o.file.statusRetensi = 'Dihapus Otomatis (Retensi Selesai)';
              o.file.waktuDihapus = nowWib;
              purgedCount++;
            }
          } else {
            o.file = {
              nama: o.file,
              terhapus: true,
              statusRetensi: 'Dihapus Otomatis (Retensi Selesai)',
              waktuDihapus: nowWib
            };
            purgedCount++;
          }
        }
      });

      if (purgedCount > 0) {
        this.saveOrders(orders);
      }
      return purgedCount;
    },

    /**
     * Menghapus pesanan dari antrean
     */
    deleteOrder: function (orderId) {
      let orders = this.getOrders();
      const initialLength = orders.length;
      orders = orders.filter(o => (o.id || o.tiket) !== orderId);
      if (orders.length !== initialLength) {
        this.saveOrders(orders);
        return true;
      }
      return false;
    },

    /**
     * Mencari pesanan berdasarkan ID Tiket atau PIN Loker
     */
    findOrderByTicket: function (query) {
      if (!query) return null;
      const q = String(query).trim().toUpperCase();
      const orders = this.getOrders();

      return orders.find(o => {
        const tiketId = String(o.id || o.tiket || '').trim().toUpperCase();
        const pin = String(o.pin || '').trim();
        return tiketId === q || pin === q;
      }) || null;
    },

    /**
     * Mengambil daftar lokasi mesin kiosk Cepatik
     */
    getKiosks: function () {
      return [...SEED_KIOSK];
    },

    /**
     * Format Rupiah standar Indonesia
     */
    formatRupiah: function (num) {
      return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    },

    /**
     * Berlangganan (Subscribe) ke perubahan data real-time
     * Menangani pembaruan dalam tab yang sama maupun antar tab browser (storage event).
     */
    subscribe: function (callback) {
      if (typeof callback !== 'function') return;

      window.addEventListener('storage', function (e) {
        if (e.key === STORAGE_KEY) {
          callback(CepatikDB.getOrders());
        }
      });

      window.addEventListener(EVENT_NAME, function (e) {
        callback(e.detail.orders || CepatikDB.getOrders());
      });
    }
  };

  // Otomatis pastikan database terinisialisasi dan jalankan auto-purge awal
  CepatikDB.getOrders();
  CepatikDB.purgeCompletedFiles();

  // Daftarkan ke window objek global
  window.CepatikDB = CepatikDB;

})(window);
