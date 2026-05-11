---
name: laravel-create-project
description: 当用户要求创建、初始化、脚手架化或规划新的 Laravel/Lumen 后端项目时使用本技能，尤其适用于“新项目”“新后端项目”“创建项目”“项目目录”“项目骨架”，或需要作为 CRUD、migration、console、queue、import/export、OpenAPI、git-flow 等 Laravel 技能的第一个前置技能。
---

# Laravel 新后端项目初始化

## 定位
这是本仓库 Laravel/Lumen 后端项目的第一个 skill。先用它确定新项目的创建方式、仓库目录、代码目录、分层骨架、环境和后续 skills 使用顺序，再交给 `laravel-standards`、`laravel-crud`、`laravel-migrations` 等具体 skills 落功能。

## 默认流程
1. 先确认项目类型：Laravel 还是 Lumen；若用户未说明，结合现有 `wg-manage-service`、`wg-his-service` 的服务形态，默认建议 Lumen 风格服务。
2. 先用 Composer 创建新项目，禁止复制既有项目目录当新项目。
3. 按 `{业务名}-{项目说明}-service` 作为仓库目录候选，代码放在 `www/service.{domain}.{company}.com` 风格目录。
4. 读取用户给出的项目名、域名、模块范围和是否管理端/服务端/API；缺少信息时给出保守占位，不要凭空决定业务命名。
5. 生成目录规划，而不是直接迁移旧项目代码；公共能力按“必须模板 / 按需模板 / 不进入模板”分档。
6. 若用户要求初始化模板、Base 类、Provider 或 bootstrap 流程，读取 `references/initialization-template.md`，并优先使用 `assets/lumen-init-template/` 中已提取的模板文件。
7. 明确后续 skills 的顺序：先 `laravel-standards` 定边界，再按需求切到 CRUD、migration、console、queue、import/export、openapi、git-flow。

## 创建命令
优先使用官方创建命令：

```bash
composer create-project --prefer-dist laravel/laravel {service-code-dir}
composer create-project --prefer-dist laravel/lumen {service-code-dir}
```

绝不通过复制 `wg-manage-service` 或 `wg-his-service` 整个代码目录来创建新项目。

## 基础目录约定
新仓库建议：

```text
{project-repo}/
├── www/
│   └── service.{name}.{company}.com/
├── docs/
└── database/
```

服务代码内的详细目录骨架见 `references/project-structure.md`。当用户要求“生成项目相关目录”或“给目录模板”时读取该文件。

初始化基础类、Provider、bootstrap、配置加载和 helper 清单见 `references/initialization-template.md`。当用户要求“通用文件”“base 型文件”“Provider 初始化”“初始化流程模板”时读取该文件。

统计类接口的分层写法见 `references/statistics-endpoint-template.md`。当用户要求“统计接口模板”“统计接口怎么写”“新增统计接口并按项目规范落地”时读取该文件；若只是创建新项目，不必默认展开统计模板。

可直接复制到新 Lumen 项目的初始化模板位于 `assets/lumen-init-template/`。复制后必须按新项目业务裁剪 middleware、Provider、guard、三方依赖和 config，不要再回头依赖 `wg-manage-service` 或 `wg-his-service` 的源文件路径。

## 分层原则
- Controller 只收参、调用 Business、包装响应。
- Business 放业务校验、流程编排、事务边界。
- Dao 放查询和持久化，一个主要表对应一个 Dao。
- 统计接口若多个统计值来自同一张表，优先在 Dao 中用一条聚合 SQL 统计，Business 只做参数校验、字段映射和结果组装。
- Model 放表映射、常量、scope 查询能力。
- Constant / Factory / Rule / Exception 按业务模块收敛，不把枚举和工厂散在 Controller。
- 配置统一 `.env` -> `config/*` -> `config()`，业务代码禁止直接 `env()`。
- 表结构必须用 migration，不用手工 SQL 作为唯一交付。
- Raw SQL 必须参数绑定，不拼接外部输入。

## 推荐模块分区
按两个现有项目的事实目录，新项目优先保留这些可裁剪分区：

- `app/Kernel/**`：BaseController、BaseBusiness、BaseDao、公共 Traits、Utils、Interface、Constant。
- `app/Http/Controllers/**`：按入口分 `Management`、`Service`、`Server`、`Common`。
- `app/Modules/Basics/**`：基础 Model、Dao、Constant、Rule、Factory、Api。
- `app/Modules/Management/Business/**`：管理端业务。
- `app/Modules/Service/**`：服务端或内部服务能力。
- `app/Jobs/**`：队列任务。
- `app/Console/Commands/**`：Artisan 命令。
- `app/Libraries/**`：第三方系统 SDK/API 封装。
- `routes/management/proxy/**`、`routes/service/v1/**`、`routes/server/v1/**`：按入口拆路由。

## 环境与分支
基础环境：
- `local`：开发环境。
- `tests`：测试环境，对应 `beta`。
- `production`：生产环境，对应 `master`。

基础分支：
- `master`：生产。
- `beta`：测试。
- `dev/{yyyyMMdd}/{requirement}`：需求分支，全小写。
- `fix/{yyyyMMdd}/{requirement}`：修复分支，全小写。

禁止把 `beta` 合并回 `dev/*`、`fix/*` 或 `master`。

## 输出格式
响应用户时优先给：
1. 项目创建命令。
2. 仓库目录和服务代码目录。
3. 服务代码目录树。
4. 必须保留/可选裁剪的基础能力。
5. 后续 skills 使用顺序。
6. 需要用户补充的项目命名、域名、模块或部署信息。

## 完成检查
- 已明确 Laravel/Lumen 选择和原因。
- 已强调使用 Composer 创建，未建议复制旧项目。
- 已给出 `www/service.*.com` 代码目录。
- 已覆盖 Controller / Business / Dao / Model / Kernel / routes / config / database / docs。
- 已按需说明 Base、Provider、bootstrap、helpers、middleware 的初始化模板分档。
- 已说明环境、分支和后续 skill 流程。
