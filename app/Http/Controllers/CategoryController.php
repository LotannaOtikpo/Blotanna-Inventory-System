<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        $category = Category::create($request->only('name'));

        // Log Activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created Category',
            'description' => "Added new category: {$category->name}",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Category created successfully.');
    }
}