<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
csrf_check();
$b = build_order($_POST);
if (empty($b['ok'])) json_out(['ok'=>false,'error'=>$b['error']]);
$c = $b['c'];
if (OTP_MODE !== 'off' && ($_SESSION['otp_ok'] ?? '') !== $c['phone']) json_out(['ok'=>false,'error'=>'Mobile number is not verified.']);

$pdo = db();
try {
    $pdo->beginTransaction();
    for ($try = 0; ; $try++) {   // retry on the (very unlikely) duplicate order number
        $no = ORDER_PREFIX.date('ymd').strtoupper(bin2hex(random_bytes(3)));
        try {
            $pdo->prepare("INSERT INTO orders(order_no,state,city,customer_name,phone,email,address,net_total,savings,subtotal,packing_percent,packing,round_off,overall_total) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
                ->execute([$no,$c['state'],$c['city'],$c['customer_name'],$c['phone'],$c['email'],$c['address'],$b['net'],$b['save'],$b['sub'],$b['pp'],$b['packing'],$b['round'],$b['overall']]);
            break;
        } catch (PDOException $ex) { if ($try >= 3 || $ex->errorInfo[1] != 1062) throw $ex; }
    }
    $oid = (int)$pdo->lastInsertId();
    $s = $pdo->prepare("INSERT INTO order_items(order_id,product_id,product_code,product_name,content,actual_price,unit_price,quantity,line_total) VALUES(?,?,?,?,?,?,?,?,?)");
    foreach ($b['items'] as $i) $s->execute([$oid,$i['p']['id'],$i['p']['code'],$i['p']['name'],$i['p']['content'],$i['p']['actual_price'],$i['unit'],$i['qty'],$i['line']]);
    $pdo->commit();
} catch (Throwable $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_out(['ok'=>false,'error'=>'Could not save the order. Please try again.'], 500);
}
unset($_SESSION['otp_ok'], $_SESSION['otp']);
session_regenerate_id(true);
$_SESSION['confirm_order_id'] = $oid;      // one-time: consumed by confirm.php
json_out(['ok'=>true,'redirect'=>'confirm.php']);