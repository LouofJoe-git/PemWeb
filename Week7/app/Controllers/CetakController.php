<?php
/**
 * CetakController.php - Cepatik MVC Controller
 * 
 * Controller untuk menangani alur pemesanan cetak mandiri 5-tahap
 * (Upload, Atur Cetakan, Pilih Kiosk, Pembayaran QRIS, Tiket Pengambilan).
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/TarifModel.php';
require_once __DIR__ . '/../Models/KioskModel.php';
require_once __DIR__ . '/../Models/OrderModel.php';

class CetakController extends BaseController
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
     * Action utama untuk halaman Wizard Pemesanan Cetak
     */
    public function index(): void
    {
        // 1. Ambil data spesifikasi dan master kiosk dari Model
        $site_info = $this->tarifModel->getSiteInfo();
        $pilihan_kertas = $this->tarifModel->getPilihanKertas();
        $pilihan_warna = $this->tarifModel->getPilihanWarna();
        $pilihan_jilid = $this->tarifModel->getPilihanJilid();
        $daftar_kiosk = $this->kioskModel->getLandingKiosks();

        // 2. Tangani request form pemesanan jika dikirim lewat POST
        $createdOrder = null;
        $pesanSukses = null;
        $pesanError = null;

        if ($this->isPost()) {
            $fileName = $this->getPost('fileName', '');
            $paperType = $this->getPost('paperType', 'a4_75');
            $inkMode = $this->getPost('inkMode', 'bw');
            $pages = (int)$this->getPost('pages', 1);
            $isDuplex = (bool)$this->getPost('isDuplex', false);
            $finishing = $this->getPost('finishing', 'none');

            // Validasi input pemesanan
            if (empty($fileName)) {
                $pesanError = 'Nama berkas dokumen wajib diisi.';
            } else {
                $createdOrder = $this->orderModel->createOrder([
                    'fileName' => $this->sanitize($fileName),
                    'paperType' => $paperType,
                    'inkMode' => $inkMode,
                    'pages' => max(1, $pages),
                    'isDuplex' => $isDuplex,
                    'finishing' => $finishing,
                    'status' => 'Menunggu Cetak'
                ]);
                $pesanSukses = "Pesanan berhasil dibuat dengan Kode Tiket: " . $createdOrder->getId();
            }
        }

        // 3. Teruskan data ke View cetak/index
        $this->render('cetak/index', compact(
            'site_info',
            'pilihan_kertas',
            'pilihan_warna',
            'pilihan_jilid',
            'daftar_kiosk',
            'createdOrder',
            'pesanSukses',
            'pesanError'
        ));
    }
}
