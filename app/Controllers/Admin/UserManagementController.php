<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ReviewModel;
use App\Models\ScamReportModel;

/**
 * UserManagementController
 * 
 * Admin interface for managing user accounts.
 * 
 * Features:
 * - View all users with pagination
 * - Search users by username/email
 * - View user details with statistics
 * - Suspend/reactivate user accounts
 * - Delete user accounts with content anonymization
 */
class UserManagementController extends BaseController
{
    protected UserModel $userModel;
    protected ReviewModel $reviewModel;
    protected ScamReportModel $scamReportModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->reviewModel = new ReviewModel();
        $this->scamReportModel = new ScamReportModel();
    }
    
    /**
     * Display user list with search and pagination
     * 
     * @return string
     */
    public function index(): string
    {
        // Get search parameter from query string
        $search = $this->request->getGet('search') ?? '';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;
        
        // Build query based on search
        $users = $this->getFilteredUsers($search, $page, $perPage);
        
        $data = [
            'title' => 'User Management',
            'users' => $users['data'],
            'pagination' => $users['pagination'],
            'search' => $search,
        ];
        
        return view('admin/users/index', $data);
    }
    
    /**
     * Display user detail view with statistics
     * 
     * @param int $id
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function view(int $id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User not found.');
        }
        
        // Get user statistics
        $reviewCount = $this->reviewModel->where('user_id', $id)->countAllResults();
        $scamReportCount = $this->scamReportModel->where('user_id', $id)->countAllResults();
        
        // Get recent reviews
        $recentReviews = $this->reviewModel
            ->select('reviews.*, apps.name as app_name, apps.slug as app_slug')
            ->join('apps', 'apps.id = reviews.app_id')
            ->where('reviews.user_id', $id)
            ->orderBy('reviews.created_at', 'DESC')
            ->limit(10)
            ->findAll();
        
        // Get recent scam reports
        $recentScamReports = $this->scamReportModel
            ->select('scam_reports.*, apps.name as app_name, apps.slug as app_slug')
            ->join('apps', 'apps.id = scam_reports.app_id')
            ->where('scam_reports.user_id', $id)
            ->orderBy('scam_reports.created_at', 'DESC')
            ->limit(10)
            ->findAll();
        
        $data = [
            'title' => 'User Details - ' . esc($user['username']),
            'user' => $user,
            'reviewCount' => $reviewCount,
            'scamReportCount' => $scamReportCount,
            'recentReviews' => $recentReviews,
            'recentScamReports' => $recentScamReports,
        ];
        
        return view('admin/users/view', $data);
    }
    
    /**
     * Suspend a user account
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function suspend(int $id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
        
        // Prevent suspending admin users
        if ($user['role'] === 'admin') {
            return redirect()->back()->with('error', 'Cannot suspend admin users.');
        }
        
        // Update user status to suspended
        $success = $this->userModel->update($id, ['status' => 'suspended']);
        
        if ($success) {
            return redirect()->back()->with('success', 'User suspended successfully. They will not be able to login.');
        }
        
        return redirect()->back()->with('error', 'Failed to suspend user.');
    }
    
    /**
     * Reactivate a suspended user account
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function reactivate(int $id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
        
        // Update user status to active
        $success = $this->userModel->update($id, ['status' => 'active']);
        
        if ($success) {
            return redirect()->back()->with('success', 'User reactivated successfully. They can now login.');
        }
        
        return redirect()->back()->with('error', 'Failed to reactivate user.');
    }
    
    /**
     * Manually verify a user's email
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function verify(int $id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if ($user['email_verified']) {
            return redirect()->back()->with('info', 'Email is already verified for this user.');
        }

        $success = $this->userModel->update($id, [
            'email_verified'     => true,
            'verification_token' => null,
        ]);

        if ($success) {
            return redirect()->back()->with('success', 'Email verified successfully for ' . esc($user['username']) . '.');
        }

        return redirect()->back()->with('error', 'Failed to verify email.');
    }

    /**
     * Delete a user account and anonymize their content
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete(int $id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
        
        // Prevent deleting admin users
        if ($user['role'] === 'admin') {
            return redirect()->back()->with('error', 'Cannot delete admin users.');
        }
        
        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Anonymize reviews - update username reference to "Deleted User"
            $this->reviewModel->where('user_id', $id)
                             ->set(['user_id' => null])
                             ->update();
            
            // Anonymize scam reports - update username reference to "Deleted User"
            $this->scamReportModel->where('user_id', $id)
                                 ->set(['user_id' => null])
                                 ->update();
            
            // Delete the user account
            $this->userModel->delete($id);
            
            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Failed to delete user. Transaction failed.');
            }
            
            return redirect()->to(base_url('admin/users'))->with('success', 'User deleted successfully. Their content has been anonymized.');
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'User deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
    
    /**
     * Get filtered users based on search criteria
     * 
     * @param string $search
     * @param int $page
     * @param int $perPage
     * @return array
     */
    protected function getFilteredUsers(string $search, int $page, int $perPage): array
    {
        $builder = $this->userModel->builder();
        
        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('username', $search)
                   ->orLike('email', $search)
                   ->groupEnd();
        }
        
        // Get total count for pagination
        $total = $builder->countAllResults(false);
        
        // Apply pagination
        $offset = ($page - 1) * $perPage;
        $builder->orderBy('created_at', 'DESC')
               ->limit($perPage, $offset);
        
        $users = $builder->get()->getResultArray();
        
        // Get review and scam report counts for each user
        foreach ($users as &$user) {
            $user['review_count'] = $this->reviewModel->where('user_id', $user['id'])->countAllResults();
            $user['scam_report_count'] = $this->scamReportModel->where('user_id', $user['id'])->countAllResults();
        }
        
        return [
            'data' => $users,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }
}

