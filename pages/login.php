<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form class="border rounded p-4 w-50 mx-auto" method="POST" action="./handlers/handle_login.php">
        <h3 class="text-center text-golden">Login</h3>
        <div class="mb-3">
            <label for="username" class="form-label text-golden">Username</label>
            <input type="username" class="form-control" id="username" name="username" value="<?php echo $_COOKIE['username'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label text-golden">Password</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-warning mx-auto">Login</button>
    </form>
</body>
</html>