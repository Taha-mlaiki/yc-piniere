<?php
namespace App\DTO;
use App\DTO\BaseDTO;

class OrderItemDTO extends BaseDTO
{
    public string $plant_slug;
    public int $quantity;

    protected function rules(): array
    {
        return [
            'plant_slug' => 'required|string|exists:plants,slug',
            'quantity' => 'required|integer|min:1',
        ];
    }
}
