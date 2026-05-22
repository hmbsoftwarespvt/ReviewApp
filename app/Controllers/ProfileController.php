<?php

namespace App\Controllers;

use App\Repositories\ReviewRepository;
use App\Repositories\ScamReportRepository;
use App\Models\UserModel;

class ProfileController extends BaseController
{
    protected ReviewRepository $reviewRepository;
    protected ScamReportRepository $scamReportRepository;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->reviewRepository = new ReviewRepository();
        $this->scamReportRepository = new ScamReportRepository();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'You must be logged in to view your profile.');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/')->with('error', 'User not found.');
        }

        $reviews = $this->reviewRepository->getByUser($userId);
        $scamReports = $this->scamReportRepository->getByUser($userId);

        $data = [
            'title' => 'My Profile',
            'user' => $user,
            'reviews' => $reviews,
            'scam_reports' => $scamReports,
        ];

        return view('user/profile', $data);
    }
}
