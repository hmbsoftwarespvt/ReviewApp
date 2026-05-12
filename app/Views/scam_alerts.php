<?php
// Fallback for optional variables
$scam_reports = isset($scam_reports) ? $scam_reports : [];
$categories   = isset($categories)   ? $categories   : [];
$filters      = isset($filters)      ? $filters      : ['category' => '', 'risk_level' => '', 'status' => '', 'search' => '', 'sort' => 'newest'];
$pagination   = isset($pagination)   ? $pagination   : ['total' => 0, 'current_page' => 1, 'total_pages' => 1];
$stats        = isset($stats)        ? $stats        : [];

$activeReports = isset($stats['active_reports']) ? $stats['active_reports'] : 234;
$highRisk      = isset($stats['high_risk'])      ? $stats['high_risk']      : 98;
$totalReports  = isset($stats['total_reports'])  ? $stats['total_reports']  : 15700;
$resolved      = isset($stats['resolved'])       ? $stats['resolved']       : 312;

$isLoggedIn = session()->get('isLoggedIn');

// Active filter type tab
$activeType = isset($_GET['type']) ? $_GET['type'] : 'all';

// Placeholder scam reports shown when DB is empty
$placeholderReports = [
    [
        'id'             => 1,
        'app_name'       => 'Quick Money – Earn Cash Online',
        'app_slug'       => 'quick-money-earn-cash-online',
        'category_name'  => 'Earning App',
        'website_url'    => 'www.quickmoneyapp.com',
        'description'    => 'Users report no payouts after reaching minimum withdrawal. Customer support not responding.',
        'risk_level'     => 'high',
        'trust_score'    => 15,
        'report_count'   => 24,
        'created_at'     => date('Y-m-d H:i:s', strtotime('-2 hours')),
        'status'         => 'active',
        'icon_bg'        => '#1a1a2e',
        'icon_text'      => 'QUICK MONEY',
    ],
    [
        'id'             => 2,
        'app_name'       => 'Easy Cash Rewards',
        'app_slug'       => 'easy-cash-rewards',
        'category_name'  => 'Earning App',
        'website_url'    => 'https://easycashrewards.xyz',
        'description'    => 'Fake promises of high returns. Many users lost money after deposit.',
        'risk_level'     => 'high',
        'trust_score'    => 20,
        'report_count'   => 37,
        'created_at'     => date('Y-m-d H:i:s', strtotime('-1 day')),
        'status'         => 'active',
        'icon_bg'        => '#16a34a',
        'icon_text'      => 'EASY CASH',
    ],
    [
        'id'             => 3,
        'app_name'       => 'BitGrow – Crypto Trading',
        'app_slug'       => 'bitgrow-crypto-trading',
        'category_name'  => 'Crypto Platform',
        'website_url'    => 'www.bitgrowtrade.com',
        'description'    => 'Possible Ponzi scheme. Website owner information is hidden.',
        'risk_level'     => 'high',
        'trust_score'    => 18,
        'report_count'   => 18,
        'created_at'     => date('Y-m-d H:i:s', strtotime('-2 days')),
        'status'         => 'active',
        'icon_bg'        => '#7c3aed',
        'icon_text'      => 'BIT GROW',
    ],
    [
        'id'             => 4,
        'app_name'       => 'DealMart Store',
        'app_slug'       => 'dealmart-store',
        'category_name'  => 'Shopping Website',
        'website_url'    => 'www.dealmartstore.shop',
        'description'    => 'Payment received but no product delivered. No contact information available.',
        'risk_level'     => 'medium',
        'trust_score'    => 35,
        'report_count'   => 51,
        'created_at'     => date('Y-m-d H:i:s', strtotime('-3 days')),
        'status'         => 'active',
        'icon_bg'        => '#ea580c',
        'icon_text'      => 'DEAL MART',
    ],
    [
        'id'             => 5,
        'app_name'       => 'Lucky Wheel – Spin & Win',
        'app_slug'       => 'lucky-wheel-spin-win',
        'category_name'  => 'Gaming App',
        'website_url'    => 'www.luckywheelgame.com',
        'description'    => 'Excessive ads, fake winnings and asks for payment to withdraw.',
        'risk_level'     => 'medium',
        'trust_score'    => 40,
        'report_count'   => 29,
        'created_at'     => date('Y-m-d H:i:s', strtotime('-4 days')),
        'status'         => 'active',
        'icon_bg'        => '#65a30d',
        'icon_text'      => 'LUCKY WHEEL',
    ],
];

$displayReports = !empty($scam_reports) ? $scam_reports : $placeholderReports;

// Time-ago helper
if (!function_exists('scamTimeAgo')) {
    function scamTimeAgo($datetime) {
        $now  = new DateTime();
        $then = new DateTime($datetime);
        $diff = $now->diff($then);
        if ($diff->y > 0) return $diff->y . ' year'  . ($diff->y > 1 ? 's' : '') . ' ago';
        if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        if ($diff->d > 0) return $diff->d . ' day'   . ($diff->d > 1 ? 's' : '') . ' ago';
        if ($diff->h > 0) return $diff->h . ' hour'  . ($diff->h > 1 ? 's' : '') . ' ago';
        if ($diff->i > 0) return $diff->i . ' minute'. ($diff->i > 1 ? 's' : '') . ' ago';
        return 'Just now';
    }
}

// Format large numbers
if (!function_exists('scamFormatNum')) {
    function scamFormatNum($n) {
        $n = (int)$n;
        if ($n >= 1000) return round($n / 1000, 1) . 'K';
        return (string)$n;
    }
}
?>
<?= $this->extend('base_template') ?>

<?= $this->section('content') ?>

<style>
        /* ===== BASE ===== */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1a1a2e;
            background: #F8FAFC;
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
            line-height: 1.1;
        }
        .at-navbar .navbar-brand .brand-icon { color: #2563EB; font-size: 1.5rem; }
        .at-navbar .brand-tagline {
            font-size: 0.68rem;
            color: #9CA3AF;
            font-weight: 400;
            display: block;
            letter-spacing: 0.02em;
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
        .at-navbar .nav-link.active-home {
            color: #2563EB;
            border-bottom-color: #2563EB;
        }
        .at-navbar .nav-link.active-scam {
            color: #EF4444;
            border-bottom-color: #EF4444;
            font-weight: 600;
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
            white-space: nowrap;
        }
        .btn-nav-login:hover { background: #EFF6FF; color: #2563EB; }
        .btn-nav-signup {
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.38rem 1rem;
            font-size: 0.88rem;
            font-weight: 600;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .btn-nav-signup:hover { background: #1D4ED8; color: #fff; }

        /* ===== PAGE HEADER ===== */
        .sa-page-header {
            background: #fff;
            border-bottom: 1px solid #E5E7EB;
            padding: 2rem 0 1.5rem;
        }
        .sa-page-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 0.4rem;
        }
        .sa-page-header p {
            color: #6B7280;
            font-size: 1rem;
            margin: 0;
        }

        /* ===== STATS CARDS ===== */
        .sa-stats-section { padding: 1.75rem 0 0; }
        .sa-stat-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
            padding: 1.1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            height: 100%;
        }
        .sa-stat-card .stat-icon-wrap {
            width: 48px; height: 48px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        .sa-stat-card .stat-num {
            font-size: 1.6rem;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }
        .sa-stat-card .stat-title {
            font-size: 0.88rem;
            font-weight: 600;
            color: #374151;
            margin-top: 0.15rem;
        }
        .sa-stat-card .stat-sub {
            font-size: 0.75rem;
            color: #9CA3AF;
            margin-top: 0.1rem;
        }

        /* ===== FILTER TABS ===== */
        .sa-filter-bar {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
            padding: 0.85rem 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .sa-type-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }
        .sa-type-tab {
            display: inline-block;
            padding: 0.35rem 0.9rem;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #6B7280;
            background: #F3F4F6;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .sa-type-tab:hover { background: #E5E7EB; color: #374151; }
        .sa-type-tab.active {
            background: #EF4444;
            color: #fff;
        }
        .sa-sort-select {
            border: 1.5px solid #E5E7EB;
            border-radius: 8px;
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            color: #374151;
            background: #fff;
            outline: none;
            cursor: pointer;
        }

        /* ===== SCAM REPORT CARD ===== */
        .sa-report-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
            padding: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
            transition: box-shadow 0.2s;
        }
        .sa-report-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,0.10); }
        .sa-app-icon {
            width: 60px; height: 60px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.6rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
            text-align: center;
            line-height: 1.2;
            padding: 4px;
            word-break: break-word;
        }
        .sa-report-body { flex: 1; min-width: 0; }
        .sa-report-body .sa-app-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.2rem;
        }
        .sa-report-body .sa-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: #6B7280;
            margin-bottom: 0.45rem;
        }
        .sa-report-body .sa-meta .cat-badge {
            background: #F3F4F6;
            color: #374151;
            border-radius: 20px;
            padding: 0.15rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .sa-report-body .sa-meta .url-link {
            color: #2563EB;
            font-size: 0.78rem;
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }
        .sa-report-body .sa-desc {
            font-size: 0.88rem;
            color: #4B5563;
            line-height: 1.55;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .sa-report-body .sa-reported-by {
            font-size: 0.75rem;
            color: #9CA3AF;
        }
        .sa-report-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
            flex-shrink: 0;
            min-width: 110px;
        }
        .sa-risk-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
        }
        .sa-risk-label {
            font-size: 0.72rem;
            color: #9CA3AF;
            text-align: right;
        }
        .sa-score-num {
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1;
            text-align: right;
        }
        .sa-score-denom {
            font-size: 0.85rem;
            font-weight: 500;
            color: #9CA3AF;
        }
        .btn-view-details {
            border: 1.5px solid #EF4444;
            color: #EF4444;
            background: transparent;
            border-radius: 8px;
            padding: 0.3rem 0.85rem;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .btn-view-details:hover { background: #FEF2F2; color: #EF4444; }

        /* ===== SIDEBAR ===== */
        .sa-sidebar-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }
        .sa-sidebar-card h6 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
        }
        .sa-sidebar-input {
            width: 100%;
            border: 1.5px solid #E5E7EB;
            border-radius: 8px;
            padding: 0.5rem 0.85rem;
            font-size: 0.85rem;
            color: #374151;
            outline: none;
            margin-bottom: 0.65rem;
            transition: border-color 0.2s;
        }
        .sa-sidebar-input:focus { border-color: #2563EB; }
        .sa-sidebar-select {
            width: 100%;
            border: 1.5px solid #E5E7EB;
            border-radius: 8px;
            padding: 0.5rem 0.85rem;
            font-size: 0.85rem;
            color: #374151;
            background: #fff;
            outline: none;
            margin-bottom: 0.65rem;
            cursor: pointer;
        }
        .btn-apply-filters {
            display: block;
            width: 100%;
            background: #EF4444;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.6rem;
            font-size: 0.88rem;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-apply-filters:hover { background: #DC2626; }

        /* Risk Level Guide */
        .risk-guide-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0;
            border-bottom: 1px solid #F3F4F6;
        }
        .risk-guide-item:last-child { border-bottom: none; }
        .risk-dot {
            width: 14px; height: 14px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .risk-guide-item .rg-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
        }
        .risk-guide-item .rg-range {
            font-size: 0.75rem;
            color: #9CA3AF;
        }
        .risk-guide-item .rg-desc {
            font-size: 0.75rem;
            color: #6B7280;
            margin-left: auto;
        }

        /* Report a Scam box */
        .sa-report-box {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 14px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            text-align: center;
        }
        .sa-report-box .report-icon {
            font-size: 2rem;
            color: #EF4444;
            margin-bottom: 0.5rem;
        }
        .sa-report-box h6 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #991B1B;
            margin-bottom: 0.4rem;
        }
        .sa-report-box p {
            font-size: 0.82rem;
            color: #7F1D1D;
            margin-bottom: 0.85rem;
        }
        .btn-report-now {
            display: inline-block;
            border: 1.5px solid #EF4444;
            color: #EF4444;
            background: transparent;
            border-radius: 8px;
            padding: 0.45rem 1.25rem;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-report-now:hover { background: #EF4444; color: #fff; }

        /* Stay Safe Tips */
        .safe-tip-item {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            padding: 0.45rem 0;
            font-size: 0.85rem;
            color: #374151;
        }
        .safe-tip-item .tip-check {
            color: #10B981;
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 0.05rem;
        }
        .safe-shield-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 0.75rem;
        }
        .safe-shield-wrap i {
            font-size: 3rem;
            color: #DBEAFE;
        }

        /* ===== FOOTER ===== */
        .at-footer {
            background: #1a1a2e;
            color: #9CA3AF;
            padding: 3rem 0 0;
            margin-top: 3rem;
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
        .social-icons { display: flex; gap: 0.6rem; margin-top: 0.75rem; }
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
        .footer-newsletter-input {
            display: flex;
            gap: 0.4rem;
            margin-top: 0.75rem;
        }
        .footer-newsletter-input input {
            flex: 1;
            border: 1px solid #2d2d4e;
            background: #2d2d4e;
            color: #fff;
            border-radius: 8px;
            padding: 0.5rem 0.85rem;
            font-size: 0.85rem;
            outline: none;
        }
        .footer-newsletter-input input::placeholder { color: #6B7280; }
        .footer-newsletter-input button {
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 0.9rem;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .footer-newsletter-input button:hover { background: #1D4ED8; }
        .footer-bottom {
            border-top: 1px solid #2d2d4e;
            padding: 1.25rem 0;
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.82rem;
            color: #6B7280;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991px) {
            .sa-sidebar { margin-top: 2rem; }
        }
        @media (max-width: 767px) {
            .sa-page-header h1 { font-size: 1.5rem; }
            .sa-report-card { flex-wrap: wrap; }
            .sa-report-right { flex-direction: row; align-items: center; min-width: unset; width: 100%; justify-content: space-between; }
            .sa-filter-bar { flex-direction: column; align-items: flex-start; }
            .at-navbar .nav-search { width: 130px; }
        }
        @media (max-width: 575px) {
            .sa-app-icon { width: 48px; height: 48px; font-size: 0.55rem; }
            .sa-score-num { font-size: 1.2rem; }
        }
    </style>
</head>
<body>
                
<!-- ===== PAGE HEADER ===== -->
<div class="sa-page-header">
    <div class="container">
        <h1>Scam Alerts 🔺</h1>
        <p>Stay safe! Check the latest scam apps and websites reported by our community.</p>
    </div>
</div>

<!-- ===== MAIN CONTENT ===== -->
<div class="container" style="padding-top:1.5rem; padding-bottom:2rem;">

    <!-- STATS ROW -->
    <div class="sa-stats-section">
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="sa-stat-card">
                    <div class="stat-icon-wrap" style="background:#FEF2F2;">
                        <i class="bi bi-exclamation-circle-fill" style="color:#EF4444;"></i>
                    </div>
                    <div>
                        <div class="stat-num"><?= esc($activeReports) ?></div>
                        <div class="stat-title">Active Scam Reports</div>
                        <div class="stat-sub">Currently under review</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="sa-stat-card">
                    <div class="stat-icon-wrap" style="background:#FFF7ED;">
                        <i class="bi bi-shield-exclamation" style="color:#F97316;"></i>
                    </div>
                    <div>
                        <div class="stat-num"><?= esc($highRisk) ?></div>
                        <div class="stat-title">High Risk Alerts</div>
                        <div class="stat-sub">Avoid immediately</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="sa-stat-card">
                    <div class="stat-icon-wrap" style="background:#EFF6FF;">
                        <i class="bi bi-people-fill" style="color:#2563EB;"></i>
                    </div>
                    <div>
                        <div class="stat-num"><?= esc(scamFormatNum($totalReports)) ?></div>
                        <div class="stat-title">Total Reports</div>
                        <div class="stat-sub">From our community</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="sa-stat-card">
                    <div class="stat-icon-wrap" style="background:#F0FDF4;">
                        <i class="bi bi-check-circle-fill" style="color:#10B981;"></i>
                    </div>
                    <div>
                        <div class="stat-num"><?= esc($resolved) ?></div>
                        <div class="stat-title">Resolved</div>
                        <div class="stat-sub">Scams removed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TWO-COLUMN LAYOUT -->
    <div class="row g-4">

        <!-- LEFT: MAIN CONTENT -->
        <div class="col-lg-8">

            <!-- FILTER TABS + SORT -->
            <div class="sa-filter-bar">
                <div class="sa-type-tabs">
                    <?php
                    $typeTabs = [
                        'all'        => 'All Alerts',
                        'apps'       => 'Apps',
                        'websites'   => 'Websites',
                        'earning'    => 'Earning Apps',
                        'investment' => 'Investment',
                        'shopping'   => 'Shopping',
                        'crypto'     => 'Crypto',
                    ];
                    foreach ($typeTabs as $key => $label):
                    ?>
                    <a href="<?= base_url('scam-alerts') ?>?type=<?= esc($key) ?>"
                       class="sa-type-tab <?= ($activeType === $key) ? 'active' : '' ?>">
                        <?= esc($label) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <div>
                    <select class="sa-sort-select" onchange="window.location=this.value">
                        <option value="<?= base_url('scam-alerts') ?>?type=<?= esc($activeType) ?>&sort=newest"
                            <?= (isset($filters['sort']) && $filters['sort'] === 'newest') ? 'selected' : '' ?>>
                            Most Recent
                        </option>
                        <option value="<?= base_url('scam-alerts') ?>?type=<?= esc($activeType) ?>&sort=highest_risk"
                            <?= (isset($filters['sort']) && $filters['sort'] === 'highest_risk') ? 'selected' : '' ?>>
                            Highest Risk
                        </option>
                        <option value="<?= base_url('scam-alerts') ?>?type=<?= esc($activeType) ?>&sort=most_reported"
                            <?= (isset($filters['sort']) && $filters['sort'] === 'most_reported') ? 'selected' : '' ?>>
                            Most Reported
                        </option>
                    </select>
                </div>
            </div>

            <!-- SCAM REPORT CARDS -->
            <?php foreach ($displayReports as $report):
                $score = (float)($report['trust_score'] ?? 50);
                if ($score <= 30) {
                    $riskLabel = 'High Risk';
                    $riskColor = '#EF4444';
                    $riskBg    = '#FEF2F2';
                    $scoreColor = '#EF4444';
                } elseif ($score <= 60) {
                    $riskLabel = 'Medium Risk';
                    $riskColor = '#F97316';
                    $riskBg    = '#FFF7ED';
                    $scoreColor = '#F97316';
                } else {
                    $riskLabel = 'Low Risk';
                    $riskColor = '#10B981';
                    $riskBg    = '#F0FDF4';
                    $scoreColor = '#10B981';
                }

                $iconBg   = $report['icon_bg']   ?? '#2563EB';
                $iconText = $report['icon_text']  ?? strtoupper(substr($report['app_name'] ?? 'A', 0, 1));
                $catName  = $report['category_name'] ?? ($report['category'] ?? 'App');
                $websiteUrl = $report['website_url'] ?? '';
                $reportCount = $report['report_count'] ?? ($report['reported_count'] ?? 0);
                $timeAgo = scamTimeAgo($report['created_at'] ?? date('Y-m-d H:i:s'));
                $detailUrl = base_url('scam-alerts/' . ($report['app_slug'] ?? $report['id']));
            ?>
            <div class="sa-report-card">
                <!-- App Icon -->
                <div class="sa-app-icon" style="background:<?= esc($iconBg) ?>;">
                    <?= esc($iconText) ?>
                </div>

                <!-- Body -->
                <div class="sa-report-body">
                    <div class="sa-app-name"><?= esc($report['app_name'] ?? '') ?></div>
                    <div class="sa-meta">
                        <span class="cat-badge"><?= esc($catName) ?></span>
                        <?php if ($websiteUrl): ?>
                        <a href="<?= esc(strpos($websiteUrl, 'http') === 0 ? $websiteUrl : 'https://' . $websiteUrl) ?>"
                           target="_blank" rel="noopener noreferrer" class="url-link">
                            <i class="bi bi-globe2"></i>
                            <?= esc($websiteUrl) ?>
                            <i class="bi bi-box-arrow-up-right" style="font-size:0.7rem;"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <p class="sa-desc"><?= esc($report['description'] ?? '') ?></p>
                    <div class="sa-reported-by">
                        <i class="bi bi-clock me-1"></i>Reported <?= esc($timeAgo) ?>
                        &nbsp;•&nbsp;
                        <i class="bi bi-people me-1"></i>By <?= esc($reportCount) ?> users
                    </div>
                </div>

                <!-- Right: Risk + Score + Button -->
                <div class="sa-report-right">
                    <span class="sa-risk-badge" style="background:<?= esc($riskBg) ?>;color:<?= esc($riskColor) ?>;">
                        <?= esc($riskLabel) ?>
                    </span>
                    <div class="sa-risk-label">Risk Score</div>
                    <div class="sa-score-num" style="color:<?= esc($scoreColor) ?>;">
                        <?= esc((int)$score) ?><span class="sa-score-denom">/100</span>
                    </div>
                    <a href="<?= esc($detailUrl) ?>" class="btn-view-details">View Details</a>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- PAGINATION -->
            <?php if ($pagination['total_pages'] > 1): ?>
            <nav aria-label="Scam alerts pagination" class="mt-3">
                <ul class="pagination justify-content-center">
                    <?php if ($pagination['current_page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('scam-alerts') ?>?type=<?= esc($activeType) ?>&page=<?= $pagination['current_page'] - 1 ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                    <li class="page-item <?= ($p === (int)$pagination['current_page']) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('scam-alerts') ?>?type=<?= esc($activeType) ?>&page=<?= $p ?>">
                            <?= $p ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('scam-alerts') ?>?type=<?= esc($activeType) ?>&page=<?= $pagination['current_page'] + 1 ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

        </div><!-- /col-lg-8 -->

        <!-- RIGHT: SIDEBAR -->
        <div class="col-lg-4 sa-sidebar">

            <!-- 1. Filter Alerts -->
            <div class="sa-sidebar-card">
                <h6><i class="bi bi-funnel-fill me-2" style="color:#EF4444;"></i>Filter Alerts</h6>
                <form action="<?= base_url('scam-alerts') ?>" method="GET">
                    <?= csrf_field() ?>
                    <div class="input-group mb-2" style="border:1.5px solid #E5E7EB;border-radius:8px;overflow:hidden;">
                        <span class="input-group-text bg-white border-0" style="padding:0.45rem 0.75rem;">
                            <i class="bi bi-search text-secondary" style="font-size:0.85rem;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 shadow-none"
                               placeholder="Search scam apps or websites..."
                               value="<?= esc($filters['search'] ?? '') ?>"
                               style="font-size:0.85rem;padding:0.45rem 0.5rem;">
                    </div>
                    <select name="category" class="sa-sidebar-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat['slug'] ?? $cat['id']) ?>"
                            <?= (isset($filters['category']) && $filters['category'] == ($cat['slug'] ?? $cat['id'])) ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="risk_level" class="sa-sidebar-select">
                        <option value="">All Risk Levels</option>
                        <option value="high"   <?= (isset($filters['risk_level']) && $filters['risk_level'] === 'high')   ? 'selected' : '' ?>>High Risk</option>
                        <option value="medium" <?= (isset($filters['risk_level']) && $filters['risk_level'] === 'medium') ? 'selected' : '' ?>>Medium Risk</option>
                        <option value="low"    <?= (isset($filters['risk_level']) && $filters['risk_level'] === 'low')    ? 'selected' : '' ?>>Low Risk</option>
                    </select>
                    <select name="status" class="sa-sidebar-select">
                        <option value="">All Status</option>
                        <option value="active"   <?= (isset($filters['status']) && $filters['status'] === 'active')   ? 'selected' : '' ?>>Active</option>
                        <option value="resolved" <?= (isset($filters['status']) && $filters['status'] === 'resolved') ? 'selected' : '' ?>>Resolved</option>
                        <option value="pending"  <?= (isset($filters['status']) && $filters['status'] === 'pending')  ? 'selected' : '' ?>>Pending</option>
                    </select>
                    <button type="submit" class="btn-apply-filters">
                        <i class="bi bi-funnel-fill me-1"></i> Apply Filters
                    </button>
                </form>
            </div>

            <!-- 2. Risk Level Guide -->
            <div class="sa-sidebar-card">
                <h6><i class="bi bi-info-circle-fill me-2" style="color:#2563EB;"></i>Risk Level Guide</h6>
                <div class="risk-guide-item">
                    <div class="risk-dot" style="background:#EF4444;"></div>
                    <div>
                        <div class="rg-label">High Risk <span style="color:#9CA3AF;font-weight:400;">(0–30)</span></div>
                        <div class="rg-range">Avoid at all costs</div>
                    </div>
                </div>
                <div class="risk-guide-item">
                    <div class="risk-dot" style="background:#F59E0B;"></div>
                    <div>
                        <div class="rg-label">Medium Risk <span style="color:#9CA3AF;font-weight:400;">(31–60)</span></div>
                        <div class="rg-range">Be careful</div>
                    </div>
                </div>
                <div class="risk-guide-item">
                    <div class="risk-dot" style="background:#10B981;"></div>
                    <div>
                        <div class="rg-label">Low Risk <span style="color:#9CA3AF;font-weight:400;">(61–100)</span></div>
                        <div class="rg-range">Generally safe</div>
                    </div>
                </div>
            </div>

            <!-- 3. Report a Scam -->
            <div class="sa-report-box">
                <div class="report-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <h6>Report a Scam</h6>
                <p>Help others by reporting scam apps or websites.</p>
                <a href="<?= base_url('report-scam') ?>" class="btn-report-now">
                    <i class="bi bi-flag-fill me-1"></i> Report Now
                </a>
            </div>

            <!-- 4. Stay Safe Tips -->
            <div class="sa-sidebar-card">
                <div class="safe-shield-wrap">
                    <i class="bi bi-shield-fill-check" style="color:#BFDBFE;font-size:3rem;"></i>
                </div>
                <h6><i class="bi bi-lightbulb-fill me-2" style="color:#F59E0B;"></i>Stay Safe Tips</h6>
                <div class="safe-tip-item">
                    <i class="bi bi-check-circle-fill tip-check"></i>
                    <span>Don't share personal information</span>
                </div>
                <div class="safe-tip-item">
                    <i class="bi bi-check-circle-fill tip-check"></i>
                    <span>Avoid apps with unrealistic promises</span>
                </div>
                <div class="safe-tip-item">
                    <i class="bi bi-check-circle-fill tip-check"></i>
                    <span>Check reviews before installing</span>
                </div>
                <div class="safe-tip-item">
                    <i class="bi bi-check-circle-fill tip-check"></i>
                    <span>Report suspicious activity</span>
                </div>
            </div>

        </div><!-- /col-lg-4 -->

    </div><!-- /row -->
</div><!-- /container -->

<?= $this->endSection() ?>
