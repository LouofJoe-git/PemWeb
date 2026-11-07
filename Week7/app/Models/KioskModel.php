<?php
/**
 * KioskModel.php - Cepatik MVC Model
 * 
 * Model ini bertanggung jawab atas data mesin kiosk Cepatik,
 * status ketersediaan kertas (A4 & F4), lokasi fisik,
 * status operasional online/offline, dan integrasi objek Kiosk.
 */

require_once __DIR__ . '/../../classes/Kiosk.php';
require_once __DIR__ . '/../../includes/data.php';

class KioskModel
{
    /** @var Kiosk[] */
    private array $kiosks;
    private array $landingKiosks;

    public function __construct()
    {
        global $daftar_kiosk;
        $this->landingKiosks = $daftar_kiosk ?? [];

        // Inisialisasi daftar objek Kiosk domain untuk panel admin & SSR
        $this->kiosks = [
            new Kiosk(
                'KSK-01',
                'Kiosk Perpustakaan Pusat UMM (Lt. 1)',
                ['a4' => 88, 'f4' => 65],
                true
            ),
            new Kiosk(
                'KSK-02',
                'Kiosk GKB 1 UMM Ganesha',
                ['a4' => 95, 'f4' => 82],
                true
            ),
            new Kiosk(
                'KSK-03',
                'Kiosk GKB 2 (Lobby Lt. 1)',
                ['a4' => 70, 'f4' => 45],
                true
            ),
            new Kiosk(
                'KSK-04',
                'Kiosk Lab Informatika & Komputer (Lt. 6)',
                ['a4' => 18, 'f4' => 20],
                false
            ),
        ];
    }

    /**
     * Mengambil seluruh instance objek Kiosk untuk keperluan Admin SSR
     * @return Kiosk[]
     */
    public function getAllKiosks(): array
    {
        return $this->kiosks;
    }

    /**
     * Mengambil daftar kiosk terformat untuk tampilan Landing Page & Wizard
     */
    public function getLandingKiosks(): array
    {
        return $this->landingKiosks;
    }

    /**
     * Mencari kiosk berdasarkan Kiosk ID (cth: 'KSK-01')
     */
    public function getKioskById(string $id): ?Kiosk
    {
        $id = strtoupper(trim($id));
        foreach ($this->kiosks as $kiosk) {
            if (strtoupper($kiosk->getKioskId()) === $id) {
                return $kiosk;
            }
        }
        return null;
    }

    /**
     * Menghitung jumlah kiosk yang aktif (online)
     * @param Kiosk[]|null $kiosks
     */
    public function countOnline(?array $kiosks = null): int
    {
        $list = $kiosks ?? $this->kiosks;
        $count = 0;
        foreach ($list as $kiosk) {
            if ($kiosk->isOnline()) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Menghitung total kiosk
     * @param Kiosk[]|null $kiosks
     */
    public function countTotal(?array $kiosks = null): int
    {
        $list = $kiosks ?? $this->kiosks;
        return count($list);
    }
}
