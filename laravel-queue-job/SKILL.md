---
name: laravel-queue-job
description: 当用户要求“新增 Job”“加队列消费者”“接入 RabbitMQ”“做异步任务”，或需要按本仓库 BaseJob 与 Queue 命令模式实现队列/异步任务时使用本技能。
---

# 本仓库 Queue / Job 开发规范

## 适用位置
- 服务目录先按当前仓库实际存在目录探测，当前重点候选为 `www/service.manage.wg.com`、`www/service.his.wg.com`。
- 如果当前目录同时包含 `wg-manage-service` 和 `wg-his-service`，先判断目标项目，再进入对应服务目录。
- Job：`app/Jobs/**`
- BaseJob：两个项目均有 `app/Jobs/BaseJob.php`，新增业务 Job 前必须先看该文件和同模块 Job。
- 事件类异步：两个项目均有 `app/EventGroup/Jobs/**`，事件订阅/观察者相关任务优先看 EventGroup 体系。
- 消费者命令：`app/Console/Commands/**/Queue/**`
- 队列初始化：`app/Console/Commands/System/Init/InitQueue.php`

## 默认流程
1. 先确认同模块 Job、`BaseJob` 能力、消费者命令、连接名、队列初始化和入队方式。
2. 判断是否适合 `BaseJob`、是否需要重复检测、失败回写和可重跑路径。
3. 优先使用 `XxxJob::push(...)` 入队；现有模块有明确例外时跟随现有模式并说明原因。
4. 消费者命令只封装 worker 调用，业务逻辑留在 Job / Business。
5. 完成后做语法检查，并给出需要用户手动执行的消费者或初始化命令。
6. 不确定是普通 Job 还是 EventGroup Job 时，先根据现有触发入口、订阅器、观察者和同模块目录判断。

## 需要先确认的情况
- 新增连接、exchange、queue、routing key 或第三方消息协议。
- 重复任务是否报错、锁释放时机、失败后是否自动重试属于业务策略。
- 消费者运行方式、tries、timeout、delay 无法从同模块推断。

## 注意事项 / 禁止项
- 默认不直接 `dispatch(new XxxJob(...))`，避免绕过 BaseJob 初始化和去重。
- 不在消费者命令中写业务逻辑。
- 不主动启动长驻消费者或执行会消费线上消息的命令。

## 完成检查
- Job 连接名、去重因子、`process()`、失败处理已评估。
- 入队端使用项目约定方式。
- 消费者命令与 Job `CONNECTION` 一致。
- 如新增 RabbitMQ 资源，已评估是否需要初始化命令。

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
