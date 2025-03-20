<?php

namespace App\DTO;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class BaseDTO
{
    public function __construct(array $data)
    {
        $validator = Validator::make($data, $this->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $this->fill($validator->validated());
    }

    abstract protected function rules(): array;

    protected function fill(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
