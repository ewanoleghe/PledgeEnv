<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TeamCode;

class TeamCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teamCodes = [
            ['team_code' => 'NJ907', 'team_name' => 'NJ Squad'],
            ['team_code' => 'NY907', 'team_name' => 'NY Warriors'],
        ];

        foreach ($teamCodes as $team) {
            TeamCode::create($team);
        }
    }
}
