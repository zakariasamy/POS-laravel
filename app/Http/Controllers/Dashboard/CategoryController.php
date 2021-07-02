<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CategoryCreateRequest;
use App\Http\Requests\Dashboard\CategoryUpdateRequest;

class CategoryController extends Controller
{
    public function __construct()
    {
        //make the function create only avalible for user has (user-create) permission handled by
        // laratrust package
        $this->middleware(['permission:categories-create'])->only('create');
        $this->middleware(['permission:categories-read'])->only('index');
        $this->middleware(['permission:categories-update'])->only('edit');
        $this->middleware(['permission:categories-delete'])->only('destroy');

    }

    public function index(Request $request)
    {

        // Get category & if the request has input called search get $q-> ...
        $categories = Category::when($request->search, function ($q) use ($request) {

            return $q->whereTranslationLike('name', '%' . $request->search . '%');
        })->latest()->paginate(5);

        return view('dashboard.categories.index', compact('categories'));
    }

    public function create()
    {

        return view('dashboard.categories.create');

    }

    public function store(CategoryCreateRequest $request)
    {

        Category::create($request->all());
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.categories.index');
    }

    public function edit(Category $category)
    {

        return view('dashboard.categories.edit', compact('category'));

    }

    public function update(CategoryUpdateRequest $request, Category $category)
    {

        $category->update($request->all());
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.categories.index');

    }

    public function destroy(Category $category)
    {
        $category->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.categories.index');
    } //end of destroy
}
