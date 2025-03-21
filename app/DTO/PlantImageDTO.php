<?php
namespace App\DTO;
use App\DTO\BaseDTO;

class PlantImageDTO extends BaseDTO
{
    public int $plant_id;
    public $image;
    public bool $is_main = false;
    
    protected function rules(): array
    {
        return [
            'plant_id' => 'required|exists:plants,id',
            'image' => 'required|image|max:2048',
            'is_main' => 'sometimes|boolean',
        ];
    }
}