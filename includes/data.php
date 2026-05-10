<?php
$site = [
    'name' => 'Kaptain One',
    'tagline' => 'Warsaw e-bike and scooter rentals for delivery couriers.',
    'phone_display' => '+48 723-385-026',
    'phone_link' => '+48723385026',
    'email' => 'kaptainonewayne@gmail.com',
    'base_url' => '',
    'city' => 'Warsaw, Poland',
    'year' => date('Y')
];

$nav = [
    ['label' => 'Services', 'href' => 'services/index.php'],
    ['label' => 'Packages', 'href' => 'packages.php'],
    ['label' => 'Couriers', 'href' => 'gig-workers.php'],
    ['label' => 'Company', 'href' => 'about.php'],
    ['label' => 'Contact', 'href' => 'contact.php']
];

$stats = [
    ['value' => '24/7', 'label' => 'Live booking response'],
    ['value' => 'Warsaw', 'label' => 'Local courier marketplace'],
    ['value' => 'E-bike', 'label' => 'Delivery-ready equipment'],
    ['value' => 'Scooter', 'label' => 'Flexible city movement']
];

$features = [
    [
        'title' => 'Duty of care',
        'text' => 'Professionally managed rides, real-time trip coordination, and premium guest support from booking to arrival.'
    ],
    [
        'title' => 'Powerful management tools',
        'text' => 'A clean booking flow, lead capture, ride request routing, and simple admin-friendly structure ready for your next phase.'
    ],
    [
        'title' => 'Global-ready presentation',
        'text' => 'A premium brand experience designed to win hotels, travel planners, executive assistants, and direct clients.'
    ],
    [
        'title' => 'Open for growth',
        'text' => 'Built in PHP and structured for Hostinger, so you can expand into dashboards, portals, and partner workflows later.'
    ]
];

$audiences = [
    [
        'eyebrow' => 'For Hotels, Concierges & Travel Planners',
        'title' => 'Premium transportation your guests will remember',
        'text' => 'Offer polished airport transfers, hourly chauffeur service, and VIP movement with a booking process that feels as premium as the ride itself.',
        'bullets' => [
            'White-label ready presentation for premium partners',
            'Fast quote and request workflow for concierge teams',
            'Executive-class service for arrivals, departures, and events'
        ]
    ],
    [
        'eyebrow' => 'For Corporate Travel & Executive Teams',
        'title' => 'Private chauffeur service made simple for business travel',
        'text' => 'Handle airport pickups, meeting transfers, roadshows, and client entertainment with a professional brand and dependable booking experience.',
        'bullets' => [
            'Simple request flow for assistants and coordinators',
            'Clear service options for airport, hourly, and event transport',
            'Professional image built for premium business clients'
        ]
    ]
];

$fleet = [
    ['name' => 'Luxury SUV', 'desc' => 'Ideal for airport transfers, VIP guests, and executive families.'],
    ['name' => 'Executive Sedan', 'desc' => 'Perfect for business travel, city transfers, and discreet premium service.'],
    ['name' => 'Sprinter / Group Vehicle', 'desc' => 'For events, teams, and coordinated guest movement.']
];

$testimonials = [
    [
        'quote' => 'Kaptain One feels polished, responsive, and high-end from the first message to final drop-off.',
        'name' => 'Luxury Hospitality Partner'
    ],
    [
        'quote' => 'Exactly the kind of executive transportation brand companies want to trust with VIP movement.',
        'name' => 'Corporate Travel Coordinator'
    ]
];
