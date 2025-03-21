<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlantTest extends TestCase
{
    use RefreshDatabase;

    public function testPlantCreation()
    {
        $category = Category::create([
            'name' => 'Plantes aromatiques',
            'description' => 'Plantes pour la cuisine',
        ]);

        $plant = Plant::create([
            'name' => 'Basilic Aromatique',
            'description' => 'Herbe aromatique méditerranéenne',
            'price' => 5.99,
            'stock' => 100,
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('plants', [
            'name' => 'Basilic Aromatique',
            'slug' => 'basilic-aromatique',
        ]);
    }

    public function testPlantSlug()
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

        $this->assertEquals('basilic-aromatique', $plant->slug);
        $this->assertNotNull(Plant::where('slug', 'basilic-aromatique')->first());
    }
}

