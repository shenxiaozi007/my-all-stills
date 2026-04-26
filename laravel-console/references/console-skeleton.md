# Console Skeleton（本仓库）

```php
<?php

namespace App\Console\Commands\{Domain};

use App\Modules\{Module}\Business\{XxxBusiness};
use Illuminate\Console\Command;

class {XxxCommand} extends Command
{
    // 按所在模块延续既有风格：模块@动作:子动作 / 模块:动作 / 模块*动作:子动作
    protected $signature = '{module}@{action} {--option=}';

    protected $description = '{描述}';

    public function handle(): void
    {
        $option = $this->option('option');

        if (blank($option)) {
            $this->error('option 不能为空');
            return;
        }

        /** @var {XxxBusiness} $business */
        $business = app({XxxBusiness}::class);

        $this->info('任务开始');

        // 复杂逻辑下沉 Business
        $business->{method}($option);

        $this->info('任务结束');
    }
}
```

## 队列消费者命令（queue:work）
```php
<?php

namespace App\Console\Commands\{Domain}\Queue;

use App\Jobs\{Domain}\{XxxJob};
use Illuminate\Console\Command;

class {XxxQueue} extends Command
{
    protected $signature = '{module}@queue:{action}';

    protected $description = '消费者：{描述}';

    public function handle(): int
    {
        return $this->call('queue:work', [
            'connection' => {XxxJob}::CONNECTION,
            '--tries'    => 3,
            '--delay'    => 10,
            '--daemon',
            '--timeout'  => 120,
        ]);
    }
}
```

## RabbitMQ 消费者命令（rabbitmq:consume）
```php
return $this->call('rabbitmq:consume', [
    'connection' => {XxxJob}::CONNECTION,
    '--tries'    => 1,
    '--daemon',
    '--timeout'  => 3600,
]);
```

## 注册与调度
1. 在 `app/Console/Kernel.php` 对应 `*Commands()` 中注册命令类。
2. 需要定时时，在对应 `*Schedule()` 增加：
   - `withoutOverlapping()`
   - `runInBackground()`
   - `appendOutputTo()`
3. 按模块开关使用 `config('schedule...')` 控制是否启用。
4. 如涉及 RabbitMQ 队列初始化，可补 `system*init:rabbitmq` 运维命令。
