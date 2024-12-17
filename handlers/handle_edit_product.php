<?php

require_once('../functions.php');
require_once('../db.php');

if (!is_owner()) 
    return_func('danger', 'You are not authorized to access this page!', 'products');

$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$id = intval($_POST['id'] ?? 0);
$description = $_POST['description'] ?? '';

check_for_empty_field('edit_product');

$img_uploaded = false;
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $new_file_name = time() . '_' . $_FILES['image']['name'];
    $upload_dir = '../uploads/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_file_name)) {
        $_SESSION['flash']['message']['type'] = 'danger';
        $_SESSION['flash']['message']['text'] = "An error occured while uploading the image!";

        header('Location: ../index.php?page=edit_product&id=' . $id);
        exit;
    } else {
        $img_uploaded = true;
    }
}

if (!is_numeric($price)) 
    return_func('danger', 'Price must be a number!', 'edit_product&id=' . $id);

if (mb_strlen($name) < 3) 
    return_func('danger', 'Name must be at least 3 characters long!', 'edit_product&id=' . $id);

if (mb_strlen($description) < 10 || mb_strlen($description) > 1024)
    return_func('danger', 'Description must be between 10 and 1024 characters long!', 'edit_product&id=' . $id);

$query = "";
if ($img_uploaded) {
    $query = "
        UPDATE PRODUCTS
        SET NAME = :name, PRICE = :price, DESCRIPTION = :description, IMAGE = :image
        WHERE ID = :id
    ";
} else {
    $query = "
        UPDATE PRODUCTS
        SET NAME = :name, PRICE = :price, DESCRIPTION = :description
        WHERE ID = :id
    ";
}

$stmt = $pdo->prepare($query);
$params = [
    'name' => $name,
    'price' => $price,
    'description' => $description,
    'id' => $id
];
if ($img_uploaded) {
    $params['image'] = $new_file_name;
}

if ($stmt->execute($params)) 
    return_func('success', 'Product edited successfully!', 'edit_product&id='. $id) ;
else 
    return_func('danger', 'An error occurred while editing the product!', 'edit_product&id=' . $id);
?>