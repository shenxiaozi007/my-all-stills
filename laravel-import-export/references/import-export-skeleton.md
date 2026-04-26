# Import/Export Skeleton（本仓库）

## 导入命令（示意）
```php
public function handle(): void
{
    $clinicAlias = $this->option('clinic_alias');
    $path        = $this->option('path');

    if (blank($clinicAlias) || blank($path) || !file_exists($path)) {
        $this->error('参数或文件不合法');
        return;
    }

    app(XxxImportBusiness::class)->import($clinicAlias, $path);
}
```

## 导入 Business（示意）
```php
public function import(string $clinicAlias, string $path): void
{
    $options = [
        'title_maps' => [
            '姓名' => 'name',
            '电话' => 'mobile',
        ],
    ];

    // 约定：Resource 基类会优先按 resource_path 解析；如传绝对路径请确认调用方支持。
    $rows = $this->getResourceData($path, $options);

    $total = count($rows);
    $current = 0;

    foreach ($rows as $row) {
        cmd_progress_bar($total, ++$current);

        try {
            app('db')->transaction(function () use ($row) {
                // store / sync
            });
        } catch (\Throwable $e) {
            // 记录行号与错误，继续处理下一行
            dump('导入失败：' . $e->getMessage());
        }
    }
}
```

## 大文件导入（示意）
```php
$chunkParser = $this->chunkParseExcel($path, [
    'title_maps' => [/* ... */],
]);

$chunkParser->chunkRead(function (array $rows) {
    foreach ($rows as $row) {
        // 分块处理
    }
});
```

## 导出（示意）
- 固化筛选条件
- 走异步报表任务（Job）
- 更新导出状态并保留失败上下文
- 在 Job 的 `failed()` 中回写失败原因
