<?php
require_once '../../connect.php';
require_once '../../_base.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $unit = $_POST['unit'];
    update_cart($product_id, $unit);

    $stm = $_db->prepare("SELECT * FROM product WHERE product_id = ?");
    $stm->execute([$product_id]);
    $product = $stm->fetch();

    $subtotal = $product->price * $unit;

    $cart = get_cart();
    $total = 0;
    $count = 0;
    foreach ($cart as $product_id => $qty) {
        $stm->execute([$product_id]);
        $p = $stm->fetch();
        $total += $p->price * $qty;
        $count += $qty;
    }

    echo json_encode([
        'subtotal' => number_format($subtotal, 2),
        'total' => number_format($total, 2),
        'count' => $count
    ]);
}
