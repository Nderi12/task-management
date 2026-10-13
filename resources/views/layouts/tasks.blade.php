<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Task Manager') &middot; {{ config('app.name') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .task-completed .task-title { text-decoration: line-through; color: #6c757d; }
        .drag-handle { cursor: grab; color: #adb5bd; user-select: none; }
        .sortable-ghost { opacity: .4; background: #e9ecef; }
        .task-description { white-space: pre-line; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('tasks.index') }}">&#10003; Task Manager</a>
            <a class="btn btn-success btn-sm ml-auto" href="{{ route('tasks.create') }}">+ New Task</a>
        </div>
    </nav>

    <main class="container pb-5">
        @include('partials.alerts')
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
