<?php

namespace Database\Seeders;

use App\Domain\GaushalEvent\GaushalEvent;
use App\Domain\Messaging\MessageTemplate;
use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EventSeeder::class,
            TemplateSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::truncate();

        AdminUser::create([
            'id'        => (string) Str::uuid(),
            'name'      => 'Super Admin',
            'username'  => 'admin',
            'password'  => Hash::make('gaushala'),
            'is_active' => true,
        ]);

        AdminUser::create([
            'id'        => (string) Str::uuid(),
            'name'      => 'Anand Gupta',
            'username'  => 'anand',
            'password'  => Hash::make('gaushala'),
            'is_active' => true,
        ]);
    }
}

class EventSeeder extends Seeder
{
    public function run(): void
    {
        GaushalEvent::truncate();

        $events = [
            ['title' => 'Gau Puja — Gopashtami', 'description' => 'Sacred cow worship festival. All devotees invited.', 'icon' => '🙏', 'type' => 'festival', 'date' => '+5 days', 'time' => '07:00', 'label' => '7:00 AM'],
            ['title' => 'Daily Morning Prayer', 'description' => 'Morning aarti and feed schedule', 'icon' => '🪔', 'type' => 'daily', 'date' => 'today', 'time' => '04:00', 'label' => '4:00 AM Daily'],
            ['title' => 'Weekly Volunteer Day', 'description' => 'Cleaning and grooming volunteer activity', 'icon' => '🤝', 'type' => 'weekly', 'date' => '+3 days', 'time' => '09:00', 'label' => 'Every Sunday'],
            ['title' => 'Veterinary Health Camp', 'description' => 'Monthly health checkup for all cows', 'icon' => '🩺', 'type' => 'health', 'date' => '+12 days', 'time' => '10:00', 'label' => '10:00 AM'],
            ['title' => 'Trustee Meeting', 'description' => 'Monthly review and planning session', 'icon' => '📋', 'type' => 'meeting', 'date' => '+18 days', 'time' => '11:00', 'label' => '11:00 AM'],
            ['title' => 'Janmashtami Celebration', 'description' => 'Grand celebration with bhajan & prasad distribution', 'icon' => '🎉', 'type' => 'festival', 'date' => '+25 days', 'time' => '06:00', 'label' => '6:00 AM'],
        ];

        foreach ($events as $ev) {
            GaushalEvent::create([
                'id'           => (string) Str::uuid(),
                'title'        => $ev['title'],
                'description'  => $ev['description'],
                'icon'         => $ev['icon'],
                'type'         => $ev['type'],
                'scheduled_at' => $ev['date'] === 'today' ? now()->setTimeFromTimeString($ev['time']) : now()->modify($ev['date'])->setTimeFromTimeString($ev['time']),
                'time_label'   => $ev['label'],
                'is_recurring' => in_array($ev['type'], ['daily', 'weekly']),
            ]);
        }
    }
}

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        MessageTemplate::truncate();

        $templates = [
            [
                'key'           => 'birthday',
                'label'         => '🎂 Birthday Default',
                'body'          => "🙏 Jai Gau Mata!\n\nDear {name} Ji,\n\n🎂 Wishing you a very Happy Birthday!\n\nMay the blessings of Gau Mata shower you with good health, happiness, and prosperity on this special day. 🐄\n\nWith warm wishes,\nKrishan Balram Gaushala\nSingla Enclave, Village Dullon Khurd, Pakhowal Road, Ludhiana",
                'variables'     => ['name'],
                'meta_name'     => 'krishan_balram_birthday_v1',
                'status'        => 'approved',
                'category'      => 'utility',
                'is_active_for' => 'birthday',
            ],
            [
                'key'           => 'anniversary',
                'label'         => '💐 Anniversary Default',
                'body'          => "🙏 Jai Gau Mata!\n\nDear {name} Ji,\n\n💐 Wishing you and your family a very Happy Wedding Anniversary!\n\nMay Gau Mata bless your bond with eternal love and togetherness. 🐄\n\nWith warm blessings,\nKrishan Balram Gaushala\nSingla Enclave, Village Dullon Khurd, Pakhowal Road, Ludhiana",
                'variables'     => ['name'],
                'meta_name'     => 'krishan_balram_anniversary_v1',
                'status'        => 'approved',
                'category'      => 'utility',
                'is_active_for' => 'anniversary',
            ],
            [
                'key'           => 'festival',
                'label'         => '🎉 Festival Default',
                'body'          => "🙏 Jai Gau Mata!\n\nDear {name} Ji,\n\nWarm greetings from Krishan Balram Gaushala on this auspicious occasion! 🐄\n\nMay the divine blessings of Gau Mata fill your home with joy and prosperity.\n\nWith love & blessings,\nKrishan Balram Gaushala\nSingla Enclave, Village Dullon Khurd, Pakhowal Road, Ludhiana",
                'variables'     => ['name'],
                'meta_name'     => 'krishan_balram_festival_v1',
                'status'        => 'approved',
                'category'      => 'utility',
                'is_active_for' => null,
            ],
            [
                'key'           => 'custom',
                'label'         => '✏️ Custom Default',
                'body'          => "🙏 Jai Gau Mata!\n\nDear {name} Ji,\n\n",
                'variables'     => ['name'],
                'meta_name'     => 'krishan_balram_custom_v1',
                'status'        => 'approved',
                'category'      => 'utility',
                'is_active_for' => null,
            ],
        ];

        foreach ($templates as $t) {
            MessageTemplate::create(array_merge($t, ['id' => (string) Str::uuid()]));
        }
    }
}
