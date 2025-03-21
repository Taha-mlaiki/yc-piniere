<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlantApiTest extends TestCase
{
    use RefreshDatabase;

    public function testGetPlantsBySlug()
    {
        $category = Category::create([
            'name' => 'Plantes aromatiques',
        ]);

        $plant = Plant::create([
            'name' => 'Basilic Aromatique',
            'description' => 'Herbe aromatique méditerranéenne',
            'price' => 5.99,
            'stock' => 100,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/plants/basilic-aromatique');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'plant' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'stock',
                    'category_id',
                    'created_at',
                    'updated_at',
                    'category',
                    'images',
                ],
            ]);
    }

    public function testPlantNotFound()
    {
        $response = $this->getJson('/api/plants/non-existent-plant');

        $response->assertStatus(404);
    }
}

