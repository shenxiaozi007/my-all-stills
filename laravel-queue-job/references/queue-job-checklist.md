# Queue / Job Checklist（本仓库）

## Job 定义
- [ ] 继承 `BaseJob`（或 RabbitMQ 特殊基类）
- [ ] `CONNECTION` 已定义且命名清晰
- [ ] 已实现 `getDuplicateFactor()`
- [ ] 已实现 `process()`

## 入队方式
- [ ] 生产端统一使用 `XxxJob::push(...)`
- [ ] 未直接使用 `dispatch(new XxxJob(...))`
- [ ] 入队参数可唯一标识业务上下文（便于去重与重跑）

## 重复与幂等
- [ ] 是否需要开启 `$enableDuplicateCheck`
- [ ] 重复任务是否要抛错（`$enableDuplicateError`）
- [ ] 锁释放时机已评估（`$delLockAtOnce`）
- [ ] 锁超时已评估（`$timeOut`）

## 消费者命令
- [ ] 消费命令与 Job `CONNECTION` 一致
- [ ] 参数已设置（`--tries` / `--timeout` / `--delay`）
- [ ] 使用 `--daemon` 常驻消费
- [ ] 消费器类型正确（`queue:work` 或 `rabbitmq:consume`）

## 失败处理
- [ ] 关键任务已实现 `failed()` 回写失败原因
- [ ] 可重跑路径已明确
- [ ] 失败日志可定位到业务主键

## 运维
- [ ] 新连接是否需要 `system*init:rabbitmq`
- [ ] exchange/queue/bind 初始化顺序已验证
