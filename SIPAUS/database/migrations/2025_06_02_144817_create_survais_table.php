<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survais', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 50)->nullable(); // Kolom nip
            $table->integer('nilai_inovasi');
            $table->integer('nilai_kualitas');
            $table->integer('nilai_ketepatan_waktu');
            $table->text('komentar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survais');
    }
};