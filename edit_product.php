<?php
require_once "./includes/session.php";
require_once "./includes/db.php";
require_once "./includes/functions.php";

if (!isOwner()) {
    redirect("index.php");
}

$id = intval($_GET['id']);
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
if (!$product) {
    die("Product not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);

    $image_update = "";
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_name = basename($_FILES['product_image']['name']);
        $image_path = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $image_path)) {
            $image_update = ", image='$image_path'";
        } else {
            $error = "Failed to upload image.";
        }
    }

    if (!isset($error)) {
        $sql = "UPDATE products SET name='$name', price='$price', description='$description' $image_update WHERE id=$id";
        if ($conn->query($sql)) {
            $success = "Product updated successfully.";
            $product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Edit Product</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="mx-auto" style="max-width: 400px;">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        
        <?php if (!empty($product['image'])): ?>
            <div class="mb-3">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Current Product Image" style="max-width: 100px; margin-bottom: 10px;">
            </div>
        <?php endif; ?>
        <div class="mb-3">
            <label for="product_image" class="form-label">Update Image</label>
            <input type="file" name="product_image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Update</button>
    </form>
    <div class="text-center mt-3">
        <a href="https://athletics-store.great-site.net/manage_products.php" class="btn btn-secondary">Back</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>