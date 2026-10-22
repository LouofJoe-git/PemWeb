<?php
require_once __DIR__ . '/data.php';

function format_rupiah($nominal) {
    return 'Rp ' . number_format((float)$nominal, 0, ',', '.');
}

/**
 * Menghitung tarif cetak per lembar berdasarkan kombinasi warna tinta dan jenis kertas.
 *
 * @param string $id_warna
 * @param string $id_kertas
 * @return int
 */
function hitung_tarif_lembar($id_warna, $id_kertas) {
    global $pilihan_warna, $pilihan_kertas;
    
    $biaya_warna = isset($pilihan_warna[$id_warna]) ? (int)$pilihan_warna[$id_warna]['harga'] : 350;
    $biaya_kertas = isset($pilihan_kertas[$id_kertas]) ? (int)$pilihan_kertas[$id_kertas]['tambah'] : 0;
    
    return $biaya_warna + $biaya_kertas;
}

/**
 * Fungsi utama untuk kalkulasi rincian estimasi biaya cetak Cepatik.
 * Logika ini 100% konsisten dengan script.js (kalkulator) dan sistem.js (wizard).
 *
 * Rumus:
 * 1. Tarif per Lembar = Harga Warna + Tambahan Kertas
 * 2. Total Lembar = Jumlah Halaman x Rangkap Salinan
 * 3. Subtotal Cetak = Tarif per Lembar x Total Lembar
 * 4. Diskon Duplex = Jika Duplex aktif & Halaman > 1, Subtotal didiskon 5% (dibulatkan)
 * 5. Total Akhir = Subtotal Cetak (setelah diskon) + Biaya Jilid
 *
 * @param int $halaman Jumlah halaman dokumen (min: 1)
 * @param string $id_warna ID warna ('bw', 'color_std', 'color_full')
 * @param string $id_kertas ID kertas ('a4_75', 'a4_80', 'f4_80')
 * @param int $rangkap Jumlah rangkap dokumen (default: 1)
 * @param bool $is_duplex Apakah cetak bolak-balik (duplex)
 * @param string $id_jilid ID finishing ('none', 'steples', 'lakban')
 * @return array Rincian estimasi biaya cetak lengkap
 */
function hitung_estimasi_cetak($halaman, $id_warna = 'bw', $id_kertas = 'a4_75', $rangkap = 1, $is_duplex = false, $id_jilid = 'none') {
    global $pilihan_warna, $pilihan_kertas, $pilihan_jilid;

    // Sanitasi dan validasi parameter batas minimum
    $halaman = max(1, (int)$halaman);
    $rangkap = max(1, (int)$rangkap);
    $is_duplex = (bool)$is_duplex;

    // Ambil data referensi tarif
    $data_warna = isset($pilihan_warna[$id_warna]) ? $pilihan_warna[$id_warna] : $pilihan_warna['bw'];
    $data_kertas = isset($pilihan_kertas[$id_kertas]) ? $pilihan_kertas[$id_kertas] : $pilihan_kertas['a4_75'];
    $data_jilid = isset($pilihan_jilid[$id_jilid]) ? $pilihan_jilid[$id_jilid] : $pilihan_jilid['none'];

    // 1. Hitung tarif per lembar
    $tarif_per_lembar = $data_warna['harga'] + $data_kertas['tambah'];

    // 2. Volume cetak
    $total_lembar = $halaman * $rangkap;

    // 3. Subtotal biaya cetak
    $subtotal_kotor = $tarif_per_lembar * $total_lembar;
    $subtotal_bersih = $subtotal_kotor;
    $diskon_duplex = 0;

    // 4. Terapkan conditional diskon duplex 5% jika bolak-balik & halaman > 1
    if ($is_duplex && $halaman > 1) {
        $subtotal_bersih = (int)round($subtotal_kotor * 0.95);
        $diskon_duplex = $subtotal_kotor - $subtotal_bersih;
    }

    // 5. Total biaya akhir termasuk finishing
    $biaya_jilid = (int)$data_jilid['harga'];
    $total_akhir = $subtotal_bersih + $biaya_jilid;

    return [
        'halaman' => $halaman,
        'rangkap' => $rangkap,
        'total_lembar' => $total_lembar,
        'id_warna' => $data_warna['id'],
        'nama_warna' => $data_warna['nama_lengkap'],
        'harga_warna' => $data_warna['harga'],
        'id_kertas' => $data_kertas['id'],
        'nama_kertas' => $data_kertas['deskripsi_lengkap'],
        'tambah_kertas' => $data_kertas['tambah'],
        'tarif_per_lembar' => $tarif_per_lembar,
        'is_duplex' => $is_duplex,
        'metode_sisi' => $is_duplex ? 'Bolak-Balik (Duplex)' : '1 Sisi (Simplex)',
        'diskon_duplex' => $diskon_duplex,
        'id_jilid' => $data_jilid['id'],
        'nama_jilid' => $data_jilid['nama'],
        'biaya_jilid' => $biaya_jilid,
        'subtotal' => $subtotal_bersih,
        'total' => $total_akhir,
        'total_formatted' => format_rupiah($total_akhir)
    ];
}

/**
 * Terapkan conditional untuk me-render badge status ketersediaan kiosk.
 *
 * @param string $status
 * @return string HTML Badge
 */
function render_kiosk_status_badge($status) {
    if ($status === 'online') {
        return '<span class="kiosk-badge-online"><span class="status-dot"></span> Online &amp; Siap</span>';
    } elseif ($status === 'busy') {
        return '<span style="font-size: 11px; background: #FEF3C7; color: #92400E; padding: 4px 10px; border-radius: 999px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;"><span class="status-dot" style="background:#D97706;"></span> Sedang Mencetak</span>';
    } else {
        return '<span style="font-size: 11px; background: #FEE2E2; color: #991B1B; padding: 4px 10px; border-radius: 999px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;"><span class="status-dot" style="background:#DC2626;"></span> Pemeliharaan</span>';
    }
}

/**
 * Terapkan conditional untuk memformat label stok kertas kiosk.
 *
 * @param int $lembar
 * @return string
 */
function render_stok_kertas_label($lembar) {
    if ($lembar > 500) {
        return '<strong>' . number_format($lembar, 0, ',', '.') . ' lbr</strong>';
    } elseif ($lembar > 0) {
        return '<strong style="color: #D97706;">' . number_format($lembar, 0, ',', '.') . ' lbr (Menipis)</strong>';
    } else {
        return '<strong style="color: #DC2626;">Habis</strong>';
    }
}

/**
 * Terapkan conditional untuk me-render status item antrean cloud hero section.
 *
 * @param array $item
 * @return string HTML status antrean
 */
function render_antrean_status_html($item) {
    $kode = isset($item['status_kode']) ? $item['status_kode'] : 'pending';

    if ($kode === 'ready') {
        $pin = htmlspecialchars($item['pin'] ?? '0000');
        $label = htmlspecialchars($item['status_teks'] ?? 'Siap');
        return '<div class="antrean-status">
                  <span>' . $label . '</span>
                  <strong>PIN: ' . $pin . '</strong>
                </div>';
    } elseif ($kode === 'spooling') {
        $progress = (int)($item['progress'] ?? 50);
        $label = htmlspecialchars($item['status_teks'] ?? 'Spooling...');
        return '<div style="font-size: 10px; font-family: var(--font-mono); color: var(--text-muted); width: 100%; max-width: 130px;">
                  <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                    <span>' . $label . '</span>
                    <strong style="color:var(--primary);">' . $progress . '%</strong>
                  </div>
                  <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width: ' . $progress . '%;"></div>
                  </div>
                </div>';
    } else {
        $label = htmlspecialchars($item['status_teks'] ?? 'Menunggu Pembayaran');
        return '<div style="font-size: 11px; background:#FEF3C7; color:#92400E; padding:4px 8px; border-radius:6px; font-weight:700;">
                  ' . $label . '
                </div>';
    }
}

/**
 * Helper untuk styling icon berkas pada antrean cloud (PDF, DOC, PPT).
 *
 * @param string $tipe
 * @return array [style, class]
 */
function get_file_icon_style($tipe) {
    $tipe = strtoupper($tipe);
    if ($tipe === 'PDF') {
        return [
            'style' => '',
            'text' => 'PDF'
        ];
    } elseif ($tipe === 'DOC' || $tipe === 'DOCX') {
        return [
            'style' => 'background:#EFECE6; color:#4B5563;',
            'text' => 'DOC'
        ];
    } else {
        return [
            'style' => 'background:#EFECE6; color:#4B5563;',
            'text' => 'PPT'
        ];
    }
}
