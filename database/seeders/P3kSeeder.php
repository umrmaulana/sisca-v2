<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\P3k;
use App\Models\P3kLocation;

class P3kSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['Kasa steril terbungkus', 40],
            ['Perban (5 cm)', 6],
            ['Perban (10 cm)', 6],
            ['Plester (1,25 cm)', 6],
            ['Plester Cepat', 20],
            ['Kapas (25 gr)', 3],
            ['Kain segitiga/mittela', 6],
            ['Gunting', 1],
            ['Peniti', 12],
            ['Sarung tangan sekali pakai', 4],
            ['Masker', 6],
            ['Pinset', 1],
            ['Lampu senter', 1],
            ['Gelas untuk cuci mata', 3],
            ['Kantong plastik bersih', 1],
            ['Aquades (100 ml lar. Saline)', 1],
            ['Povidon Iodin (60 ml)', 1],
            ['Alkohol 70%', 1],
            ['Buku panduan P3K di tempat kerja', 1],
            ['Buku catatan', 1],
            ['Daftar isi kotak', 1],
        ];

        // Ambil semua lokasi dari tabel p3k_location
        $locations = P3kLocation::all();

        foreach ($locations as $location) {
            foreach ($items as [$name, $stock]) {
                P3k::create([
                    'location_id' => $location->id,
                    'item' => $name,
                    'standard_stock' => $stock,
                    'actual_stock' => $stock,
                ]);
            }
        }
    }
}
