<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Regular user
        $user = User::create([
            'name'     => 'Demo User',
            'email'    => 'user@example.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);

        // Sample events
        $events = [
            [
                'title'             => 'Cairo Jazz Night',
                'description'       => 'An unforgettable evening of live jazz music featuring Egypt\'s finest jazz musicians. Enjoy cocktails, great food, and world-class performances in the heart of Cairo.',
                'venue'             => 'Cairo Opera House, Downtown Cairo',
                'date'              => now()->addDays(14),
                'total_tickets'     => 200,
                'available_tickets' => 200,
                'price'             => 7500,
                'image_url'         => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800',
            ],
            [
                'title'             => 'Tech Summit 2025',
                'description'       => 'Join 500+ developers, founders, and tech leaders for a full day of talks, workshops, and networking. Topics include AI, Web3, Cloud Architecture, and Product Design.',
                'venue'             => 'Smart Village Conference Center, 6th October',
                'date'              => now()->addDays(30),
                'total_tickets'     => 500,
                'available_tickets' => 350,
                'price'             => 15000,
                'image_url'         => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
            ],
            [
                'title'             => 'Nile Sunset Cruise & Dinner',
                'description'       => 'Sail the Nile at golden hour with a gourmet dinner, live Oud music, and breathtaking views of Cairo\'s skyline. A perfect evening for couples and groups.',
                'venue'             => 'Maadi Marina, Corniche El Nile',
                'date'              => now()->addDays(7),
                'total_tickets'     => 80,
                'available_tickets' => 30,
                'price'             => 20000,
                'image_url'         => 'https://images.unsplash.com/photo-1562177756-7e198da9cb8e?w=800',
            ],
            [
                'title'             => 'Stand-Up Comedy Show',
                'description'       => 'A hilarious night of comedy with Egypt\'s top stand-up comedians. Performed in both Arabic and English. All ages welcome.',
                'venue'             => 'El Sawy Culturewheel, Zamalek',
                'date'              => now()->addDays(21),
                'total_tickets'     => 300,
                'available_tickets' => 150,
                'price'             => 5000,
                'image_url'         => 'https://images.unsplash.com/photo-1585699324551-f6c309eedeca?w=800',
            ],
            [
                'title'             => 'Photography Masterclass',
                'description'       => 'A hands-on full-day workshop with award-winning photographer Karim Hassan. Cover street photography, portrait lighting, and post-processing in Lightroom. Camera required.',
                'venue'             => 'Darb 1718 Arts Center, Old Cairo',
                'date'              => now()->addDays(10),
                'total_tickets'     => 25,
                'available_tickets' => 10,
                'price'             => 25000,
                'image_url'         => 'https://images.unsplash.com/photo-1452587925148-ce544e77e70d?w=800',
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }

        // Seed a sample paid order
        $event = Event::first();
        Order::create([
            'user_id'                  => $user->id,
            'event_id'                 => $event->id,
            'quantity'                 => 2,
            'total_amount'             => $event->price * 2,
            'status'                   => 'paid',
            'stripe_payment_intent_id' => 'pi_demo_' . uniqid(),
            'stripe_payment_status'    => 'succeeded',
        ]);

        $this->command->info(' Seeded: admin@example.com | user@example.com | 5 events | 1 demo order');
    }
}
