---
name: laravel-queue-job
description: This skill should be used when the user asks to "新增 Job", "加队列消费者", "接入 RabbitMQ", "做异步任务", or needs queue/job implementation aligned with BaseJob and Queue command patterns in this repository.
---

# 本仓库 Queue / Job 开发规范

## 适用位置
- Job：`www/service.manage.wg.com/app/Jobs/**`
- 消费者命令：`www/service.manage.wg.com/app/Console/Commands/**/Queue/**`
- 队列初始化：`www/service.manage.wg.com/app/Console/Commands/System/Init/InitQueue.php`

## Job 规范（BaseJob 体系）
1. 业务 Job 优先继承 `App\Jobs\BaseJob`。
2. 必须定义 `CONNECTION` 常量，并与消费者命令保持一致。
3. 必须实现：
   - `getDuplicateFactor(): array`
   - `process(): void`
4. 入队必须优先使用 `XxxJob::push(...)`，复用基类初始化、操作人透传与重复检测。
5. 禁止直接 `dispatch(new XxxJob(...))`（会绕过 BaseJob 的重复锁与初始化流程）。
6. 有重复风险时按需开启：
   - `protected bool $enableDuplicateCheck = true;`
   - `protected bool $enableDuplicateError = true;`
   - `protected bool $delLockAtOnce = false;`
   - `protected int $timeOut = 600;`
7. 对导出类等关键任务，建议实现 `failed(Throwable $e)` 做失败回写。

## 消费者命令规范
1. 消费者命令只负责调用 worker，不写业务逻辑。
2. 常见消费方式：
   - `queue:work`
   - `rabbitmq:consume`
3. 参数至少明确：`connection`、`--tries`、`--timeout`；按需补 `--delay`。
4. 长驻消费命令统一使用 `--daemon`。

## RabbitMQ 特殊场景
- 第三方消息接入可使用 `VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob` 作为入口，内部再 `push` 到系统业务 Job。
- 新增连接后可通过 `system*init:rabbitmq` 批量初始化 exchange/queue/bind。

## 参考模板
- `references/queue-job-skeleton.md`
- `references/queue-job-checklist.md`
