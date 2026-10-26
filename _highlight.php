<?php
// Highlight (scrolling) bar at the top of the website. Text is edited in Admin > Highlight Bar.
// Used by index.php and by every page that uses _layout.php, so all pages show the same bar.
require_once __DIR__ . '/config.php';

function highlight_messages(): array {
    $raw = setting('highlight_text', null);
    if ($raw === null) { $d = defined('SALE_TEXT') ? SALE_TEXT : ''; return $d === '' ? [] : [$d]; }   // first time: use config.php text
    return array_values(array_filter(array_map('trim', preg_split('/\R/', (string)$raw)), 'strlen'));
}
function highlight_enabled(): bool { return setting('highlight_on', '1') === '1'; }

// <link> for the bar's CSS. $base = '../' when used from /admin.
function highlight_css_link(string $base = ''): string {
    return '<link rel="stylesheet" href="' . $base . 'assets/highlight.css">';
}

function highlight_bar_html(): string {
    $msgs = highlight_messages();
    if (!highlight_enabled() || !$msgs) return '';
    $px = ['slow' => 35, 'normal' => 50, 'fast' => 75][setting('highlight_speed', 'normal')] ?? 50;   // pixels per second
    $items = '';
    foreach ($msgs as $i => $m) $items .= ($i ? '<span class="hl-sep">✦</span>' : '') . '<span>' . e($m) . '</span>';
    $items .= '<span class="hl-sep">✦</span>';
    $chars = strlen(implode('', $msgs));
    $fallback = max(25, (int)round(max(1200, $chars * 9) / $px));     // used until the small script below measures the real width
    return '<div class="salebar" data-speed="' . $px . '"><div class="hl-track" style="--t:' . $fallback . 's">'
         . '<div class="hl-grp">' . $items . '</div><div class="hl-grp" aria-hidden="true">' . $items . '</div></div></div>'
         . '<script>(function(){document.querySelectorAll(".salebar[data-speed]").forEach(function(b){var t=b.querySelector(".hl-track"),g=t.firstElementChild;'
         . 'function s(){var w=g.getBoundingClientRect().width;if(w)t.style.setProperty("--t",(w/(+b.dataset.speed||50)).toFixed(1)+"s")}'
         . 's();window.addEventListener("load",s);window.addEventListener("resize",s)})})();</script>';
}
