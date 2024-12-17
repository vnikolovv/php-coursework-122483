<?php
$products = [];

$query = "SELECT * FROM PRODUCTS WHERE `NAME` LIKE :search";
$stmt = $pdo->prepare($query);
$stmt->execute([':search' => "%$search%"]);

while ($row = $stmt->fetch()) {
    $fav_query = "SELECT ID FROM SAVED_PRODUCTS WHERE USER_ID = :user_id AND PRODUCT_ID = :product_id";
    $fav_stmt = $pdo->prepare($fav_query);
    $fav_params = [
        ':user_id' => $_SESSION['user_id'] ?? 0,
        ':product_id' => $row['ID']
    ];
    $fav_stmt->execute($fav_params);
    $row['is_favorite'] = $fav_stmt->fetch() ? '1' : '0';

    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row mb-4">
            <form class="d-flex" method="GET">
                <input type="hidden" name="page" value="products">
                <input type="text" class="form-control me-2" placeholder="Search products" name="search" value="<?php echo $search ?>">
                <button class="btn btn-warning" type="submit">Search</button>
            </form>
        </div>
        <div class="row mb-4">
            <?php
            if (isset($_COOKIE['last_search'])) {
                echo '<h5 class="text-golden">Last search: ' . $_COOKIE['last_search'] . '</h5>';
            }
            ?>
        </div>
        <div class="d-flex flex-wrap justify-content-between">
            <?php
            if (count($products) === 0) {
                echo '<h1 class="text-golden">No products were found</h1>';
            } else {
                foreach ($products as $product) {
                    $fav_btn = $edit_delete = '';
                    if (isset($_SESSION['username']) && !is_owner()) {
                        if ($product['is_favorite'] == '1') {
                            $fav_btn = '
                                <div class="card-footer text-center">
                                    <button type="button" class="btn btn-sm btn-danger toggle-saved" data-product="' . $product['ID'] . '">Remove from saved</button>
                                </div>
                            ';
                        } else {
                            $fav_btn = '
                                <div class="card-footer text-center">
                                    <button type="button" class="btn btn-sm btn-light toggle-saved" data-product="' . $product['ID'] . '">Add to saved</button>
                                </div>
                            ';
                        }
                    }
                    $view_btn = '
                        <div class="card-footer text-center">
                            <a href="?page=product_information&id=' . $product['ID'] . '" class="btn btn-sm btn-warning">View Product</a>
                        </div>
                    ';
                    if (is_owner()) {
                        $edit_delete = '
                            <div class="card-header d-flex flex-row justify-content-between">
                                <a class="btn btn-sm btn-light" href="?page=edit_product&id=' . $product['ID'] . '">Change</a>
                                <form method="POST" action="./handlers/handle_delete_product.php">
                                    <input type="hidden" name="id" value="' . $product['ID'] . '">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        ';
                    }

                    echo '
                        <div class="card mb-4 bg-dark text-light" style="width: 18rem; border-color: gold;">
                            ' . $fav_btn . $edit_delete . '
                            <img src="uploads/' . htmlspecialchars($product['MAIN_IMAGE']) . '" class="card-img-top" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title text-golden">' . htmlspecialchars($product['NAME']) . '</h5>
                                <p class="card-text text-golden">' . htmlspecialchars($product['PRICE']) . '$</p>
                            </div>
                            ' . $view_btn . '
                        </div>
                    ';
                }
            }
            ?>
        </div>
    </div>
</body>
</html>