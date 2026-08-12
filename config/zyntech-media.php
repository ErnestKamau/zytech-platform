<?php

/**
 * Canonical public-site assets. Files live in public/media/zyntech and must
 * not be moved or deleted — the homepage, about, services, and projects pages
 * read these paths directly.
 */
return [
    'images' => [
        'site_prep_ballast' => [
            'path' => 'media/zyntech/images/site-prep-ballast.jpg',
            'alt' => 'Crushed ballast delivered to a Kenyan residential site for foundation work',
        ],
        'commercial_courtyard' => [
            'path' => 'media/zyntech/images/commercial-courtyard.jpg',
            'alt' => 'Finished commercial courtyard with stone cladding, interlocking pavers, and sage patio umbrellas',
        ],
        'paving_gravel_leveling' => [
            'path' => 'media/zyntech/images/paving-gravel-leveling.jpg',
            'alt' => 'Site crew leveling crushed stone beside interlocking brick paving in suburban Kenya',
        ],
        'structural_walkway' => [
            'path' => 'media/zyntech/images/structural-walkway.jpg',
            'alt' => 'Steel pergola frame over a tiled walkway during an active Kenyan construction build',
        ],
    ],

    'videos' => [
        'hero_site_work' => [
            'path' => 'media/zyntech/videos/hero-site-work.mp4',
            'poster' => 'media/zyntech/posters/hero-site-work.jpg',
            'alt' => 'On-site construction work in Kenya',
        ],
        'services_process' => [
            'path' => 'media/zyntech/videos/services-process.mp4',
            'poster' => 'media/zyntech/posters/services-process.jpg',
            'alt' => 'Zytech crew at work on a Kenyan construction site',
        ],
        'projects_showreel' => [
            'path' => 'media/zyntech/videos/projects-showreel.mp4',
            'poster' => 'media/zyntech/posters/projects-showreel.jpg',
            'alt' => 'Zytech project work across Kenyan sites',
        ],
    ],

    'contact' => [
        'email' => 'hello@zytech.co.ke',
        'phone' => '+254 700 000 000',
        'location' => 'Nairobi, Kenya',
        'service_area' => 'Serving Nairobi, Kiambu, and nationwide',
    ],
];
