<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'buku_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_sebenarnya',
        'dikembalikan_at',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }
}
