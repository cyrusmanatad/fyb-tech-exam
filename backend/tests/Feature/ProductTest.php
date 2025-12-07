<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user, 'api');
});

test('index returns products', function () {
    Product::factory()->count(3)->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->getJson('/api/products');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => ['id', 'user_id', 'sku_code', 'sku_desc', 'sku_uom', 'sku_price', 'created_at', 'updated_at', 'user']
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);
});

test('store creates product', function () {
    $productData = [
        'user_id' => $this->user->id,
        'sku_code' => 'SKU001',
        'sku_desc' => 'Test Product',
        'sku_uom' => 'Pcs',
        'sku_price' => 10.99,
    ];

    $response = $this->postJson('/api/products', $productData);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Product created successfully'
        ]);

    $this->assertDatabaseHas('products', [
        'sku_code' => 'SKU001',
    ]);
});

test('store validation errors', function () {
    $productData = [
        'user_id' => $this->user->id,
        'sku_code' => '', // Invalid
        'sku_desc' => 'Test Product',
        'sku_uom' => 'Pcs',
        'sku_price' => 'invalid-price', // Invalid
    ];

    $response = $this->postJson('/api/products', $productData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['sku_code', 'sku_price']);
});

test('show returns product', function () {
    $product = Product::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->getJson('/api/products/' . $product->id);

    $response->assertStatus(200)
        ->assertJson([
            'id' => $product->id,
            'sku_code' => $product->sku_code,
        ]);
});

test('update updates product', function () {
    $product = Product::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $updatedData = [
        'user_id' => $this->user->id,
        'sku_code' => 'UPDATED_SKU',
        'sku_desc' => 'Updated Product Description',
        'sku_uom' => 'Box',
        'sku_price' => 25.50,
    ];

    $response = $this->putJson('/api/products/' . $product->id, $updatedData);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Product updated successfully'
        ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'sku_code' => 'UPDATED_SKU',
    ]);
});

test('update validation errors', function () {
    $product = Product::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $updatedData = [
        'user_id' => $this->user->id,
        'sku_code' => '', // Invalid
        'sku_desc' => 'Updated Product Description',
        'sku_uom' => 'Box',
        'sku_price' => 'not-a-number', // Invalid
    ];

    $response = $this->putJson('/api/products/' . $product->id, $updatedData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['sku_code', 'sku_price']);
});

test('destroy deletes product', function () {
    $product = Product::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->deleteJson('/api/products/' . $product->id);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Product deleted successfully'
        ]);

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});
