<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\ReorderTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $status = is_string($status) ? $status : null;

        $tasks = Task::query()
            ->search($search)
            ->withStatus($status)
            ->ordered()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('tasks.index', [
            'tasks' => $tasks,
            'search' => $search,
            'status' => TaskStatus::tryFrom((string) $status)?->value,
            'statuses' => TaskStatus::cases(),
            'isFiltered' => $search !== '' || TaskStatus::tryFrom((string) $status) !== null,
        ]);
    }

    public function create(): View
    {
        return view('tasks.create', [
            'task' => new Task(['status' => TaskStatus::Pending]),
            'statuses' => TaskStatus::cases(),
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        Task::create($request->validated());

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function edit(Task $task): View
    {
        return view('tasks.edit', [
            'task' => $task,
            'statuses' => TaskStatus::cases(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return back()->with('success', 'Task deleted successfully.');
    }

    public function toggleStatus(Task $task): RedirectResponse
    {
        $task->update(['status' => $task->status->toggled()]);

        return back()->with('success', "Task marked as {$task->status->label()}.");
    }

    /**
     * Persist a drag-and-drop reorder.
     *
     * The client sends the IDs of the tasks on the current page in their new
     * order plus the page offset. We splice them into the full ordered list and
     * rewrite every position so ordering stays consistent across pages.
     */
    public function reorder(ReorderTaskRequest $request): JsonResponse
    {
        /** @var list<int> $pageIds */
        $pageIds = array_map('intval', $request->validated('order'));
        $offset = (int) $request->validated('offset', 0);

        DB::transaction(function () use ($pageIds, $offset) {
            $rest = array_values(array_diff(
                Task::query()->ordered()->pluck('id')->all(),
                $pageIds,
            ));

            array_splice($rest, $offset, 0, $pageIds);

            foreach ($rest as $index => $id) {
                Task::whereKey($id)->update(['position' => $index + 1]);
            }
        });

        return response()->json(['message' => 'Order updated.']);
    }
}
