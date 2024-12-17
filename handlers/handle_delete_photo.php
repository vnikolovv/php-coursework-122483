<?php

require_once('../functions.php');
require_once('../db.php');

if (!is_owner()) 
    return_func('danger', 'You are not authorized to access this page!', 'products');

$id = intval($_POST['id'] ?? 0);
$product_id = intval($_POST['product_id'] ?? 0);

if ($id <= 0 || $product_id <= 0)
    return_func('danger', 'Invalid product!', 'products');

$query = "DELETE FROM PRODUCT_IMAGES WHERE ID = :id";
$stmt = $pdo->prepare($query);
if ($stmt->execute([':id' => $id])) 
    return_func('success', 'Image deleted successfully!', 'edit_product&id=' . $product_id);
else
    return_func('danger', 'An error occurred while deleting the image!', 'edit_product&id=' . $product_id);
?>