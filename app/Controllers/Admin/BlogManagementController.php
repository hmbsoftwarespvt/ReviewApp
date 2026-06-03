<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BlogPostModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * BlogManagementController
 * 
 * Admin interface for managing blog posts.
 * 
 * Features:
 * - CRUD operations for blog posts
 * - Blog post list with pagination
 * - Rich text editor for content
 * - Draft/published status management
 * - Category selection (Guides, Tips & Tricks, Scam Alerts, News & Updates, Reviews)
 * - Featured image upload
 */
class BlogManagementController extends BaseController
{
    protected BlogPostModel $blogPostModel;
    protected UserModel $userModel;
    
    public function __construct()
    {
        $this->blogPostModel = new BlogPostModel();
        $this->userModel = new UserModel();
    }
    
    /**
     * Display blog post list with pagination
     * 
     * @return string
     */
    public function index(): string
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $status = $this->request->getGet('status') ?? '';
        $category = $this->request->getGet('category') ?? '';
        
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        // Build query
        $builder = $this->blogPostModel->builder();
        $builder->select('blog_posts.*, users.username as author_name')
                ->join('users', 'users.id = blog_posts.author_id');
        
        // Apply filters
        if (!empty($status)) {
            $builder->where('blog_posts.publication_status', $status);
        }
        
        if (!empty($category)) {
            $builder->where('blog_posts.category', $category);
        }
        
        // Get total count for pagination
        $totalCount = $builder->countAllResults(false);
        
        // Get paginated results
        $posts = $builder->orderBy('blog_posts.updated_at', 'DESC')
                        ->limit($perPage, $offset)
                        ->get()
                        ->getResultArray();
        
        // Calculate pagination
        $totalPages = ceil($totalCount / $perPage);
        
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
            'total_items' => $totalCount,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
        ];
        
        $data = [
            'title' => 'Blog Management',
            'posts' => $posts,
            'pagination' => $pagination,
            'status' => $status,
            'category' => $category,
            'categories' => $this->getCategories(),
        ];
        
        return view('admin/blog/index', $data);
    }
    
    /**
     * Show create blog post form
     * 
     * @return string
     */
    public function create(): string
    {
        $data = [
            'title' => 'Create Blog Post',
            'post' => null,
            'categories' => $this->getCategories(),
            'errors' => session('errors') ?? [],
            'old' => session('old') ?? [],
        ];
        
        return view('admin/blog/form', $data);
    }
    
    /**
     * Store new blog post
     * 
     * @return RedirectResponse
     */
    public function store(): RedirectResponse
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'title' => 'required|max_length[255]',
            'slug' => 'required|max_length[255]|alpha_dash|is_unique[blog_posts.slug]',
            'content' => 'required',
            'excerpt' => 'permit_empty',
            'category' => 'required|in_list[guides,tips_tricks,scam_alerts,news_updates,reviews]',
            'publication_status' => 'permit_empty|in_list[draft,published]',
            'featured_image' => 'permit_empty|uploaded[featured_image]|max_size[featured_image,2048]|is_image[featured_image]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
        
        // Get current user ID (author)
        $session = session();
        $authorId = $session->get('user_id');
        
        if (!$authorId) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'User not authenticated');
        }
        
        // Prepare blog post data
        $postData = [
            'title' => $this->request->getPost('title'),
            'slug' => $this->request->getPost('slug'),
            'content' => $this->request->getPost('content'),
            'excerpt' => $this->request->getPost('excerpt'),
            'author_id' => $authorId,
            'category' => $this->request->getPost('category'),
            'publication_status' => $this->request->getPost('publication_status') ?? 'draft',
        ];
        
        // Set published_at if status is published
        if ($postData['publication_status'] === 'published') {
            $postData['published_at'] = date('Y-m-d H:i:s');
        }
        
        // Handle featured image upload
        $featuredImage = $this->request->getFile('featured_image');
        if ($featuredImage && $featuredImage->isValid()) {
            $filename = $featuredImage->getRandomName();
            $uploadPath = FCPATH . 'uploads/blog/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $featuredImage->move($uploadPath, $filename);
            $postData['featured_image'] = 'uploads/blog/' . $filename;
        }
        
        // Create blog post
        $postId = $this->blogPostModel->insert($postData);
        
        if (!$postId) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create blog post');
        }
        
        return redirect()->to(base_url('admin/blog'))
                        ->with('success', 'Blog post created successfully');
    }
    
    /**
     * Show edit blog post form
     * 
     * @param int $id
     * @return string|RedirectResponse
     */
    public function edit(int $id)
    {
        $post = $this->blogPostModel->getWithAuthor($id);
        
        if (!$post) {
            return redirect()->to(base_url('admin/blog'))
                           ->with('error', 'Blog post not found');
        }
        
        $data = [
            'title' => 'Edit Blog Post',
            'post' => $post,
            'categories' => $this->getCategories(),
            'errors' => session('errors') ?? [],
            'old' => session('old') ?? [],
        ];
        
        return view('admin/blog/form', $data);
    }
    
    /**
     * Update blog post
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function update(int $id): RedirectResponse
    {
        $post = $this->blogPostModel->find($id);
        
        if (!$post) {
            return redirect()->to(base_url('admin/blog'))
                           ->with('error', 'Blog post not found');
        }
        
        $validation = \Config\Services::validation();
        
        $rules = [
            'title' => 'required|max_length[255]',
            'slug' => "required|max_length[255]|alpha_dash|is_unique[blog_posts.slug,id,{$id}]",
            'content' => 'required',
            'excerpt' => 'permit_empty',
            'category' => 'required|in_list[guides,tips_tricks,scam_alerts,news_updates,reviews]',
            'publication_status' => 'permit_empty|in_list[draft,published]',
            'featured_image' => 'permit_empty|uploaded[featured_image]|max_size[featured_image,2048]|is_image[featured_image]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
        
        // Prepare blog post data
        $postData = [
            'title' => $this->request->getPost('title'),
            'slug' => $this->request->getPost('slug'),
            'content' => $this->request->getPost('content'),
            'excerpt' => $this->request->getPost('excerpt'),
            'category' => $this->request->getPost('category'),
            'publication_status' => $this->request->getPost('publication_status') ?? 'draft',
        ];
        
        // Set published_at if status changed to published
        if ($postData['publication_status'] === 'published' && $post['publication_status'] !== 'published') {
            $postData['published_at'] = date('Y-m-d H:i:s');
        }
        
        // Handle featured image upload
        $featuredImage = $this->request->getFile('featured_image');
        if ($featuredImage && $featuredImage->isValid()) {
            // Delete old featured image if exists
            if (!empty($post['featured_image'])) {
                $this->deleteFeaturedImage($post['featured_image']);
            }
            
            $filename = $featuredImage->getRandomName();
            $uploadPath = FCPATH . 'uploads/blog/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $featuredImage->move($uploadPath, $filename);
            $postData['featured_image'] = 'uploads/blog/' . $filename;
        }
        
        // Handle featured image deletion
        if ($this->request->getPost('delete_featured_image') && !empty($post['featured_image'])) {
            $this->deleteFeaturedImage($post['featured_image']);
            $postData['featured_image'] = null;
        }
        
        // Update blog post
        $result = $this->blogPostModel->update($id, $postData);
        
        if (!$result) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update blog post');
        }
        
        return redirect()->to(base_url('admin/blog'))
                        ->with('success', 'Blog post updated successfully');
    }
    
    /**
     * Delete blog post
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        $post = $this->blogPostModel->find($id);
        
        if (!$post) {
            return redirect()->to(base_url('admin/blog'))
                           ->with('error', 'Blog post not found');
        }
        
        // Delete featured image if exists
        if (!empty($post['featured_image'])) {
            $this->deleteFeaturedImage($post['featured_image']);
        }
        
        // Delete blog post
        $result = $this->blogPostModel->delete($id);
        
        if (!$result) {
            return redirect()->to(base_url('admin/blog'))
                           ->with('error', 'Failed to delete blog post');
        }
        
        return redirect()->to(base_url('admin/blog'))
                        ->with('success', 'Blog post deleted successfully');
    }
    
    /**
     * Publish blog post
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function publish(int $id): RedirectResponse
    {
        $post = $this->blogPostModel->find($id);
        
        if (!$post) {
            return redirect()->to(base_url('admin/blog'))
                           ->with('error', 'Blog post not found');
        }
        
        $result = $this->blogPostModel->publish($id);
        
        if (!$result) {
            return redirect()->back()
                           ->with('error', 'Failed to publish blog post');
        }
        
        return redirect()->back()
                        ->with('success', 'Blog post published successfully');
    }
    
    /**
     * Unpublish blog post (set to draft)
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function unpublish(int $id): RedirectResponse
    {
        $post = $this->blogPostModel->find($id);
        
        if (!$post) {
            return redirect()->to(base_url('admin/blog'))
                           ->with('error', 'Blog post not found');
        }
        
        $result = $this->blogPostModel->unpublish($id);
        
        if (!$result) {
            return redirect()->back()
                           ->with('error', 'Failed to unpublish blog post');
        }
        
        return redirect()->back()
                        ->with('success', 'Blog post set to draft successfully');
    }
    
    /**
     * Get blog categories
     * 
     * @return array
     */
    protected function getCategories(): array
    {
        return [
            'guides' => 'Guides',
            'tips_tricks' => 'Tips & Tricks',
            'scam_alerts' => 'Scam Alerts',
            'news_updates' => 'News & Updates',
            'reviews' => 'Reviews',
        ];
    }
    
    /**
     * Delete featured image from filesystem
     * 
     * @param string $filePath
     * @return void
     */
    protected function deleteFeaturedImage(string $filePath): void
    {
        $fullPath = FCPATH . $filePath;
        
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
