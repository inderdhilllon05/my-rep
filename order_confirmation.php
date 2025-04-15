<?php
session_start();

if (!isset($_SESSION['order_confirmation'])) {
    header("Location: products.php");
    exit();
}

$order = $_SESSION['order_confirmation'];
unset($_SESSION['order_confirmation']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - DecorCraft</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <!-- Same header as other pages -->
    </header>

    <section class="confirmation-section">
        <div class="confirmation-container">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Thank You For Your Order!</h2>
            <p class="order-number">Order #<?php echo $order['order_number']; ?></p>
            <p>We've received your order and will begin processing it shortly. You'll receive a confirmation email with your order details.</p>
            
            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-row total">
                    <span>Total Paid:</span>
                    <span>$<?php echo number_format($order['total'], 2); ?></span>
                </div>
            </div>
            
            <div class="confirmation-actions">
                <a href="products.php" class="btn">Continue Shopping</a>
                <a href="index.html" class="btn outline">Back to Home</a>
            </div>
        </div>
    </section>

    <!-- Footer remains the same -->
</body>
</html>