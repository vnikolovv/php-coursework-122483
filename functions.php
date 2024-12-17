<?php

function debug($data, $die = false) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';

    if ($die) {
        die;
    }
}

function is_owner() {
    return isset($_SESSION['is_owner']) && $_SESSION['is_owner'];
}

function key_to_field($key) {
   return $name = ucfirst(str_replace('_', ' ', $key));
}

function return_func($error, $message, $location)
{
    $_SESSION['flash']['message']['type'] = $error;
    $_SESSION['flash']['message']['text'] = $message;
    $_SESSION['flash']['data'] = $_POST;
    header('Location: ../index.php?page=' . $location);
    exit;
}

function check_for_empty_field($field) {
    $error = '';
    foreach ($_POST as $key => $value) {
        if (empty($value)) {
            $error = 'Please fill field ' . key_to_field($key) . '!';
            break;
        }
    }
    if (mb_strlen($error) > 0)
        return_func('danger', $error, $field);
}

function delete_from_product_table($product_id, $table_name) {
    require_once 'db.php';
    global $pdo;
    
    $query = "DELETE FROM $table_name WHERE PRODUCT_ID = :id";
    $stmt = $pdo->prepare($query);
    if (!$stmt->execute([':id' => $product_id]))
        return_func('danger', 'An error occurred while deleting the product!', 'products');
    
}

?>