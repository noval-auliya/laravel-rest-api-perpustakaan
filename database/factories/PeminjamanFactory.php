<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Buku;
use Carbon\Carbon;

class PeminjamanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalPinjam = Carbon::now()->subDays(rand(1, 10))->toDateString();
        $tanggalKembaliRencana = Carbon::parse($tanggalPinjam)->addDays(7)->toDateString();

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'buku_id' => Buku::inRandomOrder()->first()->id ?? Buku::factory(),
            'tanggal_pinjam' => $tanggalPinjam,
            'tanggal_kembali_rencana' => $tanggalKembaliRencana,
            'tanggal_kembali_sebenarnya' => null,
            'dikembalikan_at' => null,
            'status' => 'dipinjam',
        ];
    }
}
