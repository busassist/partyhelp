<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ElementorWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function validElementorPayload(array $overrides = []): array
    {
        return array_merge([
            'First_Name' => 'Jane',
            'Last_Name' => 'Doe',
            'Email' => 'jane@example.com',
            'Phone' => '0412345678',
            'Select_the_type_of_occasion' => '50th',
            'Preferred_Date' => now()->addDays(14)->format('Y-m-d'),
            'Number_of_Guests' => '50 - 79',
            'Select_preferred_location' => 'INNER SOUTH EAST - South Yarra, St Kilda',
            'Estimated_Budget' => '5000',
            'Other_details_about_the_party:' => 'Birthday celebration',
        ], $overrides);
    }

    public function test_valid_elementor_payload_creates_lead_and_returns_200(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/webhook/elementor-lead', $this->validElementorPayload());

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'success',
                'message' => 'Lead received and queued for processing',
            ])
            ->assertJsonStructure(['lead_id']);

        $this->assertDatabaseHas('leads', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'occasion_type' => '50th_birthday',
            'suburb' => 'South Yarra',
        ]);
    }

    public function test_invalid_payload_returns_422_with_validation_errors(): void
    {
        $payload = [
            'First_Name' => 'T',
            'Last_Name' => '',
            'Email' => 'invalid',
        ];

        $response = $this->postJson('/api/webhook/elementor-lead', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'error',
                'message' => 'Validation failed',
            ])
            ->assertJsonStructure(['errors']);

        $this->assertDatabaseCount('leads', 0);
    }

    public function test_accepts_canonical_field_names(): void
    {
        Queue::fake();

        $payload = [
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john@example.com',
            'phone' => '0398765432',
            'occasion_type' => 'wedding_reception',
            'guest_count' => 80,
            'preferred_date' => now()->addDays(30)->format('Y-m-d'),
            'suburb' => 'Richmond',
            'room_styles' => 'function_room',
        ];

        $response = $this->postJson('/api/webhook/elementor-lead', $payload);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('leads', [
            'first_name' => 'John',
            'email' => 'john@example.com',
            'occasion_type' => 'wedding_reception',
        ]);
    }
}
