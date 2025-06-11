<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesan_atks', function (Blueprint $table) {
            $table->id(); // Default PK 'id' (unsignedBigInteger auto-increment)

            $table->unsignedBigInteger('supplier_id')->nullable(); // Foreign key ke tabel 'users' (supplier)
            $table->unsignedBigInteger('atk_id')->nullable();       // Foreign key ke tabel 'atk'
            
            // PENTING: Foreign key ke supplier_katalog.id atau supplier_katalog.id_katalog
            // Jika supplier_katalog menggunakan $table->id(), maka ini harus 'id'
            $table->unsignedBigInteger('id_supplier_katalog')->nullable(); // Asumsi nama kolom FK

            $table->integer('jumlah_pesan');
            $table->date('tanggal_pesan');
            $table->string('status', 50)->default('menunggu');
            // Tambahkan kolom nama_supplier dan nama_barang jika disimpan sebagai string
            $table->string('nama_supplier')->nullable();
            $table->string('nama_barang')->nullable();

            $table->timestamps();

            // Definisi Foreign Keys
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('atk_id')->references('id_atk')->on('atk')->onDelete('set null');
            // PENTING: Ubah 'id_katalog' menjadi 'id' jika supplier_katalog menggunakan $table->id()
            $table->foreign('id_supplier_katalog')->references('id')->on('supplier_katalog')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesan_atks');
    }
};
