<?php

require_once('../db.php');

$response = [
    'status' => 'pending',
    'error' => ''
];

$product_id = intval($_POST['product_id'] ?? 0);
$price = floatval($_POST['price'] ?? 0);

if ($product_id <= 0 || $price <= 0) {
    $response['status'] = 'denied';
    $response['error'] = 'Invalid product or price.';
    echo json_encode($response);
    exit;
}

$query = "SELECT * FROM PRODUCTS WHERE ID = :product_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['product_id' => $product_id]);

$product = $stmt->fetch();
if (!$product) {
    $response['status'] = 'denied';
    $response['error'] = 'Invalid product or price.';
    echo json_encode($response);
    exit;
}
$product_price = $product['PRICE'];

$offer_query = "SELECT * FROM NEGOTIATION_OFFERS WHERE PRODUCT_ID = :id AND USER_ID = :user_id AND OFFER_RESULT = 'pending'";
$stmt = $pdo->prepare($offer_query);
$stmt->execute(['id' => $product_id, 'user_id' => $_SESSION['user_id']]);
$offer = $stmt->fetch();
if ($offer) {
    $response['status'] = 'denied';
    $response['error'] = 'You have already sent an offer for this product.';
    echo json_encode($response);
    exit;
}

$reserved_query = "SELECT * FROM RESERVED_PRODUCTS WHERE PRODUCT_ID = :id";
$stmt = $pdo->prepare($reserved_query);
$stmt->execute(['id' => $product_id]);
$reserved = $stmt->fetch();
if ($reserved) {
    $response['status'] = 'denied';
    $response['error'] = 'This product is already reserved.';
    echo json_encode($response);
    exit;
}

$offer_result = '';
if ($price < $product_price * 0.9)
    $offer_result = 'denied';
 else
    $offer_result = 'pending';

$query = "INSERT INTO NEGOTIATION_OFFERS (USER_ID, PRODUCT_ID, PRICE, OFFER_RESULT) VALUES (:user_id, :product_id, :price, :offer_result)";
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare($query);
$params = [
    ':user_id' => $user_id,
    ':product_id' => $product_id,
    ':price' => $price,
    ':offer_result' => $offer_result
];

if (!$stmt->execute($params)) {
    $response['status'] = 'denied';
    $response['error'] = 'Error while submitting the offer.';
}

if ($offer_result === 'denied') {
    $response['status'] = 'denied';
    $response['error'] = 'Offer denied.';
} else {
    $response['status'] = 'pending';
    $response['error'] = 'Offer sent';
}

echo json_encode($response);
exit;

?>