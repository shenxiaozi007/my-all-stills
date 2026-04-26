# CRUD Checklist（本仓库）

## 路由与权限
- [ ] 管理端路由组已确认（`auth:jwt-management`）
- [ ] 路由 `as` 权限点使用 `WebRoute::*` 常量
- [ ] 写接口是否需要 `api_mutex` 已评估

## 分层
- [ ] Controller 仅收参与返回
- [ ] Controller 使用构造器注入 `Request + Business`
- [ ] Business 承担校验与流程编排
- [ ] Dao 承担读写与查询

## 校验与事务
- [ ] 参数规则完整（validator）
- [ ] 枚举参数使用 `Rule::in()`
- [ ] 复杂入参已评估 `customAttributes`
- [ ] 跨表写入在事务中执行
- [ ] 状态流转有前置状态检查

## 安全与可维护性
- [ ] 无 SQL 字符串拼接
- [ ] Raw 查询参数绑定
- [ ] 管理端写操作已传 `management_auth_info()`

## 交付
- [ ] 返回结构统一 `revert()`
- [ ] 异常统一项目异常体系
- [ ] 涉及结构变更时已补 migration
