<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - AppTrust Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .navbar {
            background: var(--primary-gradient);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .unsubscribe-container {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        
        .unsubscribe-card {
            max-width: 600px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .unsubscribe-header {
            background: var(--primary-gradient);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
        
        .unsubscribe-body {
            padding: 40px;
        }
        
        .icon-sad {
            font-size: 4rem;
            color: rgba(255,255,255,0.9);
        }
        
        footer {
            background: #2d3748;
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
        }
        
        footer a {
            color: #a0aec0;
            text-decoration: none;
        }
        
        footer a:hover {
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">
                <i class="bi bi-shield-check"></i> AppTrust
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('apps') ?>">Apps</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('categories') ?>">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('scam-alerts') ?>">Scam Alerts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('blog') ?>">Blog</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Unsubscribe Content -->
    <div class="unsubscribe-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card unsubscribe-card">
                        <div class="unsubscribe-header">
                            <i class="bi bi-emoji-frown icon-sad"></i>
                            <h2 class="mt-3 mb-0">Unsubscribe from Newsletter</h2>
                        </div>
                        <div class="unsubscribe-body">
                            <div class="text-center mb-4">
                                <p class="lead">We're sorry to see you go!</p>
                                <p class="text-muted">
                                    You are about to unsubscribe from our newsletter alerts for:
                                </p>
                                <p class="fw-bold text-primary fs-5">
                                    <?= esc($subscriber['email']) ?>
                                </p>
                            </div>

                            <div class="alert alert-warning" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <strong>Important:</strong> If you unsubscribe, you will no longer receive:
                                <ul class="mt-2 mb-0">
                                    <li>High-risk scam alerts</li>
                                    <li>Security warnings about dangerous apps</li>
                                    <li>Important platform updates</li>
                                </ul>
                            </div>

                            <div class="d-grid gap-3 mt-4">
                                <form action="<?= base_url('newsletter/unsubscribe/' . esc($token)) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-lg w-100">
                                        <i class="bi bi-x-circle"></i> Yes, Unsubscribe Me
                                    </button>
                                </form>
                                
                                <a href="<?= base_url('/') ?>" class="btn btn-outline-primary btn-lg">
                                    <i class="bi bi-arrow-left"></i> No, Keep Me Subscribed
                                </a>
                            </div>

                            <div class="text-center mt-4">
                                <p class="text-muted small">
                                    Changed your mind? You can always resubscribe later from our homepage.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-shield-check"></i> AppTrust
                    </h5>
                    <p class="text-muted">
                        Your trusted source for app reviews, trust scores, and scam alerts.
                    </p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Platform</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('apps') ?>">Browse Apps</a></li>
                        <li><a href="<?= base_url('categories') ?>">Categories</a></li>
                        <li><a href="<?= base_url('scam-alerts') ?>">Scam Alerts</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Resources</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('blog') ?>">Blog</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Connect</h6>
                    <div class="d-flex gap-3">
                        <a href="#"><i class="bi bi-twitter" style="font-size: 1.5rem;"></i></a>
                        <a href="#"><i class="bi bi-facebook" style="font-size: 1.5rem;"></i></a>
                        <a href="#"><i class="bi bi-linkedin" style="font-size: 1.5rem;"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center text-muted">
                <p>&copy; <?= date('Y') ?> AppTrust Platform. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

