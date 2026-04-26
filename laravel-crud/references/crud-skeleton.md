# CRUD Skeleton（本仓库）

## Route（示意）
```php
$router->group([
    'prefix'     => 'xxx',
    'namespace'  => 'Xxx',
    'middleware' => ['auth:jwt-management'],
], function ($router) {
    $router->post('store', [
        'as'         => WebRoute::MANAGEMENT_XXX_STORE,
        'uses'       => 'XxxController@store',
        'middleware' => 'api_mutex',
    ]);
});
```

## Controller（示意）
```php
public function __construct(protected Request $request, protected XxxBusiness $business)
{
}

public function store(): JsonResponse
{
    $this->business->store($this->request->all(), management_auth_info());

    return $this->revert(null);
}
```

## Business（示意）
```php
public function store(array $payload, array $adminInfo = []): void
{
    $params = validator($payload, [
        'name'   => ['required', 'string'],
        'status' => ['required', Rule::in(Status::all())],
    ], customAttributes: [
        'name'   => '名称',
        'status' => '状态',
    ])->validate();

    app('db')->transaction(function () use ($params, $adminInfo) {
        $this->xxxDao->store([
            // ...
        ]);
    });
}
```

## Dao（示意）
```php
public function getModel(): XxxModel
{
    return app(XxxModel::class);
}

public function findByMainNo(string $mainNo, array $columns = [])
{
    return $this->newBuilder()
        ->select($this->getSelectColumns($columns))
        ->MainNoQuery($mainNo)
        ->first();
}
```
