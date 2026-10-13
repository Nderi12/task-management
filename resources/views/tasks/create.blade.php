@extends('layouts.tasks')

@section('title', 'New Task')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header"><strong>Create a task</strong></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tasks.store') }}" novalidate>
                        @include('tasks._form', ['submitLabel' => 'Create Task'])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
