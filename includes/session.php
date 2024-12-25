<?php
// Set secure session cookie parameters
session_set_cookie_params([
    'secure' => true,       // Transmit cookies over HTTPS only
    'httponly' => true,     // Prevent JavaScript access
    'samesite' => 'Strict', // Prevent cross-site access
]);

session_start();
// Initialize the cart session variable if not already set
if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
