<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_kiosks_table
 * Modul Anggota B (Damar Ghulamul Abror) - Manajemen Mesin Kiosk UMM
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kiosks', function (Blueprint $table) {
            $table->id();
            $table->string('kiosk_code')->unique();      // e.g. KSK-01 / umm_perpus
            $table->string('slug')->index();             // e.g. umm_perpus
            $table->string('name');                      // Nama lokasi Kiosk UMM
            $table->text('location');                    // Deskripsi alamat gedung detail
            $table->string('distance')->default('0.3 km');
            $table->integer('paper_a4')->default(900);   // Jumlah lembar stok A4
            $table->integer('paper_f4')->default(450);   // Jumlah lembar stok F4
            $table->integer('a4_percentage')->default(88);
            $table->integer('f4_percentage')->default(65);
            $table->string('operating_hours')->default('24 Jam');
            $table->string('maps_url')->default('https://maps.google.com/?q=Universitas+Muhammadiyah+Malang');
            $table->boolean('is_online')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kiosks');
    }
};
