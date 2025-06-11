<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str; // Import Str facade untuk UUID

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom 'email'.
            // Awalnya kita buat nullable() untuk menghindari UniqueConstraintViolationException
            // jika ada duplikat string kosong.
            $table->string('email')->unique()->nullable()->after('username');

            // Menambahkan 'email_verified_at' jika belum ada (standar Laravel)
            $table->timestamp('email_verified_at')->nullable()->after('email');

            // OPSIONAL: Jika Anda ingin menghapus kolom 'username' dan hanya menggunakan 'email' untuk login,
            // aktifkan baris di bawah ini. Pastikan Anda sudah memindahkan data dari 'username' ke 'email'
            // jika ada data yang perlu dipertahankan.
            // $table->dropColumn('username');
        });

        // Setelah kolom 'email' ditambahkan sebagai nullable,
        // kita perlu mengisi nilai unik untuk baris yang sudah ada
        // agar tidak ada duplikat string kosong saat kita mencoba membuatnya NOT NULL nantinya.
        // Ini adalah langkah penting jika Anda ingin kolom 'email' menjadi NOT NULL di kemudian hari.
        // Anda bisa menjalankan ini secara manual di phpMyAdmin atau dengan seeder.
        // Contoh mengisi dengan UUID:
        // DB::table('users')->whereNull('email')->orWhere('email', '')->each(function ($user) {
        //     DB::table('users')->where('id', $user->id)->update(['email' => Str::uuid()->toString() . '@temp.com']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom 'email' dan 'email_verified_at' jika migrasi di-rollback
            $table->dropColumn('email');
            $table->dropColumn('email_verified_at');

            // OPSIONAL: Jika Anda menghapus 'username' di up(), kembalikan lagi di down()
            // $table->string('username')->unique()->nullable(false)->after('password'); // Sesuaikan posisi jika dikembalikan
        });
    }
};
