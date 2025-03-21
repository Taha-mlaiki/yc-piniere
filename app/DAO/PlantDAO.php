<?php

use App\D\BaseDAO;
use App\Models\Plant;
use App\Models\PlantImage;
use Illuminate\Database\Eloquent\Collection;

class PlantDAO extends BaseDAO
{
    public function __construct(Plant $model)
    {
        $this->model = $model;
    }

    public function findBySlug(string $slug): ?Plant
    {
        return $this->model->with(['category', 'images'])->where('slug', $slug)->first();
    }

    public function findAllWithCategory(): Collection
    {
        return $this->model->with(['category', 'images'])->get();
    }

    public function findByCategory(int $categoryId): Collection
    {
        return $this->model->where('category_id', $categoryId)->with(['category', 'images'])->get();
    }

    public function countImages(int $plantId): int
    {
        return PlantImage::where('plant_id', $plantId)->count();
    }
}
