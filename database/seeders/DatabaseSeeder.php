<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'password', 'is_admin' => true],
        );

        User::updateOrCreate(
            ['email' => 'client@example.com'],
            ['name' => 'Demo Client', 'phone' => '012345678', 'password' => 'password'],
        );

        if (Event::count() === 0) {
            $samples = [
                ['Tech Meetup 2026', 'Talks and networking for developers.', 'Phnom Penh', 7, 50, 'tech-meetup.svg'],
                ['Startup Pitch Night', 'Watch early-stage startups pitch to investors.', 'Siem Reap', 14, 30, 'pitch-night.svg'],
                ['Charity Fun Run', '5km run to raise funds for local schools.', 'Riverside Park', 21, null, 'fun-run.svg'],
            ];

            foreach ($samples as [$title, $desc, $location, $days, $capacity, $image]) {
                Storage::disk('public')->put("events/{$image}", file_get_contents(__DIR__."/images/{$image}"));

                Event::create([
                    'image' => "events/{$image}",
                    'title' => $title,
                    'description' => $desc,
                    'location' => $location,
                    'start_at' => now()->addDays($days)->setTime(9, 0),
                    'end_at' => now()->addDays($days)->setTime(17, 0),
                    'capacity' => $capacity,
                    'is_published' => true,
                    'created_by' => $admin->id,
                ]);
            }
        }
    }
}
