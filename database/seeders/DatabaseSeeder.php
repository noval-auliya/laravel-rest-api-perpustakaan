<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KategoriBuku;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Buku;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        // User Admin
        $admin = User::factory()->create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@perpus.test',
            'password' => bcrypt('admin123'),
        ]);
        
        // User Biasa
        User::factory(5)->create();

        // Kategori Buku
        $kategori = KategoriBuku::factory(5)->create();

        // Buku
        $buku = Buku::factory(20)->create();

        // Peminjaman
        $users = User::where('email', '!=', 'admin@perpus.test')->get();
        foreach ($users as $user) {
            Peminjaman::factory(rand(1, 3))->create([
                'user_id' => $user->id,
            ]);
        }

        // Pengurangan Stok Setelah Dipinjam
        foreach ($buku as $book) {
            $jumlahPeminjaman = Peminjaman::where('buku_id', $book->id)->count();
            $book->stok = max(0, $book->stok - $jumlahPeminjaman);
            $book->save();
        }
    }
}