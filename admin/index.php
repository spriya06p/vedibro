<?php
require_once '_inc.php';
$sts = ['New','Contacted','Confirmed','Completed','Cancelled'];
$status = $_GET['status'] ?? '';
$q = trim($_GET['q'] ?? '');
$where = []; $args = [];
if (in_array($status, $sts, true)) { $where[] = 'status=?'; $args[] = $status; }
if ($q !== '') { $where[] = '(order_no LIKE ? OR customer_name LIKE ? OR phone LIKE ?)'; $l = "%$q%"; array_push($args, $l, $l, $l); }
$from = $_GET['from'] ?? ''; $to = $_GET['to'] ?? '';
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) { $where[] = 'DATE(created_at) >= ?'; $args[] = $from; }
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   { $where[] = 'DATE(created_at) <= ?'; $args[] = $to; }
$sql = 'SELECT id,order_no,customer_name,phone,state,overall_total,status,created_at FROM orders'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY id DESC LIMIT 200';
$st = db()->prepare($sql); $st->execute($args); $rows = $st->fetchAll();
admin_head('Orders'); flash(); ?>
<form method="get" class="card" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
  <input name="q" value="<?=e($q)?>" placeholder="Order no, name or mobile">
  <select name="status"><option value="">All status</option>
    <?php foreach ($sts as $s): ?><option <?=$status===$s?'selected':''?>><?=$s?></option><?php endforeach; ?></select>
  <label class="dt">Start date<span class="dtw" data-ph="dd-mm-yyyy"><input type="date" name="from" value="<?=e($_GET['from'] ?? '')?>"></span></label>
  <label class="dt">End date<span class="dtw" data-ph="dd-mm-yyyy"><input type="date" name="to" value="<?=e($_GET['to'] ?? '')?>"></span></label>
  <button>Filter</button>
  <button type="submit" formaction="export_orders.php" name="type" value="orders">Download Excel</button>
</form>
<style>
.dt{margin:0;font-size:.8rem;font-weight:600;color:#556677;flex:1 1 150px;max-width:220px}
.dtw{position:relative;display:block}
.dtw input{margin-top:4px}
.dtw::after{content:attr(data-ph);position:absolute;left:13px;top:calc(50% + 2px);transform:translateY(-50%);color:#7a8a99;font-weight:400;font-size:1rem;pointer-events:none}
.dtw.has::after{display:none}
.dtw:not(.has) input{color:transparent}
.dtw:not(.has) input::-webkit-datetime-edit{color:transparent}
</style>
<script>
document.querySelectorAll('.dtw').forEach(function (w) {
  var i = w.querySelector('input');
  function sync() { w.classList.toggle('has', !!i.value); }
  i.addEventListener('input', sync); i.addEventListener('change', sync); sync();
});
</script>
<section class="card" style="overflow-x:auto">
<table>
  <thead><tr><th>Order</th><th>Date</th><th>Customer</th><th>Mobile</th><th>State</th><th>Total</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr><td><?=e($r['order_no'])?></td><td><?=e(date('d M Y, h:i A', strtotime($r['created_at'])))?></td>
        <td><?=e($r['customer_name'])?></td><td><?=e($r['phone'])?></td><td><?=e($r['state'])?></td>
        <td><?=money($r['overall_total'])?></td><td><?=e($r['status'])?></td>
        <td><a href="order.php?id=<?=(int)$r['id']?>">View</a></td></tr>
  <?php endforeach; if (!$rows): ?><tr><td colspan="8">No orders found.</td></tr><?php endif; ?>
  </tbody>
</table>
</section>
<?php admin_foot();