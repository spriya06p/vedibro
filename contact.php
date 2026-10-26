<?php
require_once '_layout.php';
$phones = array_values(array_filter([SHOP_PHONE, defined('SHOP_PHONE2') ? SHOP_PHONE2 : '', defined('SHOP_PHONE3') ? SHOP_PHONE3 : '']));
$email  = defined('SHOP_EMAIL') ? SHOP_EMAIL : '';
$insta  = (defined('SOCIAL_IG') && SOCIAL_IG) ? SOCIAL_IG : 'https://www.instagram.com/vedi_bro/';
site_head('Contact Us', 'contact'); ?>
<link rel="stylesheet" href="assets/contact.css">
<section class="ct">
  <div class="ct-bg" aria-hidden="true"><span>🎇</span><span>✨</span><span>🎆</span><span>🚀</span></div>
  <div class="ct-wrap">
    <div class="ct-intro">
      <h2 class="rv">How to Contact Us</h2>
      <span class="ct-wave rv" aria-hidden="true"></span>
      <p class="rv">We would love to hear from you. Call, message or visit our shop anytime.</p>
    </div>
    <div class="ct-cards">
      <div class="ct-card rv" style="--d:0s">
        <svg class="ct-ico" viewBox="0 0 24 24" width="56" height="56" fill="currentColor" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
        <h3>Address</h3>
        <p><a href="https://www.google.com/maps/search/?api=1&amp;query=<?=urlencode(SHOP_LOCATION)?>" target="_blank" rel="noopener"><?=e(SHOP_LOCATION)?></a></p>
      </div>
      <?php if ($phones): ?>
      <div class="ct-card rv" style="--d:.15s">
        <svg class="ct-ico ring" viewBox="0 0 24 24" width="56" height="56" fill="currentColor" aria-hidden="true"><path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11 11 0 0 0 3.6.6 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.6 3.6a1 1 0 0 1-.25 1z"/></svg>
        <h3>Phone</h3>
        <p><?php foreach ($phones as $ph): ?><a href="tel:<?=e(preg_replace('/[^\d+]/', '', $ph))?>"><?=e($ph)?></a><br><?php endforeach; ?></p>
      </div>
      <?php endif; ?>
      <?php if ($email): ?>
      <div class="ct-card rv" style="--d:.3s">
        <svg class="ct-ico" viewBox="0 0 24 24" width="56" height="56" fill="currentColor" aria-hidden="true"><path d="M2 5.5A1.5 1.5 0 0 1 3.5 4h17A1.5 1.5 0 0 1 22 5.5v.4l-10 6.5L2 5.9zM2 8.3V18.5A1.5 1.5 0 0 0 3.5 20h17a1.5 1.5 0 0 0 1.5-1.5V8.3l-10 6.5z"/></svg>
        <h3>Email</h3>
        <p><a href="mailto:<?=e($email)?>"><?=e($email)?></a></p>
      </div>
      <?php endif; ?>
      <a class="ct-card ct-insta rv" style="--d:.45s" href="<?=e($insta)?>" target="_blank" rel="noopener" aria-label="Open our Instagram page">
        <svg class="ct-ico" viewBox="0 0 24 24" width="56" height="56" fill="currentColor" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3.6" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17" cy="7" r="1.2"/></svg>
        <h3>Instagram</h3>
        <p>@vedi_bro</p>
      </a>
    </div>
  </div>
</section>
<script>
(function(){
  var els=document.querySelectorAll('.rv');
  if(!('IntersectionObserver' in window)){els.forEach(function(e){e.classList.add('in')});return}
  var io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)}})},{threshold:.15});
  els.forEach(function(el){io.observe(el)});
})();
</script>
<?php site_foot();