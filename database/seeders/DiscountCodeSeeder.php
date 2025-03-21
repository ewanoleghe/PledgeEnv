<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DiscountCode;

class DiscountCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Insert discount codes for Inspector John
        DiscountCode::create([
            'code' => 'JOHN5',                      // Discount code for 5% for John
            'inspector_name' => 'John',              // Inspector's name
            'discount_percentage' => 5.00,           // 5% discount
            'is_active' => true,                      // Active code
            'expiration_date' => now()->addDays(30), // Valid for 30 days
            'usage_limit' => 100,                     // Can be used 100 times
            'used_count' => 0,                        // Initially used 0 times
        ]);

        DiscountCode::create([
            'code' => 'JOHN10',                     // Discount code for 10% for John
            'inspector_name' => 'John',              // Inspector's name
            'discount_percentage' => 10.00,          // 10% discount
            'is_active' => true,                      // Active code
            'expiration_date' => now()->addDays(30), // Valid for 30 days
            'usage_limit' => 100,                     // Can be used 100 times
            'used_count' => 0,                        // Initially used 0 times
        ]);

        // Insert discount codes for Inspector Alex
        DiscountCode::create([
            'code' => 'ALEX5',                      // Discount code for 5% for Alex
            'inspector_name' => 'Alex',              // Inspector's name
            'discount_percentage' => 5.00,           // 5% discount
            'is_active' => true,                      // Active code
            'expiration_date' => now()->addDays(30), // Valid for 30 days
            'usage_limit' => 100,                     // Can be used 100 times
            'used_count' => 0,                        // Initially used 0 times
        ]);

        DiscountCode::create([
            'code' => 'ALEX20',                     // Discount code for 20% for Alex
            'inspector_name' => 'Alex',              // Inspector's name
            'discount_percentage' => 20.00,          // 20% discount
            'is_active' => true,                      // Active code
            'expiration_date' => now()->addDays(30), // Valid for 30 days
            'usage_limit' => 100,                     // Can be used 100 times
            'used_count' => 0,                        // Initially used 0 times
        ]);
    }

}
