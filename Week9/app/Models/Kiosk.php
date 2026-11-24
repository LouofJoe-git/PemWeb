<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent Model: Kiosk
 * Modul Anggota B (Damar Ghulamul Abror) - Representasi Tabel `kiosks`
 */
class Kiosk extends Model
{
    use HasFactory;

    protected $table = 'kiosks';

    /**
     * Daftar kolom yang diizinkan untuk Mass Assignment (Kiosk::create / $kiosk->update)
     */
    protected $fillable = [
        'kiosk_code',
        'slug',
        'name',
        'location',
        'distance',
        'paper_a4',
        'paper_f4',
        'a4_percentage',
        'f4_percentage',
        'operating_hours',
        'maps_url',
        'is_online',
        'is_default',
    ];

    protected $casts = [
        'paper_a4' => 'integer',
        'paper_f4' => 'integer',
        'a4_percentage' => 'integer',
        'f4_percentage' => 'integer',
        'is_online' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function getKioskId(): string
    {
        return $this->kiosk_code;
    }

    public function getLocationName(): string
    {
        return $this->name;
    }

    public function isOnline(): bool
    {
        return (bool) $this->is_online;
    }

    public function getA4Percentage(): int
    {
        return (int) $this->a4_percentage;
    }

    public function getF4Percentage(): int
    {
        return (int) $this->f4_percentage;
    }

    public function getStatusLabel(): string
    {
        return $this->is_online ? 'Online & Siap Cetak' : 'Offline / Maintenance';
    }

    public function getStatusBadgeClass(): string
    {
        return $this->is_online ? 'status-online' : 'status-offline';
    }
}
