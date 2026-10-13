@extends('layouts.tasks')

@section('title', 'Not found')

@section('content')
    <div class="text-center py-5">
        <h1 class="display-4">404</h1>
        <p class="lead text-muted">We couldn't find what you were looking for.</p>
        <a href="{{ route('tasks.index') }}" class="btn btn-primary">Back to tasks</a>
    </div>
@endsection
