<?php

test('the API health endpoint returns a successful response', function () {
    $response = $this->get('/api/health');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'version',
            'uptime',
            'timestamp',
            'system' => ['os', 'php_version', 'memory_usage'],
            'database' => ['status', 'latency_ms'],
            'storage' => ['status'],
            'environment',
        ]);
});

test('the API routes endpoint returns a list of registered routes', function () {
    $response = $this->get('/api/routes');

    $response->assertStatus(200)
        ->assertJsonIsArray();
});

test('the API products endpoint returns paginated products', function () {
    $response = $this->get('/api/products');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data',
            'total',
            'page',
            'pageSize',
            'totalPages',
        ]);
});
