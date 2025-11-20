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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // peminjam
            $table->foreignId('buku_id')->constrained('buku')->onDelete('restrict');
            $table->date('tanggal_pinjam')->nullable();
            $table->date('tanggal_kembali_rencana')->nullable();
            $table->date('tanggal_kembali_sebenarnya')->nullable();
            $table->timestamp('dikembalikan_at')->nullable();
            $table->enum('status', ['dipinjam','selesai'])->default('dipinjam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
