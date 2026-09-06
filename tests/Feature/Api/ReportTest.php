<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_report_returns_standardized_format()
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create([
            'user_id' => $user->id,
            'payment_status' => 'paid',
            'created_at' => now(),
        ]);

        $response = $this->getJson('/api/reports/daily');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'from',
                'to',
                'data',
                'total',
                'page',
                'pageSize',
                'totalPages',
            ]);
    }

    public function test_monthly_report_returns_standardized_format()
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create([
            'user_id' => $user->id,
            'payment_status' => 'paid',
            'created_at' => now(),
        ]);

        $response = $this->getJson('/api/reports/monthly?month=' . now()->month . '&year=' . now()->year);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'from',
                'to',
                'data',
                'total',
                'page',
                'pageSize',
                'totalPages',
            ]);
    }

    public function test_report_validation_errors()
    {
        $response = $this->getJson('/api/reports/monthly?month=13');

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }
}
