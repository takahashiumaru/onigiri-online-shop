<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_couriers_returns_paginated_list()
    {
        User::factory()->create([
            'name' => 'Courier One',
            'email' => 'courier1@example.com',
            'role' => 'courier',
        ]);

        User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'role' => 'customer',
        ]);

        $response = $this->getJson('/api/couriers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'phone', 'address', 'photo', 'created_at']
                ],
                'total',
                'page',
                'pageSize',
                'totalPages',
            ]);

        $this->assertEquals(1, $response->json('total'));
    }

    public function test_get_single_courier_returns_details()
    {
        $courier = User::factory()->create([
            'name' => 'Courier One',
            'email' => 'courier1@example.com',
            'role' => 'courier',
        ]);

        $response = $this->getJson("/api/couriers/{$courier->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $courier->id,
                'name' => 'Courier One',
                'email' => 'courier1@example.com',
            ]);
    }

    public function test_get_non_existent_courier_returns_404()
    {
        $response = $this->getJson('/api/couriers/999');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Kurir tidak ditemukan.',
            ]);
    }
}
