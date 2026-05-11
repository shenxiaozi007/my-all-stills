<?php

namespace App\Kernel\Base;

class BaseBusiness
{
    protected function getDataByMap(array $sourceData, array $map): array
    {
        $data = [];

        foreach ($map as $targetKey => $sourceKey) {
            $value = is_callable($sourceKey)
                ? $sourceKey($sourceData)
                : array_get($sourceData, $sourceKey);

            if (filled($value) || (is_string($value) && trim($value) === '')) {
                $data[$targetKey] = $value;
            }
        }

        return $data;
    }
}
