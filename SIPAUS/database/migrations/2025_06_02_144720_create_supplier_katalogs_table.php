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
        Schema::create('supplier_katalog', function (Blueprint $table) {
            // PENTING: Gunakan $table->bigIncrements('id_katalog')->primary();
            // Jika Anda ingin nama PK-nya id_katalog
            $table->id(); // Default PK 'id' (unsignedBigInteger auto-increment)
            // Jika Anda menggunakan $table->id(), maka foreign key di pesan_atks harus merujuk ke 'id', bukan 'id_katalog'

            $table->unsignedBigInteger('supplier_id'); // Foreign key ke tabel 'supplier'
            $table->unsignedBigInteger('atk_id');       // Foreign key ke tabel 'atk'
            
            $table->unsignedBigInteger('id_jenis_atk'); // Foreign key ke tabel 'jenis_atk'

            $table->decimal('harga', 10, 2);
            $table->integer('stok_supplier');

            $table->timestamps();

            // Definisi Foreign Keys
            $table->foreign('supplier_id')->references('id_supplier')->on('supplier')->onDelete('cascade');
            $table->foreign('atk_id')->references('id_atk')->on('atk')->onDelete('cascade');
            $table->foreign('id_jenis_atk')->references('id_jenis_atk')->on('jenis_atk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_katalog');
    }
};
