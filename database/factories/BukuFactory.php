<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\KategoriBuku;

class BukuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kategori_buku_id' => KategoriBuku::inRandomOrder()->first()->id ?? KategoriBuku::factory(),
            'judul' => $this->faker->sentence(3),
            'pengarang' => $this->faker->name(),
            'penerbit' => $this->faker->company(),
            'isbn' => $this->faker->isbn13(),
            'stok' => rand(1, 10),
            'deskripsi' => $this->faker->paragraph(),
        ];
    }
}
