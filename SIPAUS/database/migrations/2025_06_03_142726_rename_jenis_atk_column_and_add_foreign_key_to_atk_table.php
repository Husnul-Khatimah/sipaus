<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // --- Perubahan pada tabel 'jenis_atk' ---
        Schema::table('jenis_atk', function (Blueprint $table) {
            $table->renameColumn('jenis_atk', 'nama_kategori');
        });

        // --- Perubahan pada tabel 'atk' ---
        Schema::table('atk', function (Blueprint $table) {
            $table->renameColumn('atk_name', 'nama_barang');

            if (Schema::hasColumn('atk', 'kategori')) {
                 $table->dropColumn('kategori');
            }

            // PENTING: Hapus baris ini karena jenis_atk_id sudah dibuat di create_atks_table.php
            // $table->unsignedBigInteger('jenis_atk_id')->nullable()->after('nama_barang');

            // Tambahkan foreign key constraint
            $table->foreign('jenis_atk_id')
                  ->references('id_jenis_atk')
                  ->on('jenis_atk')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // --- Rollback tabel 'atk' ---
        Schema::table('atk', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['jenis_atk_id']);
            // PENTING: Hapus baris ini karena jenis_atk_id tidak dibuat di migrasi ini
            // $table->dropColumn('jenis_atk_id');

            // Kembalikan kolom 'kategori' jika dihapus di up()
            if (!Schema::hasColumn('atk', 'kategori')) {
               $table->string('kategori', 50)->nullable()->after('satuan');
            }
            // Kembalikan nama kolom 'nama_barang' ke 'atk_name'
            $table->renameColumn('nama_barang', 'atk_name');
        });

        // --- Rollback tabel 'jenis_atk' ---
        Schema::table('jenis_atk', function (Blueprint $table) {
            $table->renameColumn('nama_kategori', 'jenis_atk');
        });
    }
};
