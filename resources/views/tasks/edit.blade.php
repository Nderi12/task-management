@extends('layouts.tasks')

@section('title', 'Edit Task')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header"><strong>Edit task</strong></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tasks.update', $task) }}" novalidate>
                        @method('PUT')
                        @include('tasks._form', ['submitLabel' => 'Save Changes'])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
