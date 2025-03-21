<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCategoryCreation()
    {
        $category = Category::create([
            'name' => 'Plantes aromatiques',
            'description' => 'Plantes pour la cuisine',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Plantes aromatiques',
            'slug' => 'plantes-aromatiques',
        ]);
    }

    public function testCategoryRelationship()
    {
        $category = Category::create([
            'name' => 'Plantes aromatiques',
            'description' => 'Plantes pour la cuisine',
        ]);

        Plant::create([
            'name' => 'Basilic',
            'description' => 'Herbe aromatique méditerranéenne',
            'price' => 5.99,
            'stock' => 100,
            'category_id' => $category->id,
        ]);

        $this->assertEquals(1, $category->plants()->count());
    }
}
