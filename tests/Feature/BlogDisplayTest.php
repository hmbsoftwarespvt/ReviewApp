<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestDataSeeder;

/**
 * BlogDisplayTest
 * 
 * Functional tests for Task 26: Public Site - Blog Display
 * 
 * Tests cover:
 * - Blog list displays all published posts
 * - Category filtering works correctly
 * - Blog detail shows full article content
 * - Related articles displayed (3-5 articles)
 * - View count increments on article view
 * - Pagination works (12 per page)
 */
class BlogDisplayTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;
    protected $seed        = TestDataSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test: Blog list shows all published posts
     * 
     * Acceptance Criteria: Blog list shows all published posts
     */
    public function testBlogListShowsPublishedPosts(): void
    {
        // Create test blog posts
        $blogPostModel = model('BlogPostModel');
        $userModel = model('UserModel');
        
        // Create a test user for author
        $userId = $userModel->insert([
            'username' => 'testauthor',
            'email' => 'author@test.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => true,
        ]);
        
        // Create published posts
        $publishedPosts = [];
        for ($i = 1; $i <= 5; $i++) {
            $publishedPosts[] = $blogPostModel->insert([
                'title' => "Published Post $i",
                'slug' => "published-post-$i",
                'content' => "This is the content of published post $i",
                'excerpt' => "Excerpt for post $i",
                'author_id' => $userId,
                'category' => 'guides',
                'publication_status' => 'published',
                'published_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        // Create draft posts (should not appear)
        for ($i = 1; $i <= 3; $i++) {
            $blogPostModel->insert([
                'title' => "Draft Post $i",
                'slug' => "draft-post-$i",
                'content' => "This is the content of draft post $i",
                'author_id' => $userId,
                'category' => 'guides',
                'publication_status' => 'draft',
            ]);
        }
        
        // Visit blog list page
        $result = $this->get('blog');
        
        // Assert response is successful
        $result->assertStatus(200);
        
        // Assert published posts are displayed
        foreach ($publishedPosts as $i => $postId) {
            $result->assertSee("Published Post " . ($i + 1));
        }
        
        // Assert draft posts are NOT displayed
        $result->assertDontSee('Draft Post 1');
        $result->assertDontSee('Draft Post 2');
        $result->assertDontSee('Draft Post 3');
    }

    /**
     * Test: Category filtering works
     * 
     * Acceptance Criteria: Category filtering works
     */
    public function testCategoryFilteringWorks(): void
    {
        $blogPostModel = model('BlogPostModel');
        $userModel = model('UserModel');
        
        // Create a test user
        $userId = $userModel->insert([
            'username' => 'testauthor2',
            'email' => 'author2@test.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => true,
        ]);
        
        // Create posts in different categories
        $blogPostModel->insert([
            'title' => 'Guide Post',
            'slug' => 'guide-post',
            'content' => 'Guide content',
            'author_id' => $userId,
            'category' => 'guides',
            'publication_status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);
        
        $blogPostModel->insert([
            'title' => 'Scam Alert Post',
            'slug' => 'scam-alert-post',
            'content' => 'Scam alert content',
            'author_id' => $userId,
            'category' => 'scam_alerts',
            'publication_status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);
        
        $blogPostModel->insert([
            'title' => 'Tips Post',
            'slug' => 'tips-post',
            'content' => 'Tips content',
            'author_id' => $userId,
            'category' => 'tips_tricks',
            'publication_status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Test filtering by 'guides' category
        $result = $this->get('blog?category=guides');
        $result->assertStatus(200);
        $result->assertSee('Guide Post');
        $result->assertDontSee('Scam Alert Post');
        $result->assertDontSee('Tips Post');
        
        // Test filtering by 'scam_alerts' category
        $result = $this->get('blog?category=scam_alerts');
        $result->assertStatus(200);
        $result->assertSee('Scam Alert Post');
        $result->assertDontSee('Guide Post');
        $result->assertDontSee('Tips Post');
        
        // Test filtering by 'tips_tricks' category
        $result = $this->get('blog?category=tips_tricks');
        $result->assertStatus(200);
        $result->assertSee('Tips Post');
        $result->assertDontSee('Guide Post');
        $result->assertDontSee('Scam Alert Post');
    }

    /**
     * Test: Blog detail shows full article content
     * 
     * Acceptance Criteria: Blog detail shows full article content
     */
    public function testBlogDetailShowsFullContent(): void
    {
        $blogPostModel = model('BlogPostModel');
        $userModel = model('UserModel');
        
        // Create a test user
        $userId = $userModel->insert([
            'username' => 'testauthor3',
            'email' => 'author3@test.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => true,
        ]);
        
        // Create a blog post
        $postId = $blogPostModel->insert([
            'title' => 'Test Article Title',
            'slug' => 'test-article-title',
            'content' => '<p>This is the full content of the test article. It contains multiple paragraphs and detailed information.</p><p>Second paragraph with more details.</p>',
            'excerpt' => 'This is a short excerpt',
            'author_id' => $userId,
            'category' => 'guides',
            'publication_status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Visit blog detail page
        $result = $this->get('blog/test-article-title');
        
        // Assert response is successful
        $result->assertStatus(200);
        
        // Assert title is displayed
        $result->assertSee('Test Article Title');
        
        // Assert full content is displayed
        $result->assertSee('This is the full content of the test article');
        $result->assertSee('Second paragraph with more details');
        
        // Assert excerpt is displayed
        $result->assertSee('This is a short excerpt');
        
        // Assert category is displayed
        $result->assertSee('Guides');
    }

    /**
     * Test: Related articles displayed (3-5 articles)
     * 
     * Acceptance Criteria: Related articles displayed (3-5 articles)
     */
    public function testRelatedArticlesDisplayed(): void
    {
        $blogPostModel = model('BlogPostModel');
        $userModel = model('UserModel');
        
        // Create a test user
        $userId = $userModel->insert([
            'username' => 'testauthor4',
            'email' => 'author4@test.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => true,
        ]);
        
        // Create main article
        $mainPostId = $blogPostModel->insert([
            'title' => 'Main Article',
            'slug' => 'main-article',
            'content' => 'Main article content',
            'author_id' => $userId,
            'category' => 'guides',
            'publication_status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Create related articles in same category
        for ($i = 1; $i <= 6; $i++) {
            $blogPostModel->insert([
                'title' => "Related Article $i",
                'slug' => "related-article-$i",
                'content' => "Related article $i content",
                'author_id' => $userId,
                'category' => 'guides',
                'publication_status' => 'published',
                'published_at' => date('Y-m-d H:i:s', strtotime("-$i days")),
            ]);
        }
        
        // Create articles in different category (should not appear as related)
        $blogPostModel->insert([
            'title' => 'Different Category Article',
            'slug' => 'different-category-article',
            'content' => 'Different category content',
            'author_id' => $userId,
            'category' => 'scam_alerts',
            'publication_status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Visit main article page
        $result = $this->get('blog/main-article');
        
        // Assert response is successful
        $result->assertStatus(200);
        
        // Assert related articles section exists
        $result->assertSee('Related Articles');
        
        // Assert at least 3 related articles are shown (up to 5)
        $relatedCount = 0;
        for ($i = 1; $i <= 6; $i++) {
            if (strpos($result->response()->getBody(), "Related Article $i") !== false) {
                $relatedCount++;
            }
        }
        
        $this->assertGreaterThanOrEqual(3, $relatedCount, 'Should display at least 3 related articles');
        $this->assertLessThanOrEqual(5, $relatedCount, 'Should display at most 5 related articles');
        
        // Assert different category article is NOT shown
        $result->assertDontSee('Different Category Article');
    }

    /**
     * Test: View count increments on article view
     * 
     * Acceptance Criteria: View count increments
     */
    public function testViewCountIncrements(): void
    {
        $blogPostModel = model('BlogPostModel');
        $userModel = model('UserModel');
        
        // Create a test user
        $userId = $userModel->insert([
            'username' => 'testauthor5',
            'email' => 'author5@test.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => true,
        ]);
        
        // Create a blog post
        $postId = $blogPostModel->insert([
            'title' => 'View Count Test Article',
            'slug' => 'view-count-test-article',
            'content' => 'View count test content',
            'author_id' => $userId,
            'category' => 'guides',
            'publication_status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
            'view_count' => 0,
        ]);
        
        // Get initial view count
        $post = $blogPostModel->find($postId);
        $initialViewCount = (int)$post['view_count'];
        
        // Visit the article
        $result = $this->get('blog/view-count-test-article');
        $result->assertStatus(200);
        
        // Get updated view count
        $post = $blogPostModel->find($postId);
        $updatedViewCount = (int)$post['view_count'];
        
        // Assert view count incremented by 1
        $this->assertEquals($initialViewCount + 1, $updatedViewCount, 'View count should increment by 1');
        
        // Visit again
        $result = $this->get('blog/view-count-test-article');
        $result->assertStatus(200);
        
        // Get view count after second visit
        $post = $blogPostModel->find($postId);
        $finalViewCount = (int)$post['view_count'];
        
        // Assert view count incremented again
        $this->assertEquals($updatedViewCount + 1, $finalViewCount, 'View count should increment on each visit');
    }

    /**
     * Test: Pagination works (12 per page)
     * 
     * Acceptance Criteria: Pagination works (12 per page)
     */
    public function testPaginationWorks(): void
    {
        $blogPostModel = model('BlogPostModel');
        $userModel = model('UserModel');
        
        // Create a test user
        $userId = $userModel->insert([
            'username' => 'testauthor6',
            'email' => 'author6@test.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => true,
        ]);
        
        // Create 25 blog posts (more than 2 pages)
        for ($i = 1; $i <= 25; $i++) {
            $blogPostModel->insert([
                'title' => "Pagination Test Post $i",
                'slug' => "pagination-test-post-$i",
                'content' => "Content for post $i",
                'author_id' => $userId,
                'category' => 'guides',
                'publication_status' => 'published',
                'published_at' => date('Y-m-d H:i:s', strtotime("-$i hours")),
            ]);
        }
        
        // Visit page 1
        $result = $this->get('blog?page=1');
        $result->assertStatus(200);
        
        // Count posts on page 1 (should be 12)
        $page1Count = 0;
        for ($i = 1; $i <= 12; $i++) {
            if (strpos($result->response()->getBody(), "Pagination Test Post $i") !== false) {
                $page1Count++;
            }
        }
        $this->assertEquals(12, $page1Count, 'Page 1 should display exactly 12 posts');
        
        // Assert post 13 is NOT on page 1
        $result->assertDontSee('Pagination Test Post 13');
        
        // Visit page 2
        $result = $this->get('blog?page=2');
        $result->assertStatus(200);
        
        // Count posts on page 2 (should be 12)
        $page2Count = 0;
        for ($i = 13; $i <= 24; $i++) {
            if (strpos($result->response()->getBody(), "Pagination Test Post $i") !== false) {
                $page2Count++;
            }
        }
        $this->assertEquals(12, $page2Count, 'Page 2 should display exactly 12 posts');
        
        // Visit page 3
        $result = $this->get('blog?page=3');
        $result->assertStatus(200);
        
        // Page 3 should have 1 post (25 total - 24 on first 2 pages)
        $result->assertSee('Pagination Test Post 25');
        
        // Assert pagination controls are present
        $result = $this->get('blog?page=1');
        $result->assertSee('Next');
        
        $result = $this->get('blog?page=2');
        $result->assertSee('Previous');
        $result->assertSee('Next');
        
        $result = $this->get('blog?page=3');
        $result->assertSee('Previous');
    }

    /**
     * Test: Draft posts are not accessible
     * 
     * Additional test to ensure draft posts cannot be viewed directly
     */
    public function testDraftPostsNotAccessible(): void
    {
        $blogPostModel = model('BlogPostModel');
        $userModel = model('UserModel');
        
        // Create a test user
        $userId = $userModel->insert([
            'username' => 'testauthor7',
            'email' => 'author7@test.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => true,
        ]);
        
        // Create a draft post
        $postId = $blogPostModel->insert([
            'title' => 'Draft Post',
            'slug' => 'draft-post-test',
            'content' => 'Draft content',
            'author_id' => $userId,
            'category' => 'guides',
            'publication_status' => 'draft',
        ]);
        
        // Try to access draft post directly
        $result = $this->get('blog/draft-post-test');
        
        // Assert 404 response
        $result->assertStatus(404);
    }

    /**
     * Test: Invalid blog post slug returns 404
     */
    public function testInvalidSlugReturns404(): void
    {
        // Try to access non-existent blog post
        $result = $this->get('blog/non-existent-post-slug');
        
        // Assert 404 response
        $result->assertStatus(404);
    }
}

