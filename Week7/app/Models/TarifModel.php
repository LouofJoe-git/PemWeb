<?php
/**
 * TarifModel.php - Cepatik MVC Model
 * 
 * Model ini bertanggung jawab atas pengelolaan data tarif cetak,
 * pilihan spesifikasi dokumen (kertas, tinta, finishing/jilid),
 * informasi profil sistem, serta kalkulasi estimasi biaya cetak.
 */

require_once __DIR__ . '/../../includes/data.php';
require_once __DIR__ . '/../../includes/functions.php';

class TarifModel
{
    private array $siteInfo;
    private array $pilihanKertas;
    private array $pilihanWarna;
    private array $pilihanJilid;
    private array $daftarKeunggulan;

    public function __construct()
    {
        global $site_info, $pilihan_kertas, $pilihan_warna, $pilihan_jilid, $daftar_keunggulan;

        $this->siteInfo = $site_info ?? [];
        $this->pilihanKertas = $pilihan_kertas ?? [];
        $this->pilihanWarna = $pilihan_warna ?? [];
        $this->pilihanJilid = $pilihan_jilid ?? [];
        $this->daftarKeunggulan = $daftar_keunggulan ?? [];
    }

    /**
     * Mendapatkan informasi profil & konfigurasi website Cepatik
     */
    public function getSiteInfo(): array
    {
        return $this->siteInfo;
    }

    /**
     * Mendapatkan daftar opsi jenis & ketebalan kertas
     */
    public function getPilihanKertas(): array
    {
        return $this->pilihanKertas;
    }

    /**
     * Mendapatkan daftar opsi mode tinta cetak
     */
    public function getPilihanWarna(): array
    {
        return $this->pilihanWarna;
    }

    /**
     * Mendapatkan daftar opsi penjilidan & finishing
     */
    public function getPilihanJilid(): array
    {
        return $this->pilihanJilid;
    }

    /**
     * Mendapatkan daftar poin keunggulan Cepatik
     */
    public function getKeunggulan(): array
    {
        return $this->daftarKeunggulan;
    }

    /**
     * Menghitung estimasi rincian biaya cetak berdasarkan spesifikasi
     * Menggunakan validasi batas nilai minimum dan logika diskon duplex 5%.
     *
     * @param int    $halaman   Jumlah halaman dokumen (min: 1)
     * @param string $idWarna   ID opsi warna ('bw', 'color_std', 'color_full')
     * @param string $idKertas  ID opsi kertas ('a4_75', 'a4_80', 'f4_80')
     * @param int    $rangkap   Jumlah rangkap/salinan (min: 1)
     * @param bool   $isDuplex  Opsi cetak bolak-balik
     * @param string $idJilid   ID opsi jilid ('none', 'steples', 'lakban')
     * @return array Rincian estimasi biaya cetak
     */
    public function hitungEstimasi(
        int $halaman = 15,
        string $idWarna = 'bw',
        string $idKertas = 'a4_75',
        int $rangkap = 1,
        bool $isDuplex = false,
        string $idJilid = 'none'
    ): array {
        // Validasi input
        $halaman = max(1, $halaman);
        $rangkap = max(1, $rangkap);

        // Panggil fungsi inti kalkulasi yang sudah teruji
        return hitung_estimasi_cetak($halaman, $idWarna, $idKertas, $rangkap, $isDuplex, $idJilid);
    }

    /**
     * Format angka ke format mata uang Rupiah standar Indonesia
     */
    public function formatRupiah($nominal): string
    {
        return format_rupiah($nominal);
    }
}
