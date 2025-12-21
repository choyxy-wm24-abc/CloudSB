<?php 

require_once '../connect.php';
require_once '../_base.php';

if (is_post()) {
    $product_id = isset($_GET['p_id']) ? $_GET['p_id'] : null;

    $stm = $_db->prepare('SELECT image FROM product WHERE product_id = ?');
    $stm->execute([$product_id]);
    $image = $stm->fetchColumn();
    unlink("../image/$image");

    // Delete product
    $stm = $_db->prepare('DELETE FROM product WHERE product_id = ?');
    $stm->execute([$product_id]);

    temp('info', 'Product deleted successfully.');
    header('Location: /admin/product.php');
    exit;
 
}

?>
