<?php
require_once '_inc.php';
$id = (int)($_GET['id'] ?? 0);
const ORDER_STATUSES = ['New','Contacted','Confirmed','Completed','Cancelled'];

/** Recalculate every amount of an order from its items. The packing % stays as it was when booked. */
function recalc_order(PDO $pdo, int $oid): void {
    $s = $pdo->prepare("SELECT actual_price, line_total, quantity FROM order_items WHERE order_id=?");
    $s->execute([$oid]);
    $net = 0; $sub = 0;
    foreach ($s->fetchAll() as $r) { $net += $r['actual_price'] * $r['quantity']; $sub += $r['line_total']; }
    $net = round($net, 2); $sub = round($sub, 2);
    $p = $pdo->prepare("SELECT packing_percent FROM orders WHERE id=?");
    $p->execute([$oid]);
    $pp = (float)$p->fetchColumn();
    $packing = round($sub * $pp / 100, 2);
    $raw = $sub + $packing; $overall = round($raw);
    $pdo->prepare("UPDATE orders SET net_total=?, savings=?, subtotal=?, packing=?, round_off=?, overall_total=? WHERE id=?")
        ->execute([$net, round($net - $sub, 2), $sub, $packing, round($overall - $raw, 2), $overall, $oid]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $pdo = db();
    $chk = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE id=?"); $chk->execute([$id]);
    if (!$chk->fetchColumn()) { header('Location: index.php'); exit; }
    $act = $_POST['action'] ?? 'status';
    try {
        if ($act === 'status') {
            $new = $_POST['status'] ?? '';
            if (in_array($new, ORDER_STATUSES, true)) {
                $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$new, $id]);
                flash('Status updated.');
            }

        } elseif ($act === 'save') {                       // change quantities / remove items
            $pdo->beginTransaction();
            $qty = is_array($_POST['qty'] ?? null) ? $_POST['qty'] : [];
            $rm  = is_array($_POST['remove'] ?? null) ? $_POST['remove'] : [];
            if (isset($_POST['remove_item'])) $rm[(int)$_POST['remove_item']] = 1;   // Remove button clicked
            $cur = $pdo->prepare("SELECT id, unit_price FROM order_items WHERE order_id=?");
            $cur->execute([$id]);
            $upd = $pdo->prepare("UPDATE order_items SET quantity=?, line_total=? WHERE id=? AND order_id=?");
            $del = $pdo->prepare("DELETE FROM order_items WHERE id=? AND order_id=?");
            $left = 0;
            foreach ($cur->fetchAll() as $it) {
                $iid = (int)$it['id'];
                if (!isset($qty[$iid])) { $left++; continue; }
                $q = max(0, min(9999, (int)$qty[$iid]));
                if (isset($rm[$iid]) || $q <= 0) { $del->execute([$iid, $id]); continue; }
                $upd->execute([$q, round((float)$it['unit_price'] * $q, 2), $iid, $id]);
                $left++;
            }
            if ($left === 0) {
                $pdo->rollBack();
                flash('An order must keep at least one item. To drop the whole order, set its status to Cancelled.');
            } else {
                recalc_order($pdo, $id);
                $pdo->commit();
                flash(isset($_POST['remove_item']) ? 'Item removed. The totals and the PDF are updated.' : 'Items updated. The totals and the PDF now show the new items.');
            }

        } elseif ($act === 'add') {                        // add a product to the order
            $code = trim(explode('|', (string)($_POST['product'] ?? ''))[0]);
            $q = max(0, min(9999, (int)($_POST['new_qty'] ?? 0)));
            if ($code === '' || $q <= 0) {
                flash('Pick a product from the list and enter a quantity.');
            } else {
                $ps = $pdo->prepare("SELECT p.*, c.discount_percent FROM products p JOIN categories c ON c.id=p.category_id WHERE p.active=1 AND p.code=?");
                $ps->execute([$code]);
                $p = $ps->fetch();
                if (!$p) {
                    flash('Product not found. Please choose one from the list.');
                } else {
                    $pdo->beginTransaction();
                    $unit = sell_price($p['actual_price'], $p['discount_percent']);
                    $ex = $pdo->prepare("SELECT id, unit_price, quantity FROM order_items WHERE order_id=? AND product_id=?");
                    $ex->execute([$id, $p['id']]);
                    $row = $ex->fetch();
                    if ($row) {                            // already in the order: increase its quantity
                        $nq = min(9999, (int)$row['quantity'] + $q);
                        $pdo->prepare("UPDATE order_items SET quantity=?, line_total=? WHERE id=?")
                            ->execute([$nq, round((float)$row['unit_price'] * $nq, 2), $row['id']]);
                    } else {
                        $pdo->prepare("INSERT INTO order_items(order_id,product_id,product_code,product_name,content,actual_price,unit_price,quantity,line_total) VALUES(?,?,?,?,?,?,?,?,?)")
                            ->execute([$id, $p['id'], $p['code'], $p['name'], $p['content'], $p['actual_price'], $unit, $q, round($unit * $q, 2)]);
                    }
                    recalc_order($pdo, $id);
                    $pdo->commit();
                    flash('Added: ' . $p['name'] . ' x ' . $q . '. The totals and the PDF are updated.');
                }
            }
        }
    } catch (Throwable $ex) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('Could not update the order. Please try again.');
    }
    header("Location: order.php?id=$id"); exit;
}

$s = db()->prepare("SELECT * FROM orders WHERE id=?"); $s->execute([$id]); $o = $s->fetch();
if (!$o) { header('Location: index.php'); exit; }
$s = db()->prepare("SELECT * FROM order_items WHERE order_id=? ORDER BY id"); $s->execute([$id]); $items = $s->fetchAll();
$plist = db()->query("SELECT p.code, p.name, p.actual_price, c.discount_percent FROM products p JOIN categories c ON c.id=p.category_id WHERE p.active=1 ORDER BY c.sort_order, CAST(p.code AS UNSIGNED), p.code")->fetchAll();
admin_head('Order '.$o['order_no']); flash(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
.ie-qty{width:90px;display:inline-block;margin:0;padding:8px}
.ie-table td{vertical-align:middle}
.ie-rm{background:#fde2e2;color:#9a2020}.ie-rm:hover{background:#f9c9c9}
.ie-add{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end}
.ie-add label{margin:0;flex:1 1 260px}.ie-add label.q{flex:0 0 110px}
.muted{color:#5a6a85;margin-top:0}
.pdf-area tr{page-break-inside:avoid}
</style>

<section class="card"><div id="pdfArea" class="pdf-area"><?=order_sheet_html($o, $items)?></div>
<form method="post" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center"><?=csrf_field()?>
<input type="hidden" name="action" value="status">
<select name="status" style="width:auto"><?php foreach (ORDER_STATUSES as $st): ?><option <?=$o['status']===$st?'selected':''?>><?=$st?></option><?php endforeach; ?></select>
<button>Update Status</button>
<button type="button" id="pdfBtn" data-file="<?=e($o['order_no'].'.pdf')?>">Download PDF</button>
<a href="index.php">← Orders</a></form></section>

<section class="card">
  <h2 style="margin-top:0">Edit items in this order</h2>
  <p class="muted">Change a quantity and press <b>Save changes</b>, remove an item with its <b>Remove</b> button (it is deleted at once), or add a new one. Totals and the PDF above update automatically. Items already in the order keep the rate they were booked at; newly added items use today's rate.</p>

  <form method="post" id="itemsForm"><?=csrf_field()?>
    <input type="hidden" name="action" value="save">
    <button type="submit" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;padding:0;overflow:hidden">Save</button>
    <table class="ie-table">
      <thead><tr><th>Code</th><th>Product</th><th>Rate</th><th>Qty</th><th>Total</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($items as $i): ?>
        <tr data-rate="<?=e($i['unit_price'])?>">
          <td><?=e($i['product_code'])?></td>
          <td><?=e($i['product_name'])?></td>
          <td>₹<?=money($i['unit_price'])?></td>
          <td><input class="ie-qty" type="number" min="0" max="9999" step="1" inputmode="numeric" name="qty[<?=(int)$i['id']?>]" value="<?=(int)$i['quantity']?>"></td>
          <td class="lt">₹<?=money($i['line_total'])?></td>
          <td><button type="submit" name="remove_item" value="<?=(int)$i['id']?>" class="ie-rm" onclick="return confirm('Remove this item from the order?')">Remove</button></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <p>Items total (before packing): <b id="liveSub">₹<?=money($o['subtotal'])?></b></p>
    <button>Save changes</button>
  </form>

  <hr style="margin:26px 0;border:0;border-top:1px solid #E3ECF3">
  <h3 style="margin-top:0">Add an item</h3>
  <form method="post" class="ie-add"><?=csrf_field()?>
    <input type="hidden" name="action" value="add">
    <label>Product (search by code or name, then choose)
      <input type="search" id="pfilter" placeholder="Search e.g. 12 or Sparklers" autocomplete="off" style="margin-bottom:6px">
      <select name="product" id="psel" required>
        <option value="">-- Select product --</option>
        <?php foreach ($plist as $p): ?><option value="<?=e($p['code'])?> | <?=e($p['name'])?>"><?=e($p['code'])?> | <?=e($p['name'])?> (₹<?=e(money(sell_price($p['actual_price'], $p['discount_percent'])))?>)</option><?php endforeach; ?>
      </select></label>
    <label class="q">Qty
      <input name="new_qty" type="number" min="1" max="9999" step="1" inputmode="numeric" value="1" required></label>
    <button>Add to order</button>
  </form>
  <script>
  (function () {
    var f = document.getElementById('pfilter'), sel = document.getElementById('psel');
    var all = Array.prototype.slice.call(sel.options).slice(1).map(function (o) { return { v: o.value, t: o.text }; });
    f.addEventListener('input', function () {
      var q = f.value.toLowerCase().trim(), keep = sel.value;
      sel.innerHTML = '<option value="">-- Select product --</option>';
      all.forEach(function (x) {
        if (q === '' || x.t.toLowerCase().indexOf(q) !== -1) {
          var o = document.createElement('option'); o.value = x.v; o.text = x.t; if (x.v === keep) o.selected = true; sel.appendChild(o);
        }
      });
      if (q !== '' && sel.options.length === 2) sel.selectedIndex = 1;   // only one match: select it
    });
  })();
  </script>
</section>

<script>
(function () {
  var form = document.getElementById('itemsForm'), out = document.getElementById('liveSub');
  var fmt = function (n) { return '₹' + n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };
  function recalc() {
    var sum = 0;
    form.querySelectorAll('tbody tr').forEach(function (tr) {
      var q = Math.max(0, parseInt(tr.querySelector('.ie-qty').value, 10) || 0), t = Math.round(parseFloat(tr.dataset.rate) * q * 100) / 100;
      tr.querySelector('.lt').textContent = fmt(t);
      sum += t;
    });
    out.textContent = fmt(Math.round(sum * 100) / 100);
  }
  form.addEventListener('input', recalc);
})();
</script>
<script>
// Download PDF. html2canvas draws a BLANK page when the browser page is scrolled down, so always capture from the top.
(function () {
  var btn = document.getElementById('pdfBtn'), area = document.getElementById('pdfArea');
  var FALLBACK = 'https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js';
  function ensureLib(cb) {
    if (window.html2pdf) return cb(true);
    var s = document.createElement('script');
    s.src = FALLBACK; s.onload = function () { cb(!!window.html2pdf); }; s.onerror = function () { cb(false); };
    document.head.appendChild(s);
  }
  btn.addEventListener('click', function () {
    var label = btn.textContent, x = window.pageXOffset, y = window.pageYOffset;
    function reset() { btn.disabled = false; btn.textContent = label; window.scrollTo(x, y); }
    btn.disabled = true; btn.textContent = 'Preparing PDF...';
    ensureLib(function (ok) {
      if (!ok) { reset(); alert('PDF library could not be loaded. Please check your internet connection and try again.'); return; }
      window.scrollTo(0, 0);
      html2pdf().set({
        margin: 10,
        filename: btn.getAttribute('data-file') || 'order.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, scrollX: 0, scrollY: 0, backgroundColor: '#ffffff', useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: { mode: ['css', 'legacy'], avoid: ['tr', '.sheet-tot'] }
      }).from(area).save().then(reset, function () { reset(); alert('Could not create the PDF. Please try again.'); });
    });
  });
})();
</script>
<?php admin_foot();