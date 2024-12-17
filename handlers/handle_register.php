<?php

require_once('../functions.php');
require_once('../db.php');

check_for_empty_field('register');

if(isset($_SESSION['username'])) {
    return_func('danger', 'You are already logged in!', 'products');
}

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$repeat_password = $_POST['repeat_password'] ?? '';

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
    return_func('danger', 'Password must be at least 8 characters long and include at least one lowercase letter, one uppercase letter, one number, and one special character.', 'register');
}

if ($password != $repeat_password)
    return_func('danger', 'Passwords do not match!', 'register');

$query = "SELECT id FROM users WHERE EMAIL = :email OR USERNAME = :username";
$stmt = $pdo->prepare($query);
$login_parmas = [
    'email' => $email,
    'username' => $username
];

$stmt->execute($login_parmas);
$user = $stmt->fetch();

if ($user)
    return_func('danger', 'User with this username/email already exists!', 'register');

$hash = password_hash($password, PASSWORD_ARGON2I);

$query = "INSERT INTO users (USERNAME, EMAIL, `PASSWORD`, `ROLE`) VALUES (:username, :email, :hash, 'user')";
$stmt = $pdo->prepare($query);
$params = [
    'username' => $username,
    'email' => $email,
    'hash' => $hash
];

if ($stmt->execute($params)) 
    return_func('success', 'Successful registration!', 'login');
else 
    return_func('danger', 'An error occurred during registration!', 'register');

?>