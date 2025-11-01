<div align="center">

# PC Configurator — Конфигуратор ПК на Laravel

Каталог комплектующих, умный конфигуратор с проверкой совместимости, публикацией сборок и базовым админ‑модулем. UI на Blade + Tailwind CSS.

</div>

## Назначение проекта

Проект помогает быстро подобрать совместимые комплектующие и собрать конфигурацию ПК под бюджет/потребности. Реализованы:

- Каталог комплектующих с фильтрами (сокет, производитель, тип памяти и т.д.).
- Конфигуратор сборок с валидацией совместимости на основе правил и характеристик компонентов.
- Публикация сборок, лайки/дизлайки, отметка «лучшая сборка», комментарии.
- Публичная страница сборки для шаринга.
- Админ‑возможности: управление пользователями, источниками (маркетами) и парсинг позиций из магазинов.

## Ключевые возможности

- Каталог и карточки товара: `GET /catalog` (`resources/views/pccomponents/catalog.blade.php`).
- Конфигуратор: `GET /configurator` — выбор компонентов по категориям + превью изображений.
- Проверка совместимости: AJAX `POST /configurator/check-compatibility-multi` в `ComponentController`.
- Список сборок: `GET /builds` с сортировками/поиском, подробностями и сравнениями по цене.
- Рейтинг и «лучшая сборка»: эндпоинты `configurations.like/dislike/best`.
- Комментарии к сборкам: `POST /comments`, `DELETE /comments/{comment}`.
- Админ‑панель пользователей и действий (правки, удаление): `resources/views/admin/admin-panel-users.blade.php`.
- Парсинг маркетов (Roach PHP spiders): `ParserController` + пауки в `app/Spiders/*`.
- Опциональный ИИ‑помощник (DeepSeek API) для подсказок по выбору: `POST /ai-chat` (`AiChatController`).

## Технологии

- Backend: Laravel 12.x, PHP 8.2+
- Views: Blade, Tailwind CSS (CLI), частично Alpine.js (через CDN)
- Парсинг: RoachPHP (`roach-php/core`, `roach-php/laravel`), Spatie Browsershot; есть заготовки под Puppeteer
- БД: MySQL (docker-compose), Eloquent ORM
- Аутентификация: Laravel Breeze (routes/auth.php)

## Используемые технологии и зависимости

- Язык/фреймворк: PHP 8.2+, Laravel 12.x
- Шаблоны: Blade, partials и layout‑ы
- Стили: Tailwind CSS (CLI), PostCSS, Autoprefixer
- JS/поведение: Alpine.js (CDN)
- БД: MySQL 8.x, Eloquent ORM
- Очереди: Laravel Queue (заготовка `ParseMarketJob`)
- Парсинг/скрейпинг: RoachPHP, Symfony DomCrawler + CssSelector, Spatie Browsershot, Puppeteer (+ плагины)
- HTTP‑клиент/интеграции: GuzzleHTTP, DeepSeek API (опционально)

### Composer (require)

- `laravel/framework` ^12.0 — ядро фреймворка
- `inertiajs/inertia-laravel` ^2.0 — интеграция Inertia (в проекте установлено, используется ограниченно)
- `guzzlehttp/guzzle` ^7.9 — HTTP‑клиент
- `roach-php/core` ^3.2, `roach-php/laravel` ^3.2 — пауки/пайплайны для парсинга
- `spatie/browsershot` ^5.0 — headless Chrome для снимков/рендеринга
- `symfony/dom-crawler` ^7.2, `symfony/css-selector` ^7.2 — разбор HTML
- `laravel/tinker` ^2.10.1 — REPL

### Composer (require-dev)

- `laravel/breeze` ^2.3 — аутентификация/стартовый скелет
- `laravel/telescope` ^5.5 — отладка/инспекция
- `laravel/pint` ^1.13 — форматирование кода
- `laravel/sail` ^1.41 — dev‑контейнеры (по желанию)
- `laravel/pail` ^1.2.2 — лог‑вьювер в терминале
- `phpunit/phpunit` ^11.5.3, `mockery/mockery` ^1.6 — тестирование
- `nunomaduro/collision` ^8.6 — UX ошибок в консоли
- `fakerphp/faker` ^1.23 — фиктивные данные

### NPM dependencies

- `@inertiajs/inertia` ^0.11.1, `@inertiajs/inertia-vue3` ^0.6.0 — Inertia (опционально)
- `puppeteer` ^24.22.3 — headless Chrome
- `puppeteer-extra` ^3.3.6, плагины `repl` ^2.3.3, `stealth` ^2.11.2

### NPM devDependencies

- `tailwindcss` ^3.1.0 — утилитарные стили
- `@tailwindcss/forms` ^0.5.10, `@tailwindcss/typography` ^0.5.16, `@tailwindcss/aspect-ratio` ^0.4.2
- `postcss` ^8.4.31, `autoprefixer` ^10.4.2

## Структура проекта (важное)

- Контроллеры: `app/Http/Controllers` — каталог, конфигуратор, админка, аутентификация
- Модели: `app/Models` — `Component`, `Category`, `Configurations`, `CompatibilityRule`, `Comment`, `ConfigurationVote`, `Markets`, `MarketsUrls`
- Виды: `resources/views` — каталоги `pccomponents/*`, `configurationbuild/*`, лейауты `layouts/*`
- Роуты: `routes/web.php` — все веб‑маршруты приложения
- Парсеры: `app/Spiders/*` — пауки для разных маркетов и пайплайны
- Публичные ассеты: `public/css/*`, изображения по умолчанию `public/images/*`
- Tailwind: `tailwind.config.js`, `resources/css/app.css` → `public/css/app.css`
- Docker: `docker-compose.yml`, конфиги в `docker/php`, `docker/nginx`

## Структура проекта

```
app/
  Http/Controllers/
    Admin/ParserController.php           # парсинг маркетов
    Components/ComponentController.php   # каталог + проверка совместимости
    Configuration/ConfigurationController.php  # сборки, лайки/комменты
    AiChatController.php                 # интеграция с DeepSeek API
    ProfileController.php                # профиль/пользователи (админ)
  Models/                                # Eloquent-модели (Component, Category, ...)
  Spiders/                               # RoachPHP пауки и пайплайны

resources/
  views/
    layouts/ (navigation.blade.php, app.blade.php, guest.blade.php)
    pccomponents/ (catalog, show, chart, partial/*)
    configurationbuild/ (configurator, builds, showconf, publicBuild)
    admin/admin-panel-users.blade.php
    auth/* (login, register, reset, etc.)
    profile/* (edit + partials)
  css/app.css                            # исходник Tailwind

public/
  css/ (app.css, main.css, light-dark.css, login.css)
  images/ (defaulte_image.jpg)

routes/
  web.php, auth.php

database/
  migrations/                            # миграции (часть сущностей)
  seeders/                               # сиды примеров

docker/
  php/Dockerfile, nginx/default.conf

tailwind.config.js, postcss.config.js, package.json
```

## Основные Blade‑файлы

- `resources/views/layouts/navigation.blade.php` — общий layout/навигация, переключение темы, инклюдится на страницах.
- `resources/views/welcome.blade.php` — главная страница.
- `resources/views/pccomponents/catalog.blade.php` — каталог с фильтрами; частичные:
  - `resources/views/pccomponents/partial/components.blade.php`
  - `resources/views/pccomponents/partial/components_grid.blade.php`
  - `resources/views/pccomponents/partial/components_list.blade.php`
- `resources/views/pccomponents/show.blade.php` — карточка компонента.
- `resources/views/pccomponents/chart.blade.php` — графики/диаграммы по компоненту.
- `resources/views/configurationbuild/configurator.blade.php` — конфигуратор сборки с превью изображений.
- `resources/views/configurationbuild/builds.blade.php` — список сборок (поиск/сортировки/аккордеоны).
- `resources/views/configurationbuild/showconf.blade.php` — детальная страница сборки.
- `resources/views/configurationbuild/publicBuild.blade.php` — публичная страница для шаринга.
- `resources/views/admin/admin-panel-users.blade.php` — управление пользователями (редактирование/удаление).
- `resources/views/profile/edit.blade.php` + `resources/views/profile/partials/*` — профиль пользователя.
- `resources/views/auth/*.blade.php` — аутентификация/верификация.

## Установка и настройка

Требования: PHP 8.2+, Composer, Node.js 18+, NPM, MySQL 8+ (или Docker)

1. Установка зависимостей

```bash
composer install
npm install
```

2. Конфигурация окружения

```bash
cp .env.example .env           # На Windows: copy .env.example .env
php artisan key:generate
```

В `.env` настройте подключение к БД (DB_*) и при необходимости ключ для ИИ‑чата:

```
DEEPSEEK_API_KEY=your_api_key_here
```

3. Миграции и данные

```bash
php artisan migrate

# При необходимости — отдельные сиды примеров:
php artisan db:seed --class=CategoriesTableSeeder
php artisan db:seed --class=ComponentsTableSeeder
php artisan db:seed --class=CompatibilityRulesTableSeeder

# Публичные файлы/изображения
php artisan storage:link
```

4. Сборка стилей (Tailwind CLI)

```bash
npm run dev     # вотчинг в разработке
# или
npm run build   # минифицированная сборка
```

5. Запуск приложения

```bash
php artisan serve
# http://127.0.0.1:8000
```

Рекомендации:

- Создайте симлинк для публичного доступа к файлам: `php artisan storage:link`.
- Для проверки форм/лайков/комментариев создайте пользователя через регистрацию (`/register`).
- Для парсинга магазинов используйте эндпоинты из админ‑панели; при переходе на очереди — `php artisan queue:work`.
- Чтобы назначить пользователя администратором, выставьте `admin=1` в таблице `users` (через DB/миграцию/сид/`tinker`).

## Запуск через Docker

В проект включён `docker-compose.yml` с `php-fpm`, `nginx`, `mysql`, `phpmyadmin`.

1. Подготовьте `.env` — используйте хост `DB_HOST=db`, порт `3306`, имя/пароль из `docker-compose.yml`.

2. Поднимите сервисы:

```bash
docker-compose up -d --build
```

3. Сгенерируйте ключ и соберите стили (в контейнере `laravel-app`):

```bash
docker exec -it laravel-app php artisan key:generate
docker exec -it laravel-app php artisan storage:link
docker exec -it laravel-app npm ci && npm run build
```

Приложение будет доступно на `http://localhost:8000`, phpMyAdmin — `http://localhost:8080`.

Примечание: БД инициализируется дампом `docker/db/backup.sql`.

## Использование

- Главная: `/` — приветственный экран.
- Каталог: `/catalog` — фильтры по категории, сокету, производителю, цене и т.д.
- Конфигуратор: `/configurator` — выбрать по одному компоненту из каждой категории и сохранить сборку.
- Сборки: `/builds` — список с поиском/сортировкой, лайками, пометкой «лучшая» и комментариями.
- Публичная сборка: `/public-build/{id}` — шэринг сборки без авторизации.

## Проверка совместимости

Правила хранятся в таблице `compatibility_rules` и описывают соответствия между категориями и полями характеристик (операторы `==, >=, <=, >, <`, а также пересечение множеств для `form_factor`, `interface`). Логика проверки — `ComponentController::checkCompatibilityMulti()`.

## Парсер магазинов

- Маршрут админ‑парсинга: `POST /admin/parse` (`ParserController@parse`).
- Ссылки категорий на маркеты: сохраняются через `MarketsUrls` (эндпоинты `markets-urls.save/get`).
- Пауки: `app/Spiders/ComponentSpider.php`, `ComponentRegardSpider.php`, `ComponentDnsSpider.php`, `ComponentKnsSpider.php` и т.п.
- Изображения сохраняются в `storage/app/public/products` (понадобится `php artisan storage:link`).

Очереди: в репозитории есть заготовка `ParseMarketJob`, но на текущий момент парсинг стартует напрямую из контроллера (Roach::startSpider). При желании можно переключить на фоновую обработку и `queue:work`.

## Переменные окружения

Основные ключи `.env`:

- `APP_URL` — базовый URL
- `DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD`
- `DEEPSEEK_API_KEY` — ключ для ИИ‑чата (`AiChatController`)
- `FILESYSTEM_DISK=public` — для публичных изображений

### AI Assistant (Yandex Gateway)

- `YANDEX_GATEWAY_URL` — URL вашего API Gateway
- `YANDEX_API_KEY_ID`, `YANDEX_API_KEY_SECRET` — зарезервировано под будущую аутентификацию (в коде не используются, если гейтвей сам инжектит авторизацию)

Тестирование локально:

1. Установите переменные в `.env` (см. `.env.example`).
2. Запустите сервер: `php artisan serve`.
3. Авторизуйтесь в приложении и откройте `/ai` — введите текст и нажмите Send.

## Полезные скрипты

```json
// package.json
{
  "scripts": {
    "dev": "npx tailwindcss -i ./resources/css/app.css -o ./public/css/app.css --watch",
    "build": "npx tailwindcss -i ./resources/css/app.css -o ./public/css/app.css --minify"
  }
}
```

## Навигация по коду

- Роуты: `routes/web.php`
- Каталог: `app/Http/Controllers/Components/ComponentController.php`
- Конфигуратор и сборки: `app/Http/Controllers/Configuration/ConfigurationController.php`
- Админ‑операции: `app/Http/Controllers/ProfileController.php`, `app/Http/Controllers/Admin/ParserController.php`
- Виды: `resources/views/*`

## Примечания

- В некоторых Blade‑шаблонах и строках встречается искажённая кириллица из‑за кодировки. На функциональность не влияет, но рекомендуется нормализовать кодировку исходников в UTF‑8.
- В `ProfileController` найден незавершённый конфликт слияния. Перед продакшеном стоит разрешить конфликт и привести контроллер к единой версии.

## Лицензия

Проект распространяется на условиях лицензии MIT.
