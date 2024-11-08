<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run()
    {
        $events = [
            [
                'title' => 'Youth Evangelism Conference',
                'description' => 'A powerful gathering of young people passionate about spreading the gospel',
                'details' => 'Join us for an inspiring weekend of worship, teaching, and practical training in evangelism. Learn from experienced missionaries and engage in outreach activities.',
                'start_date' => '2024-02-15 00:00:00',
                'end_date' => '2024-02-17 00:00:00',
                'location' => 'Main Auditorium, Lagos',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00'
            ],
            [
                'title' => 'Community Outreach Program',
                'description' => 'Reaching our local community with the love of Christ',
                'details' => 'A day of service and evangelism in our local community. Activities include free medical checkups, food distribution, and gospel sharing.',
                'start_date' => '2024-03-01 00:00:00',
                'end_date' => '2024-03-01 00:00:00',
                'location' => 'Community Center, Abuja',
                'start_time' => '08:00:00',
                'end_time' => '16:00:00'
            ],
            [
                'title' => 'Mission Training Workshop',
                'description' => 'Equipping believers for effective mission work',
                'details' => 'Intensive training sessions covering cross-cultural communication, mission strategies, and practical ministry skills.',
                'start_date' => '2024-04-10 00:00:00',
                'end_date' => '2024-04-12 00:00:00',
                'location' => 'Training Center, Port Harcourt',
                'start_time' => '10:00:00',
                'end_time' => '15:00:00'
            ]
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }
    }
}
