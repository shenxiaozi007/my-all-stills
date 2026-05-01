# Migration Skeleton（本仓库）

## 新增表模板
```php
public function up(): void
{
    Schema::create('xxx_table', function (Blueprint $table) {
        $table->comment('模块 - 表含义');
        $table->bigIncrements('id');
        $table->string('简化的表名_no', 64)->default('')->comment('xx编号')->unique('索引名');

        // 业务字段写在这里；单字段索引优先链式写在字段定义上。
        $table->string('ip', 128)->default('')->comment('登录ip')->index('ip');

        $table->unsignedInteger('add_time')->default(0)->comment('添加时间');
        $table->unsignedInteger('last_update_time')->default(0)->comment('最后更新时间');
        $table->softDeletes();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('xxx_table');
}
```

## 新增表示例
```php
public function up(): void
{
    Schema::create('ord_restrict', function (Blueprint $table) {
        $table->comment('技加工 - 项目开单限制');
        $table->bigIncrements('id');
        $table->string('ord_restrict_no', 64)->default('')->comment('xx编号')->unique('ord_restrict_no');

        $table->unsignedInteger('add_time')->default(0)->comment('添加时间');
        $table->unsignedInteger('last_update_time')->default(0)->comment('最后更新时间');
        $table->softDeletes();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('ord_restrict');
}
```

## 改表模板
```php
public function up(): void
{
    Schema::table('xxx_table', function (Blueprint $table) {
        $table->string('new_col', 64)->default('')->comment('新字段')->index('idx_new_col');
    });
}

public function down(): void
{
    Schema::table('xxx_table', function (Blueprint $table) {
        $table->dropIndex('idx_new_col');
        $table->dropColumn('new_col');
    });
}
```

## 指定执行
```bash
php artisan migrate --path=/database/migrations/完整文件名.php
```
