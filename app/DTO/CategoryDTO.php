<?php

use App\DTO\BaseDTO;

class CategoryDTO extends BaseDTO
{
    public string $name;
    public ?string $description = null;
    
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}