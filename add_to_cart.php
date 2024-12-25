<?php
require_once "./includes/session.php";
require_once "./includes/db.php";
require_once "./includes/functions.php";

if(!isLoggedIn()) redirect("https://athletics-store.great-site.net/login.php");

$id = (int)$_GET['id'];
$_SESSION['cart'][] = $id;
$referer = $_SERVER['HTTP_REFERER'] ?? "https://athletics-store.great-site.net/index.php"; // Fallback to index.php if no referer
redirect($referer);
?>