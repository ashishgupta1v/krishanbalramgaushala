<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domain\Devotee\Devotee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class DevoteeDateInputTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_can_register_a_devotee_with_standard_date_formats()
    {
        $payload = [
            'name'                  => 'Raman Sharma',
            'whatsapp'              => '9876543210',
            'dob'                   => '1995-08-15', // formatted by frontend
            'anniversary'           => '2018-11-24', // formatted by frontend
            'fb_consent'            => true,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register.store'), $payload);

        $response->assertJson(['success' => true]);
        
        $devotee = Devotee::where('whatsapp', '9876543210')->firstOrFail();
        $this->assertEquals('Raman Sharma', $devotee->name);
        $this->assertEquals('1995-08-15', $devotee->dob->format('Y-m-d'));
        $this->assertEquals('2018-11-24', $devotee->anniversary->format('Y-m-d'));
    }

    /** @test */
    public function test_it_can_update_profile_with_standard_date_formats()
    {
        $devotee = Devotee::create([
            'id'          => 'devo-uuid-1234',
            'name'        => 'Old Name',
            'whatsapp'    => '9999999999',
            'dob'         => '1990-01-01',
            'anniversary' => '2015-05-05',
            'status'      => 'active',
            'password'    => Hash::make('password123'),
        ]);

        // Mock devotee session
        session(['gaushala_devotee_id' => $devotee->id]);

        $payload = [
            'name'        => 'Updated Name',
            'dob'         => '1995-08-15', // formatted by frontend
            'anniversary' => '2018-11-24', // formatted by frontend
            'fb_consent'  => true,
        ];

        $response = $this->put(route('devotee.profile.update'), $payload);

        $response->assertRedirect();
        
        $devotee->refresh();
        $this->assertEquals('Updated Name', $devotee->name);
        $this->assertEquals('1995-08-15', $devotee->dob->format('Y-m-d'));
        $this->assertEquals('2018-11-24', $devotee->anniversary->format('Y-m-d'));
    }

    /** @test */
    public function test_registration_dispatches_whatsapp_welcome_message_job()
    {
        \Illuminate\Support\Facades\Queue::fake();

        $payload = [
            'name'                  => 'Gauranga Das',
            'whatsapp'              => '9876543211',
            'dob'                   => '1995-08-15',
            'anniversary'           => null,
            'fb_consent'            => true,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register.store'), $payload);

        $response->assertJson(['success' => true]);

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SendWaMessageJob::class, function ($job) {
            return $job->getDevotee()->whatsapp === '9876543211'
                && str_contains($job->getMessage(), 'Welcome to our divine');
        });
    }

    /** @test */
    public function test_send_wa_message_job_prepends_sanskrit_shlok_before_jai_gau_mata()
    {
        $devotee = Devotee::create([
            'id'          => 'test-shlok-uuid',
            'name'        => 'Aarav Mehta',
            'whatsapp'    => '9876500000',
            'dob'         => '1990-01-01',
            'password'    => Hash::make('password123'),
        ]);

        $mockGateway = $this->createMock(\App\Infrastructure\Gateways\WhatsAppGateway::class);
        $mockGateway->expects($this->once())
            ->method('sendMessage')
            ->with(
                $this->equalTo('9876500000'),
                $this->callback(function ($message) {
                    return str_contains($message, 'सर्वदेवमयी गौः माता')
                        && str_contains($message, '🙏 Jai Gau Mata')
                        && str_contains($message, 'Aarav Mehta');
                }),
                $this->anything()
            )
            ->willReturn(['success' => true, 'msgid' => '12345']);

        $this->app->instance(\App\Infrastructure\Gateways\WhatsAppGateway::class, $mockGateway);

        $job = new \App\Jobs\SendWaMessageJob($devotee, "🙏 Jai Gau Mata!\n\nDear {name} Ji,\nWelcome!");
        $job->handle($mockGateway);
    }
}
