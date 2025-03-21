<?php
namespace App\DTO;
use App\DTO\BaseDTO;

class PlantDTO extends BaseDTO
{
    public string $name;
    public string $description;
    public float $price;
    public int $stock;
    public int $category_id;
    
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}