<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('form_code'); // Contoh: KKH-01-BANDARA, PRD-01A
            $table->string('location')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            
            // Kolom JSON untuk menampung seluruh daftar ceklis [S / TS / N-A] dan catatan
            $table->json('form_data'); 
            
            // Kolom untuk menampung ringkasan/catatan akhir
            $table->text('final_notes')->nullable(); 
            
            // Data Pihak yang Terlibat
            $table->string('supervisor_name');
            $table->string('provider_name')->nullable();
            $table->string('committee_name')->nullable(); // Beberapa form butuh ttd panitia
            
            // Tanda Tangan digital (disimpan dalam format teks Base64)
            $table->longText('signature_supervisor')->nullable();
            $table->longText('signature_provider')->nullable();
            $table->longText('signature_committee')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};