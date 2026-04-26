# Migration Skeleton（本仓库）

## 新增表模板
```php
public function up()
{
    Schema::create('xxx_table', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('main_no', 64)->comment('主编号');
        $table->timestamps();

        $table->index('main_no', 'idx_main_no');
    });
}

public function down()
{
    Schema::dropIfExists('xxx_table');
}
```

## 改表模板
```php
public function up()
{
    Schema::table('xxx_table', function (Blueprint $table) {
        $table->string('new_col', 64)->default('')->comment('新字段');
        $table->index('new_col', 'idx_new_col');
    });
}

public function down()
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
