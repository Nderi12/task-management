@csrf

<div class="form-group">
    <label for="title">Title <span class="text-danger">*</span></label>
    <input type="text"
           id="title"
           name="title"
           class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $task->title) }}"
           maxlength="255"
           required
           autofocus>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea id="description"
              name="description"
              rows="5"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $task->description) }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="status">Status <span class="text-danger">*</span></label>
    @php($currentStatus = old('status', $task->status?->value))
    <select id="status"
            name="status"
            class="custom-select @error('status') is-invalid @enderror"
            required>
        @foreach ($statuses as $option)
            <option value="{{ $option->value }}" @selected($currentStatus === $option->value)>
                {{ $option->label() }}
            </option>
        @endforeach
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</div>
