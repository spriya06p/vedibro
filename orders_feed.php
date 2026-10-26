<?php
// Live orders feed for Excel (Data > Get Data > From Web).
// Each order is a block of rows: customer details only on the FIRST row,
// one product per row, and the totals on the LAST row of the block.
// Protected by a secret key in the URL.
const FEED_KEY = 'VediBro-Sivakasi-7hT4nQ9xK2mLp8';   // <-- put YOUR key here (the one you already use)

require_once __DIR__ . '/config.php';
$key = (string)($_GET['key'] ?? '');
if (FEED_KEY === 'CHANGE-THIS-TO-A-LONG-RANDOM-TEXT-9xK2mQ7pL4' || !hash_equals(FEED_KEY, $key)) {
    http_response_code(403); exit('Forbidden');
}
function safe($v) { $v = (string)$v; return ($v !== '' && strpos('=+-@', $v[0]) !== false) ? "'" . $v : $v; }

$pdo = db();
$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
$items = [];
foreach ($pdo->query("SELECT * FROM order_items ORDER BY order_id, id") as $it) $items[$it['order_id']][] = $it;

header('Content-Type: text/csv; charset=utf-8');
header('Cache-Control: no-store');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");
fputcsv($out, ['Order No','Date','Customer','Mobile','Email','State','City','Address','Status',
               'Product Code','Product Name','Qty','Discount Price','Amount',
               'MRP Total','Discount','Sub Total','Packing','Round Off','Grand Total']);

$blankCust = ['','','','','','','','',''];
foreach ($orders as $o) {
    $cust = [safe($o['order_no']), date('d-m-Y h:i A', strtotime($o['created_at'])), safe($o['customer_name']), preg_replace('/\D/', '', $o['phone']),
             safe($o['email']), safe($o['state']), safe($o['city']), safe(str_replace(["\r","\n"], ' ', $o['address'])), $o['status']];
    $totals = [$o['net_total'], $o['savings'], $o['subtotal'], $o['packing'], $o['round_off'], $o['overall_total']];
    $noTotals = ['','','','','',''];
    $list = $items[$o['id']] ?? [];
    if (!$list) { fputcsv($out, array_merge($cust, ['','','','',''], $totals)); continue; }
    $n = count($list);
    foreach ($list as $i => $p) {
        fputcsv($out, array_merge(
            $i === 0 ? $cust : $blankCust,
            [safe($p['product_code']), safe($p['product_name']), $p['quantity'], $p['unit_price'], $p['line_total']],
            $i === $n - 1 ? $totals : $noTotals));
    }
}
fclose($out);