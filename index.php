<?php
require_once "./includes/session.php";
require_once "./includes/db.php";
require_once "./includes/functions.php";

// Check if user is an owner
if (isOwner()) {
    echo "<h1>Welcome, Owner</h1>";
    echo " <a href='https://athletics-store.great-site.net/manage_products.php' class='btn btn-outline-primary'>Manage Products</a>";
}

$result = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Athletics Store</a>
        <div class="d-flex">
            <?php if (!isLoggedIn()): ?>
                <a href="https://athletics-store.great-site.net/login.php" class="btn btn-light me-2">Login</a>
                <a href="https://athletics-store.great-site.net/register.php" class="btn btn-outline-light">Register</a>
            <?php else: ?>
                <?php
                $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                ?>
                <a href="cart.php" class="btn btn-light me-2">
                    <i class="bi bi-cart"></i> Cart 
                    <?php if ($cart_count > 0): ?>
                        <span class="badge bg-danger"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
                <a href="login.php" class="btn btn-outline-light">Log Out</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1 class="text-center mb-4">Products</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100">
                    <?php if (!empty($row['image'])): ?>
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="Product Image">
                    <?php endif; ?>
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo $row['name']; ?></h5>
                        <p class="card-text"><?php echo strlen($row['description']) > 50 ? substr($row['description'],0,50)."..." : $row['description']; ?></p>
                        <p class="card-text">$<?php echo number_format($row['price'], 2); ?></p>
                        <a href="add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Add to Cart</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<footer class="bg-light text-center py-3 mt-4">
    <p class="mb-0">© 2024 Athletics Store. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
