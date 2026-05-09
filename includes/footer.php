<?php
/**
 * Kaptain One - Footer Component
 */
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-column">
                <div class="footer-brand">Kaptain One</div>
                <p style="color: var(--text-secondary); font-size: 0.9375rem; max-width: 280px;">
                    Premium ground transportation for executives, VIP guests, and discerning travelers. 
                    Professional chauffeur service with modern convenience.
                </p>
            </div>
            <div class="footer-column">
                <h4>Solutions</h4>
                <a href="<?= base_url() ?>/gig-workers.php">For Gig Workers</a>
                <a href="<?= base_url() ?>/packages.php">Equipment Packages</a>
                <a href="<?= base_url() ?>/solutions.php">For Travel Agents</a>
                <a href="<?= base_url() ?>/solutions.php">For Hotels</a>
                <a href="<?= base_url() ?>/solutions.php">For Corporate Travel</a>
                <a href="<?= base_url() ?>/solutions.php">For Fleet Partners</a>
            </div>
            <div class="footer-column">
                <h4>Services</h4>
                <a href="<?= base_url() ?>/services/airport-transfers.php">Airport Transfers</a>
                <a href="<?= base_url() ?>/services/executive-transportation.php">Executive Transportation</a>
                <a href="<?= base_url() ?>/services/corporate-travel.php">Corporate Travel</a>
                <a href="<?= base_url() ?>/services/event-transportation.php">Event Transportation</a>
            </div>
            <div class="footer-column">
                <h4>Contact</h4>
                <a href="tel:<?= e(site('phone_link')) ?>"><?= e(site('phone')) ?></a>
                <a href="mailto:<?= e(site('email')) ?>"><?= e(site('email')) ?></a>
                <span><?= e(site('address')) ?></span>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© <?= date('Y') ?> Kaptain One. All rights reserved.</span>
            <div style="display: flex; gap: 1.5rem;">
                <a href="<?= base_url() ?>/privacy.php" style="color: var(--text-muted);">Privacy</a>
                <a href="<?= base_url() ?>/terms.php" style="color: var(--text-muted);">Terms</a>
            </div>
        </div>
    </div>
</footer>
<button class="back-to-top" id="backToTop" type="button" aria-label="Back to top">
    <span aria-hidden="true">↑</span>
</button>
<script src="<?= asset('js/services-section.js') ?>"></script>
<script>
    const navbar = document.getElementById('navbar');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const backToTop = document.getElementById('backToTop');

    function handleScrollState() {
        if (navbar) {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        if (backToTop) {
            if (window.scrollY > 520) {
                backToTop.classList.add('is-visible');
            } else {
                backToTop.classList.remove('is-visible');
            }
        }
    }

    window.addEventListener('scroll', handleScrollState);
    handleScrollState();

    function toggleMobileMenu() {
        mobileMenu.classList.toggle('active');
        const expanded = mobileMenu.classList.contains('active');
        if (mobileMenuToggle) {
            mobileMenuToggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        }
    }

    document.addEventListener('click', (event) => {
        if (!mobileMenu || !mobileMenuToggle) return;
        const clickedInsideMenu = mobileMenu.contains(event.target);
        const clickedToggle = mobileMenuToggle.contains(event.target);

        if (!clickedInsideMenu && !clickedToggle) {
            mobileMenu.classList.remove('active');
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
        }
    });

    function smoothScrollToTop() {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const scrollingElement = document.scrollingElement || document.documentElement || document.body;

        if (prefersReducedMotion) {
            scrollingElement.scrollTop = 0;
            if (document.body) document.body.scrollTop = 0;
            if (document.documentElement) document.documentElement.scrollTop = 0;
            window.scrollTo(0, 0);
            return;
        }

        const start = scrollingElement.scrollTop || window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
        if (start <= 0) return;

        const duration = Math.min(1100, Math.max(500, start * 0.45));
        const startTime = performance.now();

        const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

        const step = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = easeOutCubic(progress);
            const next = Math.round(start * (1 - eased));

            scrollingElement.scrollTop = next;
            if (document.body) document.body.scrollTop = next;
            if (document.documentElement) document.documentElement.scrollTop = next;
            window.scrollTo(0, next);

            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                scrollingElement.scrollTop = 0;
                if (document.body) document.body.scrollTop = 0;
                if (document.documentElement) document.documentElement.scrollTop = 0;
                window.scrollTo(0, 0);
            }
        };

        window.requestAnimationFrame(step);
    }

    if (backToTop) {
        backToTop.addEventListener('click', (event) => {
            event.preventDefault();
            smoothScrollToTop();
        });
    }
</script>
</body>
</html>
