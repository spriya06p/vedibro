<?php
require_once '_layout.php';
require_once __DIR__ . '/_about.php';
site_head('About Us', 'about'); ?>
<link rel="stylesheet" href="assets/about.css?v=<?=@filemtime(__DIR__.'/assets/about.css')?>">
<?=about_intro_html()?>
<div class="ab">
  <div class="ab-bg" aria-hidden="true"><span>🚀</span><span>🎆</span><span>🧨</span><span>🎇</span><span>✨</span></div>

  <div class="ab-wrap">
    <section class="ab-hero">
      <div>
        <h2 class="rv l">Fireworks wholesale &amp; retail store in Sivakasi</h2>
        <div class="ab-line rv l" style="--d:.15s"></div>
        <p class="rv l" style="--d:.25s"><?=e(SHOP_NAME)?> is a crackers store based in <?=e(SHOP_LOCATION)?>. We supply a wide range of Sivakasi fireworks &mdash; sparklers, flower pots, chakkars, rockets, bombs, fountains, single sound crackers and multi-shot fancy items &mdash; for every celebration.</p>
        <p class="rv l" style="--d:.35s">Every cracker is made following industry safety norms with standard quality raw materials. Browse the price list, add the items you want and send your enquiry &mdash; our team calls you within 24 hours to confirm the order and arrange the parcel.</p>
        <div class="ab-stats">
          <div class="rv z" style="--d:.45s"><b data-count="20" data-plus="1">0</b><small>Years of trust</small></div>
          <div class="rv z" style="--d:.6s"><b data-count="500" data-plus="1">0</b><small>Products</small></div>
          <div class="rv z" style="--d:.75s"><b data-count="24" data-suffix="h">0</b><small>Order callback</small></div>
        </div>
      </div>
      <div class="ab-visual">
        <div class="ab-badge rv z" style="--d:.2s">
          <span class="ab-spark s1">✦</span><span class="ab-spark s2">✧</span><span class="ab-spark s3">✧</span><span class="ab-spark s4">✦</span>
          <div><em>Diwali</em><strong>BIG</strong><span>SALE</span><small>UP TO 50% OFF</small></div>
        </div>
        <div class="ab-tiles">
          <a class="ab-tile rv r" style="--d:.4s" href="index.php#products"><span>🎁</span><div>Wholesale<small>Crackers</small></div></a>
          <a class="ab-tile rv r" style="--d:.55s" href="index.php#products"><span>🎆</span><div>Retail<small>Crackers</small></div></a>
        </div>
      </div>
    </section>
  </div>

  <section class="ab-cta">
    <div class="ab-wrap">
      <div class="ab-cta-l rv l">
        <h3>View our Products</h3>
        <p>Our products are alluring in look and dependable in quality &mdash; and your safety is our main concern.</p>
        <a class="ab-btn" href="index.php#products">Our Products</a>
      </div>
      <div class="ab-cta-r rv r">
        <h3>Call Us for Any Questions</h3>
        <a class="ab-call" href="tel:<?=e(preg_replace('/[^+\d]/', '', SHOP_PHONE))?>">&#9742; <?=e(SHOP_PHONE)?></a>
      </div>
    </div>
  </section>

  <section class="ab-spec">
    <div class="ab-wrap">
      <h2 class="rv">Our Speciality</h2>
      <span class="ab-wave rv" aria-hidden="true"></span>
      <p class="ab-sub rv">We provide quality &amp; affordable price crackers to our customers.</p>
      <div class="ab-spec-grid">
        <div class="ab-col">
          <div class="ab-card rv l"><div class="ab-ico">&#10022;</div><h4>Quality</h4><p>Manufacturing quality crackers and innovation are the key behind our success.</p></div>
          <div class="ab-card rv l" style="--d:.2s"><div class="ab-ico">&#8377;</div><h4>Genuine Price</h4><p>Clear discounted prices on every category, with net rates on gift boxes and family packs.</p></div>
        </div>
        <div class="ab-center rv z" style="--d:.15s">
          <div class="ab-orbit" aria-hidden="true"><i>🎆</i><i>🚀</i><i>🎇</i></div>
          <div class="ab-sale"><em>Happy</em><b>Diwali</b><strong>SALE</strong></div>
        </div>
        <div class="ab-col">
          <div class="ab-card rv r"><div class="ab-ico">&#10025;</div><h4>Safe to Use</h4><p>Crackers we offer are safe and made from fine quality raw materials.</p></div>
          <div class="ab-card rv r" style="--d:.2s"><div class="ab-ico">&#9829;</div><h4>Satisfaction</h4><p>Easy enquiry, quick confirmation call and careful packing &mdash; your happiness is our goal.</p></div>
        </div>
      </div>
    </div>
  </section>
</div>

<main class="page">
  <section class="pcard rv">
    <h3>How ordering works</h3>
    <p>Online sale of firecrackers is not permitted, so your order is taken as an enquiry. After you submit it, we contact you on the mobile number you gave, confirm the items and price, and arrange delivery through a registered transport service.</p>
  </section>
</main>
<script>
(function(){
  var els=document.querySelectorAll('.rv');
  function count(el){
    var to=+el.dataset.count,suf=el.dataset.suffix||(el.dataset.plus?'+':''),t0=null;
    function step(t){t0=t0||t;var p=Math.min((t-t0)/1400,1);el.textContent=Math.round(to*(1-Math.pow(1-p,3)))+suf;if(p<1)requestAnimationFrame(step)}
    requestAnimationFrame(step);
  }
  function show(el){el.classList.add('in');el.querySelectorAll('[data-count]').forEach(count)}
  if(!('IntersectionObserver' in window)){els.forEach(show);return}
  var io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){show(e.target);io.unobserve(e.target)}})},{threshold:.15});
  els.forEach(function(el){io.observe(el)});
})();
</script>
<?php site_foot();