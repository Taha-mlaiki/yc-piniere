<?php
namespace App\DTO;
use App\DTO\BaseDTO;

class OrderStatusDTO extends BaseDTO
{
    public string $status;

    protected function rules(): array
    {
        return [
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ];
    }
}
