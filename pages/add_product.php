<?php

?>
<html lang="en">

<link rel="stylesheet" href="styles.css">
<form class="border rounded p-4 w-50 mx-auto" method="POST" action="./handlers/handle_add_product.php"
    enctype="multipart/form-data">
    <h3 class="text-center text-golden">Add product</h3>
    <div class="mb-3">
        <label for="name" class="form-label text-golden">Name:</label>
        <input type="text" class="form-control" id="name" name="name">
    </div>
    <div class="mb-3">
        <label for="price" class="form-label text-golden">Price:</label>
        <input type="number" step="0.01" class="form-control" id="price" name="price">
    </div>
    <div class="mb-3">
        <label for="description" class="form-label text-golden">Description:</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label text-golden">Image:</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*">
    </div>
    <button type="submit" class="btn btn-warning mx-auto">Add</button>
</form>

</html>