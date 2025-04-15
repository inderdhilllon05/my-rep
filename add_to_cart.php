<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $options = $_POST['options'] ?? [];
    $quantity = (int)$_POST['quantity'];
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Add item to cart
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'options' => $options,
        'quantity' => $quantity
    ];
    
    header("Location: cart.php");
    exit();
} else {
    header("Location: products.php");
    exit();
}
?>