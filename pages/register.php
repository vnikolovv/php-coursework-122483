<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form class="border rounded p-4 w-50 mx-auto" method="POST" action="./handlers/handle_register.php">
        <h3 class="text-center text-golden">Register</h3>
        <div class="mb-3">
            <label for="username" class="form-label text-golden">Username</label>
            <input type="username" class="form-control" id="username" name="username" value="<?php echo $flash['data']['username'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label text-golden">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $flash['data']['email'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label text-golden">Password</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="repeat_password" class="form-label text-golden">Repeat password</label>
            <input type="password" class="form-control" id="repeat_password" name="repeat_password">
        </div>
        <button type="submit" class="btn btn-warning mx-auto">Register</button>
    </form>
</body>
</html>