# 统计接口参考模板

## 适用场景
- 新增管理端或服务端统计接口，例如总数、状态数量、金额汇总、去重人数等。
- 多个统计字段来自同一张主表时，优先使用 Dao 一条聚合 SQL 一次查出，不在 Business 中多次 `count()`。
- 查询条件仍走项目既有 `doQuery($params)` 与 Model scope，避免在 Controller 或 Business 拼查询。

## 分层约定
- Route：只声明入口、Controller 方法和权限点；不需要权限时使用 `WebRoute::AUTH_NEEDLESS`。
- Controller：只收参、调用 Business、`revert()` 返回。
- Business：负责入参校验、参数名映射、调用 Dao、返回结构组装。
- Dao：负责同表聚合统计查询，使用 `selectRaw(implode(',', $columns))`。
- Model：需要新增筛选条件时，只补确认过的 `scopeXxxQuery`。

## Route 模板
```php
// 获取公司统计数据
$router->get('statistics', [
    'as'   => WebRoute::AUTH_NEEDLESS,
    'uses' => 'DepartmentController@statistics',
]);
```

## Controller 模板
```php
/**
 * 获取公司统计数据
 * @return array|JsonResponse|null
 * @throws AppException
 * @throws ValidationException
 */
public function statistics()
{
    return $this->revert(
        $this->business->statistics($this->request->all())
    );
}
```

## Business 模板
```php
/**
 * 获取公司统计数据
 * @param array $params
 * @return array
 * @throws AppException
 * @throws ValidationException
 */
public function statistics(array $params): array
{
    $params = validator($params, [
        'department_no' => ['nullable', 'string', Rule::exists($this->coreDepartmentDao->getTableName(), 'department_no')],
    ], customAttributes: [
        'department_no' => '部门编号',
    ])->validate();

    $departmentNo = array_pull($params, 'department_no');

    if (filled($departmentNo))
    {
        $params['affiliation_department_no'] = $departmentNo;
    }

    $statistics = $this->employeeDao->statisticsStatusQuantity($params);

    return [
        'total_count'     => (int)$statistics->total_count,
        'enable_count'    => (int)$statistics->enable_count,
        'dimission_count' => (int)$statistics->dimission_count,
    ];
}
```

## Dao 模板
```php
/**
 * 统计员工状态数量
 * @param array $params
 * @return Employee|null
 */
public function statisticsStatusQuantity(array $params = []): ?Employee
{
    $enable    = AccountStatus::ENABLE;
    $dimission = AccountStatus::DIMISSION;

    $columns = [
        'count(1) as total_count',
        "count(IF(employee_status='{$enable}', id, NULL)) as enable_count",
        "count(IF(employee_status='{$dimission}', id, NULL)) as dimission_count",
    ];

    return $this->doQuery($params)
        ->selectRaw(implode(',', $columns))
        ->first();
}
```

## Model Scope 模板
```php
/**
 * 部门编号
 * @param Builder $builder
 * @param $value
 * @return Builder
 */
public function scopeAffiliationDepartmentNoQuery(Builder $builder, $value): Builder
{
    return $builder->where('affiliation_department_no', $value);
}
```

## 写法要点
- 同一张表的多个统计值放到 Dao，用一条 SQL 查出。
- 统计条件中的常量先赋值成本地变量，再写入 `$columns`，保持和项目统计方法风格一致。
- `selectRaw` 中只拼接后端常量、枚举、固定字段名；外部输入必须通过 `doQuery($params)`、scope 或参数绑定处理。
- Business 中接口入参与表字段不一致时做映射，例如 `department_no` 映射为 `affiliation_department_no`。
- 返回给前端的字段在 Business 统一转为 `int`，避免数据库聚合结果以字符串返回。
- 新增筛选字段前先确认业务需要，再补对应 Model scope。

## 检查清单
- Route 权限点已确认；免权限接口使用 `WebRoute::AUTH_NEEDLESS`。
- Controller 没有查询逻辑。
- Business 只做校验、映射、组装。
- Dao 使用一条聚合查询完成同表统计。
- 外部查询参数没有拼进 Raw SQL。
- 已运行 `php -l` 检查修改文件。
