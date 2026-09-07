# Клиентская часть Design First: OpenAPI → SDK для Vue 3 SPA (@hey-api/openapi-ts 0.99.0)

Дата: 2026-09-07. Тикет #6 карты решений. Ничего не устанавливалось; версии сверены с npm-реестром.

Первоисточники:

- документация: `hey-api/hey-api`, каталог `web/src/content/docs/docs/openapi/typescript/**`, тег
  `@hey-api/openapi-ts@0.99.0`;
- сгенерированный код: снапшоты тестов там же,
  `packages/openapi-ts-tests/main/test/__snapshots__/3.1.x/plugins/@hey-api/sdk/throwOnError/*` и
  `.../3.0.x/plugins/@hey-api/client-fetch/sdk-nested-classes/*`;
- CLI: `packages/openapi-ts/src/cli/index.ts` на том же теге;
- `@typespec/openapi3` — `README.md` из npm-тарбола `@typespec/openapi3@1.15.0`;
- `vue-tsc` — `README.md` из npm-тарбола `vue-tsc@3.3.11`.

Версии на 2026-09-07 (npm registry): `@hey-api/openapi-ts` **0.99.0** (2026-06-22),
`@hey-api/vite-plugin` **0.3.2**, `@typespec/compiler` **1.15.0**, `@typespec/openapi3` **1.15.0**,
`@tanstack/vue-query` **5.102.8**, `vue-tsc` **3.3.11**. Peer-зависимость openapi-ts:
`typescript >=5.5.3`. Docs: «runs in any Node.js 22+ environment» — локальный Node 23.6.0 подходит.

## 0. TypeSpec → OpenAPI

`@typespec/openapi3@1.15.0/README.md`:

```bash
tsp compile . --emit=@typespec/openapi3
```

либо через `tspconfig.yaml`:

```yaml
emit:
  - "@typespec/openapi3"
options:
  "@typespec/openapi3":
    option: value
```

Дефолты эмиттера: `emitter-output-dir` = `{output-dir}/@typespec/openapi3`, `file-type` = `yaml`,
`output-file` = `{service-name-if-multiple}.{version}.openapi.yaml`, `openapi-versions` =
`["3.0.0"]`. То есть по умолчанию получаем `tsp-output/@typespec/openapi3/openapi.yaml` — это и есть
`input` для openapi-ts.

## 1. Конфигурация @hey-api/openapi-ts

Установка (docs, Get Started): `npm install @hey-api/openapi-ts -D -E`; «This package is in initial
development. Please pin an exact version».

Конфиг — любой файл в корне проекта, читаемый jiti-загрузчиком c12; в докcах показаны
`openapi-ts.config.ts`, `openapi-ts.config.cjs`, `openapi-ts.config.mjs`, `openapi-ts.config.js`.

```ts
// openapi-ts.config.ts
import { defineConfig } from '@hey-api/openapi-ts';

export default defineConfig({
  input: 'hey-api/backend', // sign up at app.heyapi.dev
  output: 'src/client',
});
```

`input` — путь, URL, шорткат реестра, объект `{ path, fetch, watch, branch }` или сам объект
спецификации. `output` — строка-путь либо объект `{ path, clean, entryFile, tsConfigPath,
postProcess, ... }`.

Запуск (docs, Usage → CLI):

```json
"scripts": {
  "openapi-ts": "openapi-ts"
}
```

`npm run openapi-ts`. Без конфига — `npx @hey-api/openapi-ts -i hey-api/backend -o src/client`.

Флаги CLI (`packages/openapi-ts/src/cli/index.ts`, дословно):

```
-i, --input <path...>     OpenAPI specification (path, URL, or string)
-o, --output <path...>    Output folder(s)
-c, --client <name>       HTTP client to generate
-p, --plugins [names...]  Plugins to use
-f, --file <path>         Path to config file
-d, --debug               Enable debug logging
-s, --silent              Suppress all output
-l, --logs <path>         Logs folder path
--no-log-file             Disable log file output
--dry-run                 Skip writing files
-w, --watch [interval]    Watch for changes
```

Куда пишет (docs, Output). Дефолтный набор в `output.path`:

```
src/client/
  client/        # бандл клиента
  core/          # бандл ядра
  client.gen.ts  # export const client = createClient(createConfig());
  index.ts       # export * from './sdk.gen'; export * from './types.gen';
  sdk.gen.ts
  types.gen.ts
```

Два дословных предупреждения из docs: «You should treat the output folder as a dependency. Do not
directly modify its contents as your changes might be erased when you run `@hey-api/openapi-ts`
again» и «By default, you can't keep custom files in the `path` folder because it's emptied on every
run» (`output.clean`, по умолчанию включён).

## 2. Плагины под Vue

Ядро (`Core plugins`): `@hey-api/typescript`, `@hey-api/sdk`, `@hey-api/transformers`,
`@hey-api/schemas`. По умолчанию генерируются TypeScript-интерфейсы и SDK — то есть только типы и
функции вызова. Composables «из коробки» нет.

Vue-релевантные плагины (полный список каталога `plugins/` на теге 0.99.0):

- `@tanstack/vue-query` — плагин TanStack Query v5, вкладка `vue` в docs. Генерирует
  `queryOptions`/`infiniteQueryOptions`/`mutationOptions` и query-ключи, не хуки. Важная оговорка из
  docs: «In Vue applications, you need to wrap the options functions in `computed()` to make them
  reactive», иначе «Query will execute only once».
- `@pinia/colada` — плагин Pinia Colada v0, прямо позиционируется как Vue-решение; генерирует
  `getPetByIdQuery()` и mutation options.
- `swr` — есть, но React-only по смыслу; для Vue не берём.
- Валидаторы/трансформеры: `zod` (v3/v4/mini), `valibot`, `arktype`, `typebox`, `superstruct`,
  `joi`, `yup`, `ajv`.
- Моки: `msw`, `faker`, `falso`, `chance`, `nock`, `playwright`, `supertest`.
- Прочее (не для нас): `angular/v19|v20`, `tanstack-start`, `zustand`, `orpc`, серверные
  `express|fastify|hono|koa|nest|elysia|adonis`.

Итог: под Vue 3 либо голый SDK (`sdk.gen.ts` + `types.gen.ts`), либо SDK + `@tanstack/vue-query`,
либо SDK + `@pinia/colada`.

## 3. HTTP-клиент по умолчанию и его настройка

Fetch API — дефолт. Docs, страница Fetch API: «This step is optional because Fetch is the default
client». Список клиентов: Fetch API, Angular, Axios, Ky, Next.js, Nuxt, OFetch, Effect (голосование),
Got (голосование). Переключение — плагином:

```js
export default {
  input: 'hey-api/backend',
  output: 'src/client',
  plugins: ['@hey-api/client-axios'], // или '@hey-api/client-fetch'
};
```

или `-c @hey-api/client-fetch` в CLI.

Отдельно ставить пакет клиента не нужно: npm помечает `@hey-api/client-fetch@0.13.1` как
`deprecated: "Starting with v0.73.0, this package is bundled directly inside @hey-api/openapi-ts"`, и
снапшот `sdk.gen.ts` импортирует из локального бандла: `import type { Client, ClientMeta, Options as
Options2, RequestResult, TDataShape } from './client';`.

Три способа настройки (docs):

```js
// 1) setConfig — просто, но клиент может быть вызван до конфигурации
import { client } from 'client/client.gen';
client.setConfig({ baseUrl: 'https://example.com' });
```

```js
// 2) Runtime API — конфигурация применяется до инициализации client
export default {
  output: 'src/client',
  plugins: [{ name: '@hey-api/client-fetch', runtimeConfigPath: './src/hey-api.ts' }],
};
```

```ts
// src/hey-api.ts
import type { CreateClientConfig } from './client/client.gen';
export const createClientConfig: CreateClientConfig = (config) => ({
  ...config,
  baseUrl: 'https://example.com',
});
```

```js
// 3) createClient — свой инстанс, передаётся в SDK через опцию client
const myClient = createClient({ baseUrl: 'https://example.com' });
const response = await getFoo({ client: myClient });
```

Что настраивается (снапшот `client/types.gen.ts`, тег 0.99.0):

```ts
export interface Config<T extends ClientOptions = ClientOptions>
  extends Omit<RequestInit, 'body' | 'headers' | 'method'>, CoreConfig {
  baseUrl?: T['baseUrl'];
  fetch?: typeof fetch;                 // @default globalThis.fetch
  parseAs?: 'arrayBuffer' | 'auto' | 'blob' | 'formData' | 'json' | 'stream' | 'text';
  responseStyle?: ResponseStyle;        // @default 'fields'
  throwOnError?: T['throwOnError'];     // @default false
}
```

`credentials` покрывается наследованием от `RequestInit` (омитятся только `body`, `headers`,
`method`), то есть `client.setConfig({ credentials: 'include' })` типизирован. `headers` приходит из
`core/types.gen.ts`: «An object containing any HTTP headers that you want to pre-populate your...»,
тип `RequestInit['headers'] | ...`. Там же `auth?: ((auth: Auth) => Promise<AuthToken> | AuthToken) |
AuthToken`, `querySerializer`, `bodySerializer`.

Для Laravel-сессии (Sanctum SPA) это `credentials: 'include'` + свой `baseUrl`; для Bearer — `auth`.
Docs: «The SDK plugin currently supports only the `bearer` and `basic` auth schemes».

Есть перехватчики: `client.interceptors.request.use|eject|update`, то же для `response`.

## 4. Ошибки и коды ответов

Типизированы. `@hey-api/typescript` генерирует на операцию три типа, ключи — коды статусов
(снапшот `types.gen.ts`):

```ts
export type CallWithResponsesResponses = {
    200: { readonly value?: Array<ModelWithString> };
    201: ModelThatExtends;
};
export type CallWithResponsesErrors = {
    500: ModelWithStringError;
    501: ModelWithStringError;
    502: ModelWithStringError;
};
export type CallWithResponsesResponse = CallWithResponsesResponses[keyof CallWithResponsesResponses];
```

`sdk.gen.ts` протаскивает оба в клиент:

```ts
export const getApiVbyApiVersionSimpleOperation = <ThrowOnError extends boolean = true>(
  options: Options<GetApiVbyApiVersionSimpleOperationData, ThrowOnError>
): RequestResult<GetApiVbyApiVersionSimpleOperationResponses, GetApiVbyApiVersionSimpleOperationErrors, ThrowOnError> =>
  (options.client ?? client).get<...>({ url: '/api/v{api-version}/simple:operation', ...options });
```

Что возвращает вызов (`RequestResult` из `client/types.gen.ts`). При `throwOnError: false`
(дефолт) и `responseStyle: 'fields'` (дефолт) — дискриминированный union:

```ts
Promise<(
  | { data: TData[keyof TData]; error: undefined }
  | { data: undefined; error: TError[keyof TError] }
) & {
  /** request may be undefined, because error may be from building the request object itself */
  request?: Request;
  /** response may be undefined, because error may be ... or from a network error */
  response?: Response;
}>
```

При `throwOnError: true` — `Promise<{ data; request: Request; response: Response }>`, ошибка
бросается. При `responseStyle: 'data'` возвращается сам `data` (и `| undefined`, если не бросаем).

Важно: `data` и `error` — это `TData[keyof TData]`, объединение по всем кодам; конкретный статус
из типа не восстанавливается, различать нужно по `response.status` или по форме тела. Рантайм-
валидации ответа по умолчанию нет — docs: «Validating data at runtime comes with a performance cost,
which is why it's not enabled by default»; включается `validator: 'zod'` у `@hey-api/sdk`.

Опция `meta` по умолчанию не типизирована («By default `meta` is untyped, so typos and wrong-typed
values compile silently»); типизируется аугментацией `interface ClientMeta`.

## 5. Как ловить расхождение с контрактом на vue-tsc, а не в рантайме

Механика: `types.gen.ts` — единственный источник правды по формам запросов/ответов, и он
регенерируется целиком. Ключевые опоры:

1. `output.clean` включён по умолчанию — папка «is emptied on every run». Удалённая из контракта
   операция исчезает из `sdk.gen.ts`, импорт в компоненте перестаёт резолвиться → ошибка `vue-tsc`.
   Не выключать `clean` (docs: «Setting `clean` to `false` may result in broken output»).
2. Импортировать SDK и типы только из сгенерированной папки, никаких ручных DTO-дублей. Docs
   рекомендуют импортировать из конкретных файлов, а не из `index.ts`; при желании
   `output.entryFile: false`.
3. Не гасить union `{data, error}`: при дефолтном `throwOnError: false` TypeScript заставляет
   разобрать оба варианта. Если удобнее исключения — ставить `throwOnError: true` осознанно.
4. Типизировать `meta` через `declare module ... { interface ClientMeta { ... } }`, иначе опечатки
   компилируются молча.
5. Скрипт проверки: `vue-tsc --noEmit` (README `vue-tsc@3.3.11`: `"type-check": "vue-tsc --noEmit"`,
   «Requires TypeScript 5.0.0 or higher»). Обязательно после шага генерации.
6. `output.tsConfigPath` — генератор читает tsconfig проекта, «to generate output matching your
   project's settings»; по умолчанию ищет вверх от файла конфига. Держать один tsconfig, чтобы
   строгость (`strict`) применялась и к сгенерированному коду.
7. Порядок в CI: `tsp compile` → `openapi-ts` → `vue-tsc --noEmit`. Если output закоммичен —
   добавить `git diff --exit-code src/client`, иначе устаревший коммит пройдёт проверку.

Чего типы НЕ поймают: несовпадение реального ответа Laravel с контрактом (только рантайм-валидатор
`validator: 'zod'`), и различение конкретных статус-кодов внутри union.

## 6. Vite

Отдельный шаг генерации не обязателен — есть официальный `@hey-api/vite-plugin` (0.3.2), docs
«supports Vite 5, 6, 7, and 8», «running automatically whenever Vite resolves its configuration – no
separate script or manual step required»:

```ts
// vite.config.ts
import { heyApiPlugin } from '@hey-api/vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
  plugins: [heyApiPlugin()],
});
```

Плагин подхватывает `openapi-ts.config.ts`; можно передать `config` инлайном и ограничить фазу:

```ts
heyApiPlugin({
  config: { input: 'hey-api/backend', output: 'src/client' },
  vite: { apply: 'serve' },
})
```

Watch-режим: `input.watch: true` в конфиге или `-w` в CLI
(`npx @hey-api/openapi-ts -i hey-api/backend -o src/client -w`). Дословная оговорка docs:
**«Watch mode currently supports only remote files via URL»** — для локального
`tsp-output/@typespec/openapi3/openapi.yaml` watch не работает. Варианты: отдавать спеку по HTTP,
либо гонять `tsp compile --watch` + перегенерацию своим watcher'ом, либо просто `heyApiPlugin()`,
который дёргает генерацию при resolve конфига Vite.

`.gitignore`. Прямой рекомендации в docs нет; есть два факта: «treat the output folder as a
dependency. Do not directly modify its contents» и «it's emptied on every run». Отсюда два
непротиворечивых сценария:

- игнорировать `src/client/` (и `tsp-output/`) и генерировать в CI перед `vue-tsc`/`build` — тогда
  дрейф невозможен по построению;
- либо коммитить `src/client/` ради ревью диффа контракта, но тогда в CI обязателен
  `git diff --exit-code src/client` после генерации.

Для post-processing вывода docs дают `output.postProcess: ['eslint']` / `['prettier']` /
`['biome:format']` и подсказку: «You can skip processing by adding the output path to the tool's
ignore file (for example `.eslintignore` or `.prettierignore`)».

## Открытые вопросы

- Нужен ли `@tanstack/vue-query` или хватит SDK + Pinia — зависит от объёма кеширования, тикетом не
  закрыто.
- Аутентификация: Sanctum-сессия (`credentials: 'include'`) против Bearer (`auth`) — решение за
  серверной частью; SDK поддерживает только `bearer` и `basic` из OpenAPI security schemes.
