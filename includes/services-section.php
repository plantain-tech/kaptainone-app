<?php
$services = [
    [
        'label' => 'Arrivals & Departures',
        'title' => 'Airport Transfers',
        'description' => 'Flight-aware chauffeur service with polished meet-and-greet execution for seamless airport movement.',
        'href' => 'services/airport-transfers.php',
        'icon' => '✦'
    ],
    [
        'label' => 'Business Class',
        'title' => 'Executive Transportation',
        'description' => 'Refined black car service for executives, clients, and leadership teams who expect elevated standards.',
        'href' => 'services/executive-transportation.php',
        'icon' => '✦'
    ],
    [
        'label' => 'Enterprise Mobility',
        'title' => 'Corporate Travel',
        'description' => 'Structured transport solutions for assistants, teams, and premium corporate travel programs.',
        'href' => 'services/corporate-travel.php',
        'icon' => '✦'
    ],
    [
        'label' => 'As Directed',
        'title' => 'Hourly Chauffeur',
        'description' => 'Flexible executive coverage for meetings, roadshows, city movement, and multi-stop itineraries.',
        'href' => 'services/hourly-chauffeur.php',
        'icon' => '✦'
    ],
    [
        'label' => 'Coordinated Logistics',
        'title' => 'Event Transportation',
        'description' => 'Elevated guest transportation planning for conferences, private events, and VIP schedules.',
        'href' => 'services/event-transportation.php',
        'icon' => '✦'
    ],
    [
        'label' => 'White-Glove Service',
        'title' => 'VIP Concierge',
        'description' => 'Discreet, high-touch travel support for guests who value privacy, precision, and polish.',
        'href' => 'services/vip-concierge.php',
        'icon' => '✦'
    ],
];
?>
<section class="services-premium-band section" aria-labelledby="services-premium-title">
    <div class="container services-premium-band__container">
        <div class="services-premium-band__intro">
            <span class="services-premium-band__eyebrow">Signature Services</span>
            <h2 class="services-premium-band__title" id="services-premium-title">Executive Mobility, Refined for Every Journey</h2>
            <p class="services-premium-band__text">
                Premium chauffeur services for airport transfers, corporate movement, event logistics, and discreet VIP travel,
                delivered with precision, polish, and executive-level care.
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
                        <span class="executive-card__cta">Explore Service</span>
                        <span class="executive-card__arrow" aria-hidden="true">→</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
