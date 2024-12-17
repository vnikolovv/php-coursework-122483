<?php

require_once('../functions.php');
require_once('../db.php');

if (!is_owner()) 
    return_func('danger', 'You are not authorized to access this page!', 'products');

$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$description = $_POST['description'] ?? '';
    check_for_empty_field('add_product');

if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) 
    return_func('danger', 'Please select an image!', 'add_product');

if (!is_numeric($price)) 
    return_func('danger', 'Price must be a number!', 'add_product');

if (mb_strlen($name) < 3) 
    return_func('danger', 'Name must be at least 3 characters long!', 'add_product');

if (mb_strlen($description) < 10 || mb_strlen($description) > 1024)
    return_func('danger', 'Description must be between 10 and 1024 characters long!', 'add_product');

$new_file_name = time() . '_' . $_FILES['image']['name'];
$upload_dir = '../uploads/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_file_name))
    return_func('danger', 'Error uploading image!', 'add_product');

$query = "INSERT INTO PRODUCTS (`NAME`, PRICE, `DESCRIPTION`, `MAIN_IMAGE`) VALUES (:name, :price, :description, :image)";
$stmt = $pdo->prepare($query);
$params = [
    'name' => $name,
    'price' => $price,
    'description' => $description,
    'image' => $new_file_name
];

if ($stmt->execute($params)) 
    return_func('success', 'Product added successfully!', 'products');
else 
    return_func('danger', 'An error occurred while adding the product!', 'add_product');

?>