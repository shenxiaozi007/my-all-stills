---
name: laravel-import-export
description: This skill should be used when the user asks to "做导入", "做导出", "Excel/CSV 批量处理", "报表中心导出", or needs implementation aligned with this repository's ImportPatientBusiness and ReportCenter command patterns.
---

# 本仓库导入导出开发规范

## 适用位置
- 服务目录先按当前仓库实际存在目录探测，常见候选为 `www/service.core.ys.com`、`www/service.manage.wg.com`。
- 导入命令：`app/Console/Commands/**/Data/**`
- 导入业务：`app/Modules/Resource/Business/**`、`app/Modules/Management/Business/**`
- 报表导出：`app/Console/Commands/ReportCenter/**` + 报表业务层
- 模板资源：`resources/import/**`

## 默认流程
1. 先确认同模块导入/导出命令、Business、Job、报表中心任务和文件解析方式。
2. 导入先明确文件来源、表头映射、必填列、去重键、失败记录和重跑策略。
3. 导出先明确筛选条件快照、数据权限、敏感字段、任务状态流转和失败回写。
4. 大文件优先分块处理；大导出优先考虑异步任务或报表中心，但按现有模块模式落地。
5. 完成后做最小验证，并说明用户需要执行的命令或触发方式。

## 需要先确认的情况
- 导入模板字段、枚举映射、去重规则、覆盖/跳过策略属于业务口径。
- 导出字段范围、脱敏规则、权限边界或统计口径无法从现有代码推断。
- 文件路径语义在 `resource_path()` 相对路径和绝对路径之间不明确。

## 注意事项 / 禁止项
- 不依赖隐式列序；表头映射要显式。
- 不一次性加载明显大文件。
- 不让导出长时间阻塞请求线程。
- Raw SQL 必须参数绑定。

## 完成检查
- 文件来源、字段映射、校验、失败定位、幂等策略已说明或实现。
- 大文件有分批/分块方案。
- 导出任务有状态流转和失败上下文。
- 涉及命令/Job 时已按对应 skill 的协议检查。

## 导入（Import）标准流程
1. 命令层先校验参数：
   - 业务标识（诊所/渠道/模式）
   - 文件路径存在性
   - 开关参数合法性
2. Business 显式定义表头映射（如 `title_maps`），不要依赖隐式列序。
3. 优先使用 `getResourceData()` 或 `chunkParseExcel()` 处理文件，避免整文件一次性入内存。
4. 每行执行校验与转换（手机号、枚举、关联对象、渠道映射）。
5. 写库按批次或单条事务，保证失败隔离与重跑能力。
6. 记录处理进度和失败原因（至少能定位到行）。

## 导出（Export）标准流程
1. 固化查询条件与任务参数快照。
2. 大导出优先走异步任务/报表中心，避免阻塞请求线程。
3. 维护状态流转（待处理/处理中/成功/失败）。
4. 导出失败保留错误上下文，支持人工重试。

## 本仓库常见实现习惯
- 命令层只做参数读取和流程触发，核心逻辑下沉 Business。
- 导入过程中常用进度输出（如 `cmd_progress_bar`）。
- 行级失败通常可捕获后继续处理，最后统一汇总失败信息。
- 报表中心 Job 可实现 `failed()` 回写任务失败原因。
- 报表中心命令会先校验状态，再触发导出或失败回写。
- 资源导入业务基类默认以 `resource_path()` 读取文件；若传绝对路径，优先确认调用方逻辑支持。

## 安全与稳定性
- 输入文件不可信：先校验格式、字段、大小。
- Raw SQL 必须参数绑定。
- 重复导入必须定义去重规则或幂等键。
- 导出必须考虑权限与敏感字段脱敏。

## 参考模板
- `references/import-export-skeleton.md`
- `references/import-export-checklist.md`
