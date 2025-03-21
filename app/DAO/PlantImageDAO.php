<?php

use App\Models\PlantImage;
use Illuminate\Database\Eloquent\Collection;

class PlantImageDAO extends BaseDAO
{
    public function __construct(PlantImage $model)
    {
        $this->model = $model;
    }

    public function addImage(int $plantId, string $path, bool $isMain = false): PlantImage
    {
        // If this is the main image, reset all other main images for this plant
        if ($isMain) {
            $this->model->where('plant_id', $plantId)->update(['is_main' => false]);
        }

        return $this->model->create([
            'plant_id' => $plantId,
            'path' => $path,
            'is_main' => $isMain,
        ]);
    }

    public function getImagesForPlant(int $plantId): Collection
    {
        return $this->model->where('plant_id', $plantId)->get();
    }

    public function countImagesForPlant(int $plantId): int
    {
        return $this->model->where('plant_id', $plantId)->count();
    }
}
