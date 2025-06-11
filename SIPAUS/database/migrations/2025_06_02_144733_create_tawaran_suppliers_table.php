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
        Schema::create('tawaran_supplier', function (Blueprint $table) {
            $table->id(); // Default PK 'id' (unsignedBigInteger auto-increment)

            $table->unsignedBigInteger('supplier_id'); // Foreign key ke tabel 'supplier'
            $table->unsignedBigInteger('atk_id');      // Foreign key ke tabel 'atk'
            
            // Kolom ini merujuk ke detail_pengadaan.id (jika detail_pengadaan menggunakan $table->id())
            // Jika detail_pengadaan menggunakan PK lain (misal id_detail_pengadaan), sesuaikan ini.
            $table->unsignedBigInteger('detail_pengadaan_id')->nullable(); 

            $table->decimal('harga_tawaran', 10, 2);
            $table->date('tanggal_tawaran');
            $table->string('status', 50)->default('pending');
            // Tambahkan kolom lain yang relevan jika ada

            $table->timestamps();

            // Definisi Foreign Keys
            // Pastikan 'id_supplier' adalah PK di tabel 'supplier'
            $table->foreign('supplier_id')->references('id_supplier')->on('supplier')->onDelete('cascade');
            // Pastikan 'id_atk' adalah PK di tabel 'atk'
            $table->foreign('atk_id')->references('id_atk')->on('atk')->onDelete('cascade');
            // PENTING: Jika detail_pengadaan.id adalah PK-nya, maka ini harus merujuk ke 'id'
            $table->foreign('detail_pengadaan_id')->references('id')->on('detail_pengadaan')->onDelete('set null'); // Atau 'cascade'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tawaran_supplier');
    }
};
