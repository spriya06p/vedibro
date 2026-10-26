<?php
// Animated 4-line introduction on the About page. Text is edited in Admin > About Intro.
require_once __DIR__ . '/config.php';

function about_intro_defaults(): array {
    return [
        'Welcome to ' . SHOP_NAME,
        'Genuine Sivakasi crackers at fair, direct prices.',
        'Choose your favourites, see the total instantly and send your enquiry in minutes.',
        'We call you within 24 hours to confirm. Light up your celebration with us!',
    ];
}
function about_intro_lines(): array {
    $raw = setting('about_intro', null);
    if ($raw === null || trim((string)$raw) === '') return about_intro_defaults();
    $lines = array_values(array_filter(array_map('trim', preg_split('/\R/', (string)$raw)), 'strlen'));
    return array_slice($lines, 0, 4) ?: about_intro_defaults();
}
function about_intro_html(): string {
    ob_start(); ?>
<style>
.intro{max-width:900px;margin:26px auto 6px;padding:26px 28px;background:linear-gradient(135deg,var(--cream),#fff);border:1px solid var(--gold);border-radius:16px;box-shadow:0 8px 24px rgba(0,0,0,.08);text-align:center;width:calc(100% - 8%)}
.il{opacity:0;transform:translateY(22px);animation:ilIn .8s ease forwards;margin:0 0 12px;line-height:1.5;font-size:1.12rem;color:var(--ink)}
.il:last-child{margin-bottom:0}
.il:first-child{font-size:clamp(1.5rem,4vw,2.2rem);font-weight:800;color:var(--purple)}
.il:first-child::after{content:'';display:block;height:4px;width:0;margin:10px auto 0;background:var(--gold);border-radius:4px;animation:ilBar 1s ease .8s forwards}
.il:nth-child(4){font-weight:700;color:var(--purple2)}
.il i{font-style:normal;color:var(--gold);margin-right:6px}
@keyframes ilIn{to{opacity:1;transform:none}}
@keyframes ilBar{to{width:130px}}
@media(prefers-reduced-motion:reduce){.il{animation:none;opacity:1;transform:none}.il:first-child::after{animation:none;width:130px}}
</style>
<section class="intro" aria-label="Introduction">
<?php foreach (about_intro_lines() as $i => $line): ?>
  <p class="il" style="animation-delay:<?=number_format(0.2 + $i * 0.9, 1)?>s"><?php if ($i > 0): ?><i>✦</i><?php endif; ?><?=e($line)?></p>
<?php endforeach; ?>
</section>
<?php return ob_get_clean();
}
