# 新 Laravel/Lumen 后端项目目录骨架

## 仓库层

```text
{project-repo}/
├── www/
│   └── service.{name}.{company}.com/
├── docs/
│   ├── api/
│   ├── deployment/
│   └── database/
└── database/
    ├── schema/
    └── seed-data/
```

## 服务代码层

```text
service.{name}.{company}.com/
├── app/
│   ├── Console/
│   │   ├── Commands/
│   │   └── Kernel.php
│   ├── Exceptions/
│   ├── Exports/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Common/
│   │   │   ├── Management/
│   │   │   │   ├── App/
│   │   │   │   ├── Common/
│   │   │   │   └── Proxy/
│   │   │   ├── Server/
│   │   │   │   └── V1/
│   │   │   └── Service/
│   │   │       └── V1/
│   │   └── Middleware/
│   │       ├── Common/
│   │       ├── Permission/
│   │       ├── Trim/
│   │       └── Validate/
│   ├── Jobs/
│   ├── Kernel/
│   │   ├── Base/
│   │   ├── Constant/
│   │   ├── Interface/
│   │   ├── Tools/
│   │   ├── Traits/
│   │   └── Utils/
│   ├── Libraries/
│   │   ├── Common/
│   │   └── {ThirdParty}/
│   │       ├── Api/
│   │       ├── Business/
│   │       ├── Constant/
│   │       ├── Exceptions/
│   │       └── Transform/
│   ├── Listeners/
│   ├── Modules/
│   │   ├── Basics/
│   │   │   ├── Api/
│   │   │   ├── Business/
│   │   │   ├── Constant/
│   │   │   ├── Dao/
│   │   │   ├── Factory/
│   │   │   ├── Model/
│   │   │   └── Rule/
│   │   ├── Management/
│   │   │   ├── Business/
│   │   │   ├── Constant/
│   │   │   └── Factory/
│   │   ├── Server/
│   │   │   └── Business/
│   │   ├── Service/
│   │   │   └── Api/
│   │   └── Sync/
│   │       ├── Business/
│   │       ├── Constant/
│   │       └── Dao/
│   ├── Observer/
│   └── Providers/
│       └── Common/
├── bootstrap/
├── config/
│   ├── api/
│   ├── libraries/
│   └── rbac/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── api_mock/
│   ├── import/
│   ├── lang/
│   └── views/
├── routes/
│   ├── common/
│   ├── management/
│   │   ├── app/
│   │   │   └── api/
│   │   ├── common/
│   │   └── proxy/
│   │       └── {module}/
│   ├── server/
│   │   └── v1/
│   └── service/
│       └── v1/
├── storage/
│   ├── app/
│   ├── framework/
│   ├── logs/
│   ├── schedule/
│   └── tmp_files/
└── tests/
```

## 必须优先落地的基础能力

- `app/Kernel/Base/BaseController.php`：统一响应入口，后续 Controller 使用 `revert()`。
- `app/Kernel/Base/BaseBusiness.php`：业务层基类。
- `app/Kernel/Base/BaseDao.php`：Dao 通用查询和持久化能力。
- `app/Kernel/Base/BaseModel.php`：Model 基类、软删除/时间字段约定。
- `app/Exceptions/**`：统一业务异常和异常渲染。
- `app/Http/Middleware/**`：鉴权、权限、防重复提交、参数清洗。
- `config/*.php`：所有 `.env` 值先映射到 config。
- `routes/**`：按 Management / Service / Server 分入口。

## 可按项目裁剪的目录

- 无第三方系统接入时，可以暂不创建具体 `app/Libraries/{ThirdParty}` 子目录。
- 无异步任务时，可以只保留 `app/Jobs` 和 `app/Console/Commands` 空目录或 README 占位。
- 无导入导出时，可以暂不创建 `app/Exports` 和 `resources/import`。
- 无事件订阅时，可以暂不创建 `Listeners`、`Observer`、`EventGroup`。
- 单入口项目可以只保留对应 routes 和 Controller 入口，但目录命名仍按上述风格扩展。

## 后续 skill 串联

- 项目骨架完成后，用 `laravel-standards` 审一遍分层和边界。
- 新接口用 `laravel-crud`。
- 新表或字段用 `laravel-migrations`。
- 命令、定时任务、批处理用 `laravel-console`。
- 队列和消费者用 `laravel-queue-job`。
- 导入导出和报表用 `laravel-import-export`。
- OpenAPI/Apifox 文档用 `laravel-openapi`。
- 起分支和发布流用 `laravel-git-flow`。
