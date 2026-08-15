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
        'hero_facade_dusk' => [
            'path' => 'media/zyntech/images/hero-facade-dusk.jpeg',
            'alt' => 'Modern apartment facade at dusk with cantilevered balconies and warm window light',
        ],
        'about_architecture' => [
            'path' => 'media/zyntech/images/about-architecture.jpeg',
            'alt' => 'Two contemporary building facades meeting against an overcast sky',
        ],
        'interior_cad_overlay' => [
            'path' => 'media/zyntech/images/interior-cad-overlay.jpeg',
            'alt' => 'Interior construction space with an architectural CAD wireframe overlay',
        ],
        'interior_vision_sketch' => [
            'path' => 'media/zyntech/images/interior-vision-sketch.png',
            'alt' => 'Raw interior shell with a line-art sketch of the finished room',
        ],
        'plan_approvals_desk' => [
            'path' => 'media/zyntech/images/plan-approvals-desk.jpeg',
            'alt' => 'Architect desk with scale model, blueprints, compass, and laptop',
        ],
        'property_sketch_trench' => [
            'path' => 'media/zyntech/images/property-sketch-trench.jpeg',
            'alt' => 'Floor plan held over a foundation trench on a Kenyan building site',
        ],
        'property_sketch_blueprint' => [
            'path' => 'media/zyntech/images/property-sketch-blueprint.jpeg',
            'alt' => 'Detailed residential blueprint resting on excavated soil',
        ],
        'structure_pergola' => [
            'path' => 'media/zyntech/images/structure-pergola.jpeg',
            'alt' => 'White pergola beams against a clear sky',
        ],
        'structure_cantilever' => [
            'path' => 'media/zyntech/images/structure-cantilever.jpeg',
            'alt' => 'Cantilevered building soffit and glass facade against a blue sky',
        ],
        'structure_concrete_grid' => [
            'path' => 'media/zyntech/images/structure-concrete-grid.jpeg',
            'alt' => 'Reinforced concrete beams and coffered slab during construction',
        ],
        'interior_room_wireframe' => [
            'path' => 'media/zyntech/images/interior-room-wireframe.jpeg',
            'alt' => 'Finished double-height interior with an architectural wireframe overlay',
        ],
        'interior_tablet_elevations' => [
            'path' => 'media/zyntech/images/interior-tablet-elevations.jpeg',
            'alt' => 'Tablet showing interior elevations in a furnished living room',
        ],
        'interior_sofa_plan' => [
            'path' => 'media/zyntech/images/interior-sofa-plan.jpeg',
            'alt' => 'Sofa sketch sitting on a residential floor plan',
        ],
        'property_sketch_desk' => [
            'path' => 'media/zyntech/images/property-sketch-desk.jpeg',
            'alt' => 'Overhead property plans with tape, pencils, and a scale ruler',
        ],
        'construction_brick_frame' => [
            'path' => 'media/zyntech/images/construction-brick-frame.jpeg',
            'alt' => 'Brick structure with scaffolding and a concrete mixer on site',
        ],
        'construction_villa_fitout' => [
            'path' => 'media/zyntech/images/construction-villa-fitout.jpeg',
            'alt' => 'White villa mid-fitout during construction',
        ],
    ],

    'homepage' => [
        'hero' => 'hero_facade_dusk',
        'about' => 'about_architecture',
    ],

    /**
     * Still keys per public service slug. Used by the service show page
     * without writing to the database. First still is the banner; two stills
     * become a fade carousel on the explanation panel.
     *
     * @var array<string, list<string>>
     */
    'service_stills' => [
        'interior-design' => ['interior_room_wireframe', 'interior_tablet_elevations'],
        'exterior-design' => ['structure_cantilever', 'structure_pergola'],
        'plan-estimation' => ['plan_approvals_desk'],
        'property-sketching' => ['interior_sofa_plan', 'property_sketch_desk'],
        'plan-approvals' => ['plan_approvals_desk'],
        'construction-management' => ['construction_brick_frame'],
        'site-preparation' => ['site_prep_ballast'],
        'paving-hardscaping' => ['commercial_courtyard'],
    ],

    /**
     * Listing-card cover when it should differ from the show-page banner
     * (first service_stills key). Falls back to the first still, then DB.
     *
     * @var array<string, string>
     */
    'service_card' => [
        'interior-design' => 'interior_tablet_elevations',
        'property-sketching' => 'property_sketch_desk',
        'construction-management' => 'construction_villa_fitout',
        'plan-estimation' => 'plan_approvals_desk',
        'plan-approvals' => 'plan_approvals_desk',
    ],

    /**
     * Cover crop on catalogue cards. flush | inset | inset-portrait | inset-soft
     *
     * @var array<string, string>
     */
    'service_card_crop' => [
        'interior-design' => 'inset-soft',
        'exterior-design' => 'inset-soft',
        'plan-estimation' => 'inset-soft',
        'property-sketching' => 'inset-soft',
        'plan-approvals' => 'inset-soft',
        'construction-management' => 'inset-soft',
        'site-preparation' => 'inset-soft',
        'paving-hardscaping' => 'inset-soft',
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
