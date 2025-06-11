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
            Schema::create('jenis_atk', function (Blueprint $table) {
                $table->unsignedBigInteger('id_jenis_atk')->primary(); // Primary key seperti di DB Anda
                $table->string('jenis_atk', 100); // Kolom yang akan di-rename
                $table->timestamps(); // created_at dan updated_at
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('jenis_atk');
        }
    };