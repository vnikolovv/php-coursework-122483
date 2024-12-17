<?php

require_once('../functions.php');
require_once('../db.php');

if(isset($_SESSION['username'])) {
    return_func('danger', 'You are already logged in!', 'products');
}

check_for_empty_field('login');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$query = "SELECT * FROM USERS WHERE USERNAME = :username";
$stmt = $pdo->prepare($query);
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();

if (!$user)
    return_func('danger', 'User not found!', 'login');

if (!password_verify($password, $user['PASSWORD']))
    return_func('danger', 'Incorrect password!', 'login');

session_start();
$_SESSION['username'] = $user['USERNAME'];
$_SESSION['user_id'] = $user['ID'];
$_SESSION['is_owner'] = $user['ROLE'] == 'admin';
setcookie('username', $user['USERNAME'], time() + 3600, '/', 'localhost', false, true);

header('Location: ../index.php');
exit;

?>