---
name: laravel-import-export
description: This skill should be used when the user asks to "做导入", "做导出", "Excel/CSV 批量处理", "报表中心导出", or needs implementation aligned with this repository's ImportPatientBusiness and ReportCenter command patterns.
---

# 本仓库导入导出开发规范

## 适用位置
- 导入命令：`app/Console/Commands/**/Data/**`
- 导入业务：`app/Modules/Resource/Business/**`、`app/Modules/Management/Business/**`
- 报表导出：`app/Console/Commands/ReportCenter/**` + 报表业务层
- 模板资源：`resources/import/**`

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
