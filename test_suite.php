<?php

require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$total_tests = 0;
$passed_tests = 0;

function assert_test($description, $condition) {
    global $errors, $total_tests, $passed_tests;
    $total_tests++;
    if ($condition) {
        $passed_tests++;
        echo "  [PASS] $description\n";
    } else {
        $errors[] = $description;
        echo "  [FAIL] $description\n";
    }
}

echo "====================================================\n";
echo "1. PENGUJIAN DATA & ARRAY PHP\n";
echo "====================================================\n";

assert_test("Array pilihan_kertas memiliki minimal 3 opsi", count($pilihan_kertas) >= 3);
assert_test("Array pilihan_warna memiliki opsi bw, color_std, color_full", 
    isset($pilihan_warna['bw']) && isset($pilihan_warna['color_std']) && isset($pilihan_warna['color_full']));
assert_test("Array pilihan_jilid memiliki opsi none, steples, lakban", 
    isset($pilihan_jilid['none']) && isset($pilihan_jilid['steples']) && isset($pilihan_jilid['lakban']));
assert_test("Array daftar_kiosk memiliki 3 titik kampus", count($daftar_kiosk) === 3);
assert_test("Array daftar_antrean memiliki 3 antrean", count($daftar_antrean) === 3);

echo "\n====================================================\n";
echo "2. PENGUJIAN FUNCTION FORMAT RUPIAH\n";
echo "====================================================\n";

assert_test("Format 5250 menjadi 'Rp 5.250'", format_rupiah(5250) === 'Rp 5.250');
assert_test("Format 0 menjadi 'Rp 0'", format_rupiah(0) === 'Rp 0');
assert_test("Format 1000000 menjadi 'Rp 1.000.000'", format_rupiah(1000000) === 'Rp 1.000.000');

echo "\n====================================================\n";
echo "3. PENGUJIAN FUNCTION KALKULATOR ESTIMASI CETAK\n";
echo "====================================================\n";

$res1 = hitung_estimasi_cetak(15, 'bw', 'a4_75', 1, false, 'none');
assert_test("Skenario 1 (Default): 15 hal BW A4_75 Simplex = Rp 5.250", $res1['total'] === 5250);
assert_test("Skenario 1 tarif per lembar = 350", $res1['tarif_per_lembar'] === 350);

$res2 = hitung_estimasi_cetak(15, 'bw', 'a4_75', 1, true, 'none');
assert_test("Skenario 2 (Duplex 5%): round(5250 * 0.95) = Rp 4.988", $res2['total'] === 4988);
assert_test("Skenario 2 diskon duplex = 262", $res2['diskon_duplex'] === 262);

$res3 = hitung_estimasi_cetak(50, 'bw', 'a4_80', 2, false, 'lakban');
assert_test("Skenario 3 (Skripsi Jilid Lakban): Total = Rp 43.500", $res3['total'] === 43500);

$res4 = hitung_estimasi_cetak(10, 'color_full', 'f4_80', 1, false, 'steples');
assert_test("Skenario 4 (Color Full F4 Steples): Total = Rp 21.500", $res4['total'] === 21500);

echo "\n====================================================\n";
echo "4. PENGUJIAN CONDITIONAL RENDERING\n";
echo "====================================================\n";

$badge_online = render_kiosk_status_badge('online');
assert_test("Badge online mengandung class 'kiosk-badge-online'", strpos($badge_online, 'kiosk-badge-online') !== false);

$stok_banyak = render_stok_kertas_label(920);
assert_test("Stok 920 diformat '920 lbr'", strpos($stok_banyak, '920 lbr') !== false);

$stok_habis = render_stok_kertas_label(0);
assert_test("Stok 0 diformat 'Habis'", strpos($stok_habis, 'Habis') !== false);

echo "\n====================================================\n";
echo "5. PENGUJIAN RENDERING HALAMAN INDEX.PHP & CETAK.PHP\n";
echo "====================================================\n";

ob_start();
include __DIR__ . '/index.php';
$html_index = ob_get_clean();

assert_test("index.php ter-render tanpa error dan memiliki DOCTYPE", strpos($html_index, '<!DOCTYPE html>') !== false);
assert_test("index.php memiliki tautan ke cetak.php", strpos($html_index, 'href="cetak.php"') !== false);
assert_test("index.php merender titik kiosk UI Depok", strpos($html_index, 'Kiosk Perpustakaan UI') !== false);
assert_test("index.php merender total estimasi awal Rp 5.250", strpos($html_index, 'Rp 5.250') !== false);

ob_start();
include __DIR__ . '/cetak.php';
$html_cetak = ob_get_clean();

assert_test("cetak.php ter-render tanpa error dan memiliki DOCTYPE", strpos($html_cetak, '<!DOCTYPE html>') !== false);
assert_test("cetak.php memiliki tombol kembali ke index.php", strpos($html_cetak, 'href="index.php"') !== false);
assert_test("cetak.php merender radio kertas dari PHP array", strpos($html_cetak, 'name="app_paper"') !== false);
assert_test("cetak.php merender radio warna dari PHP array", strpos($html_cetak, 'name="app_color"') !== false);
assert_test("cetak.php merender radio jilid dari PHP array", strpos($html_cetak, 'name="app_jilid"') !== false);
assert_test("cetak.php merender radio kiosk dari PHP array", strpos($html_cetak, 'name="app_kiosk"') !== false);

echo "\n====================================================\n";
echo "RINGKASAN HASIL PENGUJIAN: $passed_tests / $total_tests BERHASIL\n";
if (empty($errors)) {
    echo "STATUS: SEMUA FITUR & LOGIKA PHP BERJALAN SEMPURNA!\n";
} else {
    echo "STATUS: TERDAPAT " . count($errors) . " KESALAHAN.\n";
}
echo "====================================================\n";
