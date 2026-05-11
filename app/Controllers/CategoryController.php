<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Repositories\AppRepository;

/**
 * CategoryController
 * 
 * Handles category browsing functionality for the public site.
 * Displays category list and category detail pages with apps.
 */
class CategoryController extends BaseController
{
    protected CategoryModel $categoryModel;
    protected AppRepository $appRepository;
    
    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->appRepository = new AppRepository();
    }
    
    /**
     * Display category list page
     * 
     * Shows all categories with icons and app counts.
     * 
     * @return string
     */
    public function index(): string
    {
        // Get all categories with app counts
        $categories = $this->categoryModel->getAllWithAppCounts();
        
        $data = [
            'title' => 'Browse Categories',
            'categories' => $categories,
        ];
        
        return view('categories/index', $data);
    }
    
    /**
     * Display category detail page with apps
     * 
     * Shows all apps in the category, sorted by trust score (descending).
     * Implements pagination with 24 apps per page.
     * 
     * @param string $slug Category slug
     * @return string
     */
    public function show(string $slug): string
    {
        // Find category by slug
        $category = $this->categoryModel->findBySlug($slug);
        
        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Category not found: ' . $slug
            );
        }
        
        // Get current page from query string
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = max(1, $page); // Ensure page is at least 1
        
        // Get apps in category with pagination (24 per page)
        $result = $this->appRepository->getByCategory($category['id'], $page, 24);
        
        $data = [
            'title' => $category['name'],
            'category' => $category,
            'apps' => $result['data'],
            'pagination' => $result['pagination'],
        ];
        
        return view('categories/show', $data);
    }
}

