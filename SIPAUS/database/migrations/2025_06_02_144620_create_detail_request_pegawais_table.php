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
             Schema::create('detail_request_pegawai', function (Blueprint $table) {
                 $table->id(); // Default PK 'id' (unsignedBigInteger auto-increment)
                 
                 // PENTING: request_pegawai_id harus unsignedBigInteger dan merujuk ke id_request_pegawai
                 $table->unsignedBigInteger('request_pegawai_id'); 
                 
                 // PENTING: atk_id harus unsignedBigInteger dan merujuk ke id_atk
                 $table->unsignedBigInteger('atk_id');             
                 
                 // PENTING: id_jenis_atk harus unsignedBigInteger dan merujuk ke id_jenis_atk
                 $table->unsignedBigInteger('id_jenis_atk')->nullable(); // Jika kolom ini ada di tabel Anda

                 $table->integer('jumlah');
                 // Tambahkan kolom lain yang relevan jika ada

                 $table->timestamps();

                 // Definisi Foreign Keys
                 $table->foreign('request_pegawai_id')->references('id_request_pegawai')->on('request_pegawai')->onDelete('cascade');
                 $table->foreign('atk_id')->references('id_atk')->on('atk')->onDelete('cascade');
                 $table->foreign('id_jenis_atk')->references('id_jenis_atk')->on('jenis_atk')->onDelete('set null');
             });
         }

         /**
          * Reverse the migrations.
          */
         public function down(): void
         {
             Schema::dropIfExists('detail_request_pegawai');
         }
     };
     