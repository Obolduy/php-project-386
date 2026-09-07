# AGENTS.md

Сервис записи на звонок: владелец публикует доступное время, гость выбирает свободный слот на 30 минут и записывается. Авторизации, личных кабинетов и интеграций с внешними календарями в проекте нет.

## Стек

Laravel 13 на PHP 8.4, Blade + Vite 8 + Tailwind 4, SQLite. Тесты — Pest, форматирование — Pint.

Для фронтенда нужен Node 22 (`nvm use 22`). На Node 16 Vite не запустится.

## Структура кода

Доменный код лежит модулями прямо в `app/`, рядом с техническими `Http/` и `Providers/`. Неймспейс модуля — `App\<Module>`.

Слои внутри модуля:

```
app/<Module>/
  Domain/          сущности, value-объекты, доменные правила
  Application/     юзкейсы
  Infrastructure/  Eloquent-модели, репозитории, контроллеры, form requests
```

Модуль не лезет во внутренности другого модуля: обращение только через `Application`.

Контроллеры — с именованными методами (`index`, `store` и так далее). Инвокабельные контроллеры с `__invoke` не используем.

`php artisan make:*` кладёт файлы в дефолтные места — после генерации переносим их в модуль и правим неймспейс. Автозагрузку менять не нужно: PSR-4 `App\` уже указывает на `app/`.

## Команды

| Что | Команда |
| --- | --- |
| Запуск всего окружения (сервер, очередь, логи, Vite) | `composer run dev` |
| Только бекенд | `php artisan serve` |
| Только фронтенд | `npm run dev` |
| Сборка фронтенда | `npm run build` |
| Тесты | `composer test` |
| Один тест | `php artisan test --filter=имяТеста` |
| Линтер (проверка) | `composer lint` |
| Линтер (исправление) | `vendor/bin/pint` |

Первичная настройка после клонирования: `composer setup`.

## Коммиты

Формат сообщений — [Conventional Commits](https://www.conventionalcommits.org): `feat:`, `fix:`, `chore:`, `docs:`, `refactor:`, `test:`. Это правило распространяется и на коммиты, которые делает агент.

По истории коммитов release-please собирает changelog и держит release-PR с версией. Неправильный префикс — версия посчитается неверно.

## CI

`.github/workflows/ci.yml` гоняет линтер и тесты на каждый push.

`.github/workflows/release-please.yml` создаёт release-PR после мержа в `main`, ему нужен секрет `RELEASE_PLEASE_TOKEN`.

`.github/workflows/hexlet-check.yml` — проверка Хекслета, его нельзя удалять и править.

## Agent skills

### Issue tracker

Задачи и спецификации живут в GitHub Issues репозитория `Obolduy/php-project-386`, работа через `gh`. См. `docs/agents/issue-tracker.md`.

### Triage labels

Канонические метки без переименований. См. `docs/agents/triage-labels.md`.

### Domain docs

Один контекст: `CONTEXT.md` и `docs/adr/` в корне. См. `docs/agents/domain.md`.

## Прочее

Гайдлайны Laravel Boost лежат в `CLAUDE.md`, его скиллы — в `.claude/skills/`. Их генерирует Boost, руками не правим.

Инженерные скиллы стоят в `.agents/skills/` (симлинки в `.claude/skills/`), версии зафиксированы в `skills-lock.json`. Обновление: `npx skills@latest update`.
