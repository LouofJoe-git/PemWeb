/**
 * ADMIN.JS - CEPATIK ADMIN PANEL & KIOSK MANAGEMENT
 * Mengelola antrean pesanan cloud printing, otentikasi operator & lokasi kiosk,
 * metrik real-time bersumber dari CepatikDB (/db/), dan pencetakan struk thermal.
 */

// Kata Kunci Pencocokan Lokasi Kiosk
const KIOSK_KEYWORDS = {
  'ui_perpus': 'UI',
  'itb_ganesha': 'ITB',
  'ugm_gelanggang': 'UGM',
  'lab_komputer': 'Lab Komputer',
  'gedung_rektorat': 'Rektorat'
};

const STATUS_OPTIONS = [
  'Menunggu Cetak',
  'Sedang Diproses',
  'Siap Diambil',
  'Selesai'
];

// State Global Admin
let currentFilter = 'Semua';
let searchQuery = '';
let currentKioskFilter = 'all';
let currentAdminSession = null;

// =============================================================================
// 1. OTENTIKASI & MANAJEMEN SESI OPERATOR
// =============================================================================
function getAdminSession() {
  try {
    const raw = sessionStorage.getItem('cepatik_admin_session') || localStorage.getItem('cepatik_admin_session');
    if (!raw) return null;
    return JSON.parse(raw);
  } catch (e) {
    return null;
  }
}

function checkAdminAuth() {
  const session = getAdminSession();
  if (!session || !session.username) {
    window.location.replace('login.html');
    return null;
  }
  return session;
}

function logoutAdmin() {
  if (confirm('Apakah Anda yakin ingin keluar dari sesi admin kiosk ini?')) {
    sessionStorage.removeItem('cepatik_admin_session');
    localStorage.removeItem('cepatik_admin_session');
    showToast('Berhasil keluar. Mengalihkan ke login...', 'toast-info');
    setTimeout(() => {
      window.location.href = 'login.html';
    }, 500);
  }
}

function setupOperatorProfileUI(session) {
  if (!session) return;

  const operatorNameEl = document.getElementById('operatorNameDisplay');
  const operatorRoleEl = document.getElementById('operatorRoleDisplay');
  const operatorAvatarEl = document.getElementById('operatorAvatar');
  const topbarKioskEl = document.getElementById('activeKioskTopbar');
  const kioskSwitcherWrap = document.getElementById('kioskSwitcherWrap');
  const selectKioskFilter = document.getElementById('selectKioskAdminFilter');

  if (operatorNameEl) operatorNameEl.textContent = session.nama || session.username;
  if (operatorRoleEl) operatorRoleEl.textContent = `${session.role} · ${session.kioskName}`;
  if (operatorAvatarEl) {
    const initial = (session.nama || session.username || 'A').charAt(0).toUpperCase();
    operatorAvatarEl.textContent = initial;
  }

  if (topbarKioskEl) {
    topbarKioskEl.innerHTML = `<strong>${session.kioskName}</strong> (Online 24/7)`;
  }

  if (session.role === 'Super Admin' || session.kioskId === 'all') {
    currentKioskFilter = 'all';
    if (kioskSwitcherWrap) kioskSwitcherWrap.style.display = 'inline-flex';
    if (selectKioskFilter) selectKioskFilter.value = 'all';
  } else {
    currentKioskFilter = session.kioskId;
    if (kioskSwitcherWrap) kioskSwitcherWrap.style.display = 'none';
  }
}

// =============================================================================
// 2. UTILITAS FORMATTING & PENCOCOKAN
// =============================================================================
function getFileExtension(filename) {
  if (!filename) return 'DOC';
  const parts = filename.split('.');
  return parts.length > 1 ? parts.pop().toUpperCase() : 'DOC';
}

function getFileBadgeStyle(ext) {
  switch (ext) {
    case 'PDF':
      return { bg: '#FEE2E2', color: '#DC2626', border: '#FECACA' };
    case 'DOC':
    case 'DOCX':
      return { bg: '#EFF6FF', color: '#2563EB', border: '#BFDBFE' };
    case 'PPT':
    case 'PPTX':
      return { bg: '#FFF7ED', color: '#EA580C', border: '#FFEDD5' };
    case 'PNG':
    case 'JPG':
    case 'JPEG':
      return { bg: '#F3E8FF', color: '#9333EA', border: '#E9D5FF' };
    default:
      return { bg: '#F3F4F6', color: '#4B5563', border: '#E5E7EB' };
  }
}

function getStatusBadgeClass(status) {
  switch (status) {
    case 'Menunggu Cetak':
      return 'menunggu';
    case 'Sedang Diproses':
      return 'proses';
    case 'Siap Diambil':
    case 'Siap di Loker':
      return 'siap';
    case 'Selesai':
      return 'selesai';
    default:
      return 'menunggu';
  }
}

function orderMatchesKiosk(order, kioskFilter) {
  if (kioskFilter === 'all') return true;
  const keyword = KIOSK_KEYWORDS[kioskFilter];
  if (!keyword) return true;
  const kioskStr = (order.kiosk || '').toLowerCase();
  return kioskStr.includes(keyword.toLowerCase());
}

// =============================================================================
// 3. FUNGSI METRIK HEADER & KIOSK
// =============================================================================
function updateMetrics(allOrders) {
  const orders = allOrders.filter(o => orderMatchesKiosk(o, currentKioskFilter));

  const totalOrdersEl = document.getElementById('metricTotalOrders');
  const totalRevenueEl = document.getElementById('metricTotalRevenue');
  const pendingOrdersEl = document.getElementById('metricPendingOrders');
  const completedOrdersEl = document.getElementById('metricCompletedOrders');

  const countTotal = orders.length;
  const countPending = orders.filter(o => o.status === 'Menunggu Cetak' || o.status === 'Sedang Diproses').length;
  const countCompleted = orders.filter(o => o.status === 'Selesai' || o.status === 'Siap Diambil' || o.status === 'Siap di Loker').length;
  const sumRevenue = orders.reduce((acc, curr) => acc + (Number(curr.total) || 0), 0);

  if (totalOrdersEl) totalOrdersEl.textContent = `${countTotal} Pesanan`;
  if (totalRevenueEl) totalRevenueEl.textContent = window.CepatikDB ? window.CepatikDB.formatRupiah(sumRevenue) : `Rp ${sumRevenue}`;
  if (pendingOrdersEl) pendingOrdersEl.textContent = `${countPending} Antrean`;
  if (completedOrdersEl) completedOrdersEl.textContent = `${countCompleted} Selesai`;

  updateFilterCounts(orders);
}

function updateFilterCounts(orders) {
  const countSemua = orders.length;
  const countMenunggu = orders.filter(o => o.status === 'Menunggu Cetak').length;
  const countProses = orders.filter(o => o.status === 'Sedang Diproses').length;
  const countSiap = orders.filter(o => o.status === 'Siap di Loker').length;
  const countSelesai = orders.filter(o => o.status === 'Selesai').length;

  const countElMap = {
    'Semua': countSemua,
    'Menunggu Cetak': countMenunggu,
    'Sedang Diproses': countProses,
    'Siap di Loker': countSiap,
    'Selesai': countSelesai
  };

  document.querySelectorAll('.admin-filter-btn').forEach(btn => {
    const filterName = btn.getAttribute('data-filter');
    const badge = btn.querySelector('.admin-filter-count');
    if (badge && countElMap[filterName] !== undefined) {
      badge.textContent = countElMap[filterName];
    }
  });
}

// =============================================================================
// 4. RENDERING TABEL PESANAN REAL-TIME
// =============================================================================
function renderTable() {
  const orders = window.CepatikDB ? window.CepatikDB.getOrders() : [];
  updateMetrics(orders);

  const tbody = document.getElementById('adminOrderTableBody');
  const emptyState = document.getElementById('emptyOrderState');
  if (!tbody) return;

  // Filter Lokasi Kiosk, Status, & Pencarian
  let filtered = orders.filter(order => {
    if (!orderMatchesKiosk(order, currentKioskFilter)) {
      return false;
    }

    if (currentFilter !== 'Semua' && order.status !== currentFilter) {
      return false;
    }

    if (searchQuery.trim() !== '') {
      const q = searchQuery.toLowerCase().trim();
      const fileName = (typeof order.file === 'object' ? order.file.nama : order.file) || '';
      const ticketId = order.id || order.tiket || '';
      const pin = order.pin || '';
      const kiosk = order.kiosk || '';

      const match =
        ticketId.toLowerCase().includes(q) ||
        fileName.toLowerCase().includes(q) ||
        pin.toLowerCase().includes(q) ||
        kiosk.toLowerCase().includes(q);

      if (!match) return false;
    }

    return true;
  });

  if (filtered.length === 0) {
    tbody.innerHTML = '';
    if (emptyState) emptyState.style.display = 'block';
    return;
  }

  if (emptyState) emptyState.style.display = 'none';

  let htmlRows = '';
  filtered.forEach(order => {
    const orderId = order.id || order.tiket || 'CPT-0000';
    const rawFileName = typeof order.file === 'object' ? (order.file.nama || 'Dokumen.pdf') : (order.file || 'Dokumen.pdf');
    const fileSize = (typeof order.file === 'object' && order.file.ukuran) ? order.file.ukuran : '1.2 MB';
    const filePages = (typeof order.file === 'object' && order.file.halaman) ? order.file.halaman : (order.halaman || 1);
    const fileExt = getFileExtension(rawFileName);
    const badgeStyle = getFileBadgeStyle(fileExt);

    const kertas = order.kertas || 'A4 Standard';
    const tinta = order.tinta || 'Hitam Putih';
    const duplex = order.duplex ? 'Duplex (Bolak-balik)' : '1 Sisi (Simplex)';
    const rangkap = order.rangkap ? `${order.rangkap}x Rangkap` : '1x Rangkap';
    const jilid = order.jilid || 'Tanpa Jilid';

    const statusBadgeClass = getStatusBadgeClass(order.status);
    const waktuTampil = order.waktu ? `${order.waktu}` : 'Hari Ini';
    const pin = order.pin || '0000';
    const totalHarga = window.CepatikDB ? window.CepatikDB.formatRupiah(order.total || 0) : `Rp ${order.total}`;

    let selectOptions = '';
    STATUS_OPTIONS.forEach(opt => {
      const isSelected = order.status === opt ? 'selected' : '';
      selectOptions += `<option value="${opt}" ${isSelected}>${opt}</option>`;
    });

    htmlRows += `
      <tr data-order-id="${orderId}">
        <!-- 1. ID Tiket -->
        <td>
          <span class="badge-ticket" title="Klik untuk salin ID" onclick="copyToClipboard('${orderId}')" style="cursor: pointer;">
            ${orderId}
          </span>
        </td>

        <!-- 2. Waktu & Lokasi -->
        <td>
          <div style="font-weight: 700; color: var(--text-dark);">${waktuTampil}</div>
          <div style="font-size: 11px; color: var(--text-muted);">${order.tanggal || 'Hari ini'}</div>
          <div style="font-size: 10.5px; color: #059669; font-weight: 600; margin-top: 2px;">
            📍 ${order.kiosk ? order.kiosk.split('(')[0] : 'Kiosk Kampus'}
          </div>
        </td>

        <!-- 3. Nama File & Info -->
        <td>
          <div class="admin-file-cell">
            <div class="admin-file-icon" style="background:${badgeStyle.bg}; color:${badgeStyle.color}; border-color:${badgeStyle.border};">
              ${fileExt}
            </div>
            <div class="admin-file-meta">
              <h4 title="${rawFileName}">${rawFileName}</h4>
              <p>${filePages} Hlm · ${fileSize} · ${rangkap}</p>
              ${(order.status === 'Selesai' || (typeof order.file === 'object' && order.file && order.file.terhapus)) ? `
                <div style="font-size: 10.5px; color: #059669; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; margin-top: 2px;">
                  🔒 Berkas Dihapus (Retensi Selesai)
                </div>
              ` : ''}
            </div>
          </div>
        </td>

        <!-- 4. Opsi Cetak -->
        <td>
          <div class="admin-specs-cell">
            <span class="badge-spec">${kertas}</span>
            <span class="badge-spec ${tinta.includes('Color') || tinta.includes('Warna') ? 'spec-color' : ''}">${tinta}</span>
            <span class="badge-spec ${order.duplex ? 'spec-duplex' : ''}">${duplex}</span>
            ${jilid !== 'Tanpa Jilid' ? `<span class="badge-spec spec-jilid">📚 ${jilid}</span>` : ''}
          </div>
        </td>

        <!-- 5. Total Biaya -->
        <td>
          <div style="font-weight: 900; color: var(--text-dark); font-size: 14px;">${totalHarga}</div>
          <span style="font-size: 10px; color: #059669; font-weight: 800; background: #ECFDF5; padding: 2px 6px; border-radius: 4px; border: 1px solid #A7F3D0; display: inline-block; margin-top: 2px;">
            ✓ QRIS Lunas
          </span>
        </td>

        <!-- 6. Status & Pengubah -->
        <td>
          <div class="admin-status-wrap">
            <span class="badge-status ${statusBadgeClass}">
              <span class="status-dot"></span>
              <span>${order.status}</span>
            </span>
            <select class="admin-select-status" onchange="handleStatusChange('${orderId}', this.value)" title="Ubah Status Pesanan">
              ${selectOptions}
            </select>
          </div>
        </td>

        <!-- 8. Aksi (Cetak Struk & Hapus) -->
        <td>
          <div class="admin-actions-cell">
            <button type="button" class="btn-action btn-action-print" onclick="cetakStruk('${orderId}')" title="Cetak Struk Kasir / Thermal Kiosk">
              <svg class="icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
              </svg>
              <span>Struk</span>
            </button>
            <button type="button" class="btn-action btn-action-delete" onclick="hapusPesanan('${orderId}')" title="Hapus Pesanan Ini">
              <svg class="icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              </svg>
            </button>
          </div>
        </td>
      </tr>
    `;
  });

  tbody.innerHTML = htmlRows;
}

// =============================================================================
// 5. UPDATE STATUS & MANIPULASI PESANAN (VIA CEPATIK DB)
// =============================================================================
function handlePurgeFiles() {
  if (window.CepatikDB) {
    const purged = window.CepatikDB.purgeCompletedFiles();
    renderTable();
    showToast(purged > 0 ?
      `Kebijakan Retensi: ${purged} berkas dokumen pesanan selesai telah dihapus dari cloud.` :
      'Seluruh berkas dokumen pada pesanan selesai sudah dalam status aman terhapus.',
      'toast-success'
    );
  }
}
function handleStatusChange(orderId, newStatus) {
  if (window.CepatikDB) {
    const updated = window.CepatikDB.updateOrderStatus(orderId, newStatus);
    if (updated) {
      renderTable();
      showToast(`Status [${orderId}] berhasil diubah ke "${newStatus}"`, 'toast-success');
    }
  }
}

function hapusPesanan(orderId) {
  if (!confirm(`Yakin ingin menghapus pesanan ${orderId}? Tindakan ini akan menghapus data dari database.`)) {
    return;
  }

  if (window.CepatikDB) {
    const deleted = window.CepatikDB.deleteOrder(orderId);
    if (deleted) {
      renderTable();
      showToast(`Pesanan ${orderId} berhasil dihapus dari database.`, 'toast-info');
    }
  }
}

// =============================================================================
// 6. CETAK STRUK KASIR THERMAL (#receipt-print-area)
// =============================================================================
function cetakStruk(orderId) {
  const order = window.CepatikDB ? window.CepatikDB.findOrderByTicket(orderId) : null;

  if (!order) {
    showToast(`Pesanan [${orderId}] tidak ditemukan!`, 'toast-warning');
    return;
  }

  const receiptArea = document.getElementById('receipt-print-area');
  if (!receiptArea) {
    console.error('Elemen #receipt-print-area tidak ditemukan di DOM!');
    return;
  }

  const tiketId = order.id || order.tiket || 'CPT-8492';
  const pin = order.pin || '0000';
  const rawFileName = typeof order.file === 'object' ? (order.file.nama || 'Dokumen.pdf') : (order.file || 'Dokumen.pdf');
  const hlm = (typeof order.file === 'object' && order.file.halaman) ? order.file.halaman : (order.halaman || 1);
  const rangkap = order.rangkap || 1;
  const kertas = order.kertas || 'A4 Standard';
  const tinta = order.tinta || 'Hitam Putih';
  const duplex = order.duplex ? 'Bolak-balik (Duplex)' : '1 Sisi (Simplex)';
  const jilid = order.jilid || 'Tanpa Jilid';
  const total = window.CepatikDB ? window.CepatikDB.formatRupiah(order.total || 0) : `Rp ${order.total}`;
  const kiosk = order.kiosk || 'Kiosk Mandiri Cepatik #01';
  const waktuCetak = order.waktu ? `${order.tanggal || '27 Okt 2026'}, ${order.waktu}` : new Date().toLocaleString('id-ID');

  receiptArea.innerHTML = `
    <div class="thermal-receipt">
      <div class="thermal-header">
        <div class="thermal-brand">CEPATIK KIOSK</div>
        <div class="thermal-sub">SELF-SERVICE CLOUD PRINTING</div>
        <div class="thermal-sub">${kiosk}</div>
        <div class="thermal-sub">Waktu: ${waktuCetak}</div>
      </div>

      <hr class="thermal-double-divider">

      <div class="thermal-big-box">
        <div class="thermal-box-title">KODE TIKET PESANAN</div>
        <div class="thermal-ticket-num">${tiketId}</div>
        <div style="margin-top: 5px; font-size: 10px; font-weight: 700; color: #222;">
          KLAIM CETAKAN LANGSUNG DI MESIN KIOSK
        </div>
      </div>

      <hr class="thermal-divider">

      <div style="margin-bottom: 6px;">
        <div class="thermal-item-title">${rawFileName}</div>
        <div class="thermal-row">
          <span>Kertas:</span>
          <span>${kertas}</span>
        </div>
        <div class="thermal-row">
          <span>Tinta / Warna:</span>
          <span>${tinta}</span>
        </div>
        <div class="thermal-row">
          <span>Metode Cetak:</span>
          <span>${duplex}</span>
        </div>
        <div class="thermal-row">
          <span>Jilid:</span>
          <span>${jilid}</span>
        </div>
        <div class="thermal-row">
          <span>Halaman x Rangkap:</span>
          <span>${hlm} Lembar x ${rangkap} Rangkap</span>
        </div>
      </div>

      <hr class="thermal-divider">

      <div class="thermal-row" style="font-size: 13px; font-weight: 900; margin: 6px 0;">
        <span>TOTAL BAYAR:</span>
        <span>${total}</span>
      </div>
      <div class="thermal-row" style="font-size: 10.5px;">
        <span>Metode Bayar:</span>
        <span>QRIS INSTAN (LUNAS)</span>
      </div>
      <div class="thermal-row" style="font-size: 10.5px;">
        <span>Status Order:</span>
        <strong>${order.status.toUpperCase()}</strong>
      </div>

      <hr class="thermal-divider">

      <div class="thermal-barcode-sim">
        <div class="thermal-barcode-lines">
          <span style="width:2px;"></span><span style="width:1px;"></span>
          <span style="width:3px;"></span><span style="width:1px;"></span>
          <span style="width:2px;"></span><span style="width:4px;"></span>
          <span style="width:1px;"></span><span style="width:3px;"></span>
          <span style="width:2px;"></span><span style="width:1px;"></span>
          <span style="width:4px;"></span><span style="width:2px;"></span>
          <span style="width:1px;"></span><span style="width:3px;"></span>
          <span style="width:2px;"></span><span style="width:4px;"></span>
          <span style="width:1px;"></span><span style="width:2px;"></span>
          <span style="width:3px;"></span><span style="width:1px;"></span>
          <span style="width:2px;"></span><span style="width:4px;"></span>
        </div>
        <div style="font-size: 9px; letter-spacing: 2px;">* ${tiketId} *</div>
      </div>

      <div class="thermal-footer">
        <strong>CARA PENGAMBILAN DI KIOSK:</strong><br>
        1. Datang ke mesin Kiosk Cepatik yang tertera.<br>
        2. Masukkan Kode Tiket <strong>[ ${tiketId} ]</strong> pada layar kiosk.<br>
        3. Mesin kiosk akan langsung memproses cetakan Anda.<br>
        4. Ambil dokumen cetak Anda dari baki keluaran kiosk.<br>
        --------------------------------<br>
        Simpan struk ini sebagai bukti validasi.<br>
        Layanan Pelanggan 24/7 www.cepatik.com
      </div>
    </div>
  `;

  showToast(`Mencetak struk kasir untuk pesanan ${tiketId}...`, 'toast-info');

  setTimeout(function () {
    window.print();
  }, 150);
}

// =============================================================================
// 7. TOAST NOTIFIKASI & CLIPBOARD
// =============================================================================
function showToast(message, type = 'toast-info') {
  let toast = document.getElementById('adminToast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'adminToast';
    toast.className = 'admin-toast';
    document.body.appendChild(toast);
  }

  toast.className = `admin-toast ${type} show`;
  toast.innerHTML = `
    <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <circle cx="12" cy="12" r="10"></circle>
      <line x1="12" y1="8" x2="12" y2="12"></line>
      <line x1="12" y1="16" x2="12.01" y2="16"></line>
    </svg>
    <span>${message}</span>
  `;

  clearTimeout(window._toastTimeout);
  window._toastTimeout = setTimeout(() => {
    toast.classList.remove('show');
  }, 3200);
}

function copyToClipboard(text) {
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(() => {
      showToast(`ID ${text} disalin ke clipboard!`, 'toast-success');
    });
  } else {
    showToast(`ID: ${text}`, 'toast-info');
  }
}

// =============================================================================
// 8. INISIALISASI HALAMAN & EVENT LISTENERS
// =============================================================================
document.addEventListener('DOMContentLoaded', function () {
  // 1. Verifikasi Otentikasi Operator & Sesi
  currentAdminSession = checkAdminAuth();
  if (!currentAdminSession) return;

  setupOperatorProfileUI(currentAdminSession);

  // 2. Render Tabel dan Metrik dari CepatikDB
  renderTable();

  // 3. Berlangganan Perubahan Database Real-time
  if (window.CepatikDB) {
    window.CepatikDB.subscribe(function () {
      renderTable();
    });
  }

  // 4. Filter Status Tabs
  const filterBtns = document.querySelectorAll('.admin-filter-btn');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      filterBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      currentFilter = this.getAttribute('data-filter') || 'Semua';
      renderTable();
    });
  });

  // 5. Input Pencarian
  const searchInput = document.getElementById('searchOrderInput');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      searchQuery = this.value;
      renderTable();
    });
  }

  // 6. Filter Lokasi Kiosk (Khusus Super Admin)
  const selectKioskFilter = document.getElementById('selectKioskAdminFilter');
  if (selectKioskFilter) {
    selectKioskFilter.addEventListener('change', function () {
      currentKioskFilter = this.value;
      renderTable();
      showToast(`Menampilkan antrean: ${this.options[this.selectedIndex].text}`, 'toast-info');
    });
  }

  // 7. Jam Real-Time di Topbar
  const liveClockEl = document.getElementById('liveClock');
  if (liveClockEl) {
    function updateClock() {
      const now = new Date();
      const options = {
        weekday: 'long',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      };
      liveClockEl.textContent = now.toLocaleDateString('id-ID', options) + ' WIB';
    }
    updateClock();
    setInterval(updateClock, 1000);
  }
});
