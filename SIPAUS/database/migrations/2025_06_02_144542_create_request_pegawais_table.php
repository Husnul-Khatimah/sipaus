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
             Schema::create('request_pegawai', function (Blueprint $table) {
                 $table->bigIncrements('id_request_pegawai'); // PK untuk request_pegawai
                 
                 // PENTING: pegawai_id harus unsignedBigInteger agar cocok dengan id_pegawai di pegawai
                 $table->unsignedBigInteger('pegawai_id'); 
                 
                 $table->date('tanggal_request');
                 $table->string('status', 50)->default('pending');
                 $table->text('catatan')->nullable();
                 
                 $table->timestamps();

                 // Definisi Foreign Key ke tabel 'pegawai'
                 // PENTING: references('id_pegawai')
                 $table->foreign('pegawai_id')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
             });
         }

         /**
          * Reverse the migrations.
          */
         public function down(): void
         {
             Schema::dropIfExists('request_pegawai');
         }
     };
     