<?php

use App\Models\Category;

class CategoryDAO extends BaseDAO
{
    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)->first();
    }
}
