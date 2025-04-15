<?php
session_start();
include 'db_connection.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle remove item action
if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header("Location: cart.php");
    exit();
}

// Handle update quantity action
if (isset($_POST['update_quantity'])) {
    $index = $_POST['index'];
    $new_quantity = (int)$_POST['quantity'];
    
    if (isset($_SESSION['cart'][$index]) && $new_quantity > 0) {
        $_SESSION['cart'][$index]['quantity'] = $new_quantity;
    }
    header("Location: cart.php");
    exit();
}

// Calculate totals
$subtotal = 0;
$cart_items = [];
$conn = OpenCon();

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
            'item_total' => $item_total,
            'index' => count($cart_items)
        ];
    }
}

$tax = $subtotal * 0.1;
$shipping = $subtotal > 100 ? 0 : 15;
$total = $subtotal + $tax + $shipping;
CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart - DecorCraft</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <!-- Same header as other pages -->
    </header>

    <section class="shopping-cart">
        <div class="section-header">
            <h2>Your Shopping Cart</h2>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <p>Your cart is empty</p>
                <a href="products.php" class="btn">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="item-image">
                            <img src="images/<?php echo $item['product']['image']; ?>" alt="<?php echo $item['product']['name']; ?>">
                        </div>
                        <div class="item-details">
                            <h3><?php echo $item['product']['name']; ?></h3>
                            <p class="item-price">$<?php echo number_format($item['product']['price'], 2); ?></p>
                            
                            <?php if (!empty($item['options'])): ?>
                            <div class="item-options">
                                <h4>Customizations:</h4>
                                <ul>
                                    <?php foreach ($item['options'] as $option_id => $value): ?>
                                        <?php 
                                        $conn = OpenCon();
                                        $option_sql = "SELECT option_name FROM customization_options WHERE id = $option_id";
                                        $option_result = $conn->query($option_sql);
                                        $option_name = $option_result->num_rows > 0 ? $option_result->fetch_assoc()['option_name'] : 'Option';
                                        CloseCon($conn);
                                        ?>
                                        <li><strong><?php echo $option_name; ?>:</strong> <?php echo $value; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                            
                            <form method="post" action="cart.php" class="quantity-form">
                                <input type="hidden" name="index" value="<?php echo $item['index']; ?>">
                                <label>Quantity:</label>
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                <button type="submit" name="update_quantity" class="btn update-btn">Update</button>
                            </form>
                            
                            <a href="cart.php?remove=<?php echo $item['index']; ?>" class="remove-item">Remove Item</a>
                        </div>
                        <div class="item-total">
                            <p>$<?php echo number_format($item['item_total'], 2); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <h3>Order Summary</h3>
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
                    <a href="checkout.php" class="btn checkout-btn">Proceed to Checkout</a>
                    <a href="products.php" class="continue-shopping">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Footer remains the same -->
</body>
</html>