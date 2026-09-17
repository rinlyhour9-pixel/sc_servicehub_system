<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_always_creates_a_client_and_issues_a_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Mobile Client', 'email' => 'client@example.test', 'password' => 'password123',
            'password_confirmation' => 'password123', 'role' => 'admin',
        ]);

        $response->assertCreated()->assertJsonPath('user.role', 'client')->assertJsonStructure(['token']);
        $this->assertDatabaseHas('users', ['email' => 'client@example.test', 'role' => 'client']);
    }

    public function test_client_cannot_read_another_clients_booking(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $other = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $service = Service::create(['name' => 'Repair']);
        $booking = Booking::create(['client_id' => $other->id, 'service_id' => $service->id, 'scheduled_at' => now()->addDay(), 'address' => 'Street 1']);

        $this->actingAs($client, 'sanctum')->getJson('/api/bookings/'.$booking->id)->assertForbidden();
    }

    public function test_admin_assignment_notifies_client_and_technician(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $technician = User::factory()->create(['role' => User::ROLE_TECHNICIAN]);
        $service = Service::create(['name' => 'Repair']);
        $booking = Booking::create(['client_id' => $client->id, 'service_id' => $service->id, 'scheduled_at' => now()->addDay(), 'address' => 'Street 1']);

        $this->actingAs($admin, 'sanctum')->patchJson('/api/admin/bookings/'.$booking->id.'/technician', ['technician_id' => $technician->id])->assertOk()->assertJsonPath('data.status', 'assigned');
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $client->id]);
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $technician->id]);
    }
}
