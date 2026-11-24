<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent Model: Order
 * Modul Anggota A (Zahra Zusseta Stiawan) - Representasi Tabel `orders`
 */
class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    /**
     * Daftar kolom yang diizinkan untuk Mass Assignment (Order::create / $order->update)
     */
    protected $fillable = [
        'ticket_code',
        'file_name',
        'file_size',
        'paper_type',
        'ink_mode',
        'pages',
        'copies',
        'is_duplex',
        'finishing',
        'kiosk_name',
        'total_price',
        'pin',
        'status',
        'notes',
        'is_purged',
        'display_time',
    ];

    protected $casts = [
        'pages' => 'integer',
        'copies' => 'integer',
        'is_duplex' => 'boolean',
        'total_price' => 'integer',
        'is_purged' => 'boolean',
    ];

    /**
     * Hitung total biaya cetak berdasarkan spesifikasi dokumen
     */
    public static function calculatePrice(
        string $paperType,
        string $inkMode,
        int $pages,
        int $copies = 1,
        bool $isDuplex = false,
        string $finishing = 'none'
    ): int {
        $inkRates = [
            'bw' => 350,
            'color_std' => 1000,
            'color_full' => 2000,
        ];
        $paperRates = [
            'a4_75' => 0,
            'a4_80' => 50,
            'f4_80' => 100,
        ];
        $finishingRates = [
            'none' => 0,
            'steples' => 1000,
            'lakban' => 3500,
        ];

        $baseInk = $inkRates[$inkMode] ?? 350;
        $addPaper = $paperRates[$paperType] ?? 0;
        $costPerPage = $baseInk + $addPaper;

        $totalPages = max(1, $pages) * max(1, $copies);
        $subtotal = $costPerPage * $totalPages;

        if ($isDuplex && $pages > 1) {
            $subtotal = (int) round($subtotal * 0.95);
        }

        $finishingCost = ($finishingRates[$finishing] ?? 0) * max(1, $copies);
        return $subtotal + $finishingCost;
    }

    public function getId(): string
    {
        return $this->ticket_code;
    }

    public function getFileName(): string
    {
        return $this->file_name;
    }

    public function getPaperType(): string
    {
        return $this->paper_type;
    }

    public function getInkMode(): string
    {
        return $this->ink_mode;
    }

    public function getPages(): int
    {
        return (int) $this->pages;
    }

    public function isDuplex(): bool
    {
        return (bool) $this->is_duplex;
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
        return $this->display_time ?: ($this->created_at ? $this->created_at->format('H:i') . ' WIB' : 'Hari ini');
    }

    public function calculateTotal(): int
    {
        if ($this->total_price > 0) {
            return (int) $this->total_price;
        }
        return self::calculatePrice(
            $this->paper_type,
            $this->ink_mode,
            (int) $this->pages,
            (int) ($this->copies ?: 1),
            (bool) $this->is_duplex,
            $this->finishing
        );
    }

    public function getFormattedTotal(): string
    {
        return 'Rp ' . number_format($this->calculateTotal(), 0, ',', '.');
    }

    public function getPaperLabel(): string
    {
        $names = [
            'a4_75' => 'A4 Standard (75 GSM)',
            'a4_80' => 'A4 Tebal (80 GSM)',
            'f4_80' => 'F4 Folio (80 GSM)',
        ];
        return $names[$this->paper_type] ?? $this->paper_type;
    }

    public function getInkLabel(): string
    {
        $names = [
            'bw' => 'Hitam Putih',
            'color_std' => 'Warna Standar',
            'color_full' => 'Full Color Foto',
        ];
        return $names[$this->ink_mode] ?? $this->ink_mode;
    }

    public function getFinishingLabel(): string
    {
        $names = [
            'none' => 'Tanpa Jilid',
            'steples' => 'Jilid Steples Sudut',
            'lakban' => 'Jilid Lakban Mika',
        ];
        return $names[$this->finishing] ?? $this->finishing;
    }

    public function getPrintSpecs(): string
    {
        $duplexLabel = $this->is_duplex ? 'Duplex (Bolak-balik)' : 'Simplex (1 Sisi)';
        return "{$this->getPaperLabel()} · {$this->getInkLabel()} · {$duplexLabel} · {$this->getFinishingLabel()} · {$this->pages} Hlm";
    }

    public function getFileExtension(): string
    {
        $ext = pathinfo($this->file_name, PATHINFO_EXTENSION);
        return !empty($ext) ? strtoupper($ext) : 'PDF';
    }

    public function toClientArray(): array
    {
        return [
            'db_id' => $this->id,
            'id' => $this->ticket_code,
            'waktu' => $this->getTimestamp(),
            'tanggal' => $this->created_at ? $this->created_at->format('d M Y') : '27 Okt 2026',
            'file' => [
                'nama' => $this->file_name,
                'ukuran' => $this->file_size ?: '1.5 MB',
                'halaman' => (int) $this->pages,
                'ekstensi' => $this->getFileExtension(),
                'terhapus' => (bool) $this->is_purged || $this->status === 'Selesai',
            ],
            'kertas' => $this->getPaperLabel(),
            'tinta' => $this->getInkLabel(),
            'duplex' => (bool) $this->is_duplex,
            'rangkap' => (int) ($this->copies ?: 1),
            'jilid' => $this->getFinishingLabel(),
            'total' => $this->calculateTotal(),
            'pin' => $this->pin,
            'kiosk' => $this->kiosk_name,
            'status' => $this->status,
        ];
    }
}
