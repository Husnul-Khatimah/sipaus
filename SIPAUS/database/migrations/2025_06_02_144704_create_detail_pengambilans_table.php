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
        Schema::create('detail_pengambilan', function (Blueprint $table) {
            $table->id(); // Default PK 'id' (unsignedBigInteger auto-increment)
            
            // PENTING: id_pengambilan harus unsignedBigInteger agar cocok dengan id_pengambilan di pengambilan
            $table->unsignedBigInteger('id_pengambilan'); 
            
            $table->unsignedBigInteger('atk_id');       // Foreign key ke tabel 'atk'
            
            $table->integer('jumlah');
            // Tambahkan kolom lain yang relevan jika ada

            $table->timestamps();

            // Definisi Foreign Keys
            $table->foreign('id_pengambilan')->references('id_pengambilan')->on('pengambilan')->onDelete('cascade');
            $table->foreign('atk_id')->references('id_atk')->on('atk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pengambilan');
    }
};
