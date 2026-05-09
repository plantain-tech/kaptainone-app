<article class="package-card">
    <div class="package-card__top">
        <span class="package-card__category"><?= e($package['category']) ?></span>
        <span class="status-pill status-<?= e($package['availability_status']) ?>"><?= e(package_status_label($package['availability_status'])) ?></span>
    </div>
    <h3><?= e($package['title']) ?></h3>
    <p><?= e($package['short_description']) ?></p>
    <div class="package-price">
        <strong><?= number_format((float)$package['weekly_price'], 0) ?> <?= e($package['currency']) ?></strong>
        <span>/ week</span>
    </div>
    <div class="package-meta">
        <span>Deposit <?= number_format((float)$package['deposit_amount'], 0) ?> <?= e($package['currency']) ?></span>
        <span>Monthly <?= number_format((float)$package['monthly_price'], 0) ?> <?= e($package['currency']) ?></span>
    </div>
    <a href="<?= base_url() ?>/package.php?slug=<?= e($package['slug']) ?>" class="btn btn-secondary">View Details</a>
</article>
