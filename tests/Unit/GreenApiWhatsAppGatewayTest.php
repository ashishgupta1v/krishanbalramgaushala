<?php

namespace Tests\Unit;

use App\Infrastructure\Gateways\GreenApiWhatsAppGateway;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GreenApiWhatsAppGatewayTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.greenapi.url' => 'https://api.green-api.com',
            'services.greenapi.id' => '7107851102',
            'services.greenapi.token' => 'mock_token',
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_send_message_cleans_phone_number_and_posts_correct_payload()
    {
        Http::fake([
            'https://api.green-api.com/waInstance7107851102/sendMessage/mock_token' => Http::response([
                'idMessage' => '3EB0D7A845C8',
                'statusMessage' => 'sent'
            ], 200)
        ]);

        $gateway = new GreenApiWhatsAppGateway();
        
        // 10-digit number should get prepended with '91' and appended with '@c.us'
        $result = $gateway->sendMessage('9087021592', 'Jai Gau Mata!');

        $this->assertTrue($result['success']);
        $this->assertEquals('3EB0D7A845C8', $result['msgid']);
        $this->assertNull($result['error']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.green-api.com/waInstance7107851102/sendMessage/mock_token'
                && $request['chatId'] === '919087021592@c.us'
                && $request['message'] === 'Jai Gau Mata!';
        });
    }

    public function test_send_message_handles_country_code_correctly_if_already_present()
    {
        Http::fake([
            'https://api.green-api.com/waInstance7107851102/sendMessage/mock_token' => Http::response([
                'idMessage' => '3EB011223344',
            ], 200)
        ]);

        $gateway = new GreenApiWhatsAppGateway();
        
        // Number with country code and formatting characters should be cleaned and formatted correctly
        $result = $gateway->sendMessage('+91 98765-43210', 'Hari Om!');

        $this->assertTrue($result['success']);
        $this->assertEquals('3EB011223344', $result['msgid']);

        Http::assertSent(function ($request) {
            return $request['chatId'] === '919876543210@c.us'
                && $request['message'] === 'Hari Om!';
        });
    }

    public function test_send_message_handles_api_failure_gracefully()
    {
        Http::fake([
            'https://api.green-api.com/waInstance7107851102/sendMessage/mock_token' => Http::response([
                'message' => 'Invalid instance status'
            ], 400)
        ]);

        $gateway = new GreenApiWhatsAppGateway();
        $result = $gateway->sendMessage('9087021592', 'Failed attempt');

        $this->assertFalse($result['success']);
        $this->assertNull($result['msgid']);
        $this->assertStringContainsString('Green-API Error', $result['error']);
    }
}
