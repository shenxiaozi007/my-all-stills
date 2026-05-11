# 新项目初始化模板清单

本清单来自 `wg-manage-service/www/service.manage.wg.com` 与 `wg-his-service/www/service.his.wg.com` 的共同结构。创建新项目时只抽通用能力，不复制旧项目整包。

已提取的独立模板文件放在：

```text
assets/lumen-init-template/
```

新项目初始化时优先复制这个目录里的文件，再按项目业务裁剪。不要让新项目依赖 `wg-manage-service` 或 `wg-his-service` 的源路径。

## 分档原则

- 必须模板：新后端服务一开始就需要，且与具体业务低耦合。
- 按需模板：常用但依赖队列、Excel、第三方 SDK、RBAC、事件或导入导出场景。
- 不进入模板：绑定 HIS/Manage 具体业务、具体第三方平台、具体表结构或具体枚举的数据。

## 必须模板

### Kernel/Base

优先整理这些基类，作为后续所有业务 skill 的共同地基：

```text
app/Kernel/Base/
├── BaseController.php
├── BaseBusiness.php
├── BaseDao.php
├── BaseModel.php
├── BaseCommand.php
├── BaseConstant.php
├── BaseRule.php
├── BaseValidator.php
├── BaseApi.php
└── BaseInvoke.php
```

职责约束：

- `BaseController`：统一 `revert()` 响应结构，使用 `ApiResponseTrait`，Controller 只收参和返回。
- `BaseBusiness`：放业务层通用数据映射、流程工具，不放具体业务规则。
- `BaseDao`：提供 `getModel()`、`newBuilder()`、`find/findOrFail`、分页、条件构造、保存/更新等通用能力；去掉旧项目里强依赖导出、具体 Factory、具体枚举的 trait 或 import。
- `BaseModel`：统一连接、软删除、时间字段、隐藏字段、scope 辅助；避免直接依赖具体业务 `SortByTrait`，除非新项目也引入对应基础 Factory。
- `BaseCommand`：命令基类，沉淀日志、进度、异常包装、批处理通用方法。
- `BaseConstant` / `BaseRule` / `BaseValidator`：枚举、规则、验证器基类。

### Kernel/Traits

优先保留低耦合 trait：

```text
app/Kernel/Traits/
├── ApiResponseTrait.php
├── CacheKeyTraits.php
├── ModelTimeTraits.php
├── PasswordTrait.php
└── SecretDataTrait.php
```

按需引入：

```text
ExportExcelTrait.php
FixDataWithProcessTrait.php
ModelEventAfterCommitTrait.php
ModelMainNoTrait.php
OldIdTrait.php
SyncLockTraits.php
StrictSyncLockTraits.php
```

### Kernel helpers

建议拆成三个文件并在 Composer autoload 或 bootstrap 中加载：

```text
app/Kernel/common.php
app/Kernel/helpers.php
app/Kernel/guard.php
```

保留方向：

- `common.php`：`trim_any`、`json_strict_decode`、`get_now`、`get_format_now`、`get_http_host`、路径和文件扫描基础函数。
- `helpers.php`：分页大小、IP、UA、终端、时间、文件扩展、脱敏等通用 helper。
- `guard.php`：只放认证上下文 helper，如 `management_auth()`、`management_auth_info()`；如果新项目无管理端登录，先不引入。

注意：

- helper 里禁止保留具体业务 Dao、具体业务枚举、具体三方 Job 的 import。
- 旧项目 `generate_mq_connection()` 仍使用 `env()`，新模板若保留必须改为读取 `config('queue.connections...')` 或只放到 `config/queue.php` 内。

## Provider 初始化模板

### 必须 Provider

```text
app/Providers/
├── AppServiceProvider.php
├── ConfigServiceProvider.php
├── EventServiceProvider.php
└── RouteServiceProvider.php
```

建议职责：

- `AppServiceProvider`
  - 注册通用 Validator 行为。
  - 注册自定义验证规则。
  - 注册自定义分页器。
  - 注册 Request macro，如 `getOrFail()`、`getFileOrFail()`、`headerOrFail()`。
- `ConfigServiceProvider`
  - 自动加载 `config/**/*.php`。
  - 可排除 `config/rbac` 等非常规配置目录。
  - 将子目录配置映射为点号 key，如 `config/api/wg_common.php` -> `api.wg_common`。
- `EventServiceProvider`
  - 作为事件监听注册入口。
  - 默认 `shouldDiscoverEvents()` 返回 false，避免隐式扫描带来不确定性。
- `RouteServiceProvider`
  - 集中定义 Management / Service / Server / Common / Notice 路由分组。
  - 根据 domain、prefix、namespace、middleware、files 加载路由文件。
  - 路由文件扫描可以缓存，但新项目初期应允许关闭缓存。

### 按需 Provider

```text
app/Providers/
├── AuthServiceProvider.php
├── LibrariesProvider.php
└── Common/
    └── ToolProvider.php
```

建议职责：

- `AuthServiceProvider`：只有需要自定义 guard，如管理端 JWT guard 时引入。
- `LibrariesProvider`：第三方系统 SDK facade/singleton 入口；不要把旧项目具体平台默认带进新项目。
- `Common/ToolProvider`：通用工具 singleton，例如雪花 ID。若引入雪花 ID，优先用 Redis/cache sequence resolver，避免高并发文件锁。

## bootstrap/app.php 初始化流程

Lumen 项目建议按这个顺序组织：

1. 加载 `vendor/autoload.php`。
2. 加载 `.env`。
3. 设置时区。
4. 创建 `Laravel\Lumen\Application`。
5. 注册 ExceptionHandler 和 Console Kernel。
6. `$app->configure('app')`，再加载 `schedule` 等基础配置。
7. 注册全局 middleware，如 CORS。
8. 注册 route middleware。
9. 注册项目 Provider。
10. 注册框架和第三方 Provider，如 Redis、RabbitMQ、MongoDB、Excel、Pinyin，按项目需要裁剪。
11. 本地环境再注册迁移生成等开发工具。
12. 开启 `$app->withFacades()` 和 `$app->withEloquent()`。
13. `return $app`。

推荐 route middleware key：

```text
auth
cors
signed
api_signed
request.expired
management.rbac
throttle
trim
rid
record.log
request.log
api_mutex
api.cache
request.operator_info
req_params_inject
```

只注册已经实现的 middleware；新项目初期可以先保留 `cors`、`trim`、`rid`、`request.log`，鉴权和 RBAC 在管理端登录方案确定后再补。

## 配置模板

必须基础配置：

```text
config/
├── app.php
├── auth.php
├── cache.php
├── cors.php
├── database.php
├── domain.php
├── logging.php
├── queue.php
├── schedule.php
├── secret.php
├── secure.php
├── service.php
└── site.php
```

按需配置：

```text
config/
├── api/
├── libraries/
├── rbac/
├── filesystems.php
├── snappy.php
└── version.php
```

规则：

- `.env` 只在 config 文件里读取。
- 业务代码统一 `config()`。
- 三方 API、域名、队列、密钥都必须有 config 映射。
- 示例配置不能带真实密钥。

## 推荐抽取来源

以 `wg-manage-service` 为主参考，`wg-his-service` 做交叉校验：

```text
wg-manage-service/www/service.manage.wg.com/app/Kernel/Base/*
wg-manage-service/www/service.manage.wg.com/app/Kernel/Traits/*
wg-manage-service/www/service.manage.wg.com/app/Kernel/common.php
wg-manage-service/www/service.manage.wg.com/app/Kernel/helpers.php
wg-manage-service/www/service.manage.wg.com/app/Kernel/guard.php
wg-manage-service/www/service.manage.wg.com/app/Providers/*
wg-manage-service/www/service.manage.wg.com/app/Providers/Common/ToolProvider.php
wg-manage-service/www/service.manage.wg.com/bootstrap/app.php
wg-manage-service/www/service.manage.wg.com/config/*.php
```

交叉校验：

```text
wg-his-service/www/service.his.wg.com/app/Kernel/Base/*
wg-his-service/www/service.his.wg.com/app/Kernel/Traits/*
wg-his-service/www/service.his.wg.com/app/Providers/*
wg-his-service/www/service.his.wg.com/bootstrap/app.php
wg-his-service/www/service.his.wg.com/config/*.php
```

## 不进入初始化模板

- 具体业务模块：患者、仓库、营销、技加工、医保、报表中心等业务类。
- 具体第三方平台：Pm、Lab、Mobile360、OceanEngine、TencentAdvert、DzCall、Yb、Zw 等具体 singleton。
- 具体业务枚举和异常码，除非新项目也复用完整业务域。
- 具体表 Model、Dao、Factory、Rule。
- 旧项目的历史迁移、数据修复命令、同步任务、临时脚本。

## 初始化交付清单

创建新项目时，输出或实现时按这张清单验收：

- Composer 创建命令已给出。
- `www/service.*.com` 代码目录已确定。
- 已从 `assets/lumen-init-template/` 复制独立初始化模板。
- `app/Kernel/Base` 必须基类已整理。
- `app/Kernel/Traits` 低耦合 trait 已整理。
- `common.php`、`helpers.php`、`guard.php` 已按业务需要裁剪。
- Provider 注册顺序已明确。
- `bootstrap/app.php` 中 middleware 和 provider 只注册已存在类。
- config 文件已覆盖 app/auth/cache/cors/database/domain/logging/queue/schedule/secret/secure/service/site。
- 所有业务读取配置使用 `config()`。
- 后续功能开发再切到对应 Laravel skill。
