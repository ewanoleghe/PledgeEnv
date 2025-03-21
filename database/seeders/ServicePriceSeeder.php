<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServicePrice;
use Illuminate\Support\Facades\DB;


class ServicePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $services = [
            ['service_name' => 'Lead Inspection', 
             'price' => 250.00, 
             'price_visual' => 250.00, 
             'price_dust' => 350.00, 
             'xrf' => 150.00, 
             'xrf_reinspection_price' => 120.00, 
             'visual_reinspection_price' => 200.00, 
             'dust_wipe_reinspection_price' => 300.00, 
             'weekendFee' => 20.00, 
             'description' => 'Ensure your home is free of lead hazards.', 
             'bookMax' => 3], 
             
            ['service_name' => 'Asbestos Management', 
             'price' => 350.00, 
             'price_visual' => 350.00, 
             'price_dust' => 450.00, 
             'xrf' => null, 
             'xrf_reinspection_price' => null, 
             'visual_reinspection_price' => null, 
             'dust_wipe_reinspection_price' => null, 
             'weekendFee' => 20.00, 
             'description' => 'Safe management and removal of asbestos.', 
             'bookMax' => 4], 
             
            ['service_name' => 'Mold Assessment', 
             'price' => 450.00, 
             'price_visual' => 450.00, 
             'price_dust' => 550.00, 
             'xrf' => null, 
             'xrf_reinspection_price' => null, 
             'visual_reinspection_price' => null, 
             'dust_wipe_reinspection_price' => null, 
             'weekendFee' => 20.00, 
             'description' => 'Assessing and identifying mold problems.', 
             'bookMax' => 5],
             
            ['service_name' => 'Radon Control', 
             'price' => 550.00, 
             'price_visual' => 550.00, 
             'price_dust' => 650.00, 
             'xrf' => null, 
             'xrf_reinspection_price' => null, 
             'visual_reinspection_price' => null, 
             'dust_wipe_reinspection_price' => null, 
             'weekendFee' => 20.00, 
             'description' => 'Control and mitigate radon gas in your home.', 
             'bookMax' => 6], 
             
            ['service_name' => 'Sewer Inspection & Cleaning', 
             'price' => 650.00, 
             'price_visual' => 650.00, 
             'price_dust' => 750.00, 
             'xrf' => null, 
             'xrf_reinspection_price' => null, 
             'visual_reinspection_price' => null, 
             'dust_wipe_reinspection_price' => null, 
             'weekendFee' => 20.00, 
             'description' => 'Inspection and cleaning of sewer systems.', 
             'bookMax' => 7], 
             
            ['service_name' => 'Residential Tank Sweep', 
             'price' => 750.00, 
             'price_visual' => 750.00, 
             'price_dust' => 850.00, 
             'xrf' => null, 
             'xrf_reinspection_price' => null, 
             'visual_reinspection_price' => null, 
             'dust_wipe_reinspection_price' => null, 
             'weekendFee' => 20.00, 
             'description' => 'Inspection of underground tanks on residential properties.', 
             'bookMax' => 8], 
             
             ['service_name' => 'Remediation & Consulting', 
             'price' => 850.00, 
             'price_visual' => 850.00, 
             'price_dust' => 950.00, 
             'xrf' => null, 
             'xrf_reinspection_price' => null, 
             'visual_reinspection_price' => null, 
             'dust_wipe_reinspection_price' => null, 
             'weekendFee' => 20.00, 
             'description' => 'Consulting and remediation services for environmental hazards.', 
             'bookMax' => 9], 

             ['service_name' => 'Lead in Drinking Water', 
             'price' => 850.00, 
             'price_visual' => 850.00, 
             'price_dust' => 950.00, 
             'xrf' => null, 
             'xrf_reinspection_price' => null, 
             'visual_reinspection_price' => null, 
             'dust_wipe_reinspection_price' => null, 
             'weekendFee' => 20.00, 
             'description' => 'We provides comprehensive testing services for copper and lead in drinking water.', 
             'bookMax' => 9], 
        ];

        // Insert each service into the service_prices table
        foreach ($services as $service) {
            DB::table('service_prices')->insert([
                'service_name' => $service['service_name'],
                'price' => $service['price'],
                'price_visual' => $service['price_visual'],
                'price_dust' => $service['price_dust'],
                'xrf' => $service['xrf'],
                'xrf_reinspection_price' => $service['xrf_reinspection_price'],
                'visual_reinspection_price' => $service['visual_reinspection_price'],
                'dust_wipe_reinspection_price' => $service['dust_wipe_reinspection_price'],
                'weekendFee' => $service['weekendFee'],
                'description' => $service['description'],
                'bookMax' => (int) $service['bookMax'],  // Ensure bookMax is an integer
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

}
