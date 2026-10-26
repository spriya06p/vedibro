<?php
require_once 'config.php';
require_once __DIR__.'/_theme.php';
$oid = (int)($_SESSION['confirm_order_id'] ?? 0);
unset($_SESSION['confirm_order_id']);            // consumed on FIRST load -> refresh / revisit goes home
if (!$oid) { header('Location: index.php'); exit; }
$pdo = db();
$s = $pdo->prepare("SELECT * FROM orders WHERE id=?"); $s->execute([$oid]); $o = $s->fetch();
if (!$o) { header('Location: index.php'); exit; }
$s = $pdo->prepare("SELECT * FROM order_items WHERE order_id=? ORDER BY id"); $s->execute([$oid]); $items = $s->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Order Submitted</title><link rel="stylesheet" href="assets/extra.css"><link rel="stylesheet" href="assets/store.css"><link rel="stylesheet" href="assets/confirm.css"><?=theme_store_link()?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script></head>
<body><div class="checkout"><div class="checkout-card success">
<div class="success-icon">✓</div><span class="eyebrow">ORDER RECEIVED</span>
<h1>Thank you, <?=e($o['customer_name'])?>.</h1>
<p>Your order has been submitted successfully. Your order number is <b>#<?=e($o['order_no'])?></b>.</p>
<p style="font-size:1.3rem;margin:10px 0">Overall Amount: <b>₹<?=e(money($o['overall_total']))?></b></p>
<p class="warn">Please download your order details PDF now. If you refresh this page or leave the site, you will not be able to download it again. Your order is already submitted, and we will contact you within 24 hours to confirm.</p>
<p class="warn ta">உங்கள் ஆர்டர் வெற்றிகரமாக சமர்ப்பிக்கப்பட்டது. ஆர்டர் விவரங்களை இப்போதே PDF ஆக பதிவிறக்கம் செய்யுங்கள். இந்தப் பக்கத்தை refresh செய்தாலோ, வெளியே சென்றாலோ மீண்டும் பதிவிறக்கம் செய்ய முடியாது. உங்கள் ஆர்டர் ஏற்கனவே சமர்ப்பிக்கப்பட்டுவிட்டது; 24 மணி நேரத்திற்குள் நாங்கள் உங்களைத் தொடர்பு கொள்வோம்.</p>
<button id="download" class="btn" type="button">Download Order PDF</button>
<div id="pdfArea" class="pdf-area"><?=order_sheet_html($o, $items)?></div>
<a href="index.php">Return to store</a>
</div></div>
<script>
const btn=document.getElementById('download');
function downloadPdf()
{window.scrollTo(0,0);
  if(typeof html2pdf==='undefined'){btn.textContent='PDF library not loaded (check internet) - tap to retry';return;}
  btn.disabled=true;btn.textContent='Preparing PDF...';
  html2pdf().set({margin:10,filename:<?=json_encode($o['order_no'].'.pdf')?>,html2canvas:{scale:2,scrollX:0,scrollY:0},jsPDF:{unit:'mm',format:'a4'}})
    .from(document.getElementById('pdfArea')).save()
    .then(()=>{btn.disabled=false;btn.textContent='Download Again';})
    .catch(()=>{btn.disabled=false;btn.textContent='Download failed - tap to retry';});
}
btn.onclick=downloadPdf;
window.addEventListener('pageshow',ev=>{if(ev.persisted)location.replace('index.php');});
</script>
<?php require_once '_footer.php'; site_footer(); ?>
</body></html>