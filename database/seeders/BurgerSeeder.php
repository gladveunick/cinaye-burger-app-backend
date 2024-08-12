<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BurgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            DB::table('burgers')->insert([
                'nom' => $faker->word . ' Burger',
                'prix' => $faker->numberBetween(2000, 6500),
                'image' => 'burger' . $i . '.jpg',
                'description' => $faker->sentence,
                'archive' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
