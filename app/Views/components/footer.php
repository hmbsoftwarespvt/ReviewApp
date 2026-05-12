<!-- Footer -->
<footer class="at-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="footer-brand">
                    <i class="bi bi-shield-fill-check"></i>
                    AppTrust
                </div>
                <p class="footer-tagline">
                    Your trusted source for app reviews, trust scores, and scam alerts. Make informed decisions about app safety.
                </p>
                <div class="social-icons">
                    <a href="#"><i class="bi bi-twitter"></i></a>
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6>Platform</h6>
                <ul>
                    <li><a href="<?= base_url('apps') ?>">Browse Apps</a></li>
                    <li><a href="<?= base_url('categories') ?>">Categories</a></li>
                    <li><a href="<?= base_url('scam-alerts') ?>">Scam Alerts</a></li>
                    <li><a href="<?= base_url('compare') ?>">Compare Apps</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6>Resources</h6>
                <ul>
                    <li><a href="<?= base_url('blog') ?>">Blog</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6>Legal</h6>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Cookie Policy</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6>Newsletter</h6>
                <p style="color: #9CA3AF; font-size: 0.85rem; margin-bottom: 1rem;">Get latest scam alerts and trusted app reviews</p>
                <form style="display: flex; gap: 0.5rem;">
                    <input type="email" placeholder="Your email" style="flex: 1; padding: 0.5rem; border: 1px solid #2d2d4e; background: #2d2d4e; border-radius: 6px; color: #fff; font-size: 0.85rem;">
                    <button type="submit" style="background: #2563EB; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> AppTrust Platform. All rights reserved.</p>
        </div>
    </div>
</footer>
