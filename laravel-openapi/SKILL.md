---
name: laravel-openapi
description: 当用户要求生成、更新或复用 Laravel 项目的 Apifox/OpenAPI 文档时使用本技能，尤其适用于 docs/apifox/*.openapi.json、stills.openapi.json、stills.openapi.template.json、RBAC 路由文档，以及基于 routes/management/proxy/**/*.php 生成可导入 Apifox 的 OpenAPI JSON。
---

# Laravel OpenAPI 文档规范

## 适用场景

用于为 Laravel/Lumen 项目生成或维护可导入 Apifox 的 OpenAPI JSON。当前重点覆盖管理端代理路由、RBAC 路由、`stills` 模式文档，以及后续新增接口的 OpenAPI 模板复用。

## 现有文档类型

- 当前项目可能没有 `docs/apifox`、`stills.openapi.json` 或 `stills.openapi.template.json`；生成前必须先在目标项目内实际查找，不要假设文件存在。
- 如存在 `docs/apifox/*.openapi.json`：优先复用项目内现有 OpenAPI JSON 和模板。
- 如不存在：根据目标项目真实路由、Controller、Business 校验新建 `docs/apifox/*.openapi.json`，或使用本 skill 内置模板作为起点。
- 本 skill 内置模板：`assets/stills.openapi.template.json`。

## 默认流程

1. 先定位目标 Laravel 项目根目录，当前重点候选为 `www/service.manage.wg.com`、`www/service.his.wg.com`；不要默认在 skill 目录里生成业务文档。
2. 优先读取真实路由文件（常见 `routes/management/proxy/**`）、Controller 方法、Request/Business 校验逻辑、Model/Dao 字段和已有 `docs/apifox/*.openapi.json`。
3. 如果是新增文档，先查项目内是否已有 `docs/apifox/stills.openapi.template.json`；项目内没有时再使用本 skill 的 `assets/stills.openapi.template.json`，并创建目标项目的 `docs/apifox`。
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
3. 导入本次生成或更新的 `docs/apifox/*.openapi.json`。
4. 如果项目已有 `crm-rbac-routes.openapi.json` 或 `stills.openapi.json`，按实际文件名导入，不要凭旧模板假设。

## 模板复用方式

1. 优先复制项目内已有 `docs/apifox/stills.openapi.template.json` 为新文件；项目内没有时从本 skill 的 `assets/stills.openapi.template.json` 复制。
2. 替换占位路径 `/management/proxy/your-prefix/your-path`。
3. 替换占位标签 `YourTag`。
4. 修改 `summary`、`parameters`、`requestBody`、`required` 和字段描述。
5. 保留 `components.securitySchemes.bearerAuth` 和 `components.schemas.ApiResponse`，除非项目已有更新的统一 schema。

## 注意事项

- 不要凭空编造接口字段；字段必须来自路由、Controller、Business 校验、DTO/FormRequest、Model 或用户明确说明。
- 项目内没有 `docs/apifox` 或 `stills.openapi.json` 时，不要声称已有；需要文档时再新建。
- 如果同一路由文件已有 OpenAPI 风格，优先保持一致。
- 只改本次任务相关的 OpenAPI 路径，不顺手重构无关文档。
- 生成后必须说明新增/修改了哪些接口、输出文件位置，以及 JSON 校验结果。
