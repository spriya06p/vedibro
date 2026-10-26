const $ = s => document.querySelector(s), $$ = s => [...document.querySelectorAll(s)];
const fmt = n => '₹' + Number(n).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
const rows = $$('.product-row'), heads = $$('.cat-head'), qtys = $$('.qty');
const form = $('#checkoutForm'), PACK = Number(form.dataset.packing || 0), OTP_ON = form.dataset.otp === '1';
let cat = 'all', term = '', tot = {net: 0, save: 0, sub: 0};

function calc() {
  let net = 0, sub = 0, used = 0;
  qtys.forEach(q => {
    const n = Math.min(9999, Math.max(0, parseInt(q.value || 0, 10) || 0));
    const line = n * Number(q.dataset.price);
    q.closest('.product-row').querySelector('.line-total').textContent = fmt(line); if (n > 0) used++;
    net += n * Number(q.dataset.actual); sub += line;
  });
  tot = {net, sub, save: net - sub};
  $('#cartCount').textContent = used; $('#mbItems').textContent = used; $('#mbCount').textContent = used; $('#mbTotal').textContent = fmt(sub); $('#netTotal').textContent = fmt(net); $('#savings').textContent = fmt(tot.save); $('#subTotal').textContent = fmt(sub);
}
function applyFilter() {
  rows.forEach(r => r.hidden = !((cat === 'all' || r.dataset.category === cat) && r.dataset.search.includes(term)));
  heads.forEach(h => h.hidden = !rows.some(r => r.dataset.category === h.dataset.category && !r.hidden));
}
qtys.forEach(q => q.addEventListener('input', calc));
$('#catBtn').onclick = e => { e.stopPropagation(); $('#catMenu').hidden = !$('#catMenu').hidden; };
document.addEventListener('click', () => { $('#catMenu').hidden = true; });
$$('.catitem').forEach(ch => ch.addEventListener('click', () => {
  $$('.catitem').forEach(x => x.classList.remove('active')); ch.classList.add('active');
  $('#catBtn').textContent = (ch.dataset.category === 'all' ? 'Category' : ch.textContent) + ' ▾';
  cat = ch.dataset.category; applyFilter();
}));
$('#menuBtn').onclick = () => $('#mainNav').classList.toggle('open');
$$('#mainNav a').forEach(x => x.addEventListener('click', () => $('#mainNav').classList.remove('open')));
$('#search').addEventListener('input', e => { term = e.target.value.toLowerCase().trim(); applyFilter(); });

// ---- checkout ----
const minOf = () => Number($('#state').selectedOptions[0]?.dataset.min || 0);
function summary() {
  const packing = Math.round(tot.sub * PACK) / 100, raw = tot.sub + packing, overall = Math.round(raw);
  $('#mSub').textContent = fmt(tot.sub); $('#mPack').textContent = fmt(packing);
  $('#mRound').textContent = fmt(overall - raw); $('#mTotal').textContent = fmt(overall);
  $('#mMin').textContent = minOf() ? fmt(minOf()) : '-';
  const need = minOf() - tot.sub;
  show($('#formError'), minOf() && need > 0 ? 'Minimum order for ' + $('#state').value + ' is ' + fmt(minOf()) + '. Please add products worth ' + fmt(need) + ' more.' : '');
}
const show = (el, msg) => { el.textContent = msg; el.hidden = !msg; if (msg) el.scrollIntoView({block: 'nearest'}); };
const openCheckout = () => {
  if (tot.sub <= 0) return alert('Please select at least one product.');
  summary(); show($('#formError'), ''); $('#otpBox').hidden = true; form.hidden = false; $('#checkoutModal').hidden = false;
};
$$('.open-checkout').forEach(b => b.addEventListener('click', openCheckout));
const resetModal = () => {           // back to the details form, buttons usable again
  $('#otpBox').hidden = true; form.hidden = false;
  $('#submitBtn').disabled = false; $('#verifyOtp').disabled = false;
  show($('#formError'), ''); show($('#otpError'), ''); $('#otpInput').value = '';
};
const closeModal = () => { $('#checkoutModal').hidden = true; resetModal(); };
$('#closeCheckout').onclick = closeModal;
$('#closeX').onclick = closeModal;
$('#cancelOtp').onclick = resetModal;                       // OTP screen -> edit details
$('#cancelOrder').onclick = closeModal;                     // OTP screen -> cancel everything
$('#checkoutModal').addEventListener('click', e => { if (e.target.id === 'checkoutModal') closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape' && !$('#checkoutModal').hidden) closeModal(); });
$('#state').addEventListener('change', summary);

function payload() {
  const fd = new FormData(form);
  qtys.forEach(q => { const n = parseInt(q.value || 0, 10) || 0; if (n > 0) fd.append('qty[' + q.dataset.id + ']', n); });
  return fd;
}
async function post(url, fd) {
  let txt = '';
  try {
    const r = await fetch(url, {method: 'POST', body: fd, credentials: 'same-origin'});
    txt = await r.text();
    const i = txt.indexOf('{"');
    return JSON.parse(i >= 0 ? txt.slice(i) : txt);
  } catch (e) {
    return {ok: false, error: 'Server error: ' + (txt.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 220) || 'no response from server')};
  }
}
function clientCheck() {
  const f = form.elements;
  if (!f.state.value) return 'Please select your state.';
  if (!f.customer_name.value.trim() || !f.address.value.trim()) return 'Please fill name and address.';
  if (!/^(\+?91|0)?[6-9]\d{9}$/.test(f.phone.value.replace(/[\s-]/g, ''))) return 'Enter a valid 10-digit mobile number.';
  if (tot.sub < minOf()) return 'Minimum order for ' + f.state.value + ' is ' + fmt(minOf()) + '.';
  return '';
}
async function placeOrder() {
  const res = await post('order.php', payload());
  if (res.ok) { location.href = res.redirect; return; }
  show($('#formError'), res.error); show($('#otpError'), res.error);
  $('#submitBtn').disabled = false; $('#verifyOtp').disabled = false;
}
async function sendOtp() {
  const res = await post('otp_send.php', payload());
  if (!res.ok) { show($('#formError'), res.error); $('#submitBtn').disabled = false; return false; }
  if (!res.otp) { placeOrder(); return true; }
  $('#otpPhone').textContent = res.phone; form.hidden = true; $('#otpBox').hidden = false; $('#otpInput').value = ''; show($('#otpError'), '');
  return true;
}
form.addEventListener('submit', async e => {
  e.preventDefault();
  const err = clientCheck(); if (err) return show($('#formError'), err);
  show($('#formError'), ''); $('#submitBtn').disabled = true;
  if (OTP_ON) sendOtp(); else placeOrder();
});
$('#resendOtp').onclick = async () => {
  const res = await post('otp_send.php', payload()); show($('#otpError'), res.ok ? 'OTP sent again.' : res.error);
};
$('#verifyOtp').onclick = async () => {
  const otp = $('#otpInput').value.trim(); if (!/^\d{6}$/.test(otp)) return show($('#otpError'), 'Enter the 6-digit OTP.');
  $('#verifyOtp').disabled = true;
  const fd = new FormData(); fd.append('csrf', form.elements.csrf.value); fd.append('otp', otp);
  const res = await post('otp_verify.php', fd);
  if (!res.ok) { show($('#otpError'), res.error); $('#verifyOtp').disabled = false; return; }
  placeOrder();
};
calc(); applyFilter();