<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create default app settings
        AppSetting::create([
            'app_name' => 'Pledge Environmental LLC',
            'company_address' => '648 Newark Ave.',
            'company_city' => 'Elizabeth',
            'company_state' => 'NJ',
            'company_zip' => '07208',
            'company_address_ny' => '1466 Saint Marks Ave #1B.',
            'company_city_ny' => 'Brooklyn',
            'company_state_ny' => 'NY',
            'company_zip_ny' => '11233',
            'company_email' => 'info@pledgeenvironmental.com',
            'company_phone' => '(609) 208-5535',
            'company_zelle' => 'pledgeenvironmental@gmail.com',
            'company_njdca' => '00862-E',
            'company_owner' => 'Saheed Alex Adeyeri',
            'stripe_public_key' => 'pk_test_51LRg3pEQYn06EyqN91GRWKKJusdeG78ANzkpRJemog4nhc0tZQ1ezHFBSqQpjGyjq2PGRZI9JRzDe56od0jmuHBF00THsDo327',
            'stripe_secret_key' => 'sk_test_51LRg3pEQYn06EyqNvdx65axylhcbCst7r6sZZnsLU42PPbzk3mFtkBWDdj1arz0rDX4J9aH2whmBuiWbrr0wqA4Q00sszzPCw0',
            'vite_stripe_key' => 'pk_test_51LRg3pEQYn06EyqN91GRWKKJusdeG78ANzkpRJemog4nhc0tZQ1ezHFBSqQpjGyjq2PGRZI9JRzDe56od0jmuHBF00THsDo327',
            'stripe_webhook_secret' => 'whsec_5ce777b2d545b2aeae32238f295c9c6f25e476144ff712a68362470304f69b14'
        ]);
    }
}
