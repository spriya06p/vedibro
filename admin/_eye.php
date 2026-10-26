<style>
.pwwrap{position:relative;margin-top:7px}
.pwwrap input{margin-top:0!important;padding-right:48px!important}
.pwwrap .eye{position:absolute;right:4px;top:50%;transform:translateY(-50%);width:40px;height:40px;padding:0;margin:0;border:0;background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#5b6b7a}
.pwwrap .eye:hover{color:#123}
</style>
<script>
(function () {
  var open = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
  var shut = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
  document.querySelectorAll('input[type=password]').forEach(function (inp) {
    var w = document.createElement('div'); w.className = 'pwwrap';
    inp.parentNode.insertBefore(w, inp); w.appendChild(inp);
    var b = document.createElement('button'); b.type = 'button'; b.className = 'eye';
    b.setAttribute('aria-label', 'Show password'); b.innerHTML = open; w.appendChild(b);
    b.addEventListener('click', function () {
      var show = inp.type === 'password';
      inp.type = show ? 'text' : 'password';
      b.innerHTML = show ? shut : open;
      b.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  });
})();
</script>
