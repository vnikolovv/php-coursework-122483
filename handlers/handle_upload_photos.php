<?php

require_once('../functions.php');
require_once('../db.php');

if (!is_owner()) 
    return_func('danger', 'You are not authorized to access this page!', 'products');

$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id <= 0) 
    return_func('danger', 'Invalid product ID!', 'products');

$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$uploaded_files = [];
if (isset($_FILES['additional_images'])) {
    foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['additional_images']['error'][$key] == 0) {
            $new_file_name = time() . '_' . $_FILES['additional_images']['name'][$key];
            if (move_uploaded_file($tmp_name, $upload_dir . $new_file_name)) {
                $uploaded_files[] = $new_file_name;
            } else {
                $_SESSION['flash']['message']['type'] = 'danger';
                $_SESSION['flash']['message']['text'] = "An error occurred while uploading the image!";
                header('Location: ../index.php?page=edit_product&id=' . $product_id);
                exit;
            }
        }
    }
}

if (!empty($uploaded_files)) {
    $query = "INSERT INTO PRODUCT_IMAGES (PRODUCT_ID, IMAGE) VALUES ";
    $params = [];
    foreach ($uploaded_files as $file) {
        $query .= "(?, ?),";
        $params[] = $product_id;
        $params[] = $file;
    }
    $query = rtrim($query, ',');

    $stmt = $pdo->prepare($query);
    if ($stmt->execute($params)) {
        return_func('success', 'Images uploaded successfully!', 'edit_product&id=' . $product_id);
    } else {
        return_func('danger', 'An error occurred while saving the images!', 'edit_product&id=' . $product_id);
    }
} else {
    return_func('danger', 'No images were uploaded!', 'edit_product&id=' . $product_id);
}

?>