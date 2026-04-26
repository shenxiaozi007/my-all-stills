# Console Checklist（本仓库）

## 命令实现
- [ ] signature / description 清晰
- [ ] 命令签名风格与同模块一致（`@` / `:` / `*` 组合）
- [ ] 参数、选项、路径等基础校验完整
- [ ] `handle()` 仅保留编排逻辑
- [ ] 复杂业务已下沉 Business

## 稳定性
- [ ] 大数据量已分批或异步
- [ ] 关键写操作有幂等策略
- [ ] 有开始/结束/失败日志
- [ ] 失败后可重跑（按 ID / 日期 / 游标）

## 队列消费者（如适用）
- [ ] 消费命令与 Job `CONNECTION` 一致
- [ ] 消费参数已明确（`--tries` / `--delay` / `--timeout`）
- [ ] 使用 `--daemon` 常驻消费
- [ ] 消费器类型已确认（`queue:work` 或 `rabbitmq:consume`）

## 调度（如适用）
- [ ] Kernel 已注册
- [ ] schedule 已配置
- [ ] 使用 `withoutOverlapping()`
- [ ] 使用 `runInBackground()`
- [ ] 设置 `appendOutputTo()`
- [ ] 开关走 `config('schedule...')`

## 运维初始化（RabbitMQ 适用）
- [ ] 是否需要补 `system*init:rabbitmq`
- [ ] exchange/queue/bind 初始化顺序已验证
