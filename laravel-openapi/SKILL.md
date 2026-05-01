---
name: laravel-openapi
description: 当用户要求生成、更新或复用 Laravel 项目的 Apifox/OpenAPI 文档时使用本 skill，尤其适用于 docs/apifox/*.openapi.json、stills.openapi.json、stills.openapi.template.json、RBAC 路由文档，以及基于 routes/management/proxy/**/*.php 生成可导入 Apifox 的 OpenAPI JSON。
---

# Laravel OpenAPI 文档规范

## 适用场景

用于为 Laravel/Lumen 项目生成或维护可导入 Apifox 的 OpenAPI JSON。当前重点覆盖管理端代理路由、RBAC 路由、`stills` 模式文档，以及后续新增接口的 OpenAPI 模板复用。

## 现有文档类型

- `docs/apifox/crm-rbac-routes.openapi.json`：基于 `adm_permission.php` 和 `adm_role.php` 生成的可导入 OpenAPI JSON。
- `docs/apifox/stills.openapi.json`：基于 `routes/management/proxy/rbac/*.php` 生成的可导入 OpenAPI JSON。
- `docs/apifox/stills.openapi.template.json`：通用可复用模板，后续新增接口可直接复制扩展。
- 本 skill 内置模板：`assets/stills.openapi.template.json`。

## 默认流程

1. 先定位目标 Laravel 项目根目录，不要默认在 skill 目录里生成业务文档。
2. 优先读取现有路由文件、Controller 方法、Request/Business 校验逻辑和已有 `docs/apifox/*.openapi.json`。
3. 如果是新增文档，优先复用项目里的 `docs/apifox/stills.openapi.template.json`；项目内没有时使用本 skill 的 `assets/stills.openapi.template.json`。
4. 根据真实路由和业务方法更新 `paths`、HTTP method、`tags`、`summary`、`parameters`、`requestBody`。
5. 管理端接口默认保留 `security: [{"bearerAuth": []}]`，除非路由明确是公开接口。
6. 响应结构默认复用 `#/components/schemas/ApiResponse`，除非项目已有更具体的响应 schema。
7. 编辑完成后必须校验 JSON，例如执行 `jq empty docs/apifox/<file>.openapi.json` 或 `python3 -m json.tool docs/apifox/<file>.openapi.json`。

## OpenAPI 约定

- 使用 OpenAPI `3.0.3`。
- `servers` 使用 `[{ "url": "/" }]`，方便 Apifox 导入。
- `summary` 尽量使用简短中文，准确描述业务动作。
- `tags` 按模块稳定命名，例如 `RBAC`、`Role`、`Permission`，或使用当前 Controller/业务模块名。
- GET 查询参数写入 `parameters`，并设置 `in: "query"`。
- POST/PUT/PATCH 的 JSON 入参写入 `requestBody.content.application/json.schema`。
- 必填 body 字段同步写入 schema 的 `required` 数组。
- 不确定字段类型时，先从 Controller 校验规则、Business validator、Model 字段和已有接口文档推断。

## Apifox 导入方式

1. 打开 Apifox。
2. 选择 `导入` -> `OpenAPI`。
3. CRM RBAC 路由导入 `docs/apifox/crm-rbac-routes.openapi.json`。
4. RBAC 系统路由导入 `docs/apifox/stills.openapi.json`。
5. 新增接口文档生成后，再通过 OpenAPI 导入对应 JSON 文件。

## 模板复用方式

1. 复制 `docs/apifox/stills.openapi.template.json` 为新文件，或从本 skill 的 `assets/stills.openapi.template.json` 复制。
2. 替换占位路径 `/management/proxy/your-prefix/your-path`。
3. 替换占位标签 `YourTag`。
4. 修改 `summary`、`parameters`、`requestBody`、`required` 和字段描述。
5. 保留 `components.securitySchemes.bearerAuth` 和 `components.schemas.ApiResponse`，除非项目已有更新的统一 schema。

## 注意事项

- 不要凭空编造接口字段；字段必须来自路由、Controller、Business 校验、DTO/FormRequest、Model 或用户明确说明。
- 如果同一路由文件已有 OpenAPI 风格，优先保持一致。
- 只改本次任务相关的 OpenAPI 路径，不顺手重构无关文档。
- 生成后必须说明新增/修改了哪些接口、输出文件位置，以及 JSON 校验结果。
