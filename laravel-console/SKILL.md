---
name: laravel-console
description: This skill should be used when the user asks to "新增 artisan 命令", "写批处理命令", "配置定时任务", "加队列消费命令", or needs command implementation aligned with app/Console/Commands and app/Console/Kernel.php in this repo.
---

# 本仓库命令行开发规范

## 目录与注册
- 服务目录先按当前仓库实际存在目录探测，常见候选为 `www/service.core.ys.com`、`www/service.manage.wg.com`。
- 命令类位置：`app/Console/Commands/**`
- 命令注册入口：`app/Console/Kernel.php` 的 `*Commands()` 分组方法
- 调度入口：`app/Console/Kernel.php` 的 `*Schedule()` 方法

## 默认流程
1. 先确认服务目录、命令目录、同模块签名风格、Kernel 注册分组。
2. 判断是否是一次性命令、定时任务、队列消费者或修复脚本。
3. `handle()` 保持编排：读取参数、基础校验、调用 Business、输出开始/结束/统计。
4. 大数据量或写操作评估分批、幂等、事务边界和可重跑方式。
5. 完成后做语法或可发现性检查，并说明需要用户手动执行的 artisan 命令。

## 需要先确认的情况
- 命令用途会修改大量数据、需要人工确认或涉及线上执行窗口。
- 调度频率、日志路径、任务开关配置无法从现有 Kernel 推断。
- 命令签名与同模块风格冲突，或需要新增业务前缀。

## 注意事项 / 禁止项
- 不把复杂业务直接写在 `handle()`。
- 调度任务不使用会阻塞的交互确认。
- 不主动执行会改数据的 artisan 命令；只提供命令给用户确认执行。

## 完成检查
- 命令签名、description、注册位置清晰。
- 复杂逻辑已下沉 Business。
- 写操作有幂等/事务/重跑策略。
- 如需调度，已配置 `withoutOverlapping()`、日志输出和配置开关。

## 命令签名习惯
本仓库同时存在多种签名风格，新增时按同模块既有风格保持一致：
- `模块@动作:子动作`：如 `report_center@queue:report_file_export`
- `模块:动作`：如 `patient:clean_ct_dcm_image`
- `模块*动作:子动作`：如 `system*init:rabbitmq`

## 推荐实现骨架
1. `handle()` 仅负责：
   - 参数/选项读取（`argument` / `option`）
   - 基础校验（空值、文件存在、枚举合法）
   - 调用 Business
   - 输出日志（开始、结束、统计）
2. 复杂逻辑下沉 Business，Dao 负责持久化。
3. 大批量任务采用分批或队列，必要时输出进度（如 `cmd_progress_bar`）。
4. 写操作明确幂等与事务边界。
5. 交互逻辑（如 `confirm()`）只用于人工触发场景，不阻塞调度任务。

## 调度实现要点（Kernel）
- 使用 `withoutOverlapping()` 避免重复并发。
- 使用 `runInBackground()` 避免阻塞 scheduler。
- 使用 `appendOutputTo()` 固定日志落盘路径。
- 任务开关通过 `config('schedule...')` 控制。

## 队列消费命令要点
- 消费命令仅封装 worker 调用，不写业务代码。
- `queue:work` 与 `rabbitmq:consume` 在仓库内均大量使用，按连接类型选。
- 统一明确 `connection`、`--tries`、`--timeout`，按需补 `--delay`。
- 长驻消费命令统一使用 `--daemon`。

## 常见场景模板
- 导入类命令：参数校验 -> 读文件 -> Business 导入 -> 失败统计。
- 报表类命令：校验任务状态 -> 触发导出 -> 更新状态。
- 修复类命令：支持按 ID/日期范围重跑，优先保证幂等。
- 队列初始化命令：可用 `system*init:rabbitmq` 模式批量声明 exchange/queue/bind。

## 参考模板
- `references/console-skeleton.md`
- `references/console-checklist.md`
