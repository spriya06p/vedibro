<?php
require_once '_layout.php';
site_head('Safety Tips', 'safety'); ?>
<link rel="stylesheet" href="assets/safety.css">
<div class="sf">
  <div class="sf-conf" aria-hidden="true"><span style="left:43%;width:10px;height:20px;background:var(--purple);animation-duration:12s;animation-delay:-17s"></span><span style="left:14%;width:13px;height:23px;background:var(--purple);animation-duration:19s;animation-delay:-6s"></span><span style="left:6%;width:9px;height:20px;background:#2DBBEE;animation-duration:12s;animation-delay:-7s"></span><span style="left:13%;width:16px;height:20px;background:var(--purple);animation-duration:20s;animation-delay:-3s"></span><span style="left:30%;width:8px;height:23px;background:#ff8a00;animation-duration:17s;animation-delay:-1s"></span><span style="left:30%;width:8px;height:22px;background:var(--gold);animation-duration:15s;animation-delay:-13s"></span><span style="left:20%;width:16px;height:15px;background:#ff8a00;animation-duration:15s;animation-delay:-17s"></span><span style="left:89%;width:10px;height:15px;background:#ff8a00;animation-duration:20s;animation-delay:-6s"></span><span style="left:49%;width:9px;height:22px;background:var(--purple);animation-duration:20s;animation-delay:-1s"></span><span style="left:81%;width:11px;height:21px;background:#ff8a00;animation-duration:17s;animation-delay:-10s"></span><span style="left:61%;width:15px;height:19px;background:#33c46f;animation-duration:14s;animation-delay:-5s"></span><span style="left:91%;width:11px;height:15px;background:#ff8a00;animation-duration:15s;animation-delay:-16s"></span><span style="left:65%;width:13px;height:25px;background:#2DBBEE;animation-duration:15s;animation-delay:-2s"></span><span style="left:17%;width:16px;height:20px;background:var(--gold);animation-duration:16s;animation-delay:-4s"></span><span style="left:64%;width:14px;height:14px;background:var(--purple);animation-duration:19s;animation-delay:-18s"></span><span style="left:42%;width:13px;height:25px;background:#33c46f;animation-duration:20s;animation-delay:-15s"></span><span style="left:76%;width:15px;height:15px;background:var(--purple);animation-duration:15s;animation-delay:-15s"></span><span style="left:91%;width:9px;height:14px;background:#33c46f;animation-duration:20s;animation-delay:-14s"></span></div>
  <div class="sf-wrap">
    <div class="sf-intro">
      <h2 class="rv"><?=e(SHOP_NAME)?></h2>
      <span class="sf-wave rv" aria-hidden="true"></span>
      <p class="rv">Crackers are fun when used with care. Follow these important Do's &amp; Don'ts every time you buy, burst or store crackers. A little carelessness can cause a serious injury.</p>
    </div>

    <section class="mm mm-do" aria-label="Do's">
      <svg class="mm-lines" aria-hidden="true"></svg>
      <div class="mm-hub">
        <span class="mm-ring"></span><span class="mm-pulse"></span><span class="mm-pulse d2"></span>
        <div class="mm-disc"><?php include __DIR__ . '/_safety_art.php'; ?></div>
        <span class="mm-tag">&#10004; Do's</span>
      </div>
    <div class="mm-n p1 l" style="--i:0"><span class="mm-ic">&#10004;</span><div class="mm-b"><h4>Open area</h4><p>Light crackers outdoors, away from houses, vehicles and dry grass.</p></div></div>
    <div class="mm-n p2 r" style="--i:1"><span class="mm-ic">&#10004;</span><div class="mm-b"><h4>Adult supervision</h4><p>Children must always be with an adult while bursting crackers.</p></div></div>
    <div class="mm-n p3 l" style="--i:2"><span class="mm-ic">&#10004;</span><div class="mm-b"><h4>Keep water ready</h4><p>Keep a bucket of water or sand nearby for any mishap.</p></div></div>
    <div class="mm-n p4 r" style="--i:3"><span class="mm-ic">&#10004;</span><div class="mm-b"><h4>One at a time</h4><p>Light one cracker at arm&#39;s length and step back at once.</p></div></div>
    </section>

    <section class="mm mm-dont" aria-label="Don'ts">
      <svg class="mm-lines" aria-hidden="true"></svg>
      <div class="mm-hub">
        <span class="mm-ring"></span><span class="mm-pulse"></span><span class="mm-pulse d2"></span>
        <div class="mm-disc"><span class="mm-bigx">&#10006;</span><small>Be careful!</small></div>
        <span class="mm-tag">&#10006; Don'ts</span>
      </div>
    <div class="mm-n p1 l" style="--i:0"><span class="mm-ic">&#10006;</span><div class="mm-b"><h4>Never relight</h4><p>If a cracker does not burst, wait, then pour water on it. Never relight it.</p></div></div>
    <div class="mm-n p2 r" style="--i:1"><span class="mm-ic">&#10006;</span><div class="mm-b"><h4>Don&#39;t bend over</h4><p>Step back after lighting. Never lean over or look into a cracker.</p></div></div>
    <div class="mm-n p3 l" style="--i:2"><span class="mm-ic">&#10006;</span><div class="mm-b"><h4>Don&#39;t carry in pockets</h4><p>Never keep crackers in your pockets or hold a lit one in your hand.</p></div></div>
    <div class="mm-n p4 r" style="--i:3"><span class="mm-ic">&#10006;</span><div class="mm-b"><h4>No kids alone</h4><p>Small children must never light crackers on their own.</p></div></div>
    <div class="mm-n p5 bot" style="--i:4"><span class="mm-ic">&#10006;</span><div class="mm-b"><h4>Don&#39;t wear loose clothes</h4><p>Loose or synthetic clothes catch fire quickly. Wear cotton.</p></div></div>
    </section>
  </div>

  <section class="sf-emg rv">
    <h3>If someone is burnt</h3>
    <p>Run cool water over the burn for several minutes and get medical help straight away.</p>
    <a href="tel:108">&#9742; 108 Ambulance</a><a href="tel:101">&#9742; 101 Fire</a>
  </section>
</div>
<script>
(function () {
  var NS = 'http://www.w3.org/2000/svg';
  function pos(el, root) {
    var x = 0, y = 0, e = el;
    while (e && e !== root) { x += e.offsetLeft; y += e.offsetTop; e = e.offsetParent; }
    return { x: x + el.offsetWidth / 2, y: y + el.offsetHeight / 2, w: el.offsetWidth, h: el.offsetHeight };
  }
  function draw(mm) {
    var svg = mm.querySelector('.mm-lines');
    svg.innerHTML = '';
    if (window.innerWidth <= 820) { svg.style.display = 'none'; return; }
    svg.style.display = '';
    svg.setAttribute('width', mm.offsetWidth); svg.setAttribute('height', mm.offsetHeight);
    svg.setAttribute('viewBox', '0 0 ' + mm.offsetWidth + ' ' + mm.offsetHeight);
    var h = pos(mm.querySelector('.mm-disc'), mm), r = h.w / 2;
    mm.querySelectorAll('.mm-n').forEach(function (n, i) {
      var c = pos(n.querySelector('.mm-ic'), mm), d;
      if (n.classList.contains('bot')) {
        d = 'M' + h.x + ' ' + (h.y + r) + ' L' + c.x + ' ' + (c.y - c.h / 2);
      } else {
        var left = c.x < h.x, sx = h.x + (left ? -r : r), ex = c.x + (left ? c.w / 2 : -c.w / 2), mx = (sx + ex) / 2;
        d = 'M' + sx + ' ' + h.y + ' C' + mx + ' ' + h.y + ' ' + mx + ' ' + c.y + ' ' + ex + ' ' + c.y;
      }
      var p = document.createElementNS(NS, 'path');
      p.setAttribute('d', d); p.setAttribute('class', 'mm-line'); p.style.setProperty('--i', i);
      svg.appendChild(p);
      var L = p.getTotalLength(); p.style.strokeDasharray = L; p.style.strokeDashoffset = L;
    });
  }
  var maps = document.querySelectorAll('.mm'), rv = document.querySelectorAll('.rv');
  function all() { maps.forEach(draw); }
  all(); window.addEventListener('load', all);
  var t; window.addEventListener('resize', function () { clearTimeout(t); t = setTimeout(all, 150); });
  function watch(list, th) {
    if (!('IntersectionObserver' in window)) { list.forEach(function (e) { e.classList.add('in'); }); return; }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: th });
    list.forEach(function (el) { io.observe(el); });
  }
  watch(maps, 0.25); watch(rv, 0.15);
})();
</script>
<?php site_foot();
