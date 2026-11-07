<?php
/**
 * AdminController.php - Cepatik MVC Controller
 * 
 * Controller untuk menangani Dashboard Operasional & Panel Admin Kiosk.
 * Mengelola pemanggilan Model Kiosk dan Order, kalkulasi metrik server-side,
 * serta meneruskan data ke View admin/index.
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/KioskModel.php';
require_once __DIR__ . '/../Models/OrderModel.php';

class AdminController extends BaseController
{
    private KioskModel $kioskModel;
    private OrderModel $orderModel;

    public function __construct()
    {
        $this->kioskModel = new KioskModel();
        $this->orderModel = new OrderModel();
    }

    /**
     * Action utama untuk menampilkan Dashboard Admin Kiosk
     */
    public function index(): void
    {
        // 1. Ambil parameter filter dari request jika ada
        $statusFilter = (string)($this->getQuery('status', ''));
        $searchQuery = (string)($this->getQuery('q', ''));

        // 2. Ambil data dari Model
        $kiosks = $this->kioskModel->getAllKiosks();
        $orders = $this->orderModel->getAllOrders($statusFilter, $searchQuery);

        // 3. Olah data metrik operasional melalui Model
        $totalOrdersCount = count($orders);
        $totalRevenue = $this->orderModel->calculateTotalRevenue($orders);
        $pendingOrdersCount = $this->orderModel->countPending($orders);
        $completedOrdersCount = $this->orderModel->countCompleted($orders);
        $onlineKiosksCount = $this->kioskModel->countOnline($kiosks);

        // 4. Kirim seluruh data ke View admin/index
        $this->render('admin/index', compact(
            'kiosks',
            'orders',
            'totalOrdersCount',
            'totalRevenue',
            'pendingOrdersCount',
            'completedOrdersCount',
            'onlineKiosksCount',
            'statusFilter',
            'searchQuery'
        ));
    }
}
