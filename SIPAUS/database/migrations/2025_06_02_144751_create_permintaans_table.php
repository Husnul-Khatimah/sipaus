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
        Schema::create('permintaans', function (Blueprint $table) {
            $table->id(); // Default PK 'id' (unsignedBigInteger auto-increment)

            $table->unsignedBigInteger('user_id'); // Foreign key ke tabel 'users'
            // PENTING: atk_id harus unsignedBigInteger agar cocok dengan id_atk di atk
            $table->unsignedBigInteger('atk_id');       

            $table->integer('jumlah');
            $table->date('tanggal_permintaan');
            $table->string('tempat', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status', 50)->default('pending');

            $table->timestamps();

            // Definisi Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // PENTING: references('id_atk')
            $table->foreign('atk_id')->references('id_atk')->on('atk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaans');
    }
};
