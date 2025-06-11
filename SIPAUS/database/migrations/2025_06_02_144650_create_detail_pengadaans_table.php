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
        Schema::create('detail_pengadaan', function (Blueprint $table) {
            // PENTING: Gunakan $table->bigIncrements('id_detail_pengadaan')->primary();
            // Jika Anda ingin nama PK-nya id_detail_pengadaan
            $table->id(); // Default PK 'id' (unsignedBigInteger auto-increment)
            // Jika Anda menggunakan $table->id(), maka foreign key di tawaran_supplier harus merujuk ke 'id', bukan 'id_detail_pengadaan'

            $table->unsignedBigInteger('pengadaan_id'); // Foreign key ke tabel 'pengadaan'
            $table->unsignedBigInteger('atk_id');       // Foreign key ke tabel 'atk'
            
            // PENTING: id_jenis_atk harus unsignedBigInteger agar cocok dengan id_jenis_atk di jenis_atk
            $table->unsignedBigInteger('id_jenis_atk')->nullable(); // Jika ada kolom ini di tabel Anda

            $table->integer('jumlah');
            $table->decimal('harga_satuan', 10, 2);
            $table->decimal('subtotal', 10, 2);
            // Tambahkan kolom lain yang relevan jika ada

            $table->timestamps();

            // Definisi Foreign Keys
            $table->foreign('pengadaan_id')->references('id_pengadaan')->on('pengadaan')->onDelete('cascade');
            $table->foreign('atk_id')->references('id_atk')->on('atk')->onDelete('cascade');
            $table->foreign('id_jenis_atk')->references('id_jenis_atk')->on('jenis_atk')->onDelete('set null'); // Atau 'cascade'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pengadaan');
    }
};
