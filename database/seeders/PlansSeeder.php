<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['slug' => 'basic',    'nome' => 'Basic (Bronze)',    'valor' => 199.90, 'viicio_plan_id' => 1],
            ['slug' => 'gold',     'nome' => 'Gold (Prata)',      'valor' => 399.90, 'viicio_plan_id' => 1],
            ['slug' => 'platinum', 'nome' => 'Platinum (Diamante)', 'valor' => 899.90, 'viicio_plan_id' => 2],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
