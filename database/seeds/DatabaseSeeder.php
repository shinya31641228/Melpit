<?php

use App\Models\PrimaryCategory;
use App\Models\SecondaryCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProductConditionSeeder::class);
        $this->call(PrimaryCategorySeeder::class);
        $this->call(SecondaryCategorySeeder::class);
    }
}
