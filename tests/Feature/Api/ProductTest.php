<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('get all products returns paginated list', function () {
    Product::factory()->count(15)->create();

    $response = $this->getJson('/api/products');

    $response->assertStatus(200)
        ->assertJsonStructure(['data', 'total', 'page', 'pageSize', 'totalPages']);
});

test('get all products filters by availability', function () {
    Product::factory()->create(['name' => 'Available Item', 'is_available' => true]);
    Product::factory()->create(['name' => 'Unavailable Item', 'is_available' => false]);

    $response = $this->getJson('/api/products?is_available=true');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'Available Item']);
});

test('get product categories returns unique categories', function () {
    Product::factory()->create(['category' => 'Food']);
    Product::factory()->create(['category' => 'Drink']);
    Product::factory()->create(['category' => 'Food']);

    $response = $this->getJson('/api/products/categories');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['data' => ['Drink', 'Food']]);
});

test('get single product returns product details', function () {
    $product = Product::factory()->create(['name' => 'Onigiri Salmon']);

    $response = $this->getJson('/api/products/'.$product->id);

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'Onigiri Salmon']);
});

test('get non-existent product returns 404', function () {
    $response = $this->getJson('/api/products/99999');

    $response->assertStatus(404)
        ->assertJsonStructure(['error']);
});
