# Design First без Java: TypeSpec → OpenAPI → PHP/Laravel

Дата: 2026-09-07. Проверено на этом репозитории: PHP 8.4.13, `laravel/framework ^13.17`, Pest 5.

## 1. jane-php/open-api-3 (v7.14.0)

Первоисточник: https://github.com/janephp/janephp/blob/v7.14.0/docs/openapi/getting_started.md,
`docs/openapi/component.md`, `docs/choose.md`, `docs/guides/validation.md`.

Установка (дословно из docs):

```bash
composer require --dev jane-php/open-api-3
composer require jane-php/open-api-runtime
```

Генерация:

```bash
php vendor/bin/jane-openapi generate
php vendor/bin/jane-openapi generate --config-file=jane-openapi-configuration.php
```

Конфиг — PHP-файл `.jane-openapi` в cwd, возвращающий массив:

```php
return [
  'openapi-file' => __DIR__ . '/open-api.json',
  'namespace' => 'Vendor\Library\Generated',
  'directory' => __DIR__ . '/generated',
];
```

Плюс в composer.json нужен psr-4 маппинг `"Vendor\\Library\\Generated\\": "generated/"`.

Что кладёт (docs, раздел "Using"): `Model\`, `Normalizer\` (+ `JaneObjectNormalizer`),
`Endpoint\` (по классу на операцию), `Client` в корневом неймспейсе, Runtime и exception-классы.

Важные опции: `validation => true` (генерирует валидаторы по JSON Schema 2020-12, вызываются
внутри нормалайзеров), `enums-as-objects`, `whitelisted-paths`, `strict`, `date-format`,
`generate-error-exceptions`, `throw-unexpected-status-code`.

Серверную часть НЕ генерирует. `docs/choose.md`: «Jane will also generate a Client, endpoints and
needed exceptions (for HTTP error responses). This is used with any API Client». Endpoint здесь —
это клиентский объект «запрос → объект», не серверный контроллер. Модели можно переиспользовать
как серверные DTO (гайд `guides/apip_dto.md` про API Platform).

Совместимость: `require: php ^8.1`. `composer require --dry-run` в этом репо ставит
`jane-php/open-api-3 v7.14.0` + `open-api-runtime v7.14.0` без конфликтов (тянет
`symfony/serializer 8.1.6`, `symfony/validator 8.1.6`, `php-http/*`, `nikic/php-parser`).

Осторожно: docs на ветке `next` описывают Jane 8 (переход на Symfony HttpClient). В 7.14.0
клиент PSR-18/HTTPlug.

## 2. league/openapi-psr7-validator (0.24)

Первоисточник: https://github.com/thephpleague/openapi-psr7-validator/blob/master/README.md

```bash
composer require league/openapi-psr7-validator
```

Валидирует и запрос, и ответ:

```php
$validator = (new \League\OpenAPIValidation\PSR7\ValidatorBuilder)->fromYamlFile($yamlFile)->getServerRequestValidator();
$match = $validator->validate($request);

$validator = (new \League\OpenAPIValidation\PSR7\ValidatorBuilder)->fromYamlFile($yamlFile)->getResponseValidator();
$operation = new \League\OpenAPIValidation\PSR7\OperationAddress('/password/gen', 'get');
$validator->validate($operation, $response);
```

Если маршрут уже известен — `getRoutedRequestValidator()`: «This would simplify validation a lot
and give you more performance».

Мидлварь у пакета — PSR-15 (`\League\OpenAPIValidation\PSR15\ValidationMiddlewareBuilder`),
Laravel-мидлварь другого интерфейса, поэтому нужен мост. Laravel 13 docs (Requests → PSR-7
Requests):

```shell
composer require symfony/psr-http-message-bridge
composer require nyholm/psr7
```

Кеш разбора спеки (PSR-6): `->setCache($cachePool)`, `->setCache($pool, $ttl)`,
`->overrideCacheKey('my_custom_key')`. Без кеша спека парсится на каждый билд валидатора.

`require: php >=7.2`; ставится в этом проекте (`devizzent/cebe-php-openapi 1.1.5`,
`respect/validation 2.5.0`, `league/uri 7.8.1` уже есть).

## 3. Laravel-обёртки

### kirschbaum-development/laravel-openapi-validator (2.0.2)

Только тесты. README: «This package will automatically verify both the request and response used
in your integration and feature tests wherever the Laravel HTTP testing methods (`->get('/uri')`,
etc) are used.» Трейт `Kirschbaum\OpenApiValidator\ValidatesOpenApiSpec` подмешивается в TestCase
и переопределяет `call()` из `MakesHttpRequests` (см. `src/ValidatesOpenApiSpec.php`).

```bash
composer require kirschbaum-development/laravel-openapi-validator
php artisan vendor:publish --provider="Kirschbaum\OpenApiValidator\OpenApiValidatorServiceProvider"
```

Конфиг `config/openapi_validator.php`: `'spec_path' => env('OPENAPI_PATH', base_path('openapi.yaml'))`.
Отключение: `withoutRequestValidation()`, `withoutResponseValidation()`, `withoutValidation()`,
`skipResponseCode()`. По умолчанию 5xx не валидируются.

Совместимость: `php ^8.0`, `illuminate/support ^10.0|^11.0|^12.0|^13.0`,
`league/openapi-psr7-validator ^0.14…^0.24`, `nyholm/psr7 ^1.3`,
`symfony/psr-http-message-bridge ^2.0|^7.0|^8.0`. Бейдж в README: laravel 11.x/12.x/13.x.
Кеш спеки не используется (`ValidatorBuilder` без `setCache`).

### osteel/openapi-httpfoundation-testing (v0.15)

Тоже тестовый, но фреймворк-независимый (работает с HttpFoundation, значит и с
`Illuminate\Http\Request/Response`).

```bash
composer require --dev osteel/openapi-httpfoundation-testing
```

```php
$validator = ValidatorBuilder::fromYamlFile($yamlFile)->getValidator();
$validator->validate($response, '/users', 'post');
$validator->post($response, '/users');
```

Кеш: `ValidatorBuilder::fromYamlFile($yamlFile)->setCache($cache)->getValidator()` (PSR-6/PSR-16).
Требует `php ^8.0`, `league/openapi-psr7-validator ^0.24`, `symfony/http-foundation ^8.0` — ок для
Laravel 13. README предупреждает: до 1.0 минорные версии могут ломать API.

## 4. Чего нет по сравнению с openapi-generator

`docs/generators.md` openapi-generator перечисляет в секции серверных генераторов `php-laravel`,
`php-lumen`, `php-slim4`, `php-symfony`, `php-mezzio-ph`, `php-flight`. Ни один PHP-пакет из
разобранных не даёт:

- заготовок `routes/api.php` из `paths`;
- серверных интерфейсов/абстрактных контроллеров на операцию;
- маппинга operationId → метод контроллера;
- FormRequest/DTO-биндинга входа;
- проверки, что в приложении не осталось маршрутов вне спеки и наоборот.

Всё это пишется руками (или собственным скриптом-генератором поверх распарсенной спеки).

## 5. Рекомендация

Минимальный набор:

```bash
composer require --dev jane-php/open-api-3
composer require jane-php/open-api-runtime
composer require --dev osteel/openapi-httpfoundation-testing   # или kirschbaum-development/laravel-openapi-validator
composer require league/openapi-psr7-validator symfony/psr-http-message-bridge nyholm/psr7  # только если нужен рантайм-контроль
```

Генерируется из спеки: типы/модели/энумы, нормалайзеры, (опционально) валидаторы значений.
Пишется руками: маршруты, контроллеры, привязка операций к хендлерам, авторизация, бизнес-валидация.
Проверка соответствия спеке — рантайм-мидлварь и/или тесты, но это проверка, а не генерация.
