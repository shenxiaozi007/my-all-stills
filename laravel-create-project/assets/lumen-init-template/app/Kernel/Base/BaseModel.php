<?php

namespace App\Kernel\Base;

use App\Kernel\Traits\ModelTimeTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use SoftDeletes;
    use ModelTimeTraits;

    protected $connection = 'mysql';

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function isDeleted(): bool
    {
        $deletedAtColumn = $this->getDeletedAtColumn();

        return !is_null($this->{$deletedAtColumn});
    }
}
