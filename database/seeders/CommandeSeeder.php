<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CommandeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Générer des commandes pour chaque mois de 2024
        for ($month = 1; $month <= 8; $month++) {
            for ($i = 0; $i < 10; $i++) {
                DB::table('commandes')->insert([
                    'burger_id' => $faker->numberBetween(1, 3), // Assurez-vous que les IDs des burgers existent
                    'nom' => $faker->name,
                    'email' => $faker->safeEmail,
                    'quantite' => $faker->numberBetween(1, 5),
                    'prix_total' => $faker->numberBetween(2000, 15000),
                    'status' => 'payé',
                    'date_paiement' => $this->generatePaymentDate($month),
                    'montant' => $faker->numberBetween(1500, 5000),
                    'created_at' => now()->setDate(2024, $month, $faker->numberBetween(1, 28)),
                    'updated_at' => now()->setDate(2024, $month, $faker->numberBetween(1, 28)),
                ]);
            }
        }
    }

    /**
     * Génère une date de paiement aléatoire pour un mois donné.
     *
     * @param int $month
     * @return string
     */
    private function generatePaymentDate($month)
    {
        $faker = Faker::create();
        return now()->setDate(2024, $month, $faker->numberBetween(1, 28))->format('Y-m-d H:i:s');
    }
}
