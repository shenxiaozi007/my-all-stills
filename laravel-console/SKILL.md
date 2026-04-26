---
name: laravel-console
description: This skill should be used when the user asks to "新增 artisan 命令", "写批处理命令", "配置定时任务", "加队列消费命令", or needs command implementation aligned with app/Console/Commands and app/Console/Kernel.php in this repo.
---

# 本仓库命令行开发规范

## 目录与注册
- 命令类位置：`www/service.manage.wg.com/app/Console/Commands/**`
- 命令注册入口：`app/Console/Kernel.php` 的 `*Commands()` 分组方法
- 调度入口：`app/Console/Kernel.php` 的 `*Schedule()` 方法

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
