<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_orders_table
 * Modul Anggota A (Zahra Zusseta Stiawan) - Manajemen Pesanan Cetak & Tiket
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();     // e.g. CPT-8492
            $table->string('file_name');                 // e.g. Skripsi_BAB_1-5_Final.pdf
            $table->string('file_size')->default('1.5 MB');
            $table->string('paper_type')->default('a4_75');
            $table->string('ink_mode')->default('bw');
            $table->integer('pages')->default(1);
            $table->integer('copies')->default(1);
            $table->boolean('is_duplex')->default(false);
            $table->string('finishing')->default('none');
            $table->string('kiosk_name')->default('Kiosk Perpustakaan Pusat UMM (Lt. 1)');
            $table->integer('total_price')->default(350);
            $table->string('pin')->default('8492');
            $table->string('status')->default('Menunggu Cetak');
            $table->text('notes')->nullable();
            $table->boolean('is_purged')->default(false);
            $table->string('display_time')->default('Hari ini');
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
        Schema::dropIfExists('orders');
    }
};
