---
name: laravel-mysql-ddl
description: 当用户要求在创建 Laravel migration 前，根据业务需求生成、设计、草拟、评审或确认 MySQL SQL/DDL 时使用本技能，尤其适用于“生成mysql语句”“先生成SQL”“建表语句”“改表语句”“字段设计”“索引设计”“表结构草案”，或需要在 laravel-migrations 前增加 SQL 确认步骤。
---

# 需求生成 MySQL DDL

## 定位
这个 skill 负责把业务需求整理成 MySQL DDL 草案，作为 `laravel-migrations` 的前置步骤。默认只输出 SQL 草案、设计说明、风险点和待确认项，不创建 migration 文件，不执行 SQL。

## 默认流程
1. 先判断变更类型：新建表、已有表加字段、修改字段、加索引、删字段、拆表、数据回填。
2. 从需求中提取表名、字段、类型、默认值、是否可空、注释、唯一性、索引、查询场景、数据量和兼容性要求。
3. 信息不足时先给“保守草案 + 待确认项”；涉及高风险操作时必须明确要求用户确认。
4. 按本仓库迁移规范生成 MySQL DDL；新表优先包含 `id`、业务编号、`add_time`、`last_update_time`、`deleted_at`、`created_at`、`updated_at`。
5. 输出 DDL 后停下，等待用户确认。
6. 用户确认 SQL 后，再建议使用 `laravel-migrations` 按该 SQL 生成 migration 文件。

## 重要边界
- 不直接执行 SQL。
- 不直接创建 migration 文件，除非用户明确要求切到 `laravel-migrations`。
- 不把需求里的业务描述直接变成大段宽表；先识别核心实体和关系。
- 不为没有查询场景的字段主动加索引。
- 删除字段、改字段类型、大表加索引、非空无默认值、唯一约束、数据回填都要标为高风险。

## DDL 规则
需要字段类型、索引和模板细则时读取 `references/mysql-ddl-guidelines.md`。

核心约定：
- 表名和字段名使用小写蛇形命名。
- 新表默认 `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci`。
- 新表主键默认 `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`。
- 业务编号字段紧跟主键：`{short_table}_no varchar(64) NOT NULL DEFAULT '' COMMENT 'xx编号'`，通常加唯一索引。
- 时间字段按现有项目习惯保留 `add_time int unsigned NOT NULL DEFAULT 0`、`last_update_time int unsigned NOT NULL DEFAULT 0`。
- 同时保留 Laravel 时间字段：`deleted_at timestamp NULL DEFAULT NULL`、`created_at timestamp NULL DEFAULT NULL`、`updated_at timestamp NULL DEFAULT NULL`。
- 字段必须写 `COMMENT`，索引名必须稳定清晰。

## 输出格式
每次输出按这个顺序：
1. 需求理解。
2. MySQL DDL 草案。
3. 字段说明。
4. 索引说明。
5. 风险与兼容性。
6. 待确认项。
7. 下一步：确认后使用 `$laravel-migrations` 根据 SQL 生成 migration。

## 需要先确认的情况
- 表名、模块名、业务编号命名无法推断。
- 字段类型会影响金额、精度、时间、枚举或状态流转。
- 是否唯一、是否允许为空、默认值、数据量级、是否大表不明确。
- 变更会影响已有线上数据或需要数据回填。
- 需求可能拆成多张表，但用户只要求“一张表”。

## 完成检查
- SQL 可直接表达结构意图，但明确未执行。
- 字段名、类型、默认值、注释齐全。
- 索引和唯一约束有查询或业务理由。
- 高风险点已标出。
- 已提醒用户确认 SQL 后再调用 `laravel-migrations`。
