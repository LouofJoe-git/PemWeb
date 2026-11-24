<?php
/**
 * test_week9.php - Automated Verification & Test Suite for Week 9 (Laravel 9 + CRUD + Database)
 * Menguji:
 * 1. Koneksi Database & Hasil Migration (tabel `kiosks` & `orders`)
 * 2. Eloquent Model (`App\Models\Order` & `App\Models\Kiosk`) & properti `$fillable`
 * 3. Eloquent ORM CRUD (Create, Read, Update, Delete)
 * 4. Routing & Controller Modul Anggota A (Zahra: HomeController, CetakController)
 * 5. Routing & Controller Modul Anggota B (Damar: AdminController, Login, CRUD Endpoints)
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Boot application
$consoleKernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$consoleKernel->bootstrap();

use App\Models\Kiosk;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$passed = 0;
$failed = 0;
$results = [];

function assertTest(string $category, string $name, bool $condition, string $detail = ''): void
{
    global $passed, $failed, $results;
    if ($condition) {
        $passed++;
        $results[] = ["PASS", $category, $name, $detail];
    } else {
        $failed++;
        $results[] = ["FAIL", $category, $name, $detail];
    }
}

// 1. DATABASE & MIGRATION TESTS
try {
    $pdo = DB::connection()->getPdo();
    assertTest('Database', 'Koneksi PDO Database Aktif', $pdo !== null, 'Driver: ' . DB::connection()->getDriverName());
} catch (\Throwable $e) {
    assertTest('Database', 'Koneksi PDO Database Aktif', false, $e->getMessage());
}

assertTest('Migration', 'Tabel `kiosks` tersedia di database', Schema::hasTable('kiosks'), 'Schema::hasTable("kiosks")');
assertTest('Migration', 'Tabel `orders` tersedia di database', Schema::hasTable('orders'), 'Schema::hasTable("orders")');
assertTest('Migration', 'Kolom utama tabel `orders` lengkap', Schema::hasColumns('orders', [
    'id', 'ticket_code', 'file_name', 'paper_type', 'ink_mode', 'pages', 'total_price', 'status', 'created_at', 'updated_at'
]), 'id, ticket_code, file_name, paper_type, ink_mode, pages, total_price, status, timestamps');
assertTest('Migration', 'Kolom utama tabel `kiosks` lengkap', Schema::hasColumns('kiosks', [
    'id', 'kiosk_code', 'slug', 'name', 'location', 'a4_percentage', 'f4_percentage', 'is_online'
]), 'id, kiosk_code, slug, name, location, a4_percentage, f4_percentage, is_online');

// 2. ELOQUENT MODEL & $FILLABLE TESTS
$orderInstance = new Order();
$kioskInstance = new Kiosk();
assertTest('Model (Zahra)', 'Model App\Models\Order memiliki $fillable', count($orderInstance->getFillable()) >= 10, implode(', ', $orderInstance->getFillable()));
assertTest('Model (Damar)', 'Model App\Models\Kiosk memiliki $fillable', count($kioskInstance->getFillable()) >= 8, implode(', ', $kioskInstance->getFillable()));

// 3. ELOQUENT ORM READ TESTS
$allKiosks = Kiosk::all();
$allOrders = Order::all();
assertTest('Eloquent READ', 'Kiosk::all() membaca 4 mesin kiosk kampus UMM', $allKiosks->count() >= 4, 'Count: ' . $allKiosks->count());
assertTest('Eloquent READ', 'Order::all() membaca 12 antrean pesanan awal', $allOrders->count() >= 12, 'Count: ' . $allOrders->count());

$firstOrder = Order::find(1);
assertTest('Eloquent READ', 'Order::find(1) mengembalikan objek Order pertama', $firstOrder !== null && !empty($firstOrder->ticket_code), 'Tiket: ' . ($firstOrder ? $firstOrder->ticket_code : 'null'));

$specificOrder = Order::where('ticket_code', 'CPT-8492')->first();
assertTest('Eloquent READ', 'Query Order CPT-8492 ditemukan di database', $specificOrder !== null && $specificOrder->file_name === 'Skripsi_BAB_1-5_Final.pdf', 'File: ' . ($specificOrder ? $specificOrder->file_name : 'null'));

// 4. ELOQUENT ORM CREATE, UPDATE, DELETE (CRUD) TESTS
$createdOrder = Order::create([
    'ticket_code' => 'CPT-9901',
    'file_name' => 'Tugas_Praktikum_Laravel_Week9.pdf',
    'file_size' => '2.2 MB',
    'paper_type' => 'a4_80',
    'ink_mode' => 'color_std',
    'pages' => 20,
    'copies' => 1,
    'is_duplex' => true,
    'finishing' => 'steples',
    'kiosk_name' => 'Kiosk Perpustakaan Pusat UMM (Lt. 1)',
    'total_price' => Order::calculatePrice('a4_80', 'color_std', 20, 1, true, 'steples'),
    'pin' => '9901',
    'status' => 'Menunggu Cetak',
    'is_purged' => false,
    'display_time' => '14:00 WIB',
]);
assertTest('Eloquent CREATE', 'Order::create() berhasil menyimpan pesanan baru ke DB', $createdOrder->exists && $createdOrder->id > 0, 'ID DB: ' . $createdOrder->id . ', Tiket: ' . $createdOrder->ticket_code);

$foundCreated = Order::find($createdOrder->id);
assertTest('Eloquent READ', 'Order::find($id) menemukan pesanan yang baru dibuat', $foundCreated !== null && $foundCreated->ticket_code === 'CPT-9901', 'Total: ' . ($foundCreated ? $foundCreated->getFormattedTotal() : ''));

$foundCreated->update([
    'status' => 'Siap Diambil',
    'pages' => 25,
    'total_price' => Order::calculatePrice('a4_80', 'color_std', 25, 1, true, 'steples'),
]);
$reloadedOrder = Order::find($createdOrder->id);
assertTest('Eloquent UPDATE', '$order->update() berhasil memperbarui status & halaman di DB', $reloadedOrder->status === 'Siap Diambil' && (int) $reloadedOrder->pages === 25, 'Status baru: ' . $reloadedOrder->status . ', Halaman: ' . $reloadedOrder->pages);

$tempId = $reloadedOrder->id;
$reloadedOrder->delete();
$deletedCheck = Order::find($tempId);
assertTest('Eloquent DELETE', '$order->delete() berhasil menghapus baris dari DB', $deletedCheck === null, 'Order::find(' . $tempId . ') === null');

// 5. ROUTE & CONTROLLER HTTP TESTS (MODUL ZAHRA & DAMAR)
$reqHome = Request::create('/', 'GET');
$resHome = $kernel->handle($reqHome);
assertTest('Route (Zahra)', 'GET / (HomeController@index) HTTP 200 OK', $resHome->getStatusCode() === 200 && stripos($resHome->getContent(), 'Cepatik') !== false, 'Status: ' . $resHome->getStatusCode());

$reqCetak = Request::create('/cetak', 'GET');
$resCetak = $kernel->handle($reqCetak);
assertTest('Route (Zahra)', 'GET /cetak (CetakController@index) HTTP 200 OK', $resCetak->getStatusCode() === 200 && stripos($resCetak->getContent(), 'Kiosk Perpustakaan Pusat UMM') !== false, 'Status: ' . $resCetak->getStatusCode());

$reqLogin = Request::create('/login', 'GET');
$resLogin = $kernel->handle($reqLogin);
assertTest('Route (Damar)', 'GET /login (AdminController@login) HTTP 200 OK', $resLogin->getStatusCode() === 200 && stripos($resLogin->getContent(), 'Portal Operator') !== false, 'Status: ' . $resLogin->getStatusCode());

$reqAdmin = Request::create('/admin', 'GET');
$resAdmin = $kernel->handle($reqAdmin);
assertTest('Route (Damar)', 'GET /admin (AdminController@index) HTTP 200 OK & Render DB Orders', $resAdmin->getStatusCode() === 200 && stripos($resAdmin->getContent(), 'CPT-8492') !== false, 'Status: ' . $resAdmin->getStatusCode());

$reqApi = Request::create('/api/orders', 'GET');
$resApi = $kernel->handle($reqApi);
$apiJson = json_decode($resApi->getContent(), true);
assertTest('API Sync', 'GET /api/orders mengembalikan JSON daftar pesanan dari DB', $resApi->getStatusCode() === 200 && !empty($apiJson['success']) && count($apiJson['orders']) >= 12, 'Orders JSON count: ' . count($apiJson['orders'] ?? []));

if (php_sapi_name() === 'cli') {
    echo "=================================================================\n";
    echo "   HASIL AUTOMATED TEST SUITE WEEK 9 (LARAVEL 9 + CRUD + DB)\n";
    echo "=================================================================\n";
    foreach ($results as [$status, $cat, $name, $detail]) {
        echo sprintf("[%s] %-16s | %-52s | %s\n", $status, $cat, $name, $detail);
    }
    echo "-----------------------------------------------------------------\n";
    echo sprintf("TOTAL: %d PASSED, %d FAILED (dari %d skenario pengujian)\n", $passed, $failed, $passed + $failed);
    echo "=================================================================\n";
    exit($failed > 0 ? 1 : 0);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Test Suite Week 9 (Laravel + CRUD + Database) - Cepatik</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #0f172a; color: #f8fafc; padding: 32px; }
    .wrap { max-width: 960px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 24px; border: 1px solid #334155; }
    h1 { margin-top: 0; font-size: 22px; color: #38bdf8; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 13.5px; }
    th, td { padding: 10px 12px; border-bottom: 1px solid #334155; text-align: left; }
    th { background: #0f172a; color: #94a3b8; }
    .pass { color: #34d399; font-weight: 700; }
    .fail { color: #f87171; font-weight: 700; }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>✓ Hasil Pengujian Otomatis Week 9 — Laravel 9 + CRUD + Database (<?= $passed; ?>/<?= $passed + $failed; ?> Lulus)</h1>
    <table>
      <thead>
        <tr><th>Status</th><th>Kategori</th><th>Skenario Pengujian</th><th>Detail</th></tr>
      </thead>
      <tbody>
        <?php foreach ($results as [$st, $cat, $nm, $dt]): ?>
          <tr>
            <td class="<?= $st === 'PASS' ? 'pass' : 'fail'; ?>"><?= $st; ?></td>
            <td><?= htmlspecialchars($cat); ?></td>
            <td><?= htmlspecialchars($nm); ?></td>
            <td><code><?= htmlspecialchars($dt); ?></code></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
