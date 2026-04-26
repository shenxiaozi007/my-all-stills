# Standards Checklist（本仓库）

## 目标
在开始编码前快速确认分层、配置、安全和发布边界，避免返工。

## 需求确认
- [ ] 已明确所属模块（Management / Resource / Basics / Service）
- [ ] 已确认入口（Controller / Command / Queue / Schedule）
- [ ] 已确认是否涉及表结构变更（migration）

## 分层检查
- [ ] Controller 仅收参与返回 `revert()`
- [ ] Business 承担校验、编排、事务
- [ ] Dao 承担查询和持久化
- [ ] 常量/枚举放 `Constant` 或 Model 常量

## 参数与规则
- [ ] 枚举入参优先用 `Rule::in(XXX::all())`
- [ ] 复杂表单已评估 `customAttributes`
- [ ] 需要防重复提交的写接口已评估 `api_mutex`
- [ ] 需要按字段防重时已评估 `api_mutex:{field}`
- [ ] 管理端路由中间件与权限点已核对（`auth:jwt-management` + `WebRoute::*`）
- [ ] 高频只读接口是否需要 `api.cache` 已评估

## 配置与安全
- [ ] 业务代码无直接 `env()`
- [ ] 配置统一走 `config()`
- [ ] Raw SQL 参数绑定，无拼接风险
- [ ] 外部输入有边界校验

## 交付检查
- [ ] 错误信息可定位且不泄露敏感细节
- [ ] 管理端写操作已评估 `management_auth_info()` 传递
- [ ] 如涉及结构变更，已补 migration 并评估回滚
