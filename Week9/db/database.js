(function (window) {
  'use strict';

  const SEED_ANTREAN = [
    {
      id: 'CPT-8492',
      waktu: '11:45 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Skripsi_BAB_1-5_Final.pdf',
        ukuran: '3.8 MB',
        halaman: 48,
        ekstensi: 'PDF',
        terhapus: false
      },
      kertas: 'A4 Tebal (80 GSM)',
      tinta: 'Hitam Putih',
      duplex: true,
      rangkap: 1,
      jilid: 'Jilid Lakban Mika',
      total: 21740,
      pin: '8492',
      kiosk: 'Kiosk Perpustakaan Pusat UMM (Lt. 1)',
      status: 'Siap Diambil'
    },
    {
      id: 'CPT-8493',
      waktu: '11:55 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Jurnal_Farmasi_Klinis.pdf',
        ukuran: '2.4 MB',
        halaman: 18,
        ekstensi: 'PDF',
        terhapus: false
      },
      kertas: 'A4 Standard (75 GSM)',
      tinta: 'Full Color Foto',
      duplex: false,
      rangkap: 1,
      jilid: 'Jilid Steples Sudut',
      total: 37000,
      pin: '8493',
      kiosk: 'Kiosk GKB 1 UMM',
      status: 'Sedang Diproses'
    },
    {
      id: 'CPT-8494',
      waktu: '12:00 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Laporan_Praktikum_Fisika.docx',
        ukuran: '1.4 MB',
        halaman: 14,
        ekstensi: 'DOCX',
        terhapus: false
      },
      kertas: 'A4 Standard (75 GSM)',
      tinta: 'Warna Standar',
      duplex: true,
      rangkap: 1,
      jilid: 'Jilid Steples Sudut',
      total: 14300,
      pin: '8494',
      kiosk: 'Kiosk GKB 2 (Lobby Lt. 1)',
      status: 'Menunggu Cetak'
    },
    {
      id: 'CPT-8495',
      waktu: '11:10 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Makalah_Ekonomi_Makro.pdf',
        ukuran: '1.9 MB',
        halaman: 26,
        ekstensi: 'PDF',
        terhapus: false
      },
      kertas: 'A4 Standard (75 GSM)',
      tinta: 'Hitam Putih',
      duplex: true,
      rangkap: 1,
      jilid: 'Tanpa Jilid',
      total: 8645,
      pin: '8495',
      kiosk: 'Kiosk Perpustakaan Pusat UMM (Lt. 1)',
      status: 'Siap Diambil'
    },
    {
      id: 'CPT-8496',
      waktu: '10:30 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Poster_Seminar_Nasional.png',
        ukuran: '4.8 MB',
        halaman: 4,
        ekstensi: 'PNG',
        terhapus: false
      },
      kertas: 'F4 Folio (80 GSM)',
      tinta: 'Full Color Foto',
      duplex: false,
      rangkap: 1,
      jilid: 'Tanpa Jilid',
      total: 8400,
      pin: '8496',
      kiosk: 'Kiosk Kantin Teknik',
      status: 'Menunggu Cetak'
    },
    {
      id: 'CPT-8497',
      waktu: '09:40 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Buku_Panduan_Himpunan.pdf',
        ukuran: '6.1 MB',
        halaman: 85,
        ekstensi: 'PDF',
        terhapus: true,
        statusRetensi: 'Dihapus Otomatis (Retensi Selesai)',
        waktuDihapus: '10:05 WIB'
      },
      kertas: 'A4 Tebal (80 GSM)',
      tinta: 'Hitam Putih',
      duplex: true,
      rangkap: 1,
      jilid: 'Jilid Lakban Mika',
      total: 35800,
      pin: '8497',
      kiosk: 'Kiosk GKB 1 UMM',
      status: 'Selesai'
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
      kiosk: 'Kiosk Lab Informatika & Komputer (Lt. 6)',
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
      kiosk: 'Kiosk Kantin Teknik',
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
      kiosk: 'Kiosk GKB 2 (Lobby Lt. 1)',
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
      kiosk: 'Kiosk Perpustakaan Pusat UMM (Lt. 1)',
      status: 'Menunggu Cetak'
    },
    {
      id: 'CPT-7721',
      waktu: '12:10 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Laporan_Praktikum_Basis_Data.docx',
        ukuran: '1.6 MB',
        halaman: 12,
        ekstensi: 'DOCX',
        terhapus: false
      },
      kertas: 'A4 Standard (75 GSM)',
      tinta: 'Hitam Putih',
      duplex: false,
      rangkap: 1,
      jilid: 'Tanpa Jilid',
      total: 4200,
      pin: '7721',
      kiosk: 'Kiosk GKB 1 UMM',
      status: 'Sedang Diproses'
    },
    {
      id: 'CPT-6340',
      waktu: '12:22 WIB',
      tanggal: '27 Okt 2026',
      file: {
        nama: 'Jurnal_Kriptografi_Modern.pdf',
        ukuran: '2.1 MB',
        halaman: 15,
        ekstensi: 'PDF',
        terhapus: false
      },
      kertas: 'A4 Standard (75 GSM)',
      tinta: 'Warna Standar',
      duplex: true,
      rangkap: 1,
      jilid: 'Tanpa Jilid',
      total: 14250,
      pin: '6340',
      kiosk: 'Kiosk Lab Informatika & Komputer (Lt. 6)',
      status: 'Menunggu Cetak'
    }
  ];

  const SEED_KIOSK = [
    {
      id: 'umm_perpus',
      nama: 'Kiosk Perpustakaan Pusat UMM (Lt. 1)',
      nama_lengkap: 'Kiosk Perpustakaan Pusat UMM (Lt. 1)',
      lokasi: 'Gedung Perpustakaan Pusat UMM Kampus III, Lantai 1',
      status: 'online'
    },
    {
      id: 'umm_gkb1',
      nama: 'Kiosk GKB 1 UMM',
      nama_lengkap: 'Kiosk GKB 1 UMM',
      lokasi: 'Lobby Utama GKB 1 UMM Kampus III, Lantai Dasar',
      status: 'online'
    },
    {
      id: 'umm_gkb2',
      nama: 'Kiosk GKB 2 (Lobby Lt. 1)',
      nama_lengkap: 'Kiosk GKB 2 (Lobby Lt. 1)',
      lokasi: 'Gedung Kuliah Bersama II (GKB 2) UMM, Lobby Lantai 1',
      status: 'online'
    },
    {
      id: 'umm_lab_it',
      nama: 'Kiosk Lab Informatika & Komputer (Lt. 6)',
      nama_lengkap: 'Kiosk Lab Informatika & Komputer (Lt. 6)',
      lokasi: 'Gedung Teknik Lt. 6, Depan Lab Komputer Informatika UMM',
      status: 'online'
    },
    {
      id: 'umm_kantin_teknik',
      nama: 'Kiosk Kantin Teknik',
      nama_lengkap: 'Kiosk Kantin Teknik',
      lokasi: 'Area Kantin Fakultas Teknik UMM Kampus III',
      status: 'online'
    }
  ];

  const STORAGE_KEY = 'cepatik_orders';
  const DELETED_KEY = 'cepatik_deleted_orders';
  const EVENT_NAME = 'cepatik:db_updated';

  function syncToLaravelBackend(payload) {
    try {
      if (typeof fetch !== 'function') return;
      fetch('admin.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(Object.assign({ _ajax: true }, payload))
      }).catch(function () {});
    } catch (e) {}
  }

  function getDeletedIds() {
    try {
      const raw = sessionStorage.getItem(DELETED_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch (e) {
      return [];
    }
  }

  function markDeletedId(orderId) {
    try {
      const list = getDeletedIds();
      if (!list.includes(orderId)) {
        list.push(orderId);
        sessionStorage.setItem(DELETED_KEY, JSON.stringify(list));
      }
    } catch (e) {}
  }

  if (Array.isArray(window.LARAVEL_DB_ORDERS) && window.LARAVEL_DB_ORDERS.length > 0) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(window.LARAVEL_DB_ORDERS));
    } catch (e) {}
  }

  const CepatikDB = {
    getOrders: function () {
      try {
        if (Array.isArray(window.LARAVEL_DB_ORDERS) && window.LARAVEL_DB_ORDERS.length > 0 && !localStorage.getItem(STORAGE_KEY)) {
          this.saveOrders(window.LARAVEL_DB_ORDERS);
          return [...window.LARAVEL_DB_ORDERS];
        }
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) {
          const initial = (Array.isArray(window.LARAVEL_DB_ORDERS) && window.LARAVEL_DB_ORDERS.length > 0)
            ? window.LARAVEL_DB_ORDERS
            : SEED_ANTREAN;
          this.saveOrders(initial);
          return [...initial];
        }
        const parsed = JSON.parse(raw);
        if (Array.isArray(parsed) && parsed.length > 0) {
          return parsed;
        }
        this.saveOrders(SEED_ANTREAN);
        return [...SEED_ANTREAN];
      } catch (e) {
        return [...SEED_ANTREAN];
      }
    },

    saveOrders: function (orders) {
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(orders));
        window.dispatchEvent(new CustomEvent(EVENT_NAME, { detail: { orders } }));
      } catch (e) {}
    },

    addOrder: function (order) {
      const orders = this.getOrders();
      orders.unshift(order);
      this.saveOrders(orders);

      syncToLaravelBackend(Object.assign({ _action: 'create_order' }, order));
      return order;
    },

    updateOrderStatus: function (orderId, newStatus) {
      const orders = this.getOrders();
      const idx = orders.findIndex(o => (o.id || o.tiket) === orderId);
      if (idx !== -1) {
        orders[idx].status = newStatus;

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
        }

        this.saveOrders(orders);

        syncToLaravelBackend({
          _action: 'update_order',
          ticket_code: orderId,
          status: newStatus
        });
        return orders[idx];
      }
      return null;
    },

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
        syncToLaravelBackend({ _action: 'purge_orders' });
      }
      return purgedCount;
    },

    deleteOrder: function (orderId) {
      let orders = this.getOrders();
      const initialLength = orders.length;
      const targetId = String(orderId || '').toUpperCase();
      orders = orders.filter(o => String(o.id || o.tiket || '').toUpperCase() !== targetId);
      if (orders.length !== initialLength) {
        markDeletedId(targetId);
        this.saveOrders(orders);

        syncToLaravelBackend({
          _action: 'delete_order',
          ticket_code: targetId
        });
        return true;
      }
      return false;
    },

    findOrderByTicket: function (query) {
      if (!query) return null;
      const q = String(query).trim().toUpperCase().replace(/^#/, '');
      const orders = this.getOrders();

      const matchOrder = (o) => {
        const tiketId = String(o.id || o.tiket || '').trim().toUpperCase();
        const pin = String(o.pin || '').trim().toUpperCase();
        const numericId = tiketId.replace(/^CPT-/, '');
        return tiketId === q || pin === q || ('CPT-' + q) === tiketId || numericId === q;
      };

      return orders.find(matchOrder) || SEED_ANTREAN.find(matchOrder) || null;
    },

    getKiosks: function () {
      return [...SEED_KIOSK];
    },

    formatRupiah: function (num) {
      return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    },

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

  CepatikDB.getOrders();
  window.CepatikDB = CepatikDB;

})(window);
