@extends('layouts.tasks')

@section('title', 'Tasks')

@section('content')
    {{-- Search & filter --}}
    <form method="GET" action="{{ route('tasks.index') }}" class="card card-body shadow-sm mb-4">
        <div class="form-row align-items-end">
            <div class="col-md-6 mb-2 mb-md-0">
                <label for="q" class="sr-only">Search</label>
                <input type="search" id="q" name="q" class="form-control"
                       placeholder="Search by title..." value="{{ $search }}">
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="status-filter" class="sr-only">Status</label>
                <select id="status-filter" name="status" class="custom-select">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $option)
                        <option value="{{ $option->value }}" @selected($status === $option->value)>
                            {{ $option->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-primary flex-fill mr-2">Filter</button>
                @if ($isFiltered)
                    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary flex-fill">Clear</a>
                @endif
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Tasks ({{ $tasks->total() }})</strong>
            @unless ($isFiltered || $tasks->isEmpty())
                <small class="text-muted">Drag <span class="drag-handle">&#9776;</span> to reorder</small>
            @endunless
        </div>

        @if ($tasks->isEmpty())
            <div class="card-body text-center text-muted py-5">
                @if ($isFiltered)
                    No tasks match your search.
                @else
                    You have no tasks yet. <a href="{{ route('tasks.create') }}">Create your first one</a>.
                @endif
            </div>
        @else
            <ul class="list-group list-group-flush" id="task-list"
                data-reorder-url="{{ route('tasks.reorder') }}"
                data-offset="{{ ($tasks->currentPage() - 1) * $tasks->perPage() }}"
                data-sortable="{{ $isFiltered ? '0' : '1' }}">
                @foreach ($tasks as $task)
                    @php($completed = $task->status === \App\Enums\TaskStatus::Completed)
                    <li class="list-group-item {{ $completed ? 'task-completed list-group-item-success' : '' }}"
                        data-id="{{ $task->id }}">
                        <div class="row align-items-center">
                            <div class="col-md-7 d-flex">
                                @unless ($isFiltered)
                                    <span class="drag-handle mr-3 mt-1" title="Drag to reorder">&#9776;</span>
                                @endunless
                                <div>
                                    <h5 class="mb-1 task-title">{{ $task->title }}</h5>
                                    @if ($task->description)
                                        <p class="mb-1 small text-muted task-description">{{ \Illuminate\Support\Str::limit($task->description, 140) }}</p>
                                    @endif
                                    <small class="text-muted">Created {{ $task->created_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>
                            <div class="col-md-2 my-2 my-md-0">
                                <span class="badge badge-{{ $task->status->color() }} p-2">{{ $task->status->label() }}</span>
                            </div>
                            <div class="col-md-3 text-md-right">
                                <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm {{ $completed ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            title="{{ $completed ? 'Mark as pending' : 'Mark as completed' }}">
                                        {{ $completed ? 'Reopen' : 'Complete' }}
                                    </button>
                                </form>
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-toggle="modal"
                                        data-target="#deleteModal"
                                        data-action="{{ route('tasks.destroy', $task) }}"
                                        data-title="{{ $task->title }}">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($tasks->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $tasks->links('pagination::bootstrap-4') }}
        </div>
    @endif

    {{-- Delete confirmation modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form method="POST" action="" id="deleteForm" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="deleteTaskTitle"></strong>?
                    This cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            $('#deleteForm').attr('action', button.data('action'));
            $('#deleteTaskTitle').text(button.data('title'));
        });

        (function () {
            var list = document.getElementById('task-list');
            if (!list || list.dataset.sortable !== '1') return;

            Sortable.create(list, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function () {
                    var order = Array.prototype.map.call(list.children, function (li) {
                        return parseInt(li.dataset.id, 10);
                    });

                    fetch(list.dataset.reorderUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ order: order, offset: parseInt(list.dataset.offset, 10) })
                    }).then(function (response) {
                        if (!response.ok) throw new Error('Request failed');
                    }).catch(function () {
                        alert('Could not save the new order. The page will reload.');
                        window.location.reload();
                    });
                }
            });
        })();
    </script>
@endpush
