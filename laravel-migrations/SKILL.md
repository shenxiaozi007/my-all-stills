---
name: laravel-migrations
description: This skill should be used when the user asks to "新增 migration", "改表结构", "加字段/索引", "写回滚", "执行指定 migration", or needs migration design and execution guidance for this repository.
---

# 本仓库 Migration 开发规范

## 适用范围
- migration 目录先按当前仓库实际存在服务目录探测，常见候选：
  - `www/service.core.ys.com/database/migrations/**`
  - `www/service.manage.wg.com/database/migrations/**`
- 本仓库存在按专题分子目录的写法（如 `database/migrations/20241226_patient/**`），新增时可按现有专题组织。

## 默认流程
1. 先确认服务目录、已有 migration 专题目录、是否已有同表/同字段 migration。
2. 新增 migration 时，优先在 Laravel 服务目录执行 `php artisan make:migration ...` 生成文件，再修改生成文件。
3. 按当前表结构设计 `up()` / `down()`，字段、默认值、注释、索引名与需求保持一致。
4. 默认不执行 `migrate` / `rollback`；完成后给用户指定 `--path` 命令。
5. 完成后至少执行 `php -l` 检查生成的 migration 文件。

## 需要先确认的情况
- 目标服务目录或专题目录无法从现有结构判断。
- 变更涉及删除字段、改字段类型、大表加索引、数据回填或发布先后顺序。
- DDL 与本仓库新表模板冲突，且无法判断应按历史表还是新规范落地。

## 注意事项 / 禁止项
- 不主动执行 `php artisan migrate`、`migrate:rollback`、`migrate --pretend`。
- 不手写 `created_at` / `updated_at` / `deleted_at`；新表优先用 `$table->softDeletes();` 和 `$table->timestamps();`。
- 单字段索引优先链式写在字段定义上；联合索引才后置声明。
- 不修改用户未要求的历史 migration。

## 完成检查
- migration 文件由 `make:migration` 生成或已说明无法生成的原因。
- `up()` / `down()` 成对完整，索引名、表名、字段名一致。
- 已通过 `php -l`。
- 已给出用户手动执行的 `php artisan migrate --path=/database/migrations/...php` 命令。

## 核心规则（Must）
1. 表结构变更必须通过 migration，禁止线上手工改表。
2. `up()` 与 `down()` 必须成对设计，可回滚。
3. 配置和环境判断走 `config()`，不要在业务代码直接 `env()`。
4. 涉及 SQL 语句时禁止拼接参数。

## 命名与创建建议
- 新建表：`create_xxx_table`
- 改表字段/索引：`alter_xxx_add_xxx` / `alter_xxx_drop_xxx`
- 新增 migration 时优先先在 Laravel 服务目录执行 `php artisan make:migration ...` 生成文件，再修改生成出来的 migration 文件。
- 命令示例：
  - `php artisan make:migration create_xxx_table --create=xxx`
  - `php artisan make:migration alter_xxx_add_yyy --table=xxx`

## 执行建议
- 默认不要替用户执行 `php artisan migrate` / `migrate:rollback` / `migrate --pretend` 等迁移相关命令。
- 完成 migration 文件修改后，把建议执行的指定文件命令提供给用户，并提醒由用户确认环境后手动执行。
- 优先给出指定文件执行命令，避免误跑全部：
  - `php artisan migrate --path=/database/migrations/完整文件名.php`
- 执行前确认环境与分支对应：
  - `local` 开发环境
  - `tests` 对应 `beta`
  - `production` 对应 `master`

## 设计要点
1. 先评估变更类型：新表、加字段、改类型、加索引、删字段。
2. 先考虑兼容与回滚：
   - 新字段优先可空或给默认值
   - 高风险删除操作优先分阶段（先停写/迁移数据，再删除）
3. 索引命名清晰且与查询场景匹配。
4. 新建表时，单字段索引优先写在字段定义链路上，不要集中放到表定义末尾：
   - 唯一索引：`$table->string('operation_log_no', 64)->default('')->comment('日志编号')->unique('operation_log_no');`
   - 普通索引：`$table->string('ip', 128)->default('')->comment('登录ip')->index('ip');`
   - 多字段联合索引或必须后置声明的索引，才使用 `$table->index([...], 'index_name')` / `$table->unique([...], 'index_name')`。
5. 新建表优先使用固定基础模板：
   - 表注释写在 `Schema::create()` 内第一行：`$table->comment('模块 - 表含义');`
   - 主键默认使用 `$table->bigIncrements('id');`
   - 业务编号字段紧跟主键：`$table->string('简化的表名_no', 64)->default('')->comment('xx编号')->unique('索引名');`
   - `add_time` / `last_update_time` 放在业务字段后：`$table->unsignedInteger('add_time')->default(0)->comment('添加时间');`、`$table->unsignedInteger('last_update_time')->default(0)->comment('最后更新时间');`
   - 不要手写 `created_at` / `updated_at` / `deleted_at` 字段；统一使用 `$table->softDeletes();` 和 `$table->timestamps();`
   - 需要完整模板时读取 `references/migration-skeleton.md`。
6. 大表变更优先拆步，减少锁表与长事务风险。

## 回滚要点
- `down()` 只回滚当前 migration 对应变更，避免误伤其他结构。
- 删除索引、字段、表时名称要和 `up()` 严格对应。
- 回滚前确认是否已有依赖新字段/新表的代码发布。

## 提交前检查
- [ ] `up()` / `down()` 对称完整
- [ ] 索引/字段命名清晰
- [ ] 指定执行路径可单独运行
- [ ] 变更对线上数据兼容性已评估
- [ ] 与当前分支发布路径一致（beta/master）

## 参考模板
- `references/migration-skeleton.md`
- `references/migration-checklist.md`
