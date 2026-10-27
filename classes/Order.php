<?php


class Order
{
    private string $id;           
    private string $fileName;     
    private string $paperType;   
    private string $inkMode;     
    private int $pages;           
    private bool $isDuplex;       
    private string $finishing;    
    private string $pin;          
    private string $status;       
    private string $timestamp;    

    /**
     * @param string $id          Kode tiket (e.g. CPT-8492)
     * @param string $fileName    
     * @param string $paperType   
     * @param string $inkMode     
     * @param int    $pages       
     * @param bool   $isDuplex    
     * @param string $finishing   
     * @param string $statusOrPin Status pesanan atau PIN kompatibilitas
     * @param string $status      Status antrean
     * @param string|null $timestamp 
     */
    public function __construct(
        string $id,
        string $fileName,
        string $paperType,
        string $inkMode,
        int $pages,
        bool $isDuplex = false,
        string $finishing = 'none',
        string $statusOrPin = 'Menunggu Cetak',
        string $status = 'Menunggu Cetak',
        ?string $timestamp = null
    ) {
        $this->id = strtoupper(trim($id));
        $this->fileName = $fileName;
        $this->paperType = $paperType;
        $this->inkMode = $inkMode;
        $this->pages = max(1, $pages);
        $this->isDuplex = $isDuplex;
        $this->finishing = $finishing;
        
        // Cek apakah parameter ke-8 adalah status langsung atau PIN lama
        $knownStatuses = ['Menunggu Cetak', 'Sedang Diproses', 'Siap Diambil', 'Siap di Loker', 'Selesai'];
        if (in_array($statusOrPin, $knownStatuses)) {
            $rawStatus = $statusOrPin;
            $this->pin = $this->id;
            $this->timestamp = ($status !== 'Menunggu Cetak') ? $status : ($timestamp ?? date('d M Y, H:i'));
        } else {
            $this->pin = !empty($statusOrPin) ? $statusOrPin : $this->id;
            $rawStatus = $status;
            $this->timestamp = $timestamp ?? date('d M Y, H:i');
        }

        $this->status = ($rawStatus === 'Siap di Loker') ? 'Siap Diambil' : $rawStatus;
    }

    /**
     * @return int
     */
    public function calculateTotal(): int
    {
        $inkRates = [
            'bw' => 350,
            'color_std' => 1000,
            'color_full' => 2000
        ];
        $baseInkCost = $inkRates[$this->inkMode] ?? 350;

        $paperRates = [
            'a4_75' => 0,
            'a4_80' => 50,
            'f4_80' => 100
        ];
        $paperAddCost = $paperRates[$this->paperType] ?? 0;

        $costPerPage = $baseInkCost + $paperAddCost;
        $subtotal = $costPerPage * $this->pages;

        if ($this->isDuplex && $this->pages > 1) {
            $subtotal = (int) round($subtotal * 0.95);
        }

        $finishingRates = [
            'none' => 0,
            'steples' => 1000,
            'lakban' => 3500
        ];
        $finishingCost = $finishingRates[$this->finishing] ?? 0;

        return $subtotal + $finishingCost;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getPaperType(): string
    {
        return $this->paperType;
    }

    public function getInkMode(): string
    {
        return $this->inkMode;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function isDuplex(): bool
    {
        return $this->isDuplex;
    }

    public function getFinishing(): string
    {
        return $this->finishing;
    }

    public function getPin(): string
    {
        return $this->pin;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }


    public function getFormattedTotal(): string
    {
        return 'Rp ' . number_format($this->calculateTotal(), 0, ',', '.');
    }


    public function getPrintSpecs(): string
    {
        $paperNames = [
            'a4_75' => 'A4 75 GSM',
            'a4_80' => 'A4 80 GSM',
            'f4_80' => 'F4 Folio 80g'
        ];
        $paperLabel = $paperNames[$this->paperType] ?? strtoupper($this->paperType);

        $inkNames = [
            'bw' => 'Hitam Putih',
            'color_std' => 'Warna Standar',
            'color_full' => 'Full Color Foto'
        ];
        $inkLabel = $inkNames[$this->inkMode] ?? $this->inkMode;

        $duplexLabel = $this->isDuplex ? 'Duplex (Bolak-balik)' : 'Simplex (1 Sisi)';

        $finishingNames = [
            'none' => 'Tanpa Jilid',
            'steples' => 'Jilid Steples',
            'lakban' => 'Jilid Lakban Mika'
        ];
        $finishingLabel = $finishingNames[$this->finishing] ?? 'Tanpa Jilid';

        return "{$paperLabel} · {$inkLabel} · {$duplexLabel} · {$finishingLabel} · {$this->pages} Hlm";
    }


    public function getFileExtension(): string
    {
        $ext = pathinfo($this->fileName, PATHINFO_EXTENSION);
        return !empty($ext) ? strtoupper($ext) : 'PDF';
    }


    public function getStatusBadgeClass(): string
    {
        switch ($this->status) {
            case 'Siap Diambil':
            case 'Siap di Loker':
                return 'badge-ready';
            case 'Sedang Diproses':
                return 'badge-processing';
            case 'Selesai':
                return 'badge-completed';
            case 'Menunggu Cetak':
            default:
                return 'badge-waiting';
        }
    }
}
