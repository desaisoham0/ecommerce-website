<?php
require_once "./includes/session.php";
require_once "./includes/db.php";
require_once "./includes/functions.php";

// Restrict to owner
if (!isOwner()) {
    error_log("User is not an owner. Redirecting to index.");
    redirect("index.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_name = basename($_FILES['product_image']['name']);
        $image_path = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $image_path)) {
            $check = $conn->query("SELECT * FROM products WHERE name = '$name'");
            if ($check && $check->num_rows > 0) {
                $error = "Product already exists!";
            } else {
                $sql = "INSERT INTO products (name, price, description, image) 
                        VALUES ('$name', '$price', '$description', '$image_path')";

                if ($conn->query($sql)) {
                    $success = "Product added successfully. <a href='index.php' class='alert-link'>View Products</a>";
                } else {
                    $error = "Error: " . $conn->error;
                }
            }
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "Please select a valid image file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h1 class="text-center mb-4">Add New Product</h1>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger text-center">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success text-center">
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="product_image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="product_image" name="product_image" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Add Product</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="manage_products.php" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</
