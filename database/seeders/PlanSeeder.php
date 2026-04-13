<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'                 => 'Basique',
                'price'                => 3000,
                'client_limit_per_day' => 15,
                'pdf_enabled'          => false,
                'statistics_enabled'   => false,
                'multi_users'          => false,
            ],
            [
                'name'                 => 'Pro',
                'price'                => 7000,
                'client_limit_per_day' => null,
                'pdf_enabled'          => true,
                'statistics_enabled'   => true,
                'multi_users'          => false,
            ],
            [
                'name'                 => 'Premium',
                'price'                => 10000,
                'client_limit_per_day' => null,
                'pdf_enabled'          => true,
                'statistics_enabled'   => true,
                'multi_users'          => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
