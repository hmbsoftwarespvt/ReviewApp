<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\AppRepository;
use App\Models\CategoryModel;
use App\Models\ScreenshotModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * AppManagementController
 * 
 * Admin interface for managing app entries.
 * 
 * Features:
 * - CRUD operations for apps
 * - App list with pagination and search
 * - Approval/rejection workflow
 * - Screenshot upload (max 10 per app)
 * - Cascade deletion of associated data
 */
class AppManagementController extends BaseController
{
    protected AppRepository $appRepository;
    protected CategoryModel $categoryModel;
    protected ScreenshotModel $screenshotModel;
    
    public function __construct()
    {
        $this->appRepository = new AppRepository();
        $this->categoryModel = new CategoryModel();
        $this->screenshotModel = new ScreenshotModel();
    }
    
    /**
     * Display app list with pagination and search
     * 
     * @return string
     */
    public function index(): string
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $search = $this->request->getGet('search') ?? '';
        $status = $this->request->getGet('status') ?? '';
        
        $filters = [];
        
        if (!empty($status)) {
            $filters['approval_status'] = $status;
        }
        
        // Search by name or developer
        if (!empty($search)) {
            $result = $this->appRepository->search($search, $filters, $page, 20);
        } else {
            $result = $this->appRepository->getAll($filters, $page, 20);
        }
        
        $data = [
            'title' => 'App Management',
            'apps' => $result['data'],
            'pagination' => $result['pagination'],
            'search' => $search,
            'status' => $status,
        ];
        
        return view('admin/apps/index', $data);
    }
    
    /**
     * Show create app form
     * 
     * @return string
     */
    public function create(): string
    {
        $categories = $this->categoryModel->getAllOrdered();
        
        $data = [
            'title' => 'Create App',
            'categories' => $categories,
            'app' => null,
            'errors' => session('errors') ?? [],
            'old' => session('old') ?? [],
        ];
        
        return view('admin/apps/form', $data);
    }
    
    /**
     * Store new app
     * 
     * @return RedirectResponse
     */
    public function store(): RedirectResponse
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|max_length[255]',
            'slug' => 'required|max_length[255]|alpha_dash|is_unique[apps.slug]',
            'description' => 'permit_empty',
            'version' => 'permit_empty|max_length[50]',
            'size' => 'permit_empty|max_length[50]',
            'platform_type' => 'required|in_list[android,ios,web,desktop]',
            'price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'developer_name' => 'required|max_length[255]',
            'release_date' => 'permit_empty|valid_date',
            'download_url' => 'permit_empty|valid_url|max_length[500]',
            'approval_status' => 'permit_empty|in_list[pending,approved,rejected]',
            'categories' => 'permit_empty',
            'permissions' => 'permit_empty',
            'has_encryption' => 'permit_empty|in_list[0,1]',
            'third_party_sdk_count' => 'permit_empty|integer|greater_than_equal_to[0]',
            'screenshots.*' => 'permit_empty|uploaded[screenshots]|max_size[screenshots,2048]|is_image[screenshots]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
        
        // Prepare app data
        $appData = [
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
            'version' => $this->request->getPost('version'),
            'size' => $this->request->getPost('size'),
            'platform_type' => $this->request->getPost('platform_type'),
            'price' => $this->request->getPost('price') ?? 0.00,
            'developer_name' => $this->request->getPost('developer_name'),
            'release_date' => $this->request->getPost('release_date'),
            'download_url' => $this->request->getPost('download_url'),
            'approval_status' => $this->request->getPost('approval_status') ?? 'pending',
            'has_encryption' => $this->request->getPost('has_encryption') ?? 0,
            'third_party_sdk_count' => $this->request->getPost('third_party_sdk_count') ?? 0,
        ];
        
        // Handle permissions (JSON)
        $permissions = $this->request->getPost('permissions');
        if (!empty($permissions)) {
            $appData['permissions'] = json_encode(array_map('trim', explode(',', $permissions)));
        }
        
        // Handle categories
        $categories = $this->request->getPost('categories') ?? [];
        if (!empty($categories)) {
            $appData['categories'] = $categories;
        }
        
        // Create app
        $appId = $this->appRepository->create($appData);
        
        if (!$appId) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create app');
        }
        
        // Handle screenshot uploads
        $this->handleScreenshotUploads($appId);
        
        return redirect()->to(base_url('admin/apps'))
                        ->with('success', 'App created successfully');
    }
    
    /**
     * Show edit app form
     * 
     * @param int $id
     * @return string|RedirectResponse
     */
    public function edit(int $id)
    {
        $app = $this->appRepository->getWithDetails($id);
        
        if (!$app) {
            return redirect()->to(base_url('admin/apps'))
                           ->with('error', 'App not found');
        }
        
        $categories = $this->categoryModel->getAllOrdered();
        
        // Get selected category IDs
        $selectedCategories = array_column($app['categories'], 'id');
        
        // Decode permissions JSON
        if (!empty($app['permissions'])) {
            $app['permissions'] = implode(', ', json_decode($app['permissions'], true));
        }
        
        $data = [
            'title' => 'Edit App',
            'app' => $app,
            'categories' => $categories,
            'selectedCategories' => $selectedCategories,
            'errors' => session('errors') ?? [],
            'old' => session('old') ?? [],
        ];
        
        return view('admin/apps/form', $data);
    }
    
    /**
     * Update app
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function update(int $id): RedirectResponse
    {
        $app = $this->appRepository->find($id);
        
        if (!$app) {
            return redirect()->to(base_url('admin/apps'))
                           ->with('error', 'App not found');
        }
        
        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|max_length[255]',
            'slug' => "required|max_length[255]|alpha_dash|is_unique[apps.slug,id,{$id}]",
            'description' => 'permit_empty',
            'version' => 'permit_empty|max_length[50]',
            'size' => 'permit_empty|max_length[50]',
            'platform_type' => 'required|in_list[android,ios,web,desktop]',
            'price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'developer_name' => 'required|max_length[255]',
            'release_date' => 'permit_empty|valid_date',
            'download_url' => 'permit_empty|valid_url|max_length[500]',
            'approval_status' => 'permit_empty|in_list[pending,approved,rejected]',
            'categories' => 'permit_empty',
            'permissions' => 'permit_empty',
            'has_encryption' => 'permit_empty|in_list[0,1]',
            'third_party_sdk_count' => 'permit_empty|integer|greater_than_equal_to[0]',
            'screenshots.*' => 'permit_empty|uploaded[screenshots]|max_size[screenshots,2048]|is_image[screenshots]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
        
        // Prepare app data
        $appData = [
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
            'version' => $this->request->getPost('version'),
            'size' => $this->request->getPost('size'),
            'platform_type' => $this->request->getPost('platform_type'),
            'price' => $this->request->getPost('price') ?? 0.00,
            'developer_name' => $this->request->getPost('developer_name'),
            'release_date' => $this->request->getPost('release_date'),
            'download_url' => $this->request->getPost('download_url'),
            'approval_status' => $this->request->getPost('approval_status') ?? 'pending',
            'has_encryption' => $this->request->getPost('has_encryption') ?? 0,
            'third_party_sdk_count' => $this->request->getPost('third_party_sdk_count') ?? 0,
        ];
        
        // Handle permissions (JSON)
        $permissions = $this->request->getPost('permissions');
        if (!empty($permissions)) {
            $appData['permissions'] = json_encode(array_map('trim', explode(',', $permissions)));
        }
        
        // Handle categories
        $categories = $this->request->getPost('categories') ?? [];
        $appData['categories'] = $categories;
        
        // Update app
        $result = $this->appRepository->update($id, $appData);
        
        if (!$result) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update app');
        }
        
        // Handle screenshot uploads
        $this->handleScreenshotUploads($id);
        
        // Handle screenshot deletions
        $deleteScreenshots = $this->request->getPost('delete_screenshots') ?? [];
        if (!empty($deleteScreenshots)) {
            foreach ($deleteScreenshots as $screenshotId) {
                $this->deleteScreenshot($screenshotId);
            }
        }
        
        return redirect()->to(base_url('admin/apps'))
                        ->with('success', 'App updated successfully');
    }
    
    /**
     * Delete app (with cascade)
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        $app = $this->appRepository->find($id);
        
        if (!$app) {
            return redirect()->to(base_url('admin/apps'))
                           ->with('error', 'App not found');
        }
        
        // Delete screenshots from filesystem
        $screenshots = $this->screenshotModel->getByApp($id);
        foreach ($screenshots as $screenshot) {
            $this->deleteScreenshotFile($screenshot['file_path']);
        }
        
        // Delete app (cascade will handle reviews, scam reports, screenshots, categories)
        $result = $this->appRepository->delete($id);
        
        if (!$result) {
            return redirect()->to(base_url('admin/apps'))
                           ->with('error', 'Failed to delete app');
        }
        
        return redirect()->to(base_url('admin/apps'))
                        ->with('success', 'App and all associated data deleted successfully');
    }
    
    /**
     * Approve app
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function approve(int $id): RedirectResponse
    {
        $app = $this->appRepository->find($id);
        
        if (!$app) {
            return redirect()->to(base_url('admin/apps'))
                           ->with('error', 'App not found');
        }
        
        $result = $this->appRepository->update($id, ['approval_status' => 'approved']);
        
        if (!$result) {
            return redirect()->back()
                           ->with('error', 'Failed to approve app');
        }
        
        return redirect()->back()
                        ->with('success', 'App approved successfully');
    }
    
    /**
     * Reject app
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function reject(int $id): RedirectResponse
    {
        $app = $this->appRepository->find($id);
        
        if (!$app) {
            return redirect()->to(base_url('admin/apps'))
                           ->with('error', 'App not found');
        }
        
        $result = $this->appRepository->update($id, ['approval_status' => 'rejected']);
        
        if (!$result) {
            return redirect()->back()
                           ->with('error', 'Failed to reject app');
        }
        
        return redirect()->back()
                        ->with('success', 'App rejected successfully');
    }
    
    /**
     * Handle screenshot uploads (max 10 per app)
     * 
     * @param int $appId
     * @return void
     */
    protected function handleScreenshotUploads(int $appId): void
    {
        $files = $this->request->getFiles();
        
        if (empty($files['screenshots'])) {
            return;
        }
        
        // Check current screenshot count
        $currentCount = $this->screenshotModel->getCountByApp($appId);
        
        $uploadedCount = 0;
        
        foreach ($files['screenshots'] as $file) {
            if (!$file->isValid()) {
                continue;
            }
            
            // Enforce max 10 screenshots per app
            if ($currentCount + $uploadedCount >= 10) {
                break;
            }
            
            // Generate unique filename
            $filename = $file->getRandomName();
            
            // Move file to uploads directory
            $uploadPath = WRITEPATH . 'uploads/screenshots/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $file->move($uploadPath, $filename);
            
            // Save screenshot record
            $this->screenshotModel->insert([
                'app_id' => $appId,
                'filename' => $filename,
                'file_path' => 'uploads/screenshots/' . $filename,
                'display_order' => $currentCount + $uploadedCount,
            ]);
            
            $uploadedCount++;
        }
    }
    
    /**
     * Delete screenshot
     * 
     * @param int $screenshotId
     * @return void
     */
    protected function deleteScreenshot(int $screenshotId): void
    {
        $screenshot = $this->screenshotModel->find($screenshotId);
        
        if (!$screenshot) {
            return;
        }
        
        // Delete file from filesystem
        $this->deleteScreenshotFile($screenshot['file_path']);
        
        // Delete record
        $this->screenshotModel->delete($screenshotId);
    }
    
    /**
     * Delete screenshot file from filesystem
     * 
     * @param string $filePath
     * @return void
     */
    protected function deleteScreenshotFile(string $filePath): void
    {
        $fullPath = WRITEPATH . $filePath;
        
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
