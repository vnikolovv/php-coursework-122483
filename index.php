<?php

require_once('functions.php');
require_once('db.php');

$page = $_GET['page'] ?? (isset($_SESSION['user_id']) ? 'products' : 'login');

if ( ( $page != 'login' && $page != 'register' && $page != 'contacts' ) && !isset($_SESSION['user_id'])) {
    header('Location: ./index.php?page=login');
    exit;
}

$search = $_GET['search'] ?? '';

if (mb_strlen($search) > 0) {
    setcookie('last_search', $search, time() + 3600, '/', 'localhost', false, false);
}

$flash = [];
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$owner_pages = ['add_product', 'edit_product'];

if (in_array($page, $owner_pages) && !is_owner()) 
    return_func('warning', 'You do not have access to the page!', 'products');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pawnshop</title>
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<link rel="stylesheet" href="styles.css">
<body>
    <script>
        $(function() {
            $(document).on('click', '.toggle-saved', function() {
                let btn = $(this);
                let productId = btn.data('product');
                let action = btn.hasClass('btn-light') ? 'add' : 'remove';
                let url = './ajax/alter_saved_product.php';

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        product_id: productId,
                        mode: action
                    },
                    success: function(response) {
                        let res = JSON.parse(response);
                        if (res.success) {
                            Swal.fire({
                            title: "The item was " + (action == 'add' ? 'added' : 'removed') + " successfully.",
                            icon: "success",
                            toast: true,
                            position: "top",
                            showConfirmButton: false,
                            timer: 6000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            },
                            showCloseButton: true,
                        });
                            let newBtn = $('<button type="button" class="btn btn-sm btn-' + (action == 'add' ? 'danger' : 'light') + ' toggle-saved" data-product="' + productId + '">' + (action == 'add' ? 'Remove from saved' : 'Add to saved') + '</button>');
                            btn.replaceWith(newBtn);
                        } else {
                            Swal.fire({
                                title: "An error occured: " + res.error,
                                icon: "error",
                                toast: true,
                                position: "top",
                                showConfirmButton: false,
                                timer: 6000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                },
                                showCloseButton: true,
                            });
                        }
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });
        });
    </script>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold d-flex align-items-center" href="?page=products">
                    <img src="https://cdn-icons-png.flaticon.com/512/3044/3044885.png" alt="Logo" style="height: 30px; margin-right: 10px;">
                    Pawnshop
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                    <ul class="navbar-nav">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo($page == 'products' ? 'active' : '') ?>" href="?page=products">Products</a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo($page == 'contacts' ? 'active' : '') ?>" href="?page=contacts">Contacts</a>
                        </li>

                        <?php
                            if (isset($_SESSION['is_owner']) && $_SESSION['is_owner']) {
                                echo '
                                    <li class="nav-item">
                                        <a class="nav-link ' . ($page == 'add_product' ? 'active' : '') . '" href="?page=add_product">Add product</a>
                                    </li>
                                ';
                            }
                        ?>
                    </ul>
                    <div class="d-flex align-items-center gap-4">
                        <?php
                            if (isset($_SESSION['username'])) {
                                echo '<span class="text-light me-3 text-golden">Hello, ' . htmlspecialchars($_SESSION['username']) . '</span>';
                                echo '
                                    <form method="POST" action="./handlers/handle_logout.php">
                                        <button type="submit" class="btn btn-outline-light">Logout</button>
                                    </form>
                                ';
                            } else {
                                echo '<a href="?page=login" class="btn btn-outline-light">Login</a>';
                                echo '<a href="?page=register" class="btn btn-outline-light">Register</a>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main class="container py-4" style="min-height:80vh;">
        <?php
            if (isset($flash['message'])) {
                $icon_values = [
                    'success' => 'success',
                    'danger' => 'error',
                    'warning' => 'warning',
                ];

                echo '
                    <script>
                        Swal.fire({
                            title: "' . $flash['message']['text'] . '",
                            icon: "' . $icon_values[$flash['message']['type']] . '",
                            toast: true,
                            position: "top",
                            showConfirmButton: false,
                            timer: 6000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            },
                            showCloseButton: true,
                        });
                    </script>
                ';
            }

            if (file_exists('./pages/' . $page . '.php')) {
                require_once('./pages/' . $page . '.php');
            } else {
                require_once('./pages/not_found.php');
            }
        ?>
    </main>
    <footer class="bg-dark text-center py-5 mt-auto">
        <div class="container">
            <span class="text-light">© 2024 All rights reserved</span>
        </div>
    </footer>
</body>
</html>