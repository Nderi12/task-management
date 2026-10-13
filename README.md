# Task Manager

A simple Laravel + Bootstrap 4 task manager: create, view, edit, complete and delete tasks.

## Features

- **Create** tasks with a required title, optional description and a status (Pending / Completed).
- **List** tasks with title, creation date and a status badge. Completed tasks are visually distinct
  (green row, strikethrough title, green badge); pending tasks get a yellow badge.
- **Edit** tasks with the same validation as creation.
- **Delete** tasks behind a Bootstrap confirmation modal.
- **Complete / Reopen** a task with one click from the list.
- Friendly validation messages, flash messages and a custom 404 page.

### Bonus features (all implemented)

- **Search & filter** by title and by status (combinable, kept across pagination).
- **Pagination** (10 per page, Bootstrap 4 styled).
- **Drag-and-drop reordering** (SortableJS). The order is persisted in a `position` column and is
  consistent across pages. Reordering is disabled while a search/filter is active.

## Approach

- **Model / enum:** `Task` casts `status` to the `TaskStatus` backed enum, which also provides the label,
  Bootstrap colour and toggle logic, so status strings live in a single place.
- **Validation:** `StoreTaskRequest` / `UpdateTaskRequest` (Form Requests); `ReorderTaskRequest` for the reorder endpoint.
- **Controller:** a resourceful `TaskController` (plus `toggleStatus` and `reorder`), kept thin.
  Query logic lives in model scopes (`search`, `withStatus`, `ordered`).
- **Views:** one Blade layout (`layouts/tasks.blade.php`), a shared `_form` partial for create/edit,
  and a single delete modal reused by every row.
- **Tests:** feature tests in `tests/Feature/TaskTest.php` cover CRUD, validation, toggle, search,
  filter, pagination and reordering.

## Assumptions

- No authentication is required for the task manager (the spec doesn't ask for it). The repository was
  started from the Laravel Livewire starter kit, so its auth and settings routes still exist but are not
  used by the task manager.
- Bootstrap 4 and jQuery are loaded from a CDN, so an internet connection is needed to see the styling.
- New tasks appear at the top of the list until you manually reorder.

## Setup

Requirements: PHP 8.3+, Composer and a database (MySQL by default; SQLite also works).

```bash
composer install
cp .env.example .env
php artisan key:generate

# Configure DB_* in .env (for SQLite: DB_CONNECTION=sqlite and `touch database/database.sqlite`)
php artisan migrate --seed      # --seed adds 25 sample tasks (optional)

php artisan serve
```

Open <http://localhost:8085>. The frontend does not need `npm` / a build step.

## Running tests

```bash
php artisan test
```

Tests use an in-memory SQLite database.
