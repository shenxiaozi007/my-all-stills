# MySQL DDL 生成规则

## 新建表模板

```sql
CREATE TABLE `{table_name}` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `{short_table}_no` varchar(64) NOT NULL DEFAULT '' COMMENT '{业务}编号',

  -- 业务字段

  `add_time` int unsigned NOT NULL DEFAULT 0 COMMENT '添加时间',
  `last_update_time` int unsigned NOT NULL DEFAULT 0 COMMENT '最后更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `{short_table}_no` (`{short_table}_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='{模块} - {表含义}';
```

## 改表模板

新增字段：

```sql
ALTER TABLE `{table_name}`
  ADD COLUMN `{column_name}` varchar(64) NOT NULL DEFAULT '' COMMENT '{字段说明}' AFTER `{after_column}`;
```

新增索引：

```sql
ALTER TABLE `{table_name}`
  ADD INDEX `idx_{column_name}` (`{column_name}`);
```

新增唯一索引：

```sql
ALTER TABLE `{table_name}`
  ADD UNIQUE KEY `uk_{column_name}` (`{column_name}`);
```

删除字段高风险，默认只出分阶段建议：

```sql
-- 第一步：确认代码不再读写该字段，并完成数据备份。
-- 第二步：发布后观察，再执行删除字段。
ALTER TABLE `{table_name}`
  DROP COLUMN `{column_name}`;
```

## 字段类型建议

- 业务编号、单号、编码：`varchar(64) NOT NULL DEFAULT ''`
- 名称、标题：`varchar(128)` 或 `varchar(255)`，根据展示长度决定。
- 描述、备注：`varchar(512)`；长文本才用 `text`。
- 状态、类型、开关：`tinyint unsigned NOT NULL DEFAULT 0`
- 数量、次数：`int unsigned NOT NULL DEFAULT 0`
- 金额：优先 `decimal(10,2) NOT NULL DEFAULT 0.00`；严肃财务场景确认是否用分单位 `int`。
- 比率：`decimal(8,4) NOT NULL DEFAULT 0.0000`
- 时间戳：项目业务字段优先 `int unsigned NOT NULL DEFAULT 0`，Laravel 时间字段使用 `timestamp NULL DEFAULT NULL`。
- JSON 配置：MySQL 版本明确支持时用 `json NULL COMMENT 'xx配置'`；否则用 `text`。

## 索引规则

- 只有明确查询、排序、唯一性或关联场景时加索引。
- 单字段普通索引命名：`idx_{column}`。
- 联合普通索引命名：`idx_{col1}_{col2}`，顺序按查询等值条件、范围条件、排序条件排列。
- 唯一索引命名：单字段可用业务字段名或 `uk_{column}`；联合唯一用 `uk_{col1}_{col2}`。
- 低区分度字段如状态、开关，不单独加索引；可放入联合索引前缀或后缀，视查询场景确认。
- 大表加索引必须标风险，提醒考虑低峰执行、在线 DDL 或分阶段方案。

## 兼容性规则

- 新增字段优先 `NOT NULL DEFAULT` 或允许 `NULL`，避免历史数据不兼容。
- 修改字段类型前确认现有数据能否无损转换。
- 加唯一索引前确认历史数据无重复。
- 删除字段前确认代码发布顺序和备份。
- 结构变更和数据回填尽量拆开。

## 输出示例

```sql
CREATE TABLE `market_activity` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `activity_no` varchar(64) NOT NULL DEFAULT '' COMMENT '活动编号',
  `activity_name` varchar(128) NOT NULL DEFAULT '' COMMENT '活动名称',
  `status` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '状态：0未启用 1启用',
  `start_time` int unsigned NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int unsigned NOT NULL DEFAULT 0 COMMENT '结束时间',
  `remark` varchar(512) NOT NULL DEFAULT '' COMMENT '备注',
  `add_time` int unsigned NOT NULL DEFAULT 0 COMMENT '添加时间',
  `last_update_time` int unsigned NOT NULL DEFAULT 0 COMMENT '最后更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_no` (`activity_no`),
  KEY `idx_status_start_time` (`status`, `start_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='营销 - 活动';
```
