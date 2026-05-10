<?php
// UNUSED: legacy service-card include kept for direct-reference safety after Day 2 MVP scope cleanup.
$services = [
    [
        'label' => 'Courier Equipment',
        'title' => 'E-bike Rentals',
        'description' => 'Work-ready e-bike options for Warsaw delivery couriers.',
        'href' => 'services/index.php',
        'icon' => '*'
    ],
    [
        'label' => 'Flexible Mobility',
        'title' => 'Scooter Rentals',
        'description' => 'Scooter rental packages for couriers who need efficient city movement.',
        'href' => 'services/index.php',
        'icon' => '*'
    ],
    [
        'label' => 'Getting Started',
        'title' => 'Courier Onboarding Support',
        'description' => 'Profile, requirements, and application steps organized in one account.',
        'href' => 'services/index.php',
        'icon' => '*'
    ],
    [
        'label' => 'Owner Income',
        'title' => 'Asset Owner Listing Support',
        'description' => 'A focused path for listing idle e-bikes and scooters for courier rentals.',
        'href' => 'services/index.php',
        'icon' => '*'
    ],
];
?>
<section class="services-premium-band section" aria-labelledby="services-premium-title">
    <div class="container services-premium-band__container">
        <div class="services-premium-band__intro">
            <span class="services-premium-band__eyebrow">Core Services</span>
            <h2 class="services-premium-band__title" id="services-premium-title">Courier Rentals, Built Around Warsaw</h2>
            <p class="services-premium-band__text">
                E-bike rentals, scooter rentals, onboarding support, and asset-owner listing support
                for the first version of the Kaptain One marketplace.
            </p>
        </div>

        <div class="services-premium-band__grid">
            <?php foreach ($services as $service): ?>
                <a href="<?= base_url() ?>/<?= e($service['href']) ?>" class="executive-card" aria-label="<?= e($service['title']) ?>">
                    <div class="executive-card__glow"></div>
                    <div class="executive-card__head">
                        <span class="executive-card__icon" aria-hidden="true"><?= e($service['icon']) ?></span>
                        <span class="executive-card__label"><?= e($service['label']) ?></span>
                    </div>
                    <div class="executive-card__body">
                        <h3 class="executive-card__title"><?= e($service['title']) ?></h3>
                        <p class="executive-card__description"><?= e($service['description']) ?></p>
                    </div>
                    <div class="executive-card__footer">
                        <span class="executive-card__cta">View Services</span>
                        <span class="executive-card__arrow" aria-hidden="true">-&gt;</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
