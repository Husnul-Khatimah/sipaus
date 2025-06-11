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
            Schema::table('users', function (Blueprint $table) {
                // Mengubah panjang kolom 'password' menjadi 255 karakter
                // Ini penting untuk menyimpan hash Bcrypt yang panjangnya 60 karakter
                $table->string('password', 255)->change();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('users', function (Blueprint $table) {
                // Mengembalikan panjang kolom 'password' ke 50 jika rollback
                // Sesuaikan dengan panjang asli jika berbeda dari 50
                $table->string('password', 50)->change();
            });
        }
    };
    