<?php
/**
 * BreakFree - Configuration application
 */

return [
    'name'    => env('APP_NAME', 'BreakFree'),
    'url'     => env('APP_URL', 'http://localhost:8080'),
    'env'     => env('APP_ENV', 'development'),
    'secret'  => env('APP_SECRET', 'change_me'),

    // Types d'addiction supportés
    'addiction_types' => [
        'tabac'   => 'Tabac (cigarettes)',
        'vape'    => 'Vape / Cigarette électronique',
        'alcool'  => 'Alcool',
        'cafe'    => 'Caféine',
        'sucre'   => 'Sucre',
        'ecrans'  => 'Écrans / Réseaux sociaux',
        'jeux'    => 'Jeux d\'argent',
        'autre'   => 'Autre',
    ],

    // Moods disponibles
    'moods' => [
        'excellent' => '😄 Excellent',
        'bien'      => '🙂 Bien',
        'neutre'    => '😐 Neutre',
        'stresse'   => '😰 Stressé',
        'triste'    => '😢 Triste',
        'en_colere' => '😠 En colère',
    ],

    // Badges / Gamification
    'badges' => [
        ['id' => 'day_1',     'name' => 'Premier Pas',       'desc' => '1 jour sans consommation',    'days' => 1,   'icon' => '🌱'],
        ['id' => 'day_3',     'name' => 'Détermination',     'desc' => '3 jours sans consommation',   'days' => 3,   'icon' => '💪'],
        ['id' => 'day_7',     'name' => 'Première Semaine',  'desc' => '7 jours sans consommation',   'days' => 7,   'icon' => '⭐'],
        ['id' => 'day_14',    'name' => 'Force Intérieure',  'desc' => '14 jours sans consommation',  'days' => 14,  'icon' => '🔥'],
        ['id' => 'day_30',    'name' => 'Premier Mois',      'desc' => '30 jours sans consommation',  'days' => 30,  'icon' => '🏆'],
        ['id' => 'day_60',    'name' => 'Résilience',        'desc' => '60 jours sans consommation',  'days' => 60,  'icon' => '💎'],
        ['id' => 'day_90',    'name' => 'Liberté',           'desc' => '90 jours sans consommation',  'days' => 90,  'icon' => '🦅'],
        ['id' => 'day_180',   'name' => 'Demi-Année',        'desc' => '180 jours sans consommation', 'days' => 180, 'icon' => '👑'],
        ['id' => 'day_365',   'name' => 'Légende',           'desc' => '1 an sans consommation',      'days' => 365, 'icon' => '🎖️'],
    ],

    // Niveaux gamification
    'levels' => [
        ['level' => 1,  'name' => 'Débutant',     'min_days' => 0,   'color' => '#94a3b8'],
        ['level' => 2,  'name' => 'Motivé',       'min_days' => 3,   'color' => '#22c55e'],
        ['level' => 3,  'name' => 'Combattant',   'min_days' => 7,   'color' => '#3b82f6'],
        ['level' => 4,  'name' => 'Guerrier',     'min_days' => 14,  'color' => '#a855f7'],
        ['level' => 5,  'name' => 'Champion',     'min_days' => 30,  'color' => '#f59e0b'],
        ['level' => 6,  'name' => 'Maître',       'min_days' => 60,  'color' => '#ef4444'],
        ['level' => 7,  'name' => 'Légende',      'min_days' => 90,  'color' => '#ec4899'],
        ['level' => 8,  'name' => 'Immortel',     'min_days' => 180, 'color' => '#14b8a6'],
        ['level' => 9,  'name' => 'Transcendant', 'min_days' => 365, 'color' => '#fbbf24'],
    ],

    // Rate limiting
    'login_max_attempts'  => 5,
    'login_lockout_minutes' => 15,
];
