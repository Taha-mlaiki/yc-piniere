<?php
namespace App\D;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseDAO
{
    protected $model;

    public function find($id): ?Model
    {
        return $this->model->find($id);
    }

    public function findBy(string $field, $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
