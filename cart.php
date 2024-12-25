<?php
require_once "./includes/session.php";
require_once "./includes/db.php";
require_once "./includes/functions.php";

if (!isLoggedIn()) redirect("login.php");

// Update cart quantities if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    $new_quantities = $_POST['quantities'];
    // Rebuild the cart based on the updated quantities
    $_SESSION['cart'] = [];
    foreach ($new_quantities as $pid => $qty) {
        $pid = intval($pid);
        $qty = intval($qty);
        if ($qty > 0) {
            for ($i = 0; $i < $qty; $i++) {
                $_SESSION['cart'][] = $pid;
            }
        }
    }
    header("Location: cart.php");
    exit;
}

// Clear the cart if "Clear Cart" action triggered
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}

$items = $_SESSION['cart'] ?? [];
if (empty($items)) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Your Cart</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-4 text-center">
            <h1 class="mb-4">Your Cart</h1>
            <p class="text-muted">Your cart is empty.</p>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit;
}

$item_counts = array_count_values($items);
$ids = implode(",", array_keys($item_counts));

$res = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        img {
            max-width: 50px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Your Cart</h1>
        <form method="POST">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price per Unit</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $res->fetch_assoc()): ?>
                            <?php 
                            $product_id = $row['id'];
                            $quantity = $item_counts[$product_id];
                            $subtotal = $row['price'] * $quantity;
                            $total += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td>
                                    <input type="number" name="quantities[<?php echo $product_id; ?>]" value="<?php echo $quantity; ?>" min="1" class="form-control" style="width:80px; margin:auto;">
                                </td>
                                <td>$<?php echo number_format($row['price'], 2); ?></td>
                                <td>$<?php echo number_format($subtotal, 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end">
                <h4>Total: $<?php echo number_format($total, 2); ?></h4>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Continue Shopping
                </a>
                <a href="cart.php?action=clear" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear your cart?')">
                    Clear Cart
                </a>
                <button type="submit" name="update_cart" class="btn btn-warning">
                    Update Cart
                </button>
                <a href="checkout.php" class="btn btn-success">
                    Checkout <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>