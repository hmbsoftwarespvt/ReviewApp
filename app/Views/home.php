
<?php
// Fallback for optional variables
$scam_reports = isset($scam_reports) ? $scam_reports : [];
$recent_reviews = isset($recent_reviews) ? $recent_reviews : [];
$trending_apps = isset($trending_apps) ? $trending_apps : [];
$categories = isset($categories) ? $categories : [];
$statistics = isset($statistics) ? $statistics : [];

// Featured app: first trending app or placeholder
$featured_app = !empty($trending_apps) ? $trending_apps[0] : null;

// Trust score helper
function getTrustMeta($score) {
    $score = (float)$score;
    if ($score >= 80) return ['color' => '#10B981', 'label' => 'Great', 'bg' => '#D1FAE5'];
    elseif ($score >= 60) return ['color' => '#F59E0B', 'label' => 'Fair', 'bg' => '#FEF3C7'];
    else return ['color' => '#EF4444', 'label' => 'Poor', 'bg' => '#FEE2E2'];
}

$isLoggedIn = session()->get('isLoggedIn');
?>
<?= $this->extend('base_template') ?>
<?= $this->section('content') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AppTrust – Check If An App Is Real or Scam</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ===== BASE ===== */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1a1a2e;
            background: #fff;
            margin: 0;
        }
        a { text-decoration: none; }

        /* ===== NAVBAR ===== */
        .at-navbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .at-navbar .navbar-brand {
            font-weight: 800;
            font-size: 1.35rem;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .at-navbar .navbar-brand .brand-icon {
            color: #2563EB;
            font-size: 1.5rem;
        }
        .at-navbar .nav-link {
            color: #4B5563;
            font-weight: 500;
            font-size: 0.92rem;
            padding: 0.4rem 0.75rem;
            border-bottom: 2px solid transparent;
            transition: color 0.2s, border-color 0.2s;
        }
        .at-navbar .nav-link:hover { color: #2563EB; }
        .at-navbar .nav-link.active {
            color: #2563EB;
            border-bottom-color: #2563EB;
        }
        .at-navbar .nav-search {
            background: #F3F4F6;
            border: none;
            border-radius: 20px;
            padding: 0.4rem 1rem;
            font-size: 0.88rem;
            width: 180px;
            outline: none;
        }
        .at-navbar .nav-search:focus { background: #E5E7EB; }
        .btn-nav-login {
            border: 1.5px solid #2563EB;
            color: #2563EB;
            background: transparent;
            border-radius: 8px;
            padding: 0.38rem 1rem;
            font-size: 0.88rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-nav-login:hover { background: #EFF6FF; }
        .btn-nav-signup {
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.38rem 1rem;
            font-size: 0.88rem;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-nav-signup:hover { background: #1D4ED8; color: #fff; }

        /* ===== HERO ===== */
        .at-hero {
            background: #F8FAFC;
            padding: 4rem 0 3.5rem;
        }
        .at-hero h1 {
            font-size: 2.6rem;
            font-weight: 800;
            line-height: 1.2;
            color: #111827;
        }
        .at-hero h1 .highlight { color: #2563EB; }
        .at-hero .hero-sub {
            color: #6B7280;
            font-size: 1.05rem;
            margin: 1rem 0 1.75rem;
            max-width: 480px;
        }
        .btn-hero-primary {
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.7rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-hero-primary:hover { background: #1D4ED8; color: #fff; }
        .btn-hero-outline {
            background: #fff;
            color: #374151;
            border: 1.5px solid #D1D5DB;
            border-radius: 10px;
            padding: 0.7rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-hero-outline:hover { border-color: #2563EB; color: #2563EB; }
        .hero-social-proof {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
        .avatar-stack { display: flex; }
        .avatar-stack img, .avatar-stack .av {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: 2px solid #fff;
            margin-left: -8px;
            object-fit: cover;
            background: #DBEAFE;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; color: #2563EB;
        }
        .avatar-stack .av:first-child { margin-left: 0; }
        .social-proof-text { font-size: 0.85rem; color: #6B7280; font-weight: 500; }
        .social-proof-text strong { color: #111827; }

        /* Featured App Card */
        .featured-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 40px rgba(37,99,235,0.10), 0 2px 8px rgba(0,0,0,0.06);
            padding: 1.75rem;
            max-width: 380px;
            margin: 0 auto;
        }
        .featured-card .app-header {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .featured-card .app-icon {
            width: 56px; height: 56px;
            border-radius: 14px;
            background: #00C4A1;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            color: #fff;
            font-weight: 800;
            flex-shrink: 0;
        }
        .featured-card .app-name { font-size: 1.2rem; font-weight: 700; color: #111827; }
        .badge-trusted {
            background: #D1FAE5;
            color: #065F46;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 20px;
            padding: 0.2rem 0.6rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        .featured-card .app-tagline { color: #6B7280; font-size: 0.88rem; margin-bottom: 1.1rem; }
        .trust-score-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #F0FDF4;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }
        .trust-score-row .score-num {
            font-size: 1.8rem;
            font-weight: 800;
            color: #10B981;
            line-height: 1;
        }
        .trust-score-row .score-label { font-size: 0.8rem; color: #6B7280; }
        .trust-score-row .score-great { font-size: 0.85rem; font-weight: 700; color: #10B981; }
        .featured-meta { display: flex; flex-direction: column; gap: 0.45rem; margin-bottom: 1rem; }
        .featured-meta .meta-row {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.88rem; color: #374151;
        }
        .featured-meta .meta-row i { color: #6B7280; }
        .featured-meta .meta-row .val-green { color: #10B981; font-weight: 600; }
        .featured-footer {
            border-top: 1px solid #F3F4F6;
            padding-top: 0.85rem;
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            color: #9CA3AF;
            margin-bottom: 1rem;
        }
        .featured-footer span strong { color: #374151; }
        .btn-view-review {
            display: block;
            width: 100%;
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.65rem;
            font-size: 0.92rem;
            font-weight: 600;
            text-align: center;
            transition: background 0.2s;
        }
        .btn-view-review:hover { background: #1D4ED8; color: #fff; }

        /* ===== SEARCH SECTION ===== */
        .at-search-section {
            background: #fff;
            padding: 3rem 0 2.5rem;
            border-bottom: 1px solid #F3F4F6;
        }
        .at-search-section h2 {
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1.5rem;
            color: #111827;
        }
        .search-bar-wrap {
            display: flex;
            max-width: 680px;
            margin: 0 auto;
            border: 2px solid #E5E7EB;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .search-bar-wrap input {
            flex: 1;
            border: none;
            outline: none;
            padding: 0.85rem 1.2rem;
            font-size: 0.95rem;
            color: #374151;
        }
        .search-bar-wrap select {
            border: none;
            border-left: 1px solid #E5E7EB;
            outline: none;
            padding: 0.85rem 0.75rem;
            font-size: 0.88rem;
            color: #6B7280;
            background: #F9FAFB;
            cursor: pointer;
        }
        .search-bar-wrap .btn-search {
            background: #2563EB;
            color: #fff;
            border: none;
            padding: 0.85rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .search-bar-wrap .btn-search:hover { background: #1D4ED8; }
        .popular-searches {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.88rem;
            color: #6B7280;
        }
        .popular-searches .pill {
            display: inline-block;
            background: #F3F4F6;
            color: #374151;
            border-radius: 20px;
            padding: 0.25rem 0.75rem;
            margin: 0.2rem 0.15rem;
            font-size: 0.82rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .popular-searches .pill:hover { background: #DBEAFE; color: #2563EB; }

        /* ===== TRENDING + SCAM ===== */
        .at-trending-section {
            background: #F8FAFC;
            padding: 3rem 0;
        }
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }
        .section-header h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        .section-header a {
            font-size: 0.88rem;
            color: #2563EB;
            font-weight: 600;
        }
        .section-header a:hover { text-decoration: underline; }

        /* Trending App Card */
        .trending-app-card {
            background: #fff;
            border-radius: 14px;
            padding: 0.9rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            transition: box-shadow 0.2s;
        }
        .trending-app-card:hover { box-shadow: 0 4px 16px rgba(37,99,235,0.10); }
        .trending-app-card .t-icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }
        .trending-app-card .t-info { flex: 1; min-width: 0; }
        .trending-app-card .t-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .trending-app-card .t-cat {
            font-size: 0.75rem;
            color: #9CA3AF;
            margin-top: 0.1rem;
        }
        .trending-app-card .t-score {
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 20px;
            padding: 0.2rem 0.6rem;
            white-space: nowrap;
        }
        .trending-app-card .t-stars {
            font-size: 0.78rem;
            color: #F59E0B;
            white-space: nowrap;
        }

        /* Scam Alerts */
        .scam-alerts-box {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .scam-alerts-header {
            background: #FEF2F2;
            padding: 0.85rem 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #FECACA;
        }
        .scam-alerts-header h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #991B1B;
            margin: 0;
        }
        .scam-alerts-header a { font-size: 0.82rem; color: #DC2626; font-weight: 600; }
        .scam-alert-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.9rem 1.1rem;
            border-bottom: 1px solid #F9FAFB;
        }
        .scam-alert-item:last-of-type { border-bottom: none; }
        .scam-alert-item .sa-icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .scam-alert-item .sa-info .sa-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: #111827;
        }
        .scam-alert-item .sa-info .sa-desc {
            font-size: 0.8rem;
            color: #6B7280;
            margin-top: 0.1rem;
        }
        .scam-alert-item .sa-time {
            font-size: 0.75rem;
            color: #9CA3AF;
            margin-left: auto;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .scam-alerts-footer {
            padding: 0.85rem 1.1rem;
            border-top: 1px solid #F3F4F6;
        }
        .btn-report-scam {
            display: block;
            width: 100%;
            border: 1.5px solid #DC2626;
            color: #DC2626;
            background: transparent;
            border-radius: 8px;
            padding: 0.5rem;
            font-size: 0.88rem;
            font-weight: 600;
            text-align: center;
            transition: all 0.2s;
        }
        .btn-report-scam:hover { background: #FEF2F2; }

        /* ===== CATEGORIES ===== */
        .at-categories-section {
            background: #fff;
            padding: 3rem 0;
        }
        .at-categories-section h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
        }
        .category-icon-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.6rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .category-icon-card:hover { transform: translateY(-3px); }
        .category-icon-card .cat-circle {
            width: 64px; height: 64px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            transition: box-shadow 0.2s;
        }
        .category-icon-card:hover .cat-circle {
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        .category-icon-card .cat-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #374151;
            text-align: center;
        }

        /* ===== STATS BAR ===== */
        .at-stats-bar {
            background: linear-gradient(135deg, #EFF6FF 0%, #F5F3FF 100%);
            padding: 2.5rem 0;
        }
        .stat-item {
            text-align: center;
            padding: 1rem;
        }
        .stat-item .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .stat-item .stat-num {
            font-size: 1.8rem;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }
        .stat-item .stat-label {
            font-size: 0.85rem;
            color: #6B7280;
            margin-top: 0.25rem;
        }

        /* ===== REVIEWS ===== */
        .at-reviews-section {
            background: #fff;
            padding: 3rem 0;
        }
        .at-reviews-section h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
        }
        .review-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 16px;
            padding: 1.25rem;
            height: 100%;
            transition: box-shadow 0.2s;
        }
        .review-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .review-card .rc-app-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .review-card .rc-app-icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }
        .review-card .rc-app-name { font-size: 0.95rem; font-weight: 700; color: #111827; }
        .review-card .rc-stars { font-size: 0.82rem; color: #F59E0B; }
        .review-card .rc-text {
            font-size: 0.88rem;
            color: #4B5563;
            line-height: 1.55;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .review-card .rc-reviewer {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border-top: 1px solid #F3F4F6;
            padding-top: 0.75rem;
        }
        .review-card .rc-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #DBEAFE;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #2563EB;
            flex-shrink: 0;
        }
        .review-card .rc-reviewer-name { font-size: 0.82rem; font-weight: 600; color: #374151; }
        .review-card .rc-time { font-size: 0.75rem; color: #9CA3AF; margin-left: auto; }

        /* Slider arrows */
        .slider-nav {
            display: flex;
            gap: 0.5rem;
        }
        .slider-nav button {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 1.5px solid #E5E7EB;
            background: #fff;
            color: #374151;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .slider-nav button:hover { border-color: #2563EB; color: #2563EB; }

        /* ===== NEWSLETTER ===== */
        .at-newsletter {
            background: linear-gradient(135deg, #2563EB 0%, #7C3AED 100%);
            padding: 3rem 0;
        }
        .at-newsletter h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .at-newsletter p { color: rgba(255,255,255,0.8); font-size: 0.95rem; margin: 0; }
        .newsletter-form {
            display: flex;
            gap: 0.5rem;
            max-width: 420px;
        }
        .newsletter-form input {
            flex: 1;
            border: none;
            border-radius: 10px;
            padding: 0.7rem 1rem;
            font-size: 0.92rem;
            outline: none;
        }
        .newsletter-form button {
            background: #fff;
            color: #2563EB;
            border: none;
            border-radius: 10px;
            padding: 0.7rem 1.25rem;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .newsletter-form button:hover { background: #EFF6FF; }

        /* ===== FOOTER ===== */
        .at-footer {
            background: #1a1a2e;
            color: #9CA3AF;
            padding: 3rem 0 0;
        }
        .at-footer .footer-brand {
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.75rem;
        }
        .at-footer .footer-brand i { color: #2563EB; }
        .at-footer .footer-tagline { font-size: 0.85rem; color: #6B7280; max-width: 220px; }
        .at-footer h6 {
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .at-footer ul { list-style: none; padding: 0; margin: 0; }
        .at-footer ul li { margin-bottom: 0.5rem; }
        .at-footer ul li a {
            color: #9CA3AF;
            font-size: 0.88rem;
            transition: color 0.2s;
        }
        .at-footer ul li a:hover { color: #fff; }
        .social-icons { display: flex; gap: 0.6rem; margin-top: 0.5rem; }
        .social-icons a {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: #2d2d4e;
            display: flex; align-items: center; justify-content: center;
            color: #9CA3AF;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .social-icons a:hover { background: #2563EB; color: #fff; }
        .footer-bottom {
            border-top: 1px solid #2d2d4e;
            padding: 1.25rem 0;
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.82rem;
            color: #6B7280;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #9CA3AF;
            font-size: 0.9rem;
        }
        .empty-state i { font-size: 2rem; display: block; margin-bottom: 0.5rem; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991px) {
            .at-hero h1 { font-size: 2rem; }
            .featured-card { margin-top: 2rem; }
        }
        @media (max-width: 767px) {
            .at-hero { padding: 2.5rem 0; }
            .at-hero h1 { font-size: 1.7rem; }
            .newsletter-form { flex-direction: column; }
            .search-bar-wrap { flex-wrap: wrap; }
            .search-bar-wrap select { border-left: none; border-top: 1px solid #E5E7EB; width: 100%; }
            .search-bar-wrap .btn-search { width: 100%; }
        }
    </style>
</head>
<body>

<!-- ===== HERO SECTION ===== -->
<section class="at-hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <!-- Left Column -->
            <div class="col-lg-6">
                <h1>Check If An App Is Real or Scam Before <span class="highlight">Using It</span></h1>
                <p class="hero-sub">Find trusted apps, honest reviews and real user experiences from Pakistan.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= base_url('search') ?>" class="btn-hero-primary">
                        <i class="bi bi-search me-1"></i> Search Apps
                    </a>
                    <a href="<?= base_url('trending') ?>" class="btn-hero-outline">
                        Explore Trending
                    </a>
                </div>
                <div class="hero-social-proof">
                    <div class="avatar-stack">
                        <div class="av">AK</div>
                        <div class="av">MR</div>
                        <div class="av">SB</div>
                        <div class="av">ZN</div>
                    </div>
                    <span class="social-proof-text"><strong>Trusted by 50,000+</strong> users in Pakistan</span>
                </div>
            </div>

            <!-- Right Column: Featured App Card -->
            <div class="col-lg-6">
                <?php if ($featured_app): ?>
                <?php
                    $fScore = (float)($featured_app['trust_score'] ?? 88);
                    $fMeta = getTrustMeta($fScore);
                    $fColors = ['#00C4A1','#2563EB','#7C3AED','#EF4444','#F59E0B','#10B981'];
                    $fColor = $fColors[0];
                    $fInitial = strtoupper(substr($featured_app['name'] ?? 'A', 0, 1));
                ?>
                <div class="featured-card">
                    <div class="app-header">
                        <div class="app-icon" style="background:<?= $fColor ?>;">
                            <?= esc($fInitial) ?>
                        </div>
                        <div>
                            <div class="app-name"><?= esc($featured_app['name'] ?? 'Canva') ?></div>
                            <span class="badge-trusted"><i class="bi bi-check-circle-fill"></i> Trusted</span>
                        </div>
                    </div>
                    <p class="app-tagline"><?= esc(substr($featured_app['description'] ?? 'Design anything. Publish anywhere.', 0, 60)) ?></p>
                    <div class="trust-score-row">
                        <i class="bi bi-shield-fill-check" style="font-size:2rem;color:#10B981;"></i>
                        <div>
                            <div class="score-num" style="color:<?= $fMeta['color'] ?>;"><?= esc($fScore) ?>/100</div>
                            <div class="score-label">Trust Score</div>
                        </div>
                        <div class="ms-auto">
                            <div class="score-great" style="color:<?= $fMeta['color'] ?>;"><?= $fMeta['label'] ?></div>
                        </div>
                    </div>
                    <div class="featured-meta">
                        <div class="meta-row">
                            <i class="bi bi-star-fill" style="color:#F59E0B;"></i>
                            <span>User Rating: <strong>4.5/5</strong></span>
                            <span style="color:#9CA3AF;">(2.4K reviews)</span>
                        </div>
                        <div class="meta-row">
                            <i class="bi bi-check-circle-fill" style="color:#10B981;"></i>
                            <span>Scam Reports: <span class="val-green">✓ Low</span></span>
                        </div>
                    </div>
                    <div class="featured-footer">
                        <span>Category: <strong>Design</strong></span>
                        <span>Downloads: <strong>10M+</strong></span>
                        <span>Updated: <strong>2 days ago</strong></span>
                    </div>
                    <a href="<?= base_url('apps/' . esc($featured_app['slug'] ?? '')) ?>" class="btn-view-review">
                        View Full Review
                    </a>
                </div>
                <?php else: ?>
                <div class="featured-card">
                    <div class="app-header">
                        <div class="app-icon" style="background:#00C4A1;">C</div>
                        <div>
                            <div class="app-name">Canva</div>
                            <span class="badge-trusted"><i class="bi bi-check-circle-fill"></i> Trusted</span>
                        </div>
                    </div>
                    <p class="app-tagline">Design anything. Publish anywhere.</p>
                    <div class="trust-score-row">
                        <i class="bi bi-shield-fill-check" style="font-size:2rem;color:#10B981;"></i>
                        <div>
                            <div class="score-num">88/100</div>
                            <div class="score-label">Trust Score</div>
                        </div>
                        <div class="ms-auto">
                            <div class="score-great">Great</div>
                        </div>
                    </div>
                    <div class="featured-meta">
                        <div class="meta-row">
                            <i class="bi bi-star-fill" style="color:#F59E0B;"></i>
                            <span>User Rating: <strong>4.5/5</strong></span>
                            <span style="color:#9CA3AF;">(2.4K reviews)</span>
                        </div>
                        <div class="meta-row">
                            <i class="bi bi-check-circle-fill" style="color:#10B981;"></i>
                            <span>Scam Reports: <span class="val-green">✓ Low</span></span>
                        </div>
                    </div>
                    <div class="featured-footer">
                        <span>Category: <strong>Design</strong></span>
                        <span>Downloads: <strong>10M+</strong></span>
                        <span>Updated: <strong>2 days ago</strong></span>
                    </div>
                    <a href="<?= base_url('search') ?>" class="btn-view-review">View Full Review</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ===== SEARCH BAR SECTION ===== -->
<section class="at-search-section">
    <div class="container">
        <h2>Find Any App. Know the Truth.</h2>
        <form action="<?= base_url('search') ?>" method="GET">
            <div class="search-bar-wrap">
                <input type="text" name="q" placeholder="Search apps, websites, earning platforms...">
                <select name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat['slug'] ?? '') ?>"><?= esc($cat['name'] ?? '') ?></option>
                    <?php endforeach; ?>
                    <?php if (empty($categories)): ?>
                        <option value="earning">Earning Apps</option>
                        <option value="ai-tools">AI Tools</option>
                        <option value="finance">Finance</option>
                        <option value="shopping">Shopping</option>
                        <option value="crypto">Crypto</option>
                    <?php endif; ?>
                </select>
                <button type="submit" class="btn-search">
                    <i class="bi bi-search me-1"></i> Search
                </button>
            </div>
        </form>
        <div class="popular-searches mt-3">
            <span class="me-1">Popular searches:</span>
            <?php
            $popularSearches = ['JazzCash','Easypaisa','CapCut','Temu','Binance','Upwork','Daraz'];
            foreach ($popularSearches as $ps):
            ?>
            <a href="<?= base_url('search?q=' . urlencode($ps)) ?>" class="pill"><?= esc($ps) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== TRENDING APPS + SCAM ALERTS ===== -->
<section class="at-trending-section">
    <div class="container">
        <div class="row g-4">
            <!-- Left: Trending Apps -->
            <div class="col-lg-8">
                <div class="section-header">
                    <h3>🔥 Trending Apps</h3>
                    <a href="<?= base_url('trending') ?>">View All →</a>
                </div>

                <?php if (!empty($trending_apps)): ?>
                <?php
                $iconColors = ['#00C4A1','#2563EB','#10B981','#F59E0B','#EF4444','#7C3AED','#EC4899','#0EA5E9'];
                $colorIdx = 0;
                foreach ($trending_apps as $app):
                    $score = (float)($app['trust_score'] ?? 0);
                    $meta = getTrustMeta($score);
                    $initial = strtoupper(substr($app['name'] ?? 'A', 0, 1));
                    $iconBg = $iconColors[$colorIdx % count($iconColors)];
                    $colorIdx++;
                    // Star rating: derive from trust score for display
                    $stars = round(($score / 100) * 5, 1);
                    $starsDisplay = '';
                    for ($s = 1; $s <= 5; $s++) {
                        if ($s <= floor($stars)) $starsDisplay .= '★';
                        elseif ($s - 0.5 <= $stars) $starsDisplay .= '½';
                        else $starsDisplay .= '☆';
                    }
                ?>
                <a href="<?= base_url('apps/' . esc($app['slug'] ?? '')) ?>" class="trending-app-card text-decoration-none">
                    <div class="t-icon" style="background:<?= $iconBg ?>;">
                        <?php if (!empty($app['thumbnail'])): ?>
                            <img src="<?= esc($app['thumbnail']) ?>" alt="<?= esc($app['name'] ?? '') ?>" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
                        <?php else: ?>
                            <?= esc($initial) ?>
                        <?php endif; ?>
                    </div>
                    <div class="t-info">
                        <div class="t-name"><?= esc($app['name'] ?? '') ?></div>
                        <div class="t-cat">
                            <i class="bi bi-eye me-1"></i><?= number_format((int)($app['view_count'] ?? 0)) ?> views
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <span class="t-score" style="background:<?= $meta['bg'] ?>;color:<?= $meta['color'] ?>;">
                            <?= esc($score) ?>/100
                        </span>
                        <span class="t-stars"><?= $starsDisplay ?></span>
                    </div>
                </a>
                <?php endforeach; ?>

                <?php else: ?>
                <!-- Placeholder trending apps -->
                <?php
                $placeholderApps = [
                    ['name'=>'Canva','score'=>88,'cat'=>'Design','stars'=>'★★★★½','color'=>'#00C4A1'],
                    ['name'=>'CapCut','score'=>84,'cat'=>'Video Editing','stars'=>'★★★★☆','color'=>'#111827'],
                    ['name'=>'Easypaisa','score'=>81,'cat'=>'Finance','stars'=>'★★★★☆','color'=>'#10B981'],
                    ['name'=>'Temu','score'=>59,'cat'=>'Shopping','stars'=>'★★★☆☆','color'=>'#F59E0B'],
                    ['name'=>'QuickEarn','score'=>22,'cat'=>'Earning','stars'=>'★☆☆☆☆','color'=>'#EF4444'],
                ];
                foreach ($placeholderApps as $pa):
                    $pMeta = getTrustMeta($pa['score']);
                ?>
                <div class="trending-app-card">
                    <div class="t-icon" style="background:<?= $pa['color'] ?>;">
                        <?= strtoupper(substr($pa['name'],0,1)) ?>
                    </div>
                    <div class="t-info">
                        <div class="t-name"><?= $pa['name'] ?></div>
                        <div class="t-cat"><?= $pa['cat'] ?></div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <span class="t-score" style="background:<?= $pMeta['bg'] ?>;color:<?= $pMeta['color'] ?>;">
                            <?= $pa['score'] ?>/100
                        </span>
                        <span class="t-stars"><?= $pa['stars'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Right: Scam Alerts -->
            <div class="col-lg-4">
                <div class="scam-alerts-box">
                    <div class="scam-alerts-header">
                        <h3><i class="bi bi-exclamation-triangle-fill me-1"></i> Scam Alerts</h3>
                        <a href="<?= base_url('scam-alerts') ?>">View All →</a>
                    </div>

                    <?php if (!empty($scam_reports)): ?>
                    <?php
                    $alertColors = ['#FEE2E2','#FEF3C7','#FFF7ED'];
                    $alertIconColors = ['#DC2626','#D97706','#F59E0B'];
                    $ai = 0;
                    foreach (array_slice($scam_reports, 0, 3) as $report):
                        $bgC = $alertColors[$ai % 3];
                        $iconC = $alertIconColors[$ai % 3];
                        $ai++;
                    ?>
                    <div class="scam-alert-item">
                        <div class="sa-icon" style="background:<?= $bgC ?>;">
                            <i class="bi bi-exclamation-triangle-fill" style="color:<?= $iconC ?>;"></i>
                        </div>
                        <div class="sa-info">
                            <div class="sa-name"><?= esc($report['app_name'] ?? $report['name'] ?? 'Unknown App') ?></div>
                            <div class="sa-desc"><?= esc(substr($report['description'] ?? $report['reason'] ?? 'Reported as suspicious', 0, 50)) ?></div>
                        </div>
                        <div class="sa-time"><?= esc($report['time_ago'] ?? $report['created_at'] ?? '') ?></div>
                    </div>
                    <?php endforeach; ?>

                    <?php else: ?>
                    <!-- Placeholder scam alerts -->
                    <div class="scam-alert-item">
                        <div class="sa-icon" style="background:#FEE2E2;">
                            <i class="bi bi-exclamation-triangle-fill" style="color:#DC2626;"></i>
                        </div>
                        <div class="sa-info">
                            <div class="sa-name">FakeReward App</div>
                            <div class="sa-desc">Users reported no payouts</div>
                        </div>
                        <div class="sa-time">2 hours ago</div>
                    </div>
                    <div class="scam-alert-item">
                        <div class="sa-icon" style="background:#FFF7ED;">
                            <i class="bi bi-exclamation-triangle-fill" style="color:#D97706;"></i>
                        </div>
                        <div class="sa-info">
                            <div class="sa-name">CashWin Rewards</div>
                            <div class="sa-desc">Possible scam detected</div>
                        </div>
                        <div class="sa-time">1 day ago</div>
                    </div>
                    <div class="scam-alert-item">
                        <div class="sa-icon" style="background:#FEF3C7;">
                            <i class="bi bi-exclamation-triangle-fill" style="color:#F59E0B;"></i>
                        </div>
                        <div class="sa-info">
                            <div class="sa-name">Gold Mine Earning</div>
                            <div class="sa-desc">High risk – Avoid using</div>
                        </div>
                        <div class="sa-time">2 days ago</div>
                    </div>
                    <?php endif; ?>

                    <div class="scam-alerts-footer">
                        <a href="<?= base_url('scam-alerts/report') ?>" class="btn-report-scam">
                            <i class="bi bi-megaphone-fill me-1"></i> 🚨 Report a Scam
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== BROWSE CATEGORIES ===== -->
<section class="at-categories-section">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="mb-0">Browse Categories</h2>
            <a href="<?= base_url('categories') ?>" style="font-size:0.9rem;color:#2563EB;font-weight:600;">View All →</a>
        </div>

        <?php
        // Default category icons mapping
        $defaultCategories = [
            ['name'=>'Earning Apps',   'slug'=>'earning-apps',   'icon'=>'bi-cash-coin',         'color'=>'#10B981','bg'=>'#D1FAE5'],
            ['name'=>'AI Tools',       'slug'=>'ai-tools',       'icon'=>'bi-robot',              'color'=>'#2563EB','bg'=>'#DBEAFE'],
            ['name'=>'Student Apps',   'slug'=>'student-apps',   'icon'=>'bi-mortarboard',        'color'=>'#7C3AED','bg'=>'#EDE9FE'],
            ['name'=>'Video Editing',  'slug'=>'video-editing',  'icon'=>'bi-camera-video',       'color'=>'#EF4444','bg'=>'#FEE2E2'],
            ['name'=>'Finance',        'slug'=>'finance',        'icon'=>'bi-graph-up',           'color'=>'#10B981','bg'=>'#D1FAE5'],
            ['name'=>'Shopping',       'slug'=>'shopping',       'icon'=>'bi-cart3',              'color'=>'#F97316','bg'=>'#FFEDD5'],
            ['name'=>'Crypto',         'slug'=>'crypto',         'icon'=>'bi-currency-bitcoin',   'color'=>'#F59E0B','bg'=>'#FEF3C7'],
            ['name'=>'More',           'slug'=>'',               'icon'=>'bi-grid',               'color'=>'#6B7280','bg'=>'#F3F4F6'],
        ];

        $catIconMap = [
            'earning' => ['icon'=>'bi-cash-coin',       'color'=>'#10B981','bg'=>'#D1FAE5'],
            'ai'      => ['icon'=>'bi-robot',            'color'=>'#2563EB','bg'=>'#DBEAFE'],
            'student' => ['icon'=>'bi-mortarboard',      'color'=>'#7C3AED','bg'=>'#EDE9FE'],
            'video'   => ['icon'=>'bi-camera-video',     'color'=>'#EF4444','bg'=>'#FEE2E2'],
            'finance' => ['icon'=>'bi-graph-up',         'color'=>'#10B981','bg'=>'#D1FAE5'],
            'shop'    => ['icon'=>'bi-cart3',            'color'=>'#F97316','bg'=>'#FFEDD5'],
            'crypto'  => ['icon'=>'bi-currency-bitcoin', 'color'=>'#F59E0B','bg'=>'#FEF3C7'],
        ];

        $displayCats = !empty($categories) ? array_slice($categories, 0, 7) : [];
        // Merge with defaults to always show 8
        $catCount = count($displayCats);
        ?>

        <div class="row row-cols-4 row-cols-sm-4 row-cols-md-8 g-3 justify-content-center">
            <?php if (!empty($displayCats)): ?>
            <?php foreach ($displayCats as $idx => $cat):
                $slug = strtolower($cat['slug'] ?? '');
                $catStyle = ['icon'=>'bi-grid','color'=>'#6B7280','bg'=>'#F3F4F6'];
                foreach ($catIconMap as $key => $style) {
                    if (strpos($slug, $key) !== false) { $catStyle = $style; break; }
                }
            ?>
            <div class="col">
                <a href="<?= base_url($cat['slug'] ? 'categories/' . esc($cat['slug']) : 'categories') ?>" class="category-icon-card text-decoration-none">
                    <div class="cat-circle" style="background:<?= $catStyle['bg'] ?>;">
                        <i class="bi <?= $catStyle['icon'] ?>" style="color:<?= $catStyle['color'] ?>;"></i>
                    </div>
                    <span class="cat-label"><?= esc($cat['name'] ?? '') ?></span>
                </a>
            </div>
            <?php endforeach; ?>
            <!-- Fill remaining with defaults if needed -->
            <?php for ($i = $catCount; $i < 8; $i++):
                $dc = $defaultCategories[$i];
            ?>
            <div class="col">
                <a href="<?= base_url($dc['slug'] ? 'categories/' . $dc['slug'] : 'categories') ?>" class="category-icon-card text-decoration-none">
                    <div class="cat-circle" style="background:<?= $dc['bg'] ?>;">
                        <i class="bi <?= $dc['icon'] ?>" style="color:<?= $dc['color'] ?>;"></i>
                    </div>
                    <span class="cat-label"><?= $dc['name'] ?></span>
                </a>
            </div>
            <?php endfor; ?>

            <?php else: ?>
            <!-- All default categories -->
            <?php foreach ($defaultCategories as $dc): ?>
            <div class="col">
                <a href="<?= base_url($dc['slug'] ? 'categories/' . $dc['slug'] : 'categories') ?>" class="category-icon-card text-decoration-none">
                    <div class="cat-circle" style="background:<?= $dc['bg'] ?>;">
                        <i class="bi <?= $dc['icon'] ?>" style="color:<?= $dc['color'] ?>;"></i>
                    </div>
                    <span class="cat-label"><?= $dc['name'] ?></span>
                </a>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===== STATS BAR ===== -->
<section class="at-stats-bar">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-people-fill" style="color:#2563EB;"></i></div>
                    <div class="stat-num">
                        <?php
                        $totalUsers = isset($statistics['total_users']) ? $statistics['total_users'] : 50000;
                        echo $totalUsers >= 1000 ? round($totalUsers/1000) . 'K+' : $totalUsers . '+';
                        ?>
                    </div>
                    <div class="stat-label">Happy Users</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-check-circle-fill" style="color:#2563EB;"></i></div>
                    <div class="stat-num">
                        <?php
                        $totalApps = isset($statistics['total_apps']) ? $statistics['total_apps'] : 2000;
                        echo $totalApps >= 1000 ? round($totalApps/1000) . 'K+' : $totalApps . '+';
                        ?>
                    </div>
                    <div class="stat-label">Apps Reviewed</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-chat-dots-fill" style="color:#10B981;"></i></div>
                    <div class="stat-num">
                        <?php
                        $totalReviews = isset($statistics['total_reviews']) ? $statistics['total_reviews'] : 10000;
                        echo $totalReviews >= 1000 ? round($totalReviews/1000) . 'K+' : $totalReviews . '+';
                        ?>
                    </div>
                    <div class="stat-label">Reviews</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill" style="color:#EF4444;"></i></div>
                    <div class="stat-num">
                        <?php
                        $totalScam = isset($statistics['total_scam_reports']) ? $statistics['total_scam_reports'] : 1000;
                        echo $totalScam >= 1000 ? round($totalScam/1000) . 'K+' : $totalScam . '+';
                        ?>
                    </div>
                    <div class="stat-label">Scam Reports</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== LATEST USER REVIEWS ===== -->
<section class="at-reviews-section">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="mb-0">Latest User Reviews</h2>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('reviews') ?>" style="font-size:0.9rem;color:#2563EB;font-weight:600;">View All →</a>
                <div class="slider-nav">
                    <button id="reviewPrev" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                    <button id="reviewNext" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>

        <div class="row g-4" id="reviewsContainer">
            <?php if (!empty($recent_reviews)): ?>
            <?php
            $reviewAppColors = ['#25D366','#F3A000','#FF4B4B','#2563EB','#7C3AED','#10B981'];
            $rIdx = 0;
            foreach (array_slice($recent_reviews, 0, 3) as $review):
                $rColor = $reviewAppColors[$rIdx % count($reviewAppColors)];
                $rIdx++;
                $appName = $review['app_name'] ?? $review['name'] ?? 'App';
                $rInitial = strtoupper(substr($appName, 0, 1));
                $rScore = (float)($review['rating'] ?? $review['trust_score'] ?? 4.0);
                $rStars = '';
                for ($s = 1; $s <= 5; $s++) {
                    $rStars .= ($s <= floor($rScore)) ? '★' : '☆';
                }
                $reviewerName = $review['reviewer_name'] ?? $review['user_name'] ?? 'Anonymous';
                $reviewerInitials = strtoupper(substr($reviewerName, 0, 2));
                $reviewText = $review['review_text'] ?? $review['comment'] ?? $review['body'] ?? 'Great app, highly recommended!';
                $timeAgo = $review['time_ago'] ?? $review['created_at'] ?? 'Recently';
            ?>
            <div class="col-md-4">
                <div class="review-card">
                    <div class="rc-app-row">
                        <div class="rc-app-icon" style="background:<?= $rColor ?>;"><?= esc($rInitial) ?></div>
                        <div>
                            <div class="rc-app-name"><?= esc($appName) ?></div>
                            <div class="rc-stars"><?= $rStars ?> <span style="color:#6B7280;font-size:0.78rem;"><?= number_format($rScore, 1) ?>/5</span></div>
                        </div>
                    </div>
                    <p class="rc-text"><?= esc($reviewText) ?></p>
                    <div class="rc-reviewer">
                        <div class="rc-avatar"><?= esc($reviewerInitials) ?></div>
                        <span class="rc-reviewer-name"><?= esc($reviewerName) ?></span>
                        <span class="rc-time"><?= esc($timeAgo) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php else: ?>
            <!-- Placeholder reviews -->
            <?php
            $placeholderReviews = [
                [
                    'app' => 'WhatsApp Messenger',
                    'color' => '#25D366',
                    'rating' => 4.5,
                    'stars' => '★★★★½',
                    'text' => 'WhatsApp is the best messaging app I have used. Fast, reliable, and secure. The new features keep getting better every update.',
                    'reviewer' => 'Ahmed K.',
                    'initials' => 'AK',
                    'time' => '2 hours ago',
                ],
                [
                    'app' => 'Binance',
                    'color' => '#F3A000',
                    'rating' => 4.2,
                    'stars' => '★★★★☆',
                    'text' => 'Binance is a solid crypto exchange. Good liquidity and many trading pairs. The app is smooth but can be complex for beginners.',
                    'reviewer' => 'Zara M.',
                    'initials' => 'ZM',
                    'time' => '5 hours ago',
                ],
                [
                    'app' => 'SnackVideo',
                    'color' => '#FF4B4B',
                    'rating' => 3.8,
                    'stars' => '★★★½☆',
                    'text' => 'SnackVideo is entertaining but the earning claims are exaggerated. Good for watching videos, but do not expect real money from it.',
                    'reviewer' => 'Bilal R.',
                    'initials' => 'BR',
                    'time' => '1 day ago',
                ],
            ];
            foreach ($placeholderReviews as $pr):
            ?>
            <div class="col-md-4">
                <div class="review-card">
                    <div class="rc-app-row">
                        <div class="rc-app-icon" style="background:<?= $pr['color'] ?>;"><?= strtoupper(substr($pr['app'],0,1)) ?></div>
                        <div>
                            <div class="rc-app-name"><?= $pr['app'] ?></div>
                            <div class="rc-stars"><?= $pr['stars'] ?> <span style="color:#6B7280;font-size:0.78rem;"><?= $pr['rating'] ?>/5</span></div>
                        </div>
                    </div>
                    <p class="rc-text"><?= $pr['text'] ?></p>
                    <div class="rc-reviewer">
                        <div class="rc-avatar"><?= $pr['initials'] ?></div>
                        <span class="rc-reviewer-name"><?= $pr['reviewer'] ?></span>
                        <span class="rc-time"><?= $pr['time'] ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===== NEWSLETTER SECTION ===== -->
<section class="at-newsletter">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex align-items-start gap-3">
                    <div style="font-size:2.5rem;line-height:1;">🛡️</div>
                    <div>
                        <h3>Stay updated about scam apps and useful tools</h3>
                        <p>Get weekly alerts about new scam apps, trusted app reviews, and earning tips — straight to your inbox.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <form action="<?= base_url('newsletter/subscribe') ?>" method="POST" class="newsletter-form">
                    <?= csrf_field() ?>
                    <input type="email" name="email" placeholder="Enter your email address" required>
                    <button type="submit">Subscribe</button>
                </form>
                <p style="color:rgba(255,255,255,0.6);font-size:0.78rem;margin-top:0.5rem;">
                    <i class="bi bi-lock-fill me-1"></i> No spam. Unsubscribe anytime.
                </p>
            </div>
        </div>
    </div>
</section>

                    
    
    <?= $this->endSection() ?>
