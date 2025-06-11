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
        Schema::create('supplier', function (Blueprint $table) {
            // PENTING: Gunakan $table->id() untuk primary key otomatis (bigint unsigned auto_increment)
            // Ini akan membuat kolom 'id'.
            // Jika Anda ingin nama kolomnya 'id_supplier' dan auto-increment, gunakan:
            $table->bigIncrements('id_supplier')->primary(); 
            // Atau jika id_supplier sudah ada dan Anda hanya ingin memastikan tipenya benar:
            // $table->unsignedBigInteger('id_supplier')->primary();

            $table->string('nama_supplier', 255);
            $table->string('alamat')->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('email', 255)->unique()->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign key ke tabel 'users' jika supplier adalah user juga

            $table->timestamps();

            // Definisi Foreign Key ke tabel 'users'
            // Pastikan 'id' ada di tabel 'users' dan tipenya unsignedBigInteger
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};
