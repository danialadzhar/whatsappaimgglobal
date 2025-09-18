<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AIActivation;
use Carbon\Carbon;

class AIActivationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Jalankan seeding database
     */
    public function run(): void
    {
        // Create default chatbot activation record if not exists
        // Cipta record default aktivasi chatbot jika tidak wujud
        AIActivation::firstOrCreate(
            ['name' => 'chatbot'],
            [
                'is_active' => true,
                'description' => 'Main chatbot AI activation status - controls whether AI responds to messages',
                'last_updated_at' => Carbon::now(),
            ]
        );

        $this->command->info('AIActivation seeder completed successfully!');
    }
}
