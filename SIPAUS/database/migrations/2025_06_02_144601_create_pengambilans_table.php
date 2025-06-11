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
        Schema::create('pengambilan', function (Blueprint $table) {
            // PENTING: Gunakan bigIncrements untuk primary key otomatis (bigint unsigned auto_increment)
            // Ini akan membuat kolom 'id_pengambilan' sebagai PK
            $table->bigIncrements('id_pengambilan')->primary(); 
            // Atau jika Anda ingin id biasa: $table->id();

            $table->unsignedBigInteger('id_pegawai'); // Foreign key ke tabel 'pegawai'
            $table->unsignedBigInteger('user_id'); // Foreign key ke tabel 'users'
            $table->date('tanggal_pengambilan');
            $table->string('status', 50)->default('pending');
            $table->text('keterangan')->nullable();
            // Tambahkan kolom lain yang relevan

            $table->timestamps();

            // Definisi Foreign Keys
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Asumsi users.id adalah PK
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengambilan');
    }
};
