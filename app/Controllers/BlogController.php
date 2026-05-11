<?php

namespace App\Controllers;

use App\Models\BlogPostModel;

/**
 * BlogController
 * 
 * Handles blog display functionality for the public site.
 * Displays blog list with category filtering and blog detail pages.
 */
class BlogController extends BaseController
{
    protected BlogPostModel $blogPostModel;
    
    public function __construct()
    {
        $this->blogPostModel = new BlogPostModel();
    }
    
    /**
     * Display blog list page
     * 
     * Shows all published blog posts with optional category filtering.
     * Implements pagination with 12 posts per page.
     * 
     * @return string
     */
    public function index(): string
    {
        // Get current page from query string
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = max(1, $page); // Ensure page is at least 1
        
        // Get category filter from query string
        $categoryFilter = $this->request->getGet('category');
        
        // Items per page
        $perPage = 12;
        $offset = ($page - 1) * $perPage;
        
        // Get posts based on category filter
        if ($categoryFilter && in_array($categoryFilter, ['guides', 'tips_tricks', 'scam_alerts', 'news_updates', 'reviews'])) {
            $posts = $this->blogPostModel->getByCategory($categoryFilter, $perPage, $offset);
            $totalPosts = $this->blogPostModel->where('publication_status', 'published')
                                              ->where('category', $categoryFilter)
                                              ->countAllResults();
        } else {
            $posts = $this->blogPostModel->getPublished($perPage, $offset);
            $totalPosts = $this->blogPostModel->where('publication_status', 'published')
                                              ->countAllResults();
        }
        
        // Calculate pagination data
        $totalPages = (int) ceil($totalPosts / $perPage);
        
        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $totalPosts,
            'total_pages' => $totalPages,
        ];
        
        // Available categories for filter
        $categories = [
            'guides' => 'Guides',
            'tips_tricks' => 'Tips & Tricks',
            'scam_alerts' => 'Scam Alerts',
            'news_updates' => 'News & Updates',
            'reviews' => 'Reviews',
        ];
        
        $data = [
            'title' => 'Blog',
            'posts' => $posts,
            'pagination' => $pagination,
            'categories' => $categories,
            'currentCategory' => $categoryFilter,
        ];
        
        return view('blog/index', $data);
    }
    
    /**
     * Display blog detail page
     * 
     * Shows full article content with related articles.
     * Increments view count on article view.
     * 
     * @param string $slug Blog post slug
     * @return string
     */
    public function show(string $slug): string
    {
        // Find blog post by slug
        $post = $this->blogPostModel->findBySlug($slug);
        
        if (!$post || $post['publication_status'] !== 'published') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Blog post not found: ' . $slug
            );
        }
        
        // Increment view count
        $this->blogPostModel->incrementViewCount($post['id']);
        
        // Get post with author details
        $postWithAuthor = $this->blogPostModel->getWithAuthor($post['id']);
        
        // Get related articles (3-5 articles from same category)
        $relatedPosts = $this->blogPostModel->getRelated($post['id'], $post['category'], 5);
        
        // Format category name for display
        $categoryNames = [
            'guides' => 'Guides',
            'tips_tricks' => 'Tips & Tricks',
            'scam_alerts' => 'Scam Alerts',
            'news_updates' => 'News & Updates',
            'reviews' => 'Reviews',
        ];
        
        $data = [
            'title' => $post['title'],
            'post' => $postWithAuthor,
            'relatedPosts' => $relatedPosts,
            'categoryNames' => $categoryNames,
        ];
        
        return view('blog/show', $data);
    }
}

