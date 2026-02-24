<?php

namespace App\Http\Controllers; // Namespace - yeh folder batata hai

use App\Models\Category; // Category Model import
use App\Http\Requests\StoreCategoryRequest; // Form Request import
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
     /**
     * Constructor - sabse pehle run hota hai
     */
    public function __construct()
    {
        // Sab methods ke liye authentication check
        $this->middleware('auth');
    }

      /**
     * GET|HEAD: /categories
     * Sab categories dikhao (LIST)
     */
    public function index()
    {
                // 1. Current logged-in user ki categories lo
        // auth()->user() = currently logged in user
        // categories() = relation (User has many Categories)
        // paginate(10) = 10 items per page
        // Sirf logged-in user ki categories dikhao, paginate 10 per page
        $categories = auth()->user()->categories()->paginate(10);

                // 2. View ko data bhejo
        // compact('categories') = ['categories' => $categories]
        return view('categories.index', compact('categories'));
    }

       /**
     * GET|HEAD: /categories/create
     * Naya category banane ka FORM dikhao
     */

    public function create()
    {
             // Sirf form show karna hai, koi data nahi chahiye
        return view('categories.create');
    }
     
     /**
     * POST: /categories
     * Form submit hokar DATA SAVE karo
     */ 
    public function store(StoreCategoryRequest $request)
    {
                // STEP 1: Validation - StoreCategoryRequest ne kar diya
        
        // STEP 2: Data save karo
        // $request->validated() = sirf validated fields
        // auth()->user()->categories()->create() = current user ki category save karo
        // Form request validation already ho chuki hai
        auth()->user()->categories()->create($request->validated());


          // STEP 3: Flash message with redirect
        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');

             // DATA FLOW: Form → Request(Validation) → Controller → Model → Databas
    }

    
        /**
     * GET|HEAD: /categories/{category}/edit
     * Edit form dikhao
     * 
     * Route Model Binding: 
     * Category $category automatically database se fetch ho gaya
     */

    public function edit(Category $category)
    {
        // Route model binding automatically category find kar lega
        // Check karo ke ye category logged-in user ki hai
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }
        
        $category->update($request->validated());
        
        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }
        
        $category->delete();
        
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}