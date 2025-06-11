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
        Schema::create('atk', function (Blueprint $table) {
            $table->bigIncrements('id_atk'); // Primary key seperti di DB Anda

            $table->string('atk_name', 100); // Nama barang asli
            $table->string('satuan', 50);
            $table->integer('stok')->default(0);
            
            // PENTING: Tambahkan kolom jenis_atk_id di sini dengan tipe yang benar
            // Ini harus unsignedBigInteger agar cocok dengan id_jenis_atk di jenis_atk
            $table->unsignedBigInteger('jenis_atk_id')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atk');
    }
};
