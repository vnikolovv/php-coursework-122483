<?php
    $id = intval($_GET['id'] ?? 0);
    $query = "SELECT * FROM PRODUCTS WHERE ID = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();
    
    $images = [];
    $images_query = "SELECT * FROM PRODUCT_IMAGES WHERE PRODUCT_ID = :id";
    $stmt = $pdo->prepare($images_query);
    $stmt->execute(["id"=> $id]);

    while ($row = $stmt->fetch()) {
        $images[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <form class="border rounded p-4" method="POST" action="./handlers/handle_edit_product.php" enctype="multipart/form-data">
                    <h3 class="text-center text-golden">Edit product</h3>
                    <div class="mb-3">
                        <label for="name" class="form-label text-golden">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $product['NAME'] ?? '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label text-golden">Price:</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo $product['PRICE'] ?? '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label text-golden">Description:</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo $product['DESCRIPTION'] ?? '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label text-golden">Image:</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <img class="img-fluid" src="uploads/<?php echo $product['MAIN_IMAGE'] ?>" alt="<?php echo $product['NAME'] ?>">
                    </div>
                    <input type="hidden" name="id" value="<?php echo $product['ID'] ?>">
                    <button type="submit" class="btn btn-warning mx-auto">Edit</button>
                </form>
            </div>
            <div class="col-md-6">
                <form class="border rounded p-4" method="POST" action="./handlers/handle_upload_photos.php" enctype="multipart/form-data">
                    <h3 class="text-center text-golden">Upload Additional Photos</h3>
                    <div class="mb-3">
                        <label for="additional_images" class="form-label text-golden">Additional Images:</label>
                        <input type="file" class="form-control" id="additional_images" name="additional_images[]" accept="image/*" multiple>
                    </div>
                    <input type="hidden" name="product_id" value="<?php echo $product['ID'] ?>">
                    <button type="submit" class="btn btn-warning mx-auto">Upload</button>
                </form>
            </div>
        </div>
        <div class="row mt-4">
            <?php foreach ($images as $image): ?>
                <div class="col-md-6">
                    <form class="border rounded p-4" method="POST" action="./handlers/handle_delete_photo.php">
                        <h3 class="text-center text-golden">Delete Photo</h3>
                        <div class="mb-3">
                            <img class="img-fluid" src="uploads/<?php echo $image['IMAGE'] ?>" alt="Product Image">
                        </div>
                        <input type="hidden" name="id" value="<?php echo $image['ID'] ?>">
                        <input type="hidden" name="product_id" value="<?php echo $product['ID'] ?>">
                        <button type="submit" class="btn btn-danger mx-auto">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>