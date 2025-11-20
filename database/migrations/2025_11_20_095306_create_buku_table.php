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
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_buku_id')->constrained('kategori_buku')->onDelete('restrict');
            $table->string('judul');
            $table->string('pengarang')->nullable();
            $table->string('penerbit')->nullable();
            $table->string('isbn')->nullable();
            $table->integer('stok')->default(1);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
