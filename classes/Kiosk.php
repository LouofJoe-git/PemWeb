<?php

class Kiosk
{
    private string $kioskId;          
    private string $locationName;     
    private array $paperStatus;       
    private bool $isOnline;           

    /**
     * @param string $kioskId          
     * @param string $locationName     
     * @param array  $paperStatus      
     * @param bool|int $isOnlineOrLockers
     * @param bool   $isOnline    
     */
    public function __construct(
        string $kioskId,
        string $locationName,
        array $paperStatus,
        $isOnlineOrLockers = true,
        bool $isOnline = true
    ) {
        $this->kioskId = $kioskId;
        $this->locationName = $locationName;
        $this->paperStatus = $paperStatus;
        if (is_bool($isOnlineOrLockers)) {
            $this->isOnline = $isOnlineOrLockers;
        } else {
            $this->isOnline = $isOnline;
        }
    }

    public function getKioskId(): string
    {
        return $this->kioskId;
    }

    public function getLocationName(): string
    {
        return $this->locationName;
    }

    public function getPaperStatus(): array
    {
        return $this->paperStatus;
    }

    public function isOnline(): bool
    {
        return $this->isOnline;
    }

    public function getA4Percentage(): int
    {
        return isset($this->paperStatus['a4']) ? (int) $this->paperStatus['a4'] : 0;
    }

    public function getF4Percentage(): int
    {
        return isset($this->paperStatus['f4']) ? (int) $this->paperStatus['f4'] : 0;
    }

    public function getStatusLabel(): string
    {
        return $this->isOnline ? 'Online & Siap Cetak' : 'Offline / Maintenance';
    }

    public function getStatusBadgeClass(): string
    {
        return $this->isOnline ? 'status-online' : 'status-offline';
    }

    /**
     * Helper kompatibilitas mundur jika ada pemanggil lama
     */
    public function getAvailableLockers(): int
    {
        return 0;
    }
}

