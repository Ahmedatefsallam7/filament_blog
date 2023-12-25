<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::factory(10)
            ->hasComments(10)
            ->create();

        Customer::factory(10)
            ->hasComments(20)
            ->create();

        Customer::factory(10)
            ->hasComments(30)
            ->create();

        Customer::factory(10)
            ->hasComments(40)
            ->create();
        Customer::factory(10)
            ->hasComments(50)
            ->create();

        Customer::factory(10)
            ->hasComments(60)
            ->create();

        Customer::factory(10)
            ->hasComments(10)
            ->create();

        Customer::factory(10)
            ->hasComments(20)
            ->create();
    }
}
