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
        Schema::create('pengadaan', function (Blueprint $table) {
            // PENTING: Gunakan bigIncrements untuk primary key otomatis (bigint unsigned auto_increment)
            // Ini akan membuat kolom 'id'.
            // Jika Anda ingin nama kolomnya 'id_pengadaan' dan auto-increment, gunakan:
            $table->bigIncrements('id_pengadaan')->primary(); 
            // Atau jika id_pengadaan sudah ada dan Anda hanya ingin memastikan tipenya benar:
            // $table->unsignedBigInteger('id_pengadaan')->primary();

            $table->date('tanggal_pengadaan');
            $table->unsignedBigInteger('supplier_id'); // Foreign key ke tabel 'supplier'
            $table->decimal('total_harga', 10, 2);
            $table->string('status', 50);
            // Tambahkan kolom lain yang relevan

            $table->timestamps();

            // Definisi Foreign Key ke tabel 'supplier'
            // PENTING: Ubah 'id' menjadi 'id_supplier'
            $table->foreign('supplier_id')->references('id_supplier')->on('supplier')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaan');
    }
};
