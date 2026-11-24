<?php

namespace App\Http\Controllers;

use App\Models\Kiosk;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * CetakController - Modul Anggota A (Zahra Zusseta Stiawan)
 * Menangani Wizard Pemesanan Cetak 5-Langkah & Operasi CREATE Pesanan ke Database via Eloquent ORM.
 */
class CetakController extends Controller
{
    /**
     * Tampilkan halaman Wizard Cetak (GET /cetak)
     */
    public function index()
    {
        HomeController::ensureDatabaseReady();
        $master = HomeController::getMasterTarifData();

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

        $clientOrders = $ordersFromDb->map(fn($o) => $o->toClientArray())->values()->all();

        return view('cetak.index', array_merge($master, [
            'daftar_kiosk' => $daftar_kiosk,
            'clientOrders' => $clientOrders,
        ]));
    }

    /**
     * Simpan pesanan cetak baru ke database (POST /cetak atau POST /api/orders)
     * Menggunakan Eloquent ORM: Order::create([...])
     */
    public function store(Request $request)
    {
        HomeController::ensureDatabaseReady();

        $rawDigits = (string) random_int(1000, 9999);
        $ticketCode = strtoupper(trim((string) $request->input('ticket_code', $request->input('id', 'CPT-' . $rawDigits))));
        $pin = trim((string) $request->input('pin', preg_replace('/^CPT-/', '', $ticketCode)));

        $fileInput = $request->input('file');
        if (is_array($fileInput)) {
            $fileName = $fileInput['nama'] ?? 'Dokumen_Cetak.pdf';
            $fileSize = $fileInput['ukuran'] ?? '1.4 MB';
            $pages = (int) ($fileInput['halaman'] ?? $request->input('pages', 12));
        } else {
            $fileName = (string) $request->input('file_name', $fileInput ?: 'Dokumen_Cetak.pdf');
            $fileSize = (string) $request->input('file_size', '1.4 MB');
            $pages = (int) $request->input('pages', 12);
        }

        $paperRaw = (string) $request->input('paper_type', $request->input('kertas', 'a4_75'));
        if (stripos($paperRaw, '80') !== false && stripos($paperRaw, 'F4') === false) {
            $paperType = 'a4_80';
        } elseif (stripos($paperRaw, 'F4') !== false || stripos($paperRaw, 'f4_80') !== false) {
            $paperType = 'f4_80';
        } elseif (in_array($paperRaw, ['a4_75', 'a4_80', 'f4_80'], true)) {
            $paperType = $paperRaw;
        } else {
            $paperType = 'a4_75';
        }

        $inkRaw = (string) $request->input('ink_mode', $request->input('tinta', 'bw'));
        if (stripos($inkRaw, 'Full') !== false || $inkRaw === 'color_full') {
            $inkMode = 'color_full';
        } elseif (stripos($inkRaw, 'Warna') !== false || $inkRaw === 'color_std') {
            $inkMode = 'color_std';
        } else {
            $inkMode = 'bw';
        }

        $finishingRaw = (string) $request->input('finishing', $request->input('jilid', 'none'));
        if (stripos($finishingRaw, 'Lakban') !== false || stripos($finishingRaw, 'Spiral') !== false || $finishingRaw === 'lakban') {
            $finishing = 'lakban';
        } elseif (stripos($finishingRaw, 'Steples') !== false || $finishingRaw === 'steples') {
            $finishing = 'steples';
        } else {
            $finishing = 'none';
        }

        $copies = max(1, (int) $request->input('copies', $request->input('rangkap', 1)));
        $isDuplex = filter_var($request->input('is_duplex', $request->input('duplex', false)), FILTER_VALIDATE_BOOLEAN);
        $kioskName = (string) $request->input('kiosk_name', $request->input('kiosk', 'Kiosk Perpustakaan Pusat UMM (Lt. 1)'));
        $status = (string) $request->input('status', 'Menunggu Cetak');

        $calculatedTotal = Order::calculatePrice($paperType, $inkMode, max(1, $pages), $copies, $isDuplex, $finishing);
        $totalPrice = (int) $request->input('total_price', $request->input('total', $calculatedTotal));
        if ($totalPrice <= 0) {
            $totalPrice = $calculatedTotal;
        }

        // ELOQUENT ORM CREATE: Simpan ke tabel `orders`
        $order = Order::updateOrCreate(
            ['ticket_code' => $ticketCode],
            [
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'paper_type' => $paperType,
                'ink_mode' => $inkMode,
                'pages' => max(1, $pages),
                'copies' => $copies,
                'is_duplex' => $isDuplex,
                'finishing' => $finishing,
                'kiosk_name' => $kioskName,
                'total_price' => $totalPrice,
                'pin' => $pin,
                'status' => $status,
                'is_purged' => $status === 'Selesai',
                'display_time' => (string) $request->input('waktu', date('H:i') . ' WIB'),
            ]
        );

        if ($request->expectsJson() || $request->is('api/*') || $request->boolean('_ajax')) {
            return response()->json([
                'success' => true,
                'message' => "Pesanan {$order->ticket_code} berhasil disimpan ke database Laravel.",
                'order' => $order->toClientArray(),
            ], 201);
        }

        $referer = (string) $request->headers->get('referer', '');
        $targetUrl = stripos($referer, '/Week9/') !== false ? '/Week9/admin.php' : '/admin';

        return redirect($targetUrl)->with('success', "Pesanan baru [{$order->ticket_code}] untuk berkas '{$order->file_name}' berhasil ditambahkan ke database!");
    }
}
