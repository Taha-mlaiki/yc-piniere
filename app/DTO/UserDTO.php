<?php

use App\DTO\BaseDTO;

class UserDTO extends BaseDTO
{
    public string $name;
    public string $email;
    public string $password;
    public string $role = 'client';
    
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'sometimes|in:client,employee,admin',
        ];
    }
}