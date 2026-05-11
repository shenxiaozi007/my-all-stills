<?php

namespace App\Kernel\Base;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseDao
{
    protected array $selectColumns = ['*'];

    abstract public function getModel(): Model;

    public static function getInstance(): static
    {
        return new static();
    }

    public function newBuilder(): Builder
    {
        return $this->getModel()->newQuery();
    }

    public function find($id, array $columns = ['*'], array $relations = [])
    {
        return $this->newBuilder()
            ->select($columns ?: $this->selectColumns)
            ->with($relations)
            ->find($id);
    }

    public function findOrFail($id, array $columns = ['*'], array $relations = [])
    {
        return $this->newBuilder()
            ->select($columns ?: $this->selectColumns)
            ->with($relations)
            ->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->getModel()->newQuery()->create($data);
    }

    public function updateById($id, array $data): int
    {
        return $this->newBuilder()
            ->whereKey($id)
            ->update($data);
    }
}
