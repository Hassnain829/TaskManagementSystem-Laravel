@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Task Details</h4>
                <div>
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning btn-sm">Edit</a>
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 150px;">Title:</th>
                        <td>{{ $task->title }}</td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td>{{ $task->description ?: 'No description provided' }}</td>
                    </tr>
                    <tr>
                        <th>Category:</th>
                        <td>{{ $task->category->name ?? 'No Category' }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
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
                    </tr>
                    <tr>
                        <th>Due Date:</th>
                        <td>{{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>{{ $task->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td>{{ $task->updated_at->format('d M Y, h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection