<?php
require_once "./includes/session.php";
require_once "./includes/db.php";
require_once "./includes/functions.php";

if (!isLoggedIn()) {
    error_log("Unauthorized checkout attempt");
    redirect("login.php");
}

if (empty($_SESSION['cart'])) {
    error_log("Empty cart checkout attempt");
    redirect("index.php");
}

$user_id = $_SESSION['user_id'];
$items = implode(",", $_SESSION['cart']);
$order_success = false;

try {
    // Start transaction
    $conn->begin_transaction();

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, items) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $items);

    if (!$stmt->execute()) {
        throw new Exception("Order insertion failed: " . $stmt->error);
    }
    
    $order_id = $conn->insert_id;

    // Fetch product details
    $item_counts = array_count_values($_SESSION['cart']);
    $ids = implode(",", array_keys($item_counts));
    $res = $conn->query("SELECT * FROM products WHERE id IN ($ids)");

    $emailItems = [];
    $totalAmount = 0;

    while ($row = $res->fetch_assoc()) {
        $product_id = $row['id'];
        $quantity = $item_counts[$product_id];
        $subtotal = $row['price'] * $quantity;
        $totalAmount += $subtotal;

        $emailItems[] = "{$row['name']} x{$quantity} (\${$row['price']} each) - \$" . number_format($subtotal, 2);
    }

    $itemDetails = implode("\n", $emailItems);
    $itemDetails .= "\n\nTotal Amount: $" . number_format($totalAmount, 2);

    // Get user email
    $userEmailQuery = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $userEmailQuery->bind_param("i", $user_id);

    if (!$userEmailQuery->execute()) {
        throw new Exception("Failed to fetch user email: " . $userEmailQuery->error);
    }

    $userEmailQuery->bind_result($user_email);
    $userEmailQuery->fetch();
    $userEmailQuery->close();

    if (empty($user_email)) {
        throw new Exception("User email not found for user_id: $user_id");
    }

    // Include the formatted product details in the email
    include "./emails/send_confirmation.php";

    // Commit transaction
    $conn->commit();
    $order_success = true;

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    error_log("Checkout error: " . $e->getMessage());
    $_SESSION['error'] = "Order processing failed. Please try again.";
    redirect("cart.php");
}


// Only clear cart and redirect if everything succeeded
if ($order_success) {
    $_SESSION['cart'] = [];
    $_SESSION['success'] = "Order placed successfully!";
    redirect("confirm.php?order_id=" . $order_id);
} else {
    redirect("cart.php");
}