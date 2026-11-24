<?php

namespace App\Http\Controllers;

use App\Models\Kiosk;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * AdminController - Modul Anggota B (Damar Ghulamul Abror)
 * Menangani Portal Login Operator, Dashboard Admin Real-Time, & Operasi CRUD Eloquent ORM (Order & Kiosk).
 */
class AdminController extends Controller
{
    /**
     * Helper redirect dinamis agar berjalan mulus baik di `php artisan serve` (/admin)
     * maupun di `start_server.bat` (/Week9/admin.php).
     */
    private function adminRedirectUrl(Request $request): string
    {
        $referer = (string) $request->headers->get('referer', '');
        if (stripos($referer, '/Week9/') !== false) {
            return '/Week9/admin.php';
        }
        return '/admin';
    }

    /**
     * Tampilkan halaman Login Operator & Super Admin (GET /login)
     */
    public function login()
    {
        HomeController::ensureDatabaseReady();
        $kiosks = Kiosk::orderBy('id', 'asc')->get();
        return view('admin.login', compact('kiosks'));
    }

    /**
     * Tampilkan Dashboard Admin Kiosk (GET /admin)
     * Membaca seluruh data Order dan Kiosk dari Database menggunakan Eloquent ORM.
     */
    public function index(Request $request)
    {
        HomeController::ensureDatabaseReady();

        // ELOQUENT ORM READ: Ambil semua data dari tabel `kiosks` dan `orders`
        $kiosks = Kiosk::orderBy('id', 'asc')->get();
        $orders = Order::orderBy('id', 'desc')->get();

        // Jika terdapat parameter ?edit_id=..., ambil 1 data menggunakan Order::find($id)
        $editingOrder = null;
        if ($request->filled('edit_id')) {
            $editingOrder = Order::find((int) $request->query('edit_id'));
        }

        $totalOrdersCount = $orders->count();
        $totalRevenue = $orders->sum(fn($o) => $o->calculateTotal());
        $pendingOrdersCount = $orders->filter(fn($o) => in_array($o->status, ['Menunggu Cetak', 'Sedang Diproses'], true))->count();
        $completedOrdersCount = $orders->filter(fn($o) => in_array($o->status, ['Siap Diambil', 'Siap di Loker', 'Selesai'], true))->count();
        $onlineKiosksCount = $kiosks->filter(fn($k) => $k->isOnline())->count();

        $clientOrders = $orders->map(fn($o) => $o->toClientArray())->values()->all();

        return view('admin.index', compact(
            'kiosks',
            'orders',
            'editingOrder',
            'totalOrdersCount',
            'totalRevenue',
            'pendingOrdersCount',
            'completedOrdersCount',
            'onlineKiosksCount',
            'clientOrders'
        ));
    }

    /**
     * Dispatcher terpadu untuk POST /admin atau POST /admin.php berdasarkan parameter `_action`
     */
    public function handleAction(Request $request)
    {
        $action = (string) $request->input('_action', 'create_order');

        switch ($action) {
            case 'create_order':
                return app(CetakController::class)->store($request);

            case 'update_order':
                $id = $request->input('order_id', $request->input('ticket_code'));
                return $this->updateOrder($request, $id);

            case 'delete_order':
                $id = $request->input('order_id', $request->input('ticket_code'));
                return $this->deleteOrder($request, $id);

            case 'purge_orders':
                return $this->purgeCompleted($request);

            case 'update_kiosk':
                $kioskId = $request->input('kiosk_id');
                return $this->updateKiosk($request, $kioskId);

            default:
                return app(CetakController::class)->store($request);
        }
    }

    /**
     * API Endpoint READ semua pesanan dari database (GET /api/orders)
     */
    public function apiOrders()
    {
        HomeController::ensureDatabaseReady();
        $orders = Order::orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'orders' => $orders->map(fn($o) => $o->toClientArray())->values()->all(),
        ]);
    }

    /**
     * ELOQUENT ORM UPDATE: Ubah status atau detail pesanan di database
     * (POST /admin/orders/{id}/update atau POST /api/orders/{ticket}/status)
     */
    public function updateOrder(Request $request, $idOrTicket)
    {
        HomeController::ensureDatabaseReady();

        $order = is_numeric($idOrTicket)
            ? Order::find((int) $idOrTicket)
            : Order::where('ticket_code', strtoupper((string) $idOrTicket))->first();

        if (!$order) {
            if ($request->expectsJson() || $request->is('api/*') || $request->boolean('_ajax')) {
                return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan di database.'], 404);
            }
            return redirect($this->adminRedirectUrl($request))->with('error', 'Pesanan tidak ditemukan di database.');
        }

        $newStatus = (string) $request->input('status', $order->status);
        $fileName = (string) $request->input('file_name', $order->file_name);
        $paperType = (string) $request->input('paper_type', $order->paper_type);
        $inkMode = (string) $request->input('ink_mode', $order->ink_mode);
        $pages = max(1, (int) $request->input('pages', $order->pages));
        $copies = max(1, (int) $request->input('copies', $order->copies ?: 1));
        $isDuplex = $request->has('is_duplex')
            ? filter_var($request->input('is_duplex'), FILTER_VALIDATE_BOOLEAN)
            : (bool) $order->is_duplex;
        $finishing = (string) $request->input('finishing', $order->finishing);
        $kioskName = (string) $request->input('kiosk_name', $order->kiosk_name);

        $totalPrice = Order::calculatePrice($paperType, $inkMode, $pages, $copies, $isDuplex, $finishing);

        $order->update([
            'file_name' => $fileName,
            'paper_type' => $paperType,
            'ink_mode' => $inkMode,
            'pages' => $pages,
            'copies' => $copies,
            'is_duplex' => $isDuplex,
            'finishing' => $finishing,
            'kiosk_name' => $kioskName,
            'total_price' => $totalPrice,
            'status' => $newStatus,
            'is_purged' => $newStatus === 'Selesai' ? true : (bool) $order->is_purged,
        ]);

        if ($request->expectsJson() || $request->is('api/*') || $request->boolean('_ajax')) {
            return response()->json([
                'success' => true,
                'message' => "Status pesanan [{$order->ticket_code}] diperbarui menjadi '{$order->status}'.",
                'order' => $order->toClientArray(),
            ]);
        }

        return redirect($this->adminRedirectUrl($request))->with('success', "Data pesanan [{$order->ticket_code}] berhasil diperbarui di database!");
    }

    /**
     * ELOQUENT ORM DELETE: Hapus pesanan dari database
     * (POST /admin/orders/{id}/delete atau DELETE /api/orders/{ticket})
     */
    public function deleteOrder(Request $request, $idOrTicket)
    {
        HomeController::ensureDatabaseReady();

        $order = is_numeric($idOrTicket)
            ? Order::find((int) $idOrTicket)
            : Order::where('ticket_code', strtoupper((string) $idOrTicket))->first();

        if (!$order) {
            if ($request->expectsJson() || $request->is('api/*') || $request->boolean('_ajax')) {
                return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
            }
            return redirect($this->adminRedirectUrl($request))->with('error', 'Pesanan tidak ditemukan.');
        }

        $ticketCode = $order->ticket_code;
        $order->delete();

        if ($request->expectsJson() || $request->is('api/*') || $request->boolean('_ajax')) {
            return response()->json([
                'success' => true,
                'message' => "Pesanan [{$ticketCode}] berhasil dihapus dari database.",
            ]);
        }

        return redirect($this->adminRedirectUrl($request))->with('success', "Pesanan [{$ticketCode}] berhasil dihapus permanen dari database!");
    }

    /**
     * ELOQUENT ORM UPDATE: Kebijakan Zero Data Retention (Purge berkas pesanan Selesai)
     */
    public function purgeCompleted(Request $request)
    {
        HomeController::ensureDatabaseReady();

        $purgedCount = Order::where('status', 'Selesai')
            ->where('is_purged', false)
            ->update(['is_purged' => true]);

        if ($request->expectsJson() || $request->is('api/*') || $request->boolean('_ajax')) {
            return response()->json([
                'success' => true,
                'purged' => $purgedCount,
            ]);
        }

        return redirect($this->adminRedirectUrl($request))->with('success', "Kebijakan Retensi Berkas dijalankan pada database ({$purgedCount} berkas dimusnahkan).");
    }

    /**
     * ELOQUENT ORM UPDATE KIOSK: Ubah status online/offline & stok kertas Kiosk
     */
    public function updateKiosk(Request $request, $id)
    {
        HomeController::ensureDatabaseReady();
        $kiosk = Kiosk::find((int) $id);
        if ($kiosk) {
            $kiosk->update([
                'name' => (string) $request->input('name', $kiosk->name),
                'a4_percentage' => max(0, min(100, (int) $request->input('a4_percentage', $kiosk->a4_percentage))),
                'f4_percentage' => max(0, min(100, (int) $request->input('f4_percentage', $kiosk->f4_percentage))),
                'is_online' => filter_var($request->input('is_online', $kiosk->is_online), FILTER_VALIDATE_BOOLEAN),
            ]);
            return redirect($this->adminRedirectUrl($request))->with('success', "Status mesin [{$kiosk->kiosk_code} - {$kiosk->name}] berhasil diperbarui di database!");
        }
        return redirect($this->adminRedirectUrl($request))->with('error', 'Unit Kiosk tidak ditemukan.');
    }
}
