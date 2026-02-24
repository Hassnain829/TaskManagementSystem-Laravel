<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of tasks with search filter
     */
    public function index(Request $request) // ✅ Request $request parameter add kiya
    {
        // ✅ Step 1: Current logged-in user ki tasks query banayi
        $query = auth()->user()->tasks()->with('category'); // with('category') = category relation load karo
        
        // ✅ Step 2: Check karo agar search parameter hai URL mein
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search; // User ne jo type kiya hai wo variable mein store karo
            
            // ✅ Step 3: WHERE clauses add karo
            $query->where(function($q) use ($search) {
                // Yeh closure multiple conditions ke liye hai
                // (title LIKE %search% OR description LIKE %search% OR id = search OR status LIKE %search%)
                
                $q->where('title', 'LIKE', "%{$search}%") // Title mein search
                  ->orWhere('description', 'LIKE', "%{$search}%") // Description mein search
                  ->orWhere('id', 'LIKE', "%{$search}%") // ID mein search (agar number hai to)
                  ->orWhere('status', 'LIKE', "%{$search}%"); // Status mein search
                  
                // ✅ Step 4: Date field ke liye special handling
                // Agar search date jaisa lagta hai to due_date mein bhi search karo
                if (strtotime($search)) {
                    // strtotime() check karta hai ke yeh valid date hai ya nahi
                    $q->orWhere('due_date', 'LIKE', "%{$search}%");
                }
            });
            
            // ✅ Step 5: Category name mein bhi search karo
            // Yeh thoda complex hai - relation ke through search
            $query->orWhereHas('category', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%"); // Category table ke name column mein search
            });
        }
        
        // ✅ Step 6: Filter by status (agar specific status filter ho)
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // ✅ Step 7: Filter by category (agar specific category filter ho)
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }
        
        // ✅ Step 8: Filter by date range
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('due_date', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('due_date', '<=', $request->to_date);
        }
        
        // ✅ Step 9: Order by and paginate
        $tasks = $query->orderBy('created_at', 'desc') // Newest first
                       ->paginate(10) // 10 per page
                       ->withQueryString(); // ✅ IMPORTANT: Pagination ke saath search parameters bhi pass karo
        
        // ✅ Step 10: Categories fetch karo dropdown ke liye
        $categories = auth()->user()->categories()->pluck('name', 'id');
        
        // ✅ Step 11: View ko data bhejo
        return view('tasks.index', compact('tasks', 'categories'));
    }

/*
    public function index()
    {
        // Sirf logged-in user ke tasks dikhao
        $tasks = auth()->user()->tasks()
            ->with('category') // Eager loading for performance
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('tasks.index', compact('tasks'));
    }
 */

    public function create()
    {
        // Sirf logged-in user ki categories select box ke liye
        $categories = auth()->user()->categories()->pluck('name', 'id');
        return view('tasks.create', compact('categories'));
    }

    public function store(StoreTaskRequest $request)
    {
        auth()->user()->tasks()->create($request->validated());
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }
        
        $categories = auth()->user()->categories()->pluck('name', 'id');
        return view('tasks.edit', compact('task', 'categories'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }
        
        $task->update($request->validated());
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }
        
        $task->delete(); // Soft delete
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task moved to trash.');
    }

    // Custom method for restore
    public function restore($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }
        
        $task->restore();
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task restored successfully.');
    }
}