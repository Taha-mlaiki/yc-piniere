<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function testOrderCreation()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Plantes aromatiques']);

        $plant1 = Plant::create([
            'name' => 'Basilic',
            'description' => 'Herbe aromatique',
            'price' => 5.99,
            'stock' => 100,
            'category_id' => $category->id,
        ]);

        $plant2 = Plant::create([
            'name' => 'Menthe',
            'description' => 'Herbe aromatique',
            'price' => 4.99,
            'stock' => 100,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/orders', [
                'items' => [
                    ['plant_slug' => $plant1->slug, 'quantity' => 2],
                    ['plant_slug' => $plant2->slug, 'quantity' => 1],
                ],
                'shipping_address' => '123 Test Street, Test City',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'order' => [
                    'id',
                    'user_id',
                    'status',
                    'total_amount',
                    'shipping_address',
                    'items',
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
            'shipping_address' => '123 Test Street, Test City',
        ]);

        $this->assertDatabaseHas('order_items', [
            'plant_id' => $plant1->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('order_items', [
            'plant_id' => $plant2->id,
            'quantity' => 1,
        ]);
    }

    public function testOrderCancellation()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Plantes aromatiques']);

        $plant = Plant::create([
            'name' => 'Basilic',
            'description' => 'Herbe aromatique',
            'price' => 5.99,
            'stock' => 100,
            'category_id' => $category->id,
        ]);

        $orderResponse = $this->actingAs($user)
            ->postJson('/api/orders', [
                'items' => [
                    ['plant_slug' => $plant->slug, 'quantity' => 2],
                ],
                'shipping_address' => '123 Test Street, Test City',
            ]);

        $orderId = $orderResponse->json('order.id');

        $response = $this->actingAs($user)
            ->postJson("/api/orders/{$orderId}/cancel");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Order cancelled successfully',
                'order' => [
                    'status' => 'cancelled',
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'cancelled',
        ]);
    }
}
