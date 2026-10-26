<?php
// Shared footer (About / Information / Contact columns). Used on every page.
require_once __DIR__ . '/config.php';

function site_footer(string $base = ''): void {   // $base = '../' when called from /admin
    $phones = array_filter([SHOP_PHONE, defined('SHOP_PHONE2') ? SHOP_PHONE2 : '', defined('SHOP_PHONE3') ? SHOP_PHONE3 : '']);
    $email  = defined('SHOP_EMAIL') ? SHOP_EMAIL : '';
    $soc = [
        'Facebook'  => [defined('SOCIAL_FB') ? SOCIAL_FB : '', '<path d="M14 8h3V4h-3c-2.8 0-4.5 1.8-4.5 4.5V11H7v4h2.5v6h4v-6H16l.5-4h-3V8.7c0-.5.3-.7 1-.7z"/>'],
        'Instagram' => [defined('SOCIAL_IG') ? SOCIAL_IG : '', '<rect x="4" y="4" width="16" height="16" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3.6" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17" cy="7" r="1.2"/>'],
        'YouTube'   => [defined('SOCIAL_YT') ? SOCIAL_YT : '', '<path d="M21 8.2a2.5 2.5 0 0 0-1.8-1.8C17.6 6 12 6 12 6s-5.6 0-7.2.4A2.5 2.5 0 0 0 3 8.2C2.6 9.8 2.6 12 2.6 12s0 2.2.4 3.8a2.5 2.5 0 0 0 1.8 1.8C6.4 18 12 18 12 18s5.6 0 7.2-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-3.8.4-3.8s0-2.2-.4-3.8zM10 15V9l5 3z"/>'],
    ];
    $links = ['Home' => 'index.php', 'About Us' => 'about.php', 'Price List' => 'index.php?view=price#products', 'Safety Tips' => 'safetytips.php', 'Contact Us' => 'contact.php'];
    ?>
<footer id="contact" class="site-foot">
  <div class="foot-grid">
    <div>
      <h3 class="foot-h">About Us</h3>
      <h4 class="foot-name"><?=e(SHOP_NAME)?></h4>
      <p><?=e(SHOP_NAME)?> is a crackers store in <?=e(SHOP_LOCATION)?>. We supply Sivakasi crackers at fair prices. Pick your items, send your enquiry, and we will call you to confirm.</p>
      <div class="foot-social">
        <?php foreach ($soc as $name => [$url, $svg]): if (!$url) continue; ?>
          <a href="<?=e($url)?>" target="_blank" rel="noopener" aria-label="<?=e($name)?>"><svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><?=$svg?></svg></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div>
      <h3 class="foot-h">Information</h3>
      <ul class="foot-links">
        <?php foreach ($links as $label => $href): ?><li><a href="<?=e($href[0] === '#' ? $href : $base . $href)?>"><i>&#8250;</i><?=e($label)?></a></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h3 class="foot-h">Contact Us</h3>
      <ul class="foot-contact">
        <li><i>&#128205;</i><span><?=e(SHOP_LOCATION)?></span></li>
        <?php foreach ($phones as $ph): ?><li><i>&#9742;</i><span><?=e($ph)?></span></li><?php endforeach; ?>
        <?php if ($email): ?><li><i>&#9993;</i><span><?=e($email)?></span></li><?php endif; ?>
      </ul>
    </div>
  </div>
  <p class="foot-note">As per the 2018 Supreme Court order, online sale of firecrackers is not permitted. Please add your products to the cart and submit the enquiry. We will contact you within 24 hours and confirm the order through WhatsApp or phone call.</p>
</footer>
<?php
    // Floating quick-access buttons (public pages only: admin pages do not call site_footer)
    $tel = preg_replace('/[^+\d]/', '', SHOP_PHONE);
    $waNum = preg_replace('/\D/', '', defined('SHOP_WHATSAPP') && SHOP_WHATSAPP ? SHOP_WHATSAPP : SHOP_PHONE);
    if (strlen($waNum) === 10) $waNum = '91' . $waNum;
    $waUrl = 'https://wa.me/' . $waNum . '?text=' . rawurlencode('Hello ' . SHOP_NAME . ', I would like to enquire about crackers.');
    // Home and Price List are both index.php, so hide the buttons there
    if (basename($_SERVER['SCRIPT_NAME'] ?? '') !== 'index.php'): ?>
<style>
.qa{position:fixed;z-index:40;display:flex;flex-direction:column;gap:12px;bottom:24px}
.qa-l{left:14px}.qa-r{right:0}
.qa a{text-decoration:none}
.qa-btn{position:relative;display:grid;place-items:center;width:54px;height:54px;border-radius:50%;color:#fff;box-shadow:0 4px 12px rgba(0,0,0,.35)}
.qa-btn svg{width:28px;height:28px;fill:currentColor}
.qa-call{background:var(--purple);animation:qaBlink 1.1s ease-in-out infinite}
.qa-call::after{content:'';position:absolute;inset:0;border-radius:50%;border:3px solid var(--purple);animation:qaRing 1.1s ease-out infinite}
.qa-wa{background:#25d366}
.qa-order{display:block;background:var(--gold);color:var(--ink);font-weight:800;font-size:.85rem;letter-spacing:1px;text-transform:uppercase;padding:12px 16px;border-radius:24px 0 0 24px;box-shadow:0 4px 12px rgba(0,0,0,.3);animation:qaNudge 2.2s ease-in-out infinite}
.qa-btn:hover,.qa-order:hover{filter:brightness(1.1)}
@keyframes qaBlink{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.45;transform:scale(1.1)}}
@keyframes qaRing{0%{transform:scale(1);opacity:.9}100%{transform:scale(1.7);opacity:0}}
@keyframes qaNudge{0%,100%{transform:translateX(0)}50%{transform:translateX(-6px)}}
@media(max-width:760px){.qa{bottom:88px}.qa-btn{width:48px;height:48px}.qa-order{font-size:.72rem;padding:10px 12px}}
@media(prefers-reduced-motion:reduce){.qa-call,.qa-call::after,.qa-order{animation:none}}
</style>
<div class="qa qa-l">
  <a class="qa-btn qa-call" href="tel:<?=e($tel)?>" aria-label="Call us"><svg viewBox="0 0 24 24"><path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11 11 0 0 0 3.6.6 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.6 3.6a1 1 0 0 1-.25 1z"/></svg></a>
  <a class="qa-btn qa-wa" href="<?=e($waUrl)?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.2 14.2c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.2-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.8s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 1.9c.1.2.1.4 0 .6l-.4.6c-.1.2-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.1 1 2 1.3 2.3 1.4.3.1.4.1.6-.1l.8-1c.2-.3.4-.2.6-.1l1.8.9c.3.1.5.2.5.3.1.2.1.8-.1 1.3z"/></svg></a>
</div>
<div class="qa qa-r">
  <a class="qa-order" href="<?=e($base . 'index.php?view=price#products')?>">Quick Order</a>
</div>
<?php endif; }