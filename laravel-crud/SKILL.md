---
name: laravel-crud
description: This skill should be used when the user asks to "新增接口", "改增删改查", "做列表详情", "调整业务逻辑", or needs CRUD implementation that follows BaseController + Modules Business/Dao patterns in this repository.
---

# 本仓库 CRUD 开发规范

## 典型分层路径
- Controller：`app/Http/Controllers/Management/Proxy/**`（常继承 `BaseController`）
- Business：`app/Modules/Management/Business/**`（常继承 `BaseBusiness`）
- Dao：`app/Modules/Basics/Dao/**`、`app/Modules/**/Dao/**`
- Model：`app/Modules/**/Model/**`

## Controller 约束
- 仅收参、调用 Business、`revert()` 返回。
- 推荐构造器注入：`__construct(protected Request $request, protected XxxBusiness $business)`。
- 不在 Controller 写复杂业务分支。
- 管理端写操作通常透传 `management_auth_info()`。
- 管理端路由通常配 `auth:jwt-management` + `WebRoute::*` 权限点，保持与现有路由文件一致。

## Business 约束
- 承担参数校验、业务编排、事务控制。
- 常用 `validator(...)->validate()` 做规则校验。
- 枚举型参数优先使用 `Rule::in(XXX::all())`。
- 复杂入参场景建议补 `customAttributes` 优化报错提示。
- 跨表写入必须明确事务边界（`DB::transaction` 或 `app('db')->transaction`）。
- 外部 API 调用与事件触发只保留在业务编排层。

## Dao 约束
- 封装查询、分页、落库、局部更新。
- 复用项目基础 Dao 能力（如 `getList/getPageList/findBy...`）。
- 禁止 SQL 字符串拼接；Raw 语句必须参数绑定。

## 高风险点清单
- 状态流转要校验前置状态，避免越级更新。
- 写接口建议考虑防重复提交（路由 `api_mutex`）。
- 业务异常统一抛项目异常，不直接返回散乱错误结构。

## 实施步骤
1. 先定义接口输入输出与权限边界。
2. 新增/修改 Controller 方法并保持薄。
3. 在 Business 实现校验和主流程。
4. 在 Dao 完成数据读写。
5. 需要常量时补到 `Constant`/Model。
6. 结构变更补 migration。

## 参考模板
- `references/crud-skeleton.md`
- `references/crud-checklist.md`
