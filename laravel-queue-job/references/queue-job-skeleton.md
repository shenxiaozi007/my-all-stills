# Queue / Job Skeleton（本仓库）

## Job（示意）
```php
<?php

namespace App\Jobs\{Domain};

use App\Jobs\BaseJob;

class {XxxJob} extends BaseJob
{
    public const CONNECTION = '{connection_name}';

    protected bool $enableDuplicateCheck = true;

    public function __construct(protected array $payload)
    {
    }

    public function getDuplicateFactor(): array
    {
        return $this->payload ?: ['all' => 1];
    }

    public function process(): void
    {
        app({XxxBusiness}::class)->{method}($this->payload);
    }

    public function failed(\Throwable $e): void
    {
        // 可选：失败回写/告警
    }
}
```

## 生产端（示意）
```php
// 必须用 push，避免绕过 BaseJob 初始化与去重
{XxxJob}::push($payload);
```

## 消费者命令（queue:work）
```php
return $this->call('queue:work', [
    'connection' => {XxxJob}::CONNECTION,
    '--tries'    => 3,
    '--delay'    => 10,
    '--daemon',
    '--timeout'  => 120,
]);
```

## 消费者命令（rabbitmq:consume）
```php
return $this->call('rabbitmq:consume', [
    'connection' => {XxxJob}::CONNECTION,
    '--tries'    => 1,
    '--daemon',
    '--timeout'  => 3600,
]);
```

## 第三方消息桥接（示意）
```php
class RabbitMQJob extends BaseRabbitMQJob
{
    public function fire()
    {
        ReceiveDlOrderChangeMessageJob::push($this->getRawBody());
        $this->delete();
    }
}
```
