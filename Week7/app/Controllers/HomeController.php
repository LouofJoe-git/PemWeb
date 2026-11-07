<?php
/**
 * HomeController.php - Cepatik MVC Controller
 * 
 * Controller untuk menangani halaman utama (Landing Page) Cepatik.
 * Menghubungkan Model tarif, kiosk, dan antrean pesanan
 * serta merender tampilan ke View home/index.
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/TarifModel.php';
require_once __DIR__ . '/../Models/KioskModel.php';
require_once __DIR__ . '/../Models/OrderModel.php';

class HomeController extends BaseController
{
    private TarifModel $tarifModel;
    private KioskModel $kioskModel;
    private OrderModel $orderModel;

    public function __construct()
    {
        $this->tarifModel = new TarifModel();
        $this->kioskModel = new KioskModel();
        $this->orderModel = new OrderModel();
    }

    /**
     * Action utama untuk halaman Beranda
     */
    public function index(): void
    {
        // 1. Ambil data master dari Model
        $site_info = $this->tarifModel->getSiteInfo();
        $pilihan_kertas = $this->tarifModel->getPilihanKertas();
        $pilihan_warna = $this->tarifModel->getPilihanWarna();
        $pilihan_jilid = $this->tarifModel->getPilihanJilid();
        $daftar_kiosk = $this->kioskModel->getLandingKiosks();
        $daftar_antrean = $this->orderModel->getHeroQueue();
        $daftar_keunggulan = $this->tarifModel->getKeunggulan();

        // 2. Tangani input simulasi kalkulator jika ada (GET / POST)
        $calc_halaman = (int)($this->getQuery('halaman', $this->getPost('halaman', 15)));
        $calc_warna = (string)($this->getQuery('warna', $this->getPost('warna', 'bw')));
        $calc_kertas = (string)($this->getQuery('kertas', $this->getPost('kertas', 'a4_75')));
        $calc_duplex = (bool)($this->getQuery('duplex', $this->getPost('duplex', false)));
        $calc_rangkap = (int)($this->getQuery('rangkap', $this->getPost('rangkap', 1)));

        // Validasi input kalkulator
        $calc_halaman = max(1, $calc_halaman);
        $calc_rangkap = max(1, $calc_rangkap);

        // Model memproses estimasi tarif awal
        $estimasi_awal = $this->tarifModel->hitungEstimasi(
            $calc_halaman,
            $calc_warna,
            $calc_kertas,
            $calc_rangkap,
            $calc_duplex
        );

        // 3. Tangani fitur Quick Order Tracker (jika ada input tiket dari form)
        $searchQuery = (string)($this->getQuery('ticket_id', $this->getPost('ticket_id', '')));
        $search_result = null;
        if (!empty($searchQuery)) {
            $search_result = $this->orderModel->findByTicket($searchQuery);
        }

        // 4. Kirim data yang sudah diolah ke View
        $this->render('home/index', compact(
            'site_info',
            'pilihan_kertas',
            'pilihan_warna',
            'pilihan_jilid',
            'daftar_kiosk',
            'daftar_antrean',
            'daftar_keunggulan',
            'estimasi_awal',
            'searchQuery',
            'search_result',
            'calc_halaman',
            'calc_warna',
            'calc_kertas',
            'calc_duplex',
            'calc_rangkap'
        ));
    }
}
