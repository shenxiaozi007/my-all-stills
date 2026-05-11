<?php

namespace App\Kernel\Traits;

use Carbon\Carbon;

trait ModelTimeTraits
{
    public function freshTimestamp()
    {
        return Carbon::now();
    }

    public function fromDateTime($value): string
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
