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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Ini akan membuat kolom 'id' BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('username', 100)->unique(); // Add unique for username
            $table->string('password', 100);
            $table->enum('role', ['admin', 'pegawai', 'supplier', '']); // Pastikan enum sesuai
            $table->string('nip', 50)->nullable(); // Kolom 'nip' yang baru ditambahkan
            // Anda mungkin perlu menambahkan timestamps jika ingin menggunakan created_at/updated_at Laravel
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};