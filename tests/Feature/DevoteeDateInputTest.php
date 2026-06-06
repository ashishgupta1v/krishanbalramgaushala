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
}
