<?php

return [
    'default' => 'dark-boho',

    'themes' => [
        'dark-boho' => [
            'key' => 'dark-boho',
            'name' => 'Dark Boho',
            'description' => 'Tema undangan gelap yang hangat, organik, dan elegan dengan aksen pampas, lengkungan arch, dan ilustrasi garis tipis.',
            'view' => 'themes.dark-boho.index',
            'asset_path' => 'themes/dark-boho',
            'presets' => [
                'terracotta' => [
                    'label' => 'Terracotta (Default)',
                    'primary' => '#c0714f',
                    'secondary' => '#d8c3a0',
                ],
                'sage' => [
                    'label' => 'Sage',
                    'primary' => '#8a9a7b',
                    'secondary' => '#d8c3a0',
                ],
                'dusty-rose' => [
                    'label' => 'Dusty Rose',
                    'primary' => '#c99a8b',
                    'secondary' => '#c9a45c',
                ],
                'gold' => [
                    'label' => 'Gold',
                    'primary' => '#c9a45c',
                    'secondary' => '#c0714f',
                ],
            ],
            'default_config' => [
                'theme' => 'dark-boho',
                'preset' => 'terracotta',
                'cover' => [
                    'style' => 'default',
                    'usephoto' => true,
                    'overlayColor' => '#1d1d1b',
                    'overlayOpacity' => 0.55,
                    'textColor' => '#efe6d8',
                    'showGuestName' => true,
                ],
                'layout' => [
                    'mapsAndRsvpBelowEvents' => true,
                    'giftInline' => true,
                    'illustrationMode' => 'illustrated',
                    'brideFirst' => true,
                ],
                'sections' => [
                    'verse' => true,
                    'video' => false,
                    'gallery' => true,
                    'livestream' => false,
                    'gift' => true,
                    'wishes' => true,
                ],
            ],
        ],
    ],
];
