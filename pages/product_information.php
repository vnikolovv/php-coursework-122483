<?php
    $images = [];

    $id = intval($_GET['id'] ?? 0);
    if ($id < 0)
        return_func('danger', 'Invalid product!', 'products');

    $query = "SELECT * FROM PRODUCTS WHERE ID LIKE :id";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();

    $images_query = "SELECT IMAGE FROM PRODUCT_IMAGES WHERE PRODUCT_ID = :id";
    $stmt = $pdo->prepare($images_query);
    $stmt->execute(["id"=> $id]);

    while ($row = $stmt->fetch()) {
        $images[] = $row['IMAGE'];
    }

    $reserved_item = false;
    $reserved_query = "SELECT * FROM RESERVED_PRODUCTS WHERE PRODUCT_ID = :id";
    $stmt = $pdo->prepare($reserved_query);
    $stmt->execute(['id' => $id]);
    $reserved = $stmt->fetch();
    if ($reserved)
        $reserved_item = true;

    $offer_sent = false;
    $offer_query = "SELECT * FROM NEGOTIATION_OFFERS WHERE PRODUCT_ID = :id AND USER_ID = :user_id AND OFFER_RESULT = 'pending'";
    $stmt = $pdo->prepare($offer_query);
    $stmt->execute(['id' => $id, 'user_id' => $_SESSION['user_id']]);
    $offer = $stmt->fetch();
    if ($offer)
        $offer_sent = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center text-golden"><?php echo htmlspecialchars($product['NAME']); ?></h1>
        <div class="row">
            <div class="col-md-6">
                <div id="productCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="uploads/<?php echo htmlspecialchars($product['MAIN_IMAGE']); ?>" class="d-block w-100" alt="Product Main Image">
                        </div>
                        <?php foreach ($images as $image): ?>
                        <div class="carousel-item">
                            <img src="uploads/<?php echo htmlspecialchars($image); ?>" class="d-block w-100" alt="Product Image">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <p class="mb-4 text-justify text-golden"><?php echo nl2br(htmlspecialchars($product['DESCRIPTION'])); ?></p>
                <div class="d-flex justify-content-center">
                    <form action="./handlers/handle_reserve_product.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                        <button type="submit" class="btn btn-light mx-2" <?php echo $reserved_item ? 'disabled' : ''; ?>>Reserve</button>
                    </form>
                    <button type="button" class="btn btn-warning mx-2" id="negotiationButton" <?php echo $offer_sent || $reserved ? 'disabled' : ''; ?>><?php echo $offer_sent ? 'Offer Sent' : 'Negotiate'; ?></button>
                </div>
                <script>
                    document.getElementById('negotiationButton').addEventListener('click', function() {
                        Swal.fire({
                            title: 'Enter your price',
                            input: 'number',
                            inputAttributes: {
                                min: 0,
                                step: '0.01'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Submit',
                            showLoaderOnConfirm: true,
                            preConfirm: (price) => {
                                return new Promise((resolve) => {
                                    resolve(price);
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let negotiatedPrice = result.value;
                                
                                $.ajax({
                                    url: "./ajax/negotiation_offer.php",
                                    method: 'POST',
                                    data: {
                                        product_id: <?php echo $id; ?>,
                                        price: negotiatedPrice
                                    },
                                    success: function(response) {
                                        let res = JSON.parse(response);
                                        if (res.status === 'pending') {
                                            Swal.fire({
                                                title: res.error,
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
                                            document.getElementById('negotiationButton').disabled = true;
                                            document.getElementById('negotiationButton').innerText = 'Offer sent';
                                        } else {
                                            Swal.fire({
                                                title: res.error,
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
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</body>
</html>