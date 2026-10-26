/* Banner swipe carousel: no auto slide, no arrows. Swipe on phone, drag / trackpad on laptop, dots at bottom centre. */
(function () {
  var css =
    '.banner.bn-swipe{position:relative;overflow:hidden}' +
    '.bn-track{display:flex;overflow-x:auto;overflow-y:hidden;scroll-snap-type:x mandatory;scrollbar-width:none;-ms-overflow-style:none;-webkit-overflow-scrolling:touch;overscroll-behavior-x:contain;cursor:grab}' +
    '.bn-track::-webkit-scrollbar{display:none}' +
    '.bn-track.drag{scroll-snap-type:none;cursor:grabbing;user-select:none;-webkit-user-select:none}' +
    '.bn-track picture{flex:0 0 100%;min-width:0;scroll-snap-align:start;scroll-snap-stop:always}' +
    '.bn-track picture img{display:block!important;width:100%;-webkit-user-drag:none;user-select:none;-webkit-user-select:none}' +
    '.bn-dots{position:absolute;left:0;right:0;bottom:10px;display:flex;justify-content:center;gap:9px;z-index:3;pointer-events:none}' +
    '.bn-dots button{pointer-events:auto;width:10px;height:10px;padding:0;border:0;border-radius:50%;background:rgba(255,255,255,.5);box-shadow:0 0 4px rgba(0,0,0,.55);cursor:pointer;transition:transform .25s,background .25s}' +
    '.bn-dots button.on{background:#fff;transform:scale(1.3)}';
  var st = document.createElement('style'); st.textContent = css; document.head.appendChild(st);

  function init(box) {
    if (!box || box.getAttribute('data-swipe')) return;
    var pics = [].slice.call(box.querySelectorAll('picture'));
    if (!pics.length) return;
    box.setAttribute('data-swipe', '1');
    [].slice.call(box.querySelectorAll('.ba')).forEach(function (b) { b.parentNode.removeChild(b); });   // no arrows

    var track = document.createElement('div'); track.className = 'bn-track';
    pics.forEach(function (p) {
      track.appendChild(p);
      [].slice.call(p.querySelectorAll('img')).forEach(function (im) { im.classList.remove('off'); im.draggable = false; });
    });
    box.insertBefore(track, box.firstChild);
    box.className += ' bn-swipe';
    var dots = document.createElement('div'); dots.className = 'bn-dots'; box.appendChild(dots);

    var slides = [], idx = 0, raf = 0;
    function visible() { return pics.filter(function (p) { return p.offsetWidth > 0; }); }
    function mark() {
      [].slice.call(dots.children).forEach(function (d, k) { d.className = k === idx ? 'on' : ''; });
    }
    function goTo(k, instant) {
      k = Math.max(0, Math.min(slides.length - 1, k));
      track.scrollTo({ left: k * track.clientWidth, behavior: instant ? 'auto' : 'smooth' });
      idx = k; mark();
    }
    function build() {
      slides = visible(); dots.innerHTML = '';
      dots.style.display = slides.length > 1 ? 'flex' : 'none';
      slides.forEach(function (s, k) {
        var b = document.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', 'Slide ' + (k + 1));
        b.onclick = function () { goTo(k); }; dots.appendChild(b);
      });
      idx = Math.min(idx, Math.max(0, slides.length - 1));
      mark(); track.scrollLeft = idx * track.clientWidth;
    }
    track.addEventListener('scroll', function () {
      if (raf) return;
      raf = requestAnimationFrame(function () {
        raf = 0;
        var w = track.clientWidth || 1, k = Math.round(track.scrollLeft / w);
        if (k !== idx) { idx = Math.max(0, Math.min(slides.length - 1, k)); mark(); }
      });
    }, { passive: true });

    /* mouse drag for laptop (touch uses normal swipe) */
    var down = false, sx = 0, sl = 0;
    track.addEventListener('mousedown', function (e) {
      if (e.button !== 0 || slides.length < 2) return;
      down = true; sx = e.pageX; sl = track.scrollLeft; track.className += ' drag';
    });
    window.addEventListener('mousemove', function (e) { if (down) track.scrollLeft = sl - (e.pageX - sx); });
    window.addEventListener('mouseup', function (e) {
      if (!down) return; down = false;
      track.className = track.className.replace(' drag', '');
      var dx = e.pageX - sx, k = Math.round(sl / (track.clientWidth || 1));
      if (dx < -40) k++; else if (dx > 40) k--;
      goTo(k);
    });

    window.addEventListener('resize', build);
    window.addEventListener('load', build);
    build();
  }

  [].slice.call(document.querySelectorAll('section.banner')).forEach(init);
})();
