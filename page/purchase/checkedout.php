<?php
require_once '../../connect.php';
require_once '../../_base.php';

$cart = get_cart();

$_db->beginTransaction();

if ($_POST) {
    // Insert into order table (fix: use order_date, tquantity, tprice)
    $now = date('Y-m-d H:i:s'); // Malaysia time
    $stm = $_db->prepare('INSERT INTO `order` (order_date, user_id) VALUES (?, ?)');
    $stm->execute([$now, $user->user_id]);
    $order_id = $_db->lastInsertId();

    $order = $order_id; // Use the order ID that was just inserted


    // Insert into orderlist table
    $stm = $_db->prepare('
        INSERT INTO orderlist (order_id, product_id, price_per_unit, quantity, subtotal)
        VALUES (?, ?, (SELECT price FROM product WHERE product_id = ?), ?, 
        (SELECT price FROM product WHERE product_id = ?) * ?)
    ');
    foreach ($cart as $product_id => $unit) {
        $stm->execute([$order, $product_id, $product_id, $unit, $product_id, $unit]);
    }

    // Update order's tquantity and tprice
    $stm = $_db->prepare('
        UPDATE `order`
        SET tquantity = (SELECT SUM(quantity) FROM orderlist WHERE order_id = ?),
            tprice = (SELECT SUM(subtotal) FROM orderlist WHERE order_id = ?)
        WHERE order_id = ?
    ');
    $stm->execute([$order, $order, $order]);

    $_db->commit();

    // Clear cart and redirect
    set_cart();
    temp('info', 'Order Placed, Please Check in Order History');
    header("Location: ../../index.php?user_id=$user->user_id");
    exit;

} 

include '../background/zero1.php';
?>

