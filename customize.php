<?php
include 'db_connection.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];
$conn = OpenCon();

// Get product details
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: products.php");
    exit();
}

$product = $result->fetch_assoc();

// Get customization options
$sql_options = "SELECT * FROM customization_options WHERE product_id = $product_id";
$options_result = $conn->query($sql_options);
$options = [];

if ($options_result->num_rows > 0) {
    while($row = $options_result->fetch_assoc()) {
        $options[] = $row;
    }
}

// Color names mapping for all specified colors
$color_names = [
    '#FFFFFF' => 'Pure White',
    '#000000' => 'Midnight Black',
    '#B71C1C' => 'Cherry Red',
    '#0D47A1' => 'Deep Ocean Blue',
    '#1B5E20' => 'Forest Green',
    '#FFD600' => 'Golden Yellow',
   '#E0E0E0'=>'Gray',
    '#E65100' => 'Burnt Orange',
    '#F5F5F5'=>'Offwhite',
    '#6A1B9A'=>'Purple',
    
'#C0C0C0'=>'Gray'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customize <?php echo $product['name']; ?> - DecorCraft</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .color-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .color-swatch {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
            border: 1px solid #ddd;
        }
        .color-name {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <!-- Rest of your header code remains the same -->
    <header>
        <div class="logo">
            <h1>DecorCraft</h1>
            <p>Customize Your Dream Home</p>
        </div>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="#customize">Customize</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
            </ul>
        </nav>
    </header>

    <section class="customize-product">
        <div class="section-header">
            <h2>Customize Your <?php echo $product['name']; ?></h2>
        </div>
        
        <div class="customization-container">
            <div class="product-preview">
                <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" id="productImage">
                <div class="preview-controls">
                    <button id="rotateLeft"><i class="fas fa-undo"></i></button>
                    <button id="zoomIn"><i class="fas fa-search-plus"></i></button>
                    <button id="zoomOut"><i class="fas fa-search-minus"></i></button>
                    <button id="rotateRight"><i class="fas fa-redo"></i></button>
                </div>
            </div>
            
            <div class="customization-options">
                <form id="customizationForm" action="add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <?php foreach ($options as $option): ?>
                    <div class="option-group">
                        <h3><?php echo $option['option_name']; ?></h3>
                        
                        <?php if ($option['option_type'] == 'select'): ?>
                            <select name="options[<?php echo $option['id']; ?>]" class="option-select" data-option="<?php echo $option['option_name']; ?>">
                                <?php 
                                $choices = explode(',', $option['choices']);
                                foreach ($choices as $choice): 
                                ?>
                                <option value="<?php echo trim($choice); ?>"><?php echo trim($choice); ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                        <?php elseif ($option['option_type'] == 'color'): ?>
                            <div class="color-options">
                                <?php 
                                $colors = explode(',', $option['choices']);
                                foreach ($colors as $color): 
                                    $color = trim($color);
                                    $color_name = isset($color_names[$color]) ? $color_names[$color] : $color;
                                ?>
                                <label class="color-option">
                                    <input type="radio" name="options[<?php echo $option['id']; ?>]" value="<?php echo $color; ?>" <?php echo $color == $colors[0] ? 'checked' : ''; ?>>
                                    <span class="color-swatch" style="background-color: <?php echo $color; ?>" title="<?php echo $color_name; ?>"></span>
                                    <span class="color-name"><?php echo $color_name; ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            
                        <?php elseif ($option['option_type'] == 'size'): ?>
                            <div class="size-options">
                                <?php 
                                $sizes = explode(',', $option['choices']);
                                foreach ($sizes as $size): 
                                    $size = trim($size);
                                ?>
                                <label class="size-option">
                                    <input type="radio" name="options[<?php echo $option['id']; ?>]" value="<?php echo $size; ?>" <?php echo $size == $sizes[0] ? 'checked' : ''; ?>>
                                    <span class="size-label"><?php echo $size; ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            
                        <?php elseif ($option['option_type'] == 'text'): ?>
                            <input type="text" name="options[<?php echo $option['id']; ?>]" placeholder="Enter your text" class="text-option">
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="quantity-group">
                        <h3>Quantity</h3>
                        <div class="quantity-control">
                            <button type="button" class="quantity-minus">-</button>
                            <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                            <button type="button" class="quantity-plus">+</button>
                        </div>
                    </div>
                    
                    <div class="price-summary">
                        <h3>Price Summary</h3>
                        <p>Base Price: <span class="base-price">$<?php echo number_format($product['price'], 2); ?></span></p>
                        <p>Customization: <span class="customization-price">$0.00</span></p>
                        <p class="total-price">Total: <span>$<?php echo number_format($product['price'], 2); ?></span></p>
                    </div>
                    
                    <button type="submit" class="btn add-to-cart-btn">Add to Cart</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer remains the same -->
</body>
</html>