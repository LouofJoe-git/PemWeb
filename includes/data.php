<?php
$site_info = [
    'nama' => 'Cepatik',
    'tagline' => 'Self-Service Cloud Printing Kiosk',
    'deskripsi' => 'Cepatik - Self-Service Cloud Printing Kiosk & Cetak Mandiri Modern untuk Ekosistem Kampus.',
    'kiosk_aktif_count' => 42,
    'kampus_mitra_count' => 40,
    'kecepatan_ppm' => 35,
    'resolusi_dpi' => '1200 x 1200 DPI',
    'retensi_jam' => 2,
    'kontak_wa' => '+62 812-3456-7890',
    'kontak_wa_link' => 'https://wa.me/6281234567890',
    'tahun' => 2026,
    'perusahaan' => 'PT Cepatik Teknologi Nusantara'
];

$pilihan_kertas = [
    'a4_75' => [
        'id' => 'a4_75',
        'nama' => 'A4 Standard',
        'gsm' => '75 GSM Harian',
        'tambah' => 0,
        'label_harga' => '+Rp 0',
        'deskripsi_lengkap' => 'A4 Standard (75 GSM)',
        'default' => true
    ],
    'a4_80' => [
        'id' => 'a4_80',
        'nama' => 'A4 Tebal',
        'gsm' => '80 GSM Skripsi',
        'tambah' => 50,
        'label_harga' => '+Rp 50/lbr',
        'deskripsi_lengkap' => 'A4 Tebal (80 GSM)',
        'default' => false
    ],
    'f4_80' => [
        'id' => 'f4_80',
        'nama' => 'F4 Folio',
        'nama_alt' => 'F4 / Folio',
        'gsm' => '80 GSM Dokumen',
        'tambah' => 100,
        'label_harga' => '+Rp 100/lbr',
        'deskripsi_lengkap' => 'F4 Folio (80 GSM)',
        'default' => false
    ]
];

$pilihan_warna = [
    'bw' => [
        'id' => 'bw',
        'nama' => 'Hitam Putih',
        'deskripsi' => 'Teks makalah & skripsi',
        'deskripsi_singkat' => 'Teks makalah',
        'harga' => 350,
        'label_harga' => 'Rp 350',
        'label_harga_per_lbr' => 'Rp 350/lbr',
        'nama_lengkap' => 'Hitam Putih (B/W)',
        'default' => true
    ],
    'color_std' => [
        'id' => 'color_std',
        'nama' => 'Warna Standar',
        'deskripsi' => 'Grafik, diagram & logo',
        'deskripsi_singkat' => 'Grafik & diagram',
        'harga' => 1000,
        'label_harga' => 'Rp 1.000',
        'label_harga_per_lbr' => 'Rp 1.000/lbr',
        'nama_lengkap' => 'Warna Standar',
        'default' => false
    ],
    'color_full' => [
        'id' => 'color_full',
        'nama' => 'Full Color Foto',
        'nama_pendek' => 'Full Color',
        'deskripsi' => 'Foto, poster & cover',
        'deskripsi_singkat' => 'Foto & poster',
        'harga' => 2000,
        'label_harga' => 'Rp 2.000',
        'label_harga_per_lbr' => 'Rp 2.000/lbr',
        'nama_lengkap' => 'Full Color Foto',
        'default' => false
    ]
];

$pilihan_jilid = [
    'none' => [
        'id' => 'none',
        'nama' => 'Tanpa Jilid',
        'deskripsi' => 'Lepasan biasa',
        'harga' => 0,
        'label_harga' => '+Rp 0',
        'default' => true
    ],
    'steples' => [
        'id' => 'steples',
        'nama' => 'Steples Pojok',
        'nama_alt' => 'Steples Sudut',
        'deskripsi' => 'Rapih sudut kiri atas',
        'harga' => 500,
        'label_harga' => '+Rp 500',
        'default' => false
    ],
    'lakban' => [
        'id' => 'lakban',
        'nama' => 'Jilid Lakban Mika',
        'deskripsi' => 'Mika bening + lakban hitam',
        'harga' => 3500,
        'label_harga' => '+Rp 3.500',
        'default' => false
    ]
];

$daftar_kiosk = [
    [
        'id' => 'ui_depok',
        'nama' => 'Kiosk Perpustakaan UI',
        'nama_lengkap_app' => 'Kiosk Perpustakaan UI',
        'lokasi' => 'Lantai 1, Lobby Depan Starbucks Kampus Depok',
        'jarak' => '0.3 km',
        'stok_a4' => 920,
        'stok_f4' => 450,
        'jam_buka' => '24 Jam',
        'status' => 'online', 
        'maps_url' => 'https://maps.google.com/?q=Perpustakaan+UI+Depok',
        'default' => true
    ],
    [
        'id' => 'itb_ganesha',
        'nama' => 'Mitra Barokah ITB',
        'nama_lengkap_app' => 'Mitra Barokah ITB',
        'lokasi' => 'Jl. Ganesha No. 10 (Samping Gerbang Utama ITB)',
        'jarak' => '0.8 km',
        'stok_a4' => 1400,
        'stok_f4' => 620,
        'jam_buka' => '07.00 - 23.00',
        'status' => 'online',
        'maps_url' => 'https://maps.google.com/?q=Jl.+Ganesha+No.+10+Bandung',
        'default' => false
    ],
    [
        'id' => 'ugm_gelanggang',
        'nama' => 'Kiosk Gelanggang UGM',
        'nama_lengkap_app' => 'Kiosk Gelanggang UGM',
        'lokasi' => 'Student Center UGM, Sayap Barat Lt. Dasar',
        'jarak' => '1.2 km',
        'stok_a4' => 850,
        'stok_f4' => 110,
        'jam_buka' => '24 Jam',
        'status' => 'online',
        'maps_url' => 'https://maps.google.com/?q=Gelanggang+Mahasiswa+UGM',
        'default' => false
    ]
];

$daftar_antrean = [
    [
        'id' => 'CPT-8492',
        'nama_file' => 'Skripsi_BAB_1-5_Final.pdf',
        'tipe' => 'PDF',
        'keterangan' => '24 Lembar · A4 Duplex',
        'status_kode' => 'ready',
        'status_teks' => 'Siap Diambil di Kiosk',
        'pin' => 'CPT-8492',
        'progress' => 100
    ],
    [
        'id' => 'CPT-7721',
        'nama_file' => 'Jurnal_Ilmiah_TI.docx',
        'tipe' => 'DOC',
        'keterangan' => '12 Lembar · Hitam Putih',
        'status_kode' => 'spooling',
        'status_teks' => 'Spooling ke Kiosk...',
        'pin' => null,
        'progress' => 85
    ],
    [
        'id' => 'CPT-6340',
        'nama_file' => 'Proposal_Proyek_Akhir.pdf',
        'tipe' => 'PPT',
        'keterangan' => '8 Lembar · Full Color',
        'status_kode' => 'pending',
        'status_teks' => 'Menunggu Pembayaran QRIS',
        'pin' => null,
        'progress' => 0
    ]
];

$daftar_keunggulan = [
    [
        'id' => 'privacy',
        'judul' => 'Privasi Berkas Terjamin',
        'deskripsi' => 'Dokumen skripsi dan data pribadi dienkripsi TLS 256-bit dan otomatis terhapus permanen dari server 2 jam setelah selesai cetak.',
        'tag' => '✓ Zero Data Retention',
        'tag_style' => 'color: #059669;',
        'icon_class' => 'green',
        'svg_type' => 'shield'
    ],
    [
        'id' => 'speed',
        'judul' => 'Laserjet Cepat 35 ppm',
        'deskripsi' => 'Kiosk dilengkapi printer laserjet enterprise berkecepatan tinggi dengan toner tahan air dan sensor presisi anti-paper-jam.',
        'tag' => '✓ Resolusi 1200 x 1200 DPI',
        'tag_style' => 'color: var(--primary);',
        'icon_class' => 'coral',
        'svg_type' => 'printer'
    ],
    [
        'id' => 'access',
        'judul' => 'Akses Kiosk 24 Jam',
        'deskripsi' => 'Mesin kiosk mandiri tersebar di perpustakaan, kantin, dan mitra kampus. Deadline tugas subuh pun tetap bisa diambil kapan saja.',
        'tag' => '✓ 24/7 Self-Pickup',
        'tag_style' => '',
        'icon_class' => 'gray',
        'svg_type' => 'clock'
    ]
];
