<?php
// Theme + logo helpers (theme is chosen in Admin > Theme, stored in the settings table)
require_once __DIR__ . '/config.php';

const THEMES = [
    'vedibro' => ['name' => 'VediBro (Pink, Yellow & Navy)', 'sw' => ['#EC3A78', '#FDE405', '#14225E', '#2DBBEE']],
    'sky'     => ['name' => 'Sky Blue & Sunshine (from logo)', 'sw' => ['#0A86BF', '#FDE405', '#14225E', '#EC3A78']],
    'navy'    => ['name' => 'Royal Navy & Yellow (from logo)',  'sw' => ['#14225E', '#FDE405', '#EC3A78', '#2DBBEE']],
    'red'     => ['name' => 'Festive Red & Gold',                'sw' => ['#B71C2E', '#F5B301', '#5C0A17', '#FFF6EA']],
    'green'   => ['name' => 'Emerald Green & Marigold',          'sw' => ['#0B7A4B', '#FFB400', '#063D2A', '#F0FAF4']],
    'classic' => ['name' => 'Classic (Dark Purple & Gold)',  'sw' => ['#2D1258', '#D9A521', '#160A2B', '#4A2088']],
];
function current_theme(): string { $t = setting('theme', 'vedibro'); return isset(THEMES[$t]) ? $t : 'vedibro'; }
/** Extra stylesheet for the store pages (empty for the default theme). Put it AFTER store.css. */
function theme_store_link(): string { $t = current_theme(); return $t === 'vedibro' ? '' : '<link rel="stylesheet" href="assets/theme-'.$t.'.css">'; }
/** Extra stylesheet for admin pages. */
function theme_admin_link(): string { $t = current_theme(); return $t === 'vedibro' ? '' : '<link rel="stylesheet" href="../assets/admin-'.$t.'.css">'; }
/** Logo URL: the one uploaded in Admin > Theme, else assets/logo.*, else null. */
function site_logo_url(string $base = ''): ?string {
    $f = setting('logo', '');
    if ($f !== '' && is_file(__DIR__ . '/uploads/' . $f)) return $base . 'uploads/' . $f;
    $g = glob(__DIR__ . '/assets/logo.{png,jpg,jpeg,webp}', GLOB_BRACE)[0] ?? null;
    return $g ? $base . 'assets/' . basename($g) : null;
}
