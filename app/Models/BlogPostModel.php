<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlogPostModel
 * 
 * Manages blog posts with publication workflow.
 * 
 * Relationships:
 * - belongsTo: author (UserModel)
 */
class BlogPostModel extends Model
{
    protected $table            = 'blog_posts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'author_id',
        'category',
        'publication_status',
        'published_at',
        'view_count',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'title'     => 'required|max_length[255]',
        'slug'      => 'required|max_length[255]|alpha_dash|is_unique[blog_posts.slug,id,{id}]',
        'content'   => 'required',
        'author_id' => 'required|integer|is_not_unique[users.id]',
        'category'  => 'required|in_list[guides,tips_tricks,scam_alerts,news_updates,reviews]',
        'publication_status' => 'permit_empty|in_list[draft,published]',
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => 'Title is required',
            'max_length' => 'Title cannot exceed 255 characters',
        ],
        'slug' => [
            'required'   => 'Slug is required',
            'is_unique'  => 'Slug must be unique',
            'alpha_dash' => 'Slug can only contain alphanumeric characters, dashes, and underscores',
        ],
        'content' => [
            'required' => 'Content is required',
        ],
        'category' => [
            'required' => 'Category is required',
            'in_list'  => 'Invalid category selected',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Find blog post by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get published posts
     */
    public function getPublished(int $limit = 12, int $offset = 0): array
    {
        return $this->where('publication_status', 'published')
                    ->orderBy('published_at', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Get published posts by category
     */
    public function getByCategory(string $category, int $limit = 12, int $offset = 0): array
    {
        return $this->where('publication_status', 'published')
                    ->where('category', $category)
                    ->orderBy('published_at', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Get draft posts
     */
    public function getDrafts(int $limit = 20, int $offset = 0): array
    {
        return $this->where('publication_status', 'draft')
                    ->orderBy('updated_at', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Get posts by author
     */
    public function getByAuthor(int $authorId, int $limit = 20, int $offset = 0): array
    {
        return $this->where('author_id', $authorId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(int $postId): bool
    {
        return $this->set('view_count', 'view_count + 1', false)
                    ->where('id', $postId)
                    ->update();
    }

    /**
     * Publish post
     */
    public function publish(int $postId): bool
    {
        return $this->update($postId, [
            'publication_status' => 'published',
            'published_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Unpublish post (set to draft)
     */
    public function unpublish(int $postId): bool
    {
        return $this->update($postId, [
            'publication_status' => 'draft',
        ]);
    }

    /**
     * Get blog post with author details
     */
    public function getWithAuthor(int $postId): ?array
    {
        $db = \Config\Database::connect();
        
        return $db->table('blog_posts')
                  ->select('blog_posts.*, users.username as author_name, users.email as author_email')
                  ->join('users', 'users.id = blog_posts.author_id')
                  ->where('blog_posts.id', $postId)
                  ->get()
                  ->getRowArray();
    }

    /**
     * Get related posts (same category, excluding current post)
     */
    public function getRelated(int $postId, string $category, int $limit = 5): array
    {
        return $this->where('publication_status', 'published')
                    ->where('category', $category)
                    ->where('id !=', $postId)
                    ->orderBy('published_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
