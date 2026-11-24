<?php

namespace App\Http\Controllers;

use App\Models\Kiosk;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

/**
 * HomeController - Modul Anggota A (Zahra Zusseta Stiawan)
 * Menangani Halaman Beranda (Landing Page), Kalkulator Tarif, & Quick Order Tracker berbasis Eloquent ORM.
 */
class HomeController extends Controller
{
    /**
     * Pastikan tabel database sudah termigrasi dan terisi data awal (auto-heal)
     */
    public static function ensureDatabaseReady(): void
    {
        try {
            if (!Schema::hasTable('kiosks') || !Schema::hasTable('orders')) {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
            } elseif (Kiosk::count() === 0 && Order::count() === 0) {
                Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // Fallback gracefully if DB is read-only
        }
    }

    public static function getMasterTarifData(): array
    {
        $site_info = [
            'nama' => 'Cepatik',
            'tagline' => 'Self-Service Cloud Printing Kiosk',
            'deskripsi' => 'Cepatik - Self-Service Cloud Printing Kiosk & Cetak Mandiri Modern untuk Ekosistem Kampus (Laravel 9 + Eloquent ORM).',
            'kiosk_aktif_count' => 42,
            'kampus_mitra_count' => 40,
            'kecepatan_ppm' => 35,
            'tarif_mulai' => 350,
            'resolusi_dpi' => '1200 x 1200 DPI',
            'retensi_jam' => 2,
            'kontak_wa' => '+62 812-3456-7890',
            'kontak_wa_link' => 'https://wa.me/6281234567890',
            'tahun' => 2026,
            'perusahaan' => 'PT Cepatik Teknologi Nusantara',
        ];

        $pilihan_kertas = [
            'a4_75' => [
                'id' => 'a4_75',
                'nama' => 'A4 Standard',
                'gsm' => '75 GSM Harian',
                'tambah' => 0,
                'label_harga' => '+Rp 0',
                'deskripsi_lengkap' => 'A4 Standard (75 GSM)',
                'default' => true,
            ],
            'a4_80' => [
                'id' => 'a4_80',
                'nama' => 'A4 Tebal',
                'gsm' => '80 GSM Skripsi',
                'tambah' => 50,
                'label_harga' => '+Rp 50/lbr',
                'deskripsi_lengkap' => 'A4 Tebal (80 GSM)',
                'default' => false,
            ],
            'f4_80' => [
                'id' => 'f4_80',
                'nama' => 'F4 Folio',
                'nama_alt' => 'F4 / Folio',
                'gsm' => '80 GSM Dokumen',
                'tambah' => 100,
                'label_harga' => '+Rp 100/lbr',
                'deskripsi_lengkap' => 'F4 Folio (80 GSM)',
                'default' => false,
            ],
        ];

        $pilihan_warna = [
            'bw' => [
                'id' => 'bw',
                'nama' => 'Hitam Putih',
                'nama_pendek' => 'Hitam Putih',
                'deskripsi' => 'Teks makalah & skripsi',
                'deskripsi_singkat' => 'Teks makalah',
                'harga' => 350,
                'label_harga' => 'Rp 350',
                'label_harga_per_lbr' => 'Rp 350/lbr',
                'default' => true,
            ],
            'color_std' => [
                'id' => 'color_std',
                'nama' => 'Warna Standar',
                'nama_pendek' => 'Warna Standar',
                'deskripsi' => 'Grafik & logo',
                'deskripsi_singkat' => 'Grafik & diagram',
                'harga' => 1000,
                'label_harga' => 'Rp 1.000',
                'label_harga_per_lbr' => 'Rp 1.000/lbr',
                'default' => false,
            ],
            'color_full' => [
                'id' => 'color_full',
                'nama' => 'Full Color Foto',
                'nama_pendek' => 'Full Color',
                'deskripsi' => 'Poster & foto',
                'deskripsi_singkat' => 'Foto & poster',
                'harga' => 2000,
                'label_harga' => 'Rp 2.000',
                'label_harga_per_lbr' => 'Rp 2.000/lbr',
                'default' => false,
            ],
        ];

        $pilihan_jilid = [
            'none' => [
                'id' => 'none',
                'nama' => 'Tanpa Jilid',
                'deskripsi' => 'Lepasan biasa',
                'harga' => 0,
                'label_harga' => '+Rp 0',
                'default' => true,
            ],
            'steples' => [
                'id' => 'steples',
                'nama' => 'Steples Pojok',
                'deskripsi' => 'Rapih sudut kiri atas',
                'harga' => 1000,
                'label_harga' => '+Rp 1.000',
                'default' => false,
            ],
            'lakban' => [
                'id' => 'lakban',
                'nama' => 'Jilid Lakban Mika',
                'deskripsi' => 'Mika bening + lakban hitam',
                'harga' => 3500,
                'label_harga' => '+Rp 3.500',
                'default' => false,
            ],
        ];

        $daftar_keunggulan = [
            [
                'id' => 'privacy',
                'judul' => 'Privasi Berkas Terjamin',
                'deskripsi' => 'Dokumen skripsi dan data pribadi dienkripsi TLS 256-bit dan otomatis terhapus permanen dari server 2 jam setelah selesai cetak.',
                'tag' => '✓ Zero Data Retention',
                'tag_style' => 'color: #059669;',
                'icon_class' => 'green',
                'svg_type' => 'shield',
            ],
            [
                'id' => 'speed',
                'judul' => 'Laserjet Cepat 35 ppm',
                'deskripsi' => 'Kiosk dilengkapi printer laserjet enterprise berkecepatan tinggi dengan toner tahan air dan sensor presisi anti-paper-jam.',
                'tag' => '✓ Resolusi 1200 x 1200 DPI',
                'tag_style' => 'color: var(--primary);',
                'icon_class' => 'coral',
                'svg_type' => 'printer',
            ],
            [
                'id' => 'access',
                'judul' => 'Akses Kiosk 24 Jam',
                'deskripsi' => 'Mesin kiosk mandiri tersebar di perpustakaan, kantin, dan mitra kampus. Deadline tugas subuh pun tetap bisa diambil kapan saja.',
                'tag' => '✓ 24/7 Self-Pickup',
                'tag_style' => '',
                'icon_class' => 'gray',
                'svg_type' => 'clock',
            ],
        ];

        return compact('site_info', 'pilihan_kertas', 'pilihan_warna', 'pilihan_jilid', 'daftar_keunggulan');
    }

    /**
     * Tampilkan halaman Beranda (GET /)
     * Membaca data Kiosk dan Order langsung dari database menggunakan Eloquent ORM.
     */
    public function index(Request $request)
    {
        self::ensureDatabaseReady();
        $master = self::getMasterTarifData();

        // READ dari Database via Eloquent ORM: Kiosk::all() & Order::all()
        $kiosksFromDb = Kiosk::orderBy('id', 'asc')->get();
        $ordersFromDb = Order::orderBy('id', 'desc')->get();

        $daftar_kiosk = [];
        foreach ($kiosksFromDb as $k) {
            $daftar_kiosk[] = [
                'id' => $k->slug,
                'nama' => $k->name,
                'nama_lengkap_app' => $k->name,
                'lokasi' => $k->location,
                'jarak' => $k->distance,
                'stok_a4' => $k->paper_a4,
                'stok_f4' => $k->paper_f4,
                'loker_tersedia' => 10,
                'jam_buka' => $k->operating_hours,
                'status' => $k->is_online ? 'online' : 'offline',
                'maps_url' => $k->maps_url,
                'default' => (bool) $k->is_default,
            ];
        }

        // Kalkulator Estimasi Awal
        $calc_halaman = max(1, (int) $request->query('halaman', 15));
        $calc_warna = (string) $request->query('warna', 'bw');
        $calc_kertas = (string) $request->query('kertas', 'a4_75');
        $calc_duplex = (bool) $request->query('duplex', false);
        $calc_rangkap = max(1, (int) $request->query('rangkap', 1));

        $totalEstimasi = Order::calculatePrice($calc_kertas, $calc_warna, $calc_halaman, $calc_rangkap, $calc_duplex, 'none');
        $estimasi_awal = [
            'halaman' => $calc_halaman,
            'rangkap' => $calc_rangkap,
            'nama_kertas' => $master['pilihan_kertas'][$calc_kertas]['deskripsi_lengkap'] ?? 'A4 Standard (75 GSM)',
            'nama_warna' => $master['pilihan_warna'][$calc_warna]['nama'] ?? 'Hitam Putih (B/W)',
            'metode_sisi' => $calc_duplex ? 'Bolak-Balik (Duplex)' : '1 Sisi (Simplex)',
            'total' => $totalEstimasi,
            'total_formatted' => 'Rp ' . number_format($totalEstimasi, 0, ',', '.'),
        ];

        // Pencarian Tiket via Eloquent ORM jika ada parameter ?ticket_id=...
        $searchQuery = trim((string) $request->query('ticket_id', ''));
        $search_result = null;
        if ($searchQuery !== '') {
            $cleanQ = strtoupper(ltrim($searchQuery, '#'));
            $search_result = Order::where('ticket_code', $cleanQ)
                ->orWhere('ticket_code', 'CPT-' . $cleanQ)
                ->orWhere('pin', $cleanQ)
                ->first();
        }

        $clientOrders = $ordersFromDb->map(fn($o) => $o->toClientArray())->values()->all();

        return view('home.index', array_merge($master, [
            'daftar_kiosk' => $daftar_kiosk,
            'kiosksFromDb' => $kiosksFromDb,
            'ordersFromDb' => $ordersFromDb,
            'clientOrders' => $clientOrders,
            'calc_halaman' => $calc_halaman,
            'calc_warna' => $calc_warna,
            'calc_kertas' => $calc_kertas,
            'calc_duplex' => $calc_duplex,
            'calc_rangkap' => $calc_rangkap,
            'estimasi_awal' => $estimasi_awal,
            'searchQuery' => $searchQuery,
            'search_result' => $search_result,
        ]));
    }
}
