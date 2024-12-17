<?php

require_once('../functions.php');
require_once('../db.php');

$product_id = intval($_POST['product_id'] ?? 0);
$user_id = $_SESSION['user_id'];

$check_query = "SELECT COUNT(*) FROM RESERVED_PRODUCTS WHERE PRODUCT_ID = :product_id";
$check_stmt = $pdo->prepare($check_query);
$check_stmt->execute(['product_id' => $product_id]);
$is_reserved = $check_stmt->fetchColumn();

if ($is_reserved)
    return_func('danger', 'This product is already reserved!', 'product_information&id=' . $product_id);


$product_id = intval($_POST['product_id'] ?? 0);
$user_id = $_SESSION['user_id'];

$query = "INSERT INTO RESERVED_PRODUCTS (USER_ID, PRODUCT_ID) VALUES (:user_id, :product_id)";
$stmt = $pdo->prepare($query);
$params = [
    'user_id' => $user_id,
    'product_id' => $product_id
];

if ($stmt->execute($params)) 
    return_func('success', 'Product reserved successfully!', 'product_information&id=' . $product_id);
else 
    return_func('danger', 'An error occurred while reserving the product!', 'product_information&id=' . $product_id);

?>