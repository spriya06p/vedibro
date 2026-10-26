<?php
require_once 'config.php';
require_once '_theme.php';
require_once '_highlight.php';
$pdo = db();
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order,name")->fetchAll();
$products = $pdo->query("SELECT p.*, c.discount_percent FROM products p JOIN categories c ON c.id=p.category_id WHERE p.active=1 ORDER BY c.sort_order, CAST(p.code AS UNSIGNED), p.code")->fetchAll();
$states = $pdo->query("SELECT name,min_order FROM states WHERE active=1 ORDER BY name")->fetchAll();
$byCat = [];
foreach ($products as $p) $byCat[$p['category_id']][] = $p;
$pack = (float)setting('packing_percent', 0);
$maxDisc = 0; foreach ($categories as $c) $maxDisc = max($maxDisc, (float)$c['discount_percent']);
$saleText = defined('SALE_TEXT') ? SALE_TEXT : 'Diwali sale is open now. Order early to get the best discounts & offers!';
$phone2 = defined('SHOP_PHONE2') ? SHOP_PHONE2 : '';
$email  = defined('SHOP_EMAIL') ? SHOP_EMAIL : '';
$isPrice = ($_GET['view'] ?? '') === 'price';   // Price List link = index.php?view=price
$banners = $isPrice ? (glob(__DIR__.'/assets/pricelist-banner*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: []) : [];
if (!$banners) $banners = glob(__DIR__.'/assets/banner*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
$logo = site_logo_url();
$social = array_filter(['f'=>defined('SOCIAL_FB')?SOCIAL_FB:'','in'=>defined('SOCIAL_IG')?SOCIAL_IG:'','▶'=>defined('SOCIAL_YT')?SOCIAL_YT:'']);
$phoneIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="#ffd21f"><path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11 11 0 0 0 3.6.6 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.6 3.6a1 1 0 0 1-.25 1z"/></svg>';
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e(SHOP_NAME)?></title>
<link rel="stylesheet" href="assets/extra.css"><link rel="stylesheet" href="assets/store.css"><?=theme_store_link()?><link rel="stylesheet" href="assets/header.css?v=<?=@filemtime(__DIR__.'/assets/header.css')?>"><?=highlight_css_link()?><style>
@media(max-width:760px){
  #banner .ba{display:none!important}
  header.sh .sh-banner{display:none!important}
  #banner picture.nomob{display:none!important}
  #banner:not(.home-ban):not(.has-mob){display:none!important}
}
@media(min-width:761px){
  #banner picture.mobonly{display:none!important}
  #banner.nodesk{display:none!important}
}
</style></head>
<body>
<?=highlight_bar_html()?>

<?php require_once '_header.php'; site_header($isPrice ? 'products' : 'home', false, !$isPrice); ?>

<?php
$mobDir = $isPrice ? 'pricelist/' : '';   /* Home: assets/mobile/   Price List: assets/mobile/pricelist/ */
$mobFiles = glob(__DIR__ . '/assets/mobile/' . $mobDir . '*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [];
usort($mobFiles, 'strnatcasecmp');
$n = max(count($banners), count($mobFiles));
?>
<section class="banner<?= $isPrice ? '' : ' home-ban' ?><?= ($isPrice && $mobFiles) ? ' has-mob' : '' ?><?= (!$banners && $mobFiles) ? ' nodesk' : '' ?>" id="banner">
  <?php if ($n): for ($i = 0; $i < $n; $i++):
      $d = $banners[$i] ?? null; $m = $mobFiles[$i] ?? null;
      $pc = trim((!$d ? 'mobonly ' : '') . (!$m && $mobFiles ? 'nomob' : ''));
      $mUrl = $m ? 'assets/mobile/' . $mobDir . e(rawurlencode(basename($m))) . '?v=' . filemtime($m) : '';
      $src = $d ? 'assets/' . e(basename($d)) : $mUrl;
?><picture<?=$pc ? ' class="' . $pc . '"' : ''?>><?php if ($m): ?><source media="(max-width:760px)" srcset="<?=$mUrl?>"><?php endif; ?><img class="slide<?=$i?' off':''?>" src="<?=$src?>" alt=""></picture><?php endfor; ?>
  <?php if ($n > 1): ?><button type="button" class="ba prev" aria-label="Previous">‹</button><button type="button" class="ba next" aria-label="Next">›</button><?php endif; ?>
  <?php else: ?><div class="banner-fallback"><div><small>DIWALI SALE</small><b><?=$maxDisc>0 ? 'DISCOUNT '.rtrim(rtrim(number_format($maxDisc,2),'0'),'.').'%' : 'BEST PRICES'?></b></div><div class="ta">தீபாவளி கொண்டாட்டம்</div></div><?php endif; ?>
</section>
<script src="assets/swipe.js?v=<?=@filemtime(__DIR__.'/assets/swipe.js')?>"></script>

<style>
/* Phones: Menu button sits on the left of Category, inside the toolbar */
.mnav{display:none}
@media(max-width:900px){
  .sh-btn,.sh-nav{display:none!important}
  .mnav{display:block}
  .mnav>.catbtn{background:#fff;color:var(--purple2)}
  .mitem{display:block;padding:12px 16px;color:var(--ink);text-decoration:none;border-bottom:1px solid #eee;background:#fff;font-size:.95rem}
  .mitem.active{background:var(--cream2);color:var(--purple2);font-weight:700}
  .tool-left .catbtn{padding:0 11px;font-size:.85rem;white-space:nowrap}
}
</style>
<div class="toolbar" id="toolbar">
  <div class="tool-left">
    <div class="dropdown mnav">
      <button type="button" class="catbtn" id="mMenuBtn" aria-expanded="false">&#9776; Menu</button>
      <div class="dropmenu" id="mMenu" hidden>
        <a class="mitem<?=$isPrice ? '' : ' active'?>" href="index.php">Home</a>
        <a class="mitem" href="about.php">About Us</a>
        <a class="mitem<?=$isPrice ? ' active' : ''?>" href="index.php?view=price#products">Price List</a>
        <a class="mitem" href="safetytips.php">Safety Tips</a>
        <a class="mitem" href="contact.php">Contact Us</a>
      </div>
    </div>
    <div class="dropdown">
      <button type="button" class="catbtn" id="catBtn">Category ▾</button>
      <div class="dropmenu" id="catMenu" hidden>
        <button type="button" class="catitem active" data-category="all">All Categories</button>
        <?php foreach ($categories as $c): if (empty($byCat[$c['id']])) continue; ?><button type="button" class="catitem" data-category="<?=e($c['id'])?>"><?=e($c['name'])?></button><?php endforeach; ?>
      </div>
    </div>
    <input id="search" class="search" placeholder="Search for an item">
  </div>
  <div class="totals-pills">
    <span>Net Total : <b id="netTotal">₹0</b></span>
    <span>You Save: <b id="savings">₹0</b></span>
    <span>Overall Total : <b id="subTotal">₹0</b></span>
  </div>
  <button type="button" class="cartbtn open-checkout" aria-label="Confirm estimate">
    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.5 12h11L21 7H6"/></svg>
    <i id="cartCount">0</i>
  </button>
</div>

<main id="products" class="tablewrap">
<table class="ptable">
  <thead><tr><th>Image</th><th>Code</th><th>Product Name</th><th>Content</th><th>Actual Price</th><th>Discount Price</th><th>Quantity</th><th>Total</th></tr></thead>
  <tbody>
  <?php foreach ($categories as $c): if (empty($byCat[$c['id']])) continue; ?>
    <tr class="cat-head" data-category="<?=e($c['id'])?>"><td colspan="8"><?=e(strtoupper($c['name']))?> (<?=e(strtoupper(discount_label($c['discount_percent'])))?>)</td></tr>
    <?php foreach ($byCat[$c['id']] as $p): $sell = sell_price($p['actual_price'], $p['discount_percent']); ?>
    <tr class="product-row" data-category="<?=e($p['category_id'])?>" data-search="<?=e(strtolower($p['name'].' '.$p['code']))?>">
      <td class="c-img"><?php if ($p['image']): ?><img src="uploads/<?=e($p['image'])?>" alt="" loading="lazy"><?php else: ?><span class="noimg">✦</span><?php endif; ?></td>
      <td class="c-code"><?=e($p['code'])?></td>
      <td class="c-name"><?=e($p['name'])?></td>
      <td class="c-content"><?=e($p['content'])?></td>
      <td class="c-actual"><?php if ($sell < $p['actual_price']): ?><del><?=e(rtrim(rtrim(number_format($p['actual_price'],2,'.',''),'0'),'.'))?></del><?php else: ?><span class="dash">-</span><?php endif; ?><span class="per"><?=e($p['content'])?></span></td>
      <td class="c-price"><?=e(rtrim(rtrim(number_format($sell,2,'.',''),'0'),'.'))?><span class="per"> / <?=e($p['content'])?></span></td>
      <td class="c-qty"><input class="qty" type="number" min="0" max="9999" step="1" inputmode="numeric" placeholder="Qty" data-id="<?=e($p['id'])?>" data-price="<?=e($sell)?>" data-actual="<?=e($p['actual_price'])?>"></td>
      <td class="c-total"><span class="line-total">₹0.00</span></td>
    </tr>
    <?php endforeach; ?>
  <?php endforeach; ?>
  </tbody>
</table>
</main>

<div class="confirm-wrap"><button type="button" class="btn open-checkout confirm-bottom">Please Check Your Order</button></div>

<div class="modal" id="checkoutModal" hidden><div class="modal-box">
  <button type="button" class="closex" id="closeX" aria-label="Close">✕</button>
  <h2>Your Details</h2>
  <p class="minnote"><b>Min. order amount:</b> <?php foreach ($states as $s): ?><span><?=e($s['name'])?> ₹<?=number_format($s['min_order'])?></span> <?php endforeach; ?></p>
  <form id="checkoutForm" data-packing="<?=e($pack)?>" data-otp="<?=OTP_MODE === 'off' ? '0' : '1'?>" novalidate>
    <?=csrf_field()?>
    <label>State *<select name="state" id="state" required><option value="">Select State</option>
      <?php foreach ($states as $s): ?><option value="<?=e($s['name'])?>" data-min="<?=e($s['min_order'])?>"><?=e($s['name'])?></option><?php endforeach; ?></select></label>
    <label>City<input name="city" maxlength="100"></label>
    <label>Name *<input name="customer_name" required maxlength="150"></label>
    <label>Mobile No *<input name="phone" required inputmode="numeric" maxlength="14" placeholder="10-digit mobile"></label>
    <label>Email<input name="email" type="email" maxlength="150"></label>
    <label>Address *<textarea name="address" rows="3" required></textarea></label>
    <div class="review totals">
      <div><span>Sub Total</span><b id="mSub">₹0.00</b></div>
      <div><span>Min. Order Amount</span><b id="mMin">-</b></div>
      <div><span>Packing Charges (<?=e($pack)?>%)</span><b id="mPack">₹0.00</b></div>
      <div><span>Round Off</span><b id="mRound">₹0.00</b></div>
      <div><strong>Overall Amount</strong><strong id="mTotal">₹0.00</strong></div>
    </div>
    <p class="error" id="formError" hidden></p>
    <div class="modal-actions"><button type="button" class="btn ghost" id="closeCheckout">Back</button><button type="submit" class="btn" id="submitBtn">Submit</button></div>
  </form>
  <div id="otpBox" hidden>
    <h3>OTP Verification</h3>
    <p>We have sent an OTP to <b id="otpPhone"></b></p>
    <input id="otpInput" inputmode="numeric" maxlength="6" placeholder="Enter OTP" autocomplete="one-time-code">
    <p class="error" id="otpError" hidden></p>
    <div class="modal-actions"><button type="button" class="btn" id="verifyOtp">Submit OTP</button></div>
    <div class="otp-links"><button type="button" class="linkbtn" id="resendOtp">Resend OTP</button><button type="button" class="linkbtn" id="cancelOtp">← Change details</button><button type="button" class="linkbtn red" id="cancelOrder">Cancel order</button></div>
  </div>
</div></div>

<?php require_once '_footer.php'; site_footer(); ?>
<div class="mobilebar"><span><b id="mbItems">0</b> items . <b id="mbTotal">₹0.00</b></span><button type="button" class="open-checkout mb-view">View Cart <i id="mbCount">0</i></button></div>
<script src="assets/app.js?v=<?=@filemtime(__DIR__.'/assets/app.js')?>"></script>
<script>
(function () {
  var b = document.getElementById('mMenuBtn'), m = document.getElementById('mMenu'), c = document.getElementById('catMenu'), cb = document.getElementById('catBtn');
  if (!b || !m) return;
  b.addEventListener('click', function (e) { e.stopPropagation(); if (c) c.hidden = true; m.hidden = !m.hidden; b.setAttribute('aria-expanded', m.hidden ? 'false' : 'true'); });
  if (cb) cb.addEventListener('click', function () { m.hidden = true; });
  document.addEventListener('click', function () { m.hidden = true; b.setAttribute('aria-expanded', 'false'); });
})();
</script>
</body></html>