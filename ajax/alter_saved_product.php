<?php

require_once('../db.php');

$response = [
    'success' => true,
    'data' => [],
    'error' => ''
];

$product_id = intval($_POST['product_id'] ?? 0);
$mode = $_POST['mode'] ?? '';

if ($product_id <= 0) {
    $response['success'] = false;
    $response['error'] = 'Invalid product.';
    echo json_encode($response);
    exit;
}
$user_id = $_SESSION['user_id'];

if ($mode === 'add') {
    $query = "INSERT INTO SAVED_PRODUCTS (USER_ID, PRODUCT_ID) VALUES (:user_id, :product_id)";
} else {
    $query = "DELETE FROM SAVED_PRODUCTS WHERE USER_ID = :user_id AND PRODUCT_ID = :product_id";
}

$stmt = $pdo->prepare($query);
$params = [
    ':user_id' => $user_id,
    ':product_id' => $product_id
];

if (!$stmt->execute($params)) {
    $response['success'] = false;
    $response['error'] = $mode === 'add' ? 'Грешка при добавяне в любими.' : 'Грешка при премахване от любими.';
}

echo json_encode($response);
exit;

?>