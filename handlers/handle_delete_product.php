<?php

require_once('../functions.php');
require_once('../db.php');

if (!is_owner()) 
    return_func('danger', 'You are not authorized to access this page!', 'products');

$id = intval($_POST['id'] ?? 0);

if ($id <= 0)
    return_func('danger', 'Invalid product!', 'products');

delete_from_product_table($id, 'saved_products');
delete_from_product_table($id, 'product_images');

$query = "DELETE FROM products WHERE ID = :id";
$stmt = $pdo->prepare($query);
if ($stmt->execute([':id' => $id])) 
    return_func('success', 'Product deleted successfully!', 'products');
?>