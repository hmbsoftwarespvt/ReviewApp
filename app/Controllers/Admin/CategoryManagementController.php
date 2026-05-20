<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CategoryManagementController
 * 
 * Admin interface for managing app categories.
 * 
 * Features:
 * - CRUD operations for categories
 * - Category list with pagination
 * - Auto-generate slug from name
 */
class CategoryManagementController extends BaseController
{
    protected CategoryModel $categoryModel;
    
    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }
    
    /**
     * Display category list
     * 
     * @return string
     */
    public function index(): string
    {
        $categories = $this->categoryModel->getAllOrdered();
        
        // Get app counts for each category
        foreach ($categories as &$category) {
            $category['app_count'] = $this->categoryModel->getAppCount($category['id']);
        }
        
        $data = [
            'title' => 'Category Management',
            'categories' => $categories,
        ];
        
        return view('admin/categories/index', $data);
    }
    
    /**
     * Show create category form
     * 
     * @return string
     */
    public function create(): string
    {
        $data = [
            'title' => 'Create Category',
            'category' => null,
            'errors' => session('errors') ?? [],
            'old' => session('old') ?? [],
        ];
        
        return view('admin/categories/form', $data);
    }
    
    /**
     * Store new category
     * 
     * @return RedirectResponse
     */
    public function store(): RedirectResponse
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|max_length[100]|is_unique[categories.name]',
            'slug' => 'required|max_length[100]|alpha_dash|is_unique[categories.slug]',
            'description' => 'permit_empty',
            'icon' => 'permit_empty|max_length[100]',
            'display_order' => 'permit_empty|integer',
        ];
        
        // Auto-generate slug from name if not provided
        $slug = $this->request->getPost('slug');
        $name = $this->request->getPost('name');
        
        if (empty($slug) && !empty($name)) {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            $slug = preg_replace('/^-+|-+$/', '', $slug);
            $_POST['slug'] = $slug;
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
        
        // Prepare category data
        $categoryData = [
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
            'icon' => $this->request->getPost('icon'),
            'display_order' => $this->request->getPost('display_order') ?? 0,
        ];
        
        // Create category
        $categoryId = $this->categoryModel->insert($categoryData);
        
        if (!$categoryId) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create category');
        }
        
        return redirect()->to(base_url('admin/categories'))
                        ->with('success', 'Category created successfully');
    }
    
    /**
     * Show edit category form
     * 
     * @param int $id
     * @return string|RedirectResponse
     */
    public function edit(int $id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to(base_url('admin/categories'))
                           ->with('error', 'Category not found');
        }
        
        $data = [
            'title' => 'Edit Category',
            'category' => $category,
            'errors' => session('errors') ?? [],
            'old' => session('old') ?? [],
        ];
        
        return view('admin/categories/form', $data);
    }
    
    /**
     * Update category
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function update(int $id): RedirectResponse
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to(base_url('admin/categories'))
                           ->with('error', 'Category not found');
        }
        
        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => "required|max_length[100]|is_unique[categories.name,id,{$id}]",
            'description' => 'permit_empty',
            'icon' => 'permit_empty|max_length[100]',
            'display_order' => 'permit_empty|integer',
        ];
        
        // Auto-generate slug from name if name changed and slug not provided
        $slug = $this->request->getPost('slug');
        $name = $this->request->getPost('name');
        $oldName = $category['name'] ?? '';
        
        // If name changed and slug is empty, auto-generate slug
        if ($name !== $oldName && empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            $slug = preg_replace('/^-+|-+$/', '', $slug);
            $_POST['slug'] = $slug;
        }
        
        // If slug is provided, validate it; otherwise keep existing slug
        if (empty($slug)) {
            $slug = $category['slug'];
            $_POST['slug'] = $slug;
        }
        
        // Only validate slug if it's provided (not empty)
        if (!empty($this->request->getPost('slug'))) {
            $rules['slug'] = "max_length[100]|alpha_dash|is_unique[categories.slug,id,{$id}]";
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
        
        // Prepare category data
        $categoryData = [
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
            'icon' => $this->request->getPost('icon'),
            'display_order' => $this->request->getPost('display_order') ?? 0,
        ];
        
        // Update category
        $result = $this->categoryModel->update($id, $categoryData);
        
        if (!$result) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update category');
        }
        
        return redirect()->to(base_url('admin/categories'))
                        ->with('success', 'Category updated successfully');
    }
    
    /**
     * Delete category
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to(base_url('admin/categories'))
                           ->with('error', 'Category not found');
        }
        
        // Check if category has apps
        $appCount = $this->categoryModel->getAppCount($id);
        
        if ($appCount > 0) {
            return redirect()->back()
                           ->with('error', "Cannot delete category. It has {$appCount} apps assigned to it.");
        }
        
        // Delete category
        $result = $this->categoryModel->delete($id);
        
        if (!$result) {
            return redirect()->to(base_url('admin/categories'))
                           ->with('error', 'Failed to delete category');
        }
        
        return redirect()->to(base_url('admin/categories'))
                        ->with('success', 'Category deleted successfully');
    }
}
