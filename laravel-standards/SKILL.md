---
name: laravel-standards
description: This skill should be used when the user asks to "按后端规范开发", "检查分层是否合理", "按项目规范改造", "做接口但要符合当前仓库规范", or needs a baseline checklist for this Lumen/Laravel service.
---

# 本仓库 Laravel/Lumen 开发基线

## 适用范围
- 代码根目录：`www/service.manage.wg.com`
- 主要分层：
  - Controller：`app/Http/Controllers/**`
  - Business：`app/Modules/**/Business/**`
  - Dao：`app/Modules/**/Dao/**`
  - Model：`app/Modules/**/Model/**`

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
