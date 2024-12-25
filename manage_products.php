<?php
require_once "./includes/session.php";
require_once "./includes/db.php";
require_once "./includes/functions.php";

if (!isOwner()) {
    redirect("https://athletics-store.great-site.net/index.php");
}

$products = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">Manage Products</h1>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th><th>Price</th><th>Description</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $products->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo htmlspecialchars(substr($row['description'], 0, 50)) . (strlen($row['description'])>50?'...':''); ?></td>
                <td>
                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a href="add_product.php" class="btn btn-primary">Add New Product</a>
    <a href="https://athletics-store.great-site.net/index.php" class="btn btn-secondary">Back to Home</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
