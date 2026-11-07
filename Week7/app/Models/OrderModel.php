<?php
/**
 * OrderModel.php - Cepatik MVC Model
 * 
 * Model ini bertanggung jawab atas pengelolaan antrean dan dokumen pesanan cetak,
 * pencarian pesanan berdasarkan Kode Tiket / PIN, filter status,
 * serta kalkulasi omzet server-side menggunakan objek Order.
 */

require_once __DIR__ . '/../../classes/Order.php';
require_once __DIR__ . '/../../includes/data.php';

class OrderModel
{
    /** @var Order[] */
    private array $orders;
    private array $heroQueue;

    public function __construct()
    {
        global $daftar_antrean;
        $this->heroQueue = $daftar_antrean ?? [];

        // Inisialisasi daftar objek Order domain untuk admin & antrean SSR
        $this->orders = [
            new Order(
                'CPT-8492',
                'Skripsi_BAB_1-5_Final.pdf',
                'a4_80',       // A4 Tebal 80 GSM
                'bw',          // Hitam Putih
                48,            // 48 Halaman
                true,          // Duplex (Bolak-balik) -> Diskon 5%
                'lakban',      // Jilid Lakban Mika
                'Siap Diambil',
                '10 menit yang lalu'
            ),
            new Order(
                'CPT-8493',
                'Jurnal_Farmasi_Klinis.pdf',
                'a4_75',       // A4 Standard
                'color_full',  // Full Color Foto
                18,
                false,         // Simplex (1 Sisi)
                'steples',     // Jilid Steples Sudut
                'Sedang Diproses',
                '25 menit yang lalu'
            ),
            new Order(
                'CPT-8494',
                'Laporan_Praktikum_Fisika.docx',
                'a4_75',
                'color_std',   // Warna Standar
                14,
                true,          // Duplex
                'steples',
                'Menunggu Cetak',
                '40 menit yang lalu'
            ),
            new Order(
                'CPT-8495',
                'Makalah_Ekonomi_Makro.pdf',
                'a4_75',
                'bw',
                26,
                true,
                'none',        // Tanpa Jilid
                'Siap Diambil',
                '1 jam yang lalu'
            ),
            new Order(
                'CPT-8496',
                'Poster_Seminar_Nasional.png',
                'f4_80',       // F4 Folio 80 GSM
                'color_full',
                4,
                false,
                'none',
                'Menunggu Cetak',
                '2 jam yang lalu'
            ),
            new Order(
                'CPT-8497',
                'Buku_Panduan_Himpunan.pdf',
                'a4_80',
                'bw',
                85,
                true,
                'lakban',
                'Selesai',
                '3 jam yang lalu'
            ),
        ];
    }

    /**
     * Mengambil daftar pesanan dengan dukungan filter status dan pencarian
     * @return Order[]
     */
    public function getAllOrders(string $statusFilter = '', string $search = ''): array
    {
        $filtered = $this->orders;

        if (!empty($statusFilter)) {
            $filtered = array_filter($filtered, function (Order $order) use ($statusFilter) {
                return strcasecmp($order->getStatus(), $statusFilter) === 0;
            });
        }

        if (!empty($search)) {
            $search = strtoupper(trim($search));
            $filtered = array_filter($filtered, function (Order $order) use ($search) {
                return (strpos(strtoupper($order->getId()), $search) !== false) ||
                       (strpos(strtoupper($order->getFileName()), $search) !== false);
            });
        }

        return array_values($filtered);
    }

    /**
     * Mengambil antrean aktif awal untuk Hero Section Landing Page
     */
    public function getHeroQueue(): array
    {
        return $this->heroQueue;
    }

    /**
     * Mencari pesanan berdasarkan ID Tiket atau PIN
     */
    public function findByTicket(string $ticketQuery): ?Order
    {
        $q = strtoupper(trim($ticketQuery));
        if (empty($q)) {
            return null;
        }

        foreach ($this->orders as $order) {
            if (strtoupper($order->getId()) === $q || strtoupper($order->getPin()) === $q) {
                return $order;
            }
        }
        return null;
    }

    /**
     * Menghitung total omzet seluruh pesanan via method calculateTotal()
     * @param Order[]|null $orders
     */
    public function calculateTotalRevenue(?array $orders = null): int
    {
        $list = $orders ?? $this->orders;
        $total = 0;
        foreach ($list as $order) {
            $total += $order->calculateTotal();
        }
        return $total;
    }

    /**
     * Menghitung jumlah pesanan yang masih dalam proses atau antrean
     * @param Order[]|null $orders
     */
    public function countPending(?array $orders = null): int
    {
        $list = $orders ?? $this->orders;
        $count = 0;
        foreach ($list as $order) {
            $status = $order->getStatus();
            if ($status === 'Menunggu Cetak' || $status === 'Sedang Diproses') {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Menghitung jumlah pesanan yang sudah siap diambil atau selesai
     * @param Order[]|null $orders
     */
    public function countCompleted(?array $orders = null): int
    {
        $list = $orders ?? $this->orders;
        $count = 0;
        foreach ($list as $order) {
            $status = $order->getStatus();
            if ($status === 'Siap Diambil' || $status === 'Siap di Loker' || $status === 'Selesai') {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Membuat pesanan baru ke dalam daftar antrean
     */
    public function createOrder(array $data): Order
    {
        $order = new Order(
            $data['id'] ?? ('CPT-' . rand(1000, 9999)),
            $data['fileName'] ?? 'Dokumen_Cetak.pdf',
            $data['paperType'] ?? 'a4_75',
            $data['inkMode'] ?? 'bw',
            (int)($data['pages'] ?? 1),
            (bool)($data['isDuplex'] ?? false),
            $data['finishing'] ?? 'none',
            $data['status'] ?? 'Menunggu Cetak',
            date('d M Y, H:i')
        );

        array_unshift($this->orders, $order);
        return $order;
    }
}
