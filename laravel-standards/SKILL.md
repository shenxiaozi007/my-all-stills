---
name: laravel-standards
description: This skill should be used when the user asks to "按后端规范开发", "检查分层是否合理", "按项目规范改造", "做接口但要符合当前仓库规范", or needs a baseline checklist for this Lumen/Laravel service.
---

# 本仓库 Laravel/Lumen 开发基线

## 适用范围
- 代码根目录优先探测当前仓库实际存在的服务目录，常见候选：
  - `www/service.core.ys.com`
  - `www/service.manage.wg.com`
- 多个候选都存在时，结合用户打开文件、当前任务、已有模块归属判断；仍无法判断再问用户。
- 主要分层：
  - Controller：`app/Http/Controllers/**`
  - Business：`app/Modules/**/Business/**`
  - Dao：`app/Modules/**/Dao/**`
  - Model：`app/Modules/**/Model/**`

## 默认流程
1. 先探索当前仓库事实：服务目录、路由入口、同模块 Controller/Business/Dao/Model、已有命名和工具函数。
2. 复用现有模块、分层、异常、校验、事务和返回模式；只在缺少本地模式时参考模板。
3. 控制改动范围，只处理用户目标必需的文件；不顺手做无关重构。
4. 涉及高风险变更或产品意图不明确时先确认，再执行。
5. 交付时说明改了什么、验证了什么、用户下一步需要执行什么命令。

## 需要先确认的情况
- 多个服务目录或模块候选都合理，且无法从打开文件/现有代码判断归属。
- 需求涉及删除字段、改字段类型、大表索引、历史数据迁移或发布顺序风险。
- 权限边界、状态流转、导入导出口径、异步任务语义等属于产品决策而非代码事实。

## 注意事项 / 禁止项
- 不凭记忆猜路径；先用 `rg` / `find` / 读取同模块文件确认。
- 不主动覆盖用户已有改动，不执行破坏性 git 操作。
- 不直接在业务代码使用 `env()`，不拼接 Raw SQL。
- 不把模板当硬规则；已有模块写法清晰时优先跟随现有模式。

## 完成检查
- 已说明采用的服务目录和模块归属。
- 已保持 Controller / Business / Dao / Model 分层边界。
- 已做最小验证：如 `php -l`、相关测试、静态搜索，或说明无法执行的原因。
- 如有 migration、队列、命令或发布步骤，已给出用户可手动执行的命令。

## 核心约束（Must）
1. 控制器只做收参、调用、响应包装，统一通过 `revert()` 返回（`App\Kernel\Base\BaseController`）。
2. 业务规则写在 Business（常继承 `App\Kernel\Base\BaseBusiness`）。
3. 数据访问写在 Dao（一个表对应一个 Dao）。
4. 业务代码统一通过 `config()` 读取配置，禁止直接 `env()`。
5. 涉及表结构变更必须使用 migration。
6. SQL 禁止字符串拼接；Raw 查询必须参数绑定。

## 仓库高频实现习惯
- 管理端路由组通常统一挂 `auth:jwt-management`，并通过 `WebRoute` 常量配置 `as` 权限点。
- 管理端写操作通常透传 `management_auth_info()` 到 Business。
- 防重复提交接口优先考虑路由中间件 `api_mutex`（支持指定锁字段，如 `api_mutex:source_agent_no`）。
- 高频只读选项接口可评估 `api.cache`（常见于 `addRoute(['GET','POST'], ...)` 的 options 接口）。
- 参数校验常在 Business 使用 `validator(...)->validate()`。
- 枚举值优先放在 `App\Modules\Basics\Constant\**` 或 Model 常量，并通过 `Rule::in()` 约束入参。
- 复杂表单建议在 Business 使用 `customAttributes` 提升校验报错可读性。

## 推荐开发步骤
1. 先确认需求归属模块（Management / Resource / Basics / Service）。
2. 先定分层方法签名：Controller 方法、Business 主流程方法、Dao 读写方法。
3. 在 Business 完成校验、流程编排、事务边界。
4. 在 Dao 实现查询和写入，避免把 SQL 散在业务层。
5. 回到 Controller 统一包装响应。
6. 若涉及字段、索引、表结构变化，补 migration 并核对环境分支。

## 安全基线
- 所有外部输入在边界层校验。
- 所有 Raw SQL 参数绑定。
- 导入、导出、命令行批处理必须做批次与内存控制。
- 异常可定位但不泄露敏感配置与 SQL 细节。

## 参考模板
- `references/standards-checklist.md`
