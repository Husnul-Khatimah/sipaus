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
        Schema::create('pegawai', function (Blueprint $table) {
            // PENTING: Gunakan bigIncrements untuk primary key auto-increment bigint unsigned
            $table->bigIncrements('id_pegawai'); // Ini akan membuat id_pegawai sebagai PK

            // Tambahkan kolom-kolom lain yang relevan untuk tabel pegawai
            $table->string('nama', 255);
            $table->string('nip', 50)->unique()->nullable(); // Asumsi NIP
            $table->string('jabatan', 100)->nullable();
            $table->string('departemen', 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
