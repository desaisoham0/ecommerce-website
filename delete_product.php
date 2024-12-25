<?php
require_once "./includes/session.php";
require_once "./includes/db.php";
require_once "./includes/functions.php";

if (!isOwner()) {
    redirect("index.php");
}

$id = intval($_GET['id']);
$conn->query("DELETE FROM products WHERE id=$id");
redirect("manage_products.php");
