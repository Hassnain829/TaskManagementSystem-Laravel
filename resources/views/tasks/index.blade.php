@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Tasks</h1>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">Add New Task</a>
</div>
<div class="alert alert-info">
    <strong>🔍 Search Tips:</strong>
    <ul class="mb-0">
        <li>Type any text to search in title or description</li>
        <li>Type status name like "pending", "completed"</li>
        <li>Type date like "2024-01-15" to search by due date</li>
        <li>Type category name to see all tasks in that category</li>
        <li>Type task ID to find specific task</li>
    </ul>
</div>

<!-- ============================================= -->
<!-- 🔍 STEP 1: SEARCH FORM - YEH PURA BLOCK ADD KARO -->
<!-- ============================================= -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">🔍 Search & Filter Tasks</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('tasks.index') }}" class="row g-3">
            <!-- CSRF token nahi chahiye because GET request hai -->
            
            <!-- 📝 Main Search Box -->
            <div class="col-md-6">
                <label for="search" class="form-label">Search</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           placeholder="Search by title, description, ID, status, date..."
                           value="{{ request('search') }}"> <!-- request('search') = URL se search parameter lo -->
                </div>
                <small class="text-muted">You can search by title, description, ID, status, due date, or category</small>
            </div>
            
            <!-- 📊 Filter by Status -->
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            
            <!-- 🗂️ Filter by Category -->
            <div class="col-md-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">All Categories</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- 📅 Date Range Filters -->
            <div class="col-md-3">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
            </div>
            
            <div class="col-md-3">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
            </div>
            
            <!-- Form Buttons -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filter"></i> Apply Filters
                </button>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>
</div>
<!-- ============================================= -->
<!-- 🔍 SEARCH FORM END -->
<!-- ============================================= -->

<!-- Tasks Table -->
<div class="card">
    <div class="card-body">
        <!-- ✅ Show search results count -->
        @if(request()->has('search') || request()->has('status') || request()->has('category_id') || request()->has('from_date'))
            <div class="alert alert-info">
                Showing filtered results. 
                <a href="{{ route('tasks.index') }}" class="alert-link">Clear all filters</a>
            </div>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td> <!-- ID show kar rahe -->
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->category->name ?? 'No Category' }}</td>
                    <td>
                        @php
                            $badgeClass = match($task->status) {
                                'pending' => 'bg-warning',
                                'in_progress' => 'bg-info',
                                'completed' => 'bg-success',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', ucfirst($task->status)) }}</span>
                    </td>
                    <td>{{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</td>
                    <td>
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Move this task to trash?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">
                        <!-- Different message based on whether search is active -->
                        @if(request()->has('search') || request()->has('status') || request()->has('category_id'))
                            No tasks match your filters.
                        @else
                            No tasks found. <a href="{{ route('tasks.create') }}">Create your first task!</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination with search parameters -->
        <div class="d-flex justify-content-center">
            {{ $tasks->links() }}
        </div>
        
        <!-- Show total results -->
        <div class="text-muted text-center">
            Showing {{ $tasks->firstItem() ?? 0 }} to {{ $tasks->lastItem() ?? 0 }} of {{ $tasks->total() }} tasks
        </div>
    </div>
</div>
@endsection