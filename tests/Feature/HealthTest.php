<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_health_reports_a_connected_database(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJsonPath('application', 'DompetKu')
            ->assertJsonPath('database', 'connected');
    }
}
