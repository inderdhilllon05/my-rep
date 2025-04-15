<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$conn = OpenCon();
$subtotal = 0;
$cart_items = [];

foreach ($_SESSION['cart'] as $item) {
    $product_id = $item['product_id'];
    $sql = "SELECT name, price, image FROM products WHERE id = $product_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $item_total = $product['price'] * $item['quantity'];
        $subtotal += $item_total;
        
        $cart_items[] = [
            'product' => $product,
            'options' => $item['options'],
            'quantity' => $item['quantity'],
            'item_total' => $item_total
        ];
    }
}

$tax = $subtotal * 0.1;
$shipping = $subtotal > 100 ? 0 : 15;
$total = $subtotal + $tax + $shipping;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];
    
    $_SESSION['order_confirmation'] = [
        'order_number' => 'ORD-' . uniqid(),
        'total' => $total,
        'items' => $cart_items
    ];
    
    unset($_SESSION['cart']);
    header("Location: order_confirmation.php");
    exit();
}
CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - DecorCraft</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <!-- Same header as other pages -->
    </header>

    <section class="checkout-section">
        <div class="section-header">
            <h2>Checkout</h2>
        </div>
        
        <div class="checkout-container">
            <div class="checkout-form">
                <form method="POST" action="checkout.php">
                    <h3>Shipping Information</h3>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Shipping Address</label>
                        <textarea id="address" name="address" rows="4" required></textarea>
                    </div>
                    
                    <h3>Payment Method</h3>
                    <div class="payment-methods">
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="credit_card" checked>
                            <i class="fab fa-cc-visa"></i> Credit Card
                        </label>
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="paypal">
                            <i class="fab fa-paypal"></i> PayPal
                        </label>
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="debit_card">
                            <i class="fas fa-credit-card"></i> Debit Card
                        </label>
                    </div>
                    
                    <div class="credit-card-info" id="creditCardInfo">
                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry">Expiry Date</label>
                                <input type="text" id="expiry" name="expiry" placeholder="MM/YY">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" placeholder="123">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="card_name">Name on Card</label>
                            <input type="text" id="card_name" name="card_name">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn place-order-btn">Place Order</button>
                </form>
            </div>
            
            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="order-items">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="order-item">
                        <div class="item-image">
                            <img src="images/<?php echo $item['product']['image']; ?>" alt="<?php echo $item['product']['name']; ?>">
                        </div>
                        <div class="item-details">
                            <h4><?php echo $item['product']['name']; ?></h4>
                            <p>Qty: <?php echo $item['quantity']; ?></p>
                            <p class="item-price">$<?php echo number_format($item['item_total'], 2); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-details">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (10%):</span>
                        <span>$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>$<?php echo number_format($shipping, 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer remains the same -->
</body>
</html>