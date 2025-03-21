<?php
namespace App\DTO;
use App\DTO\BaseDTO;

class OrderDTO extends BaseDTO
{
    public array $items;
    public string $shipping_address;
    
    protected function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.plant_slug' => 'required|string|exists:plants,slug',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
        ];
    }
}