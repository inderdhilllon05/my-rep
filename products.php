<?php
include 'db_connection.php';

$conn = OpenCon();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products - DecorCraft</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
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

    <section class="products-section">
        <h2>Our Products</h2>
        <div class="filters">
            <select id="categoryFilter">
                <option value="all">All Categories</option>
                <option value="furniture">Furniture</option>
                <option value="wall-decor">Wall Decor</option>
                <option value="textiles">Textiles</option>
                <option value="lighting">Lighting</option>
            </select>
            <select id="priceFilter">
                <option value="all">All Prices</option>
                <option value="0-50">Under $50</option>
                <option value="50-100">$50 - $100</option>
                <option value="100-200">$100 - $200</option>
                <option value="200+">Over $200</option>
            </select>
        </div>
        <div class="product-grid" id="allProducts">
            <?php
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);
            
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '
                    <div class="product-card" data-category="'.strtolower($row["category"]).'" data-price="'.$row["price"].'">
                        <div class="product-img">
                            <img src="images/'.$row["image"].'" alt="'.$row["name"].'">
                        </div>
                        <div class="product-info">
                            <h3>'.$row["name"].'</h3>
                            <p>'.$row["description"].'</p>
                            <p class="price">$'.number_format($row["price"], 2).'</p>
                            <a href="customize.php?id='.$row["id"].'" class="btn">Customize</a>
                        </div>
                    </div>
                    ';
                }
            } else {
                echo "<p>No products found</p>";
            }
            ?>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="#customize">Customize</a></li>
                    <li><a href="#about">About</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: info@decorcraft.com</p>
                <p>Phone: +1 234 567 890</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2023 DecorCraft. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Filter products
        document.getElementById('categoryFilter').addEventListener('change', filterProducts);
        document.getElementById('priceFilter').addEventListener('change', filterProducts);
        
        function filterProducts() {
            const category = document.getElementById('categoryFilter').value;
            const priceRange = document.getElementById('priceFilter').value;
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const productCategory = product.dataset.category;
                const productPrice = parseFloat(product.dataset.price);
                
                let categoryMatch = category === 'all' || productCategory.includes(category);
                let priceMatch = false;
                
                if (priceRange === 'all') {
                    priceMatch = true;
                } else if (priceRange === '0-50') {
                    priceMatch = productPrice < 50;
                } else if (priceRange === '50-100') {
                    priceMatch = productPrice >= 50 && productPrice <= 100;
                } else if (priceRange === '100-200') {
                    priceMatch = productPrice > 100 && productPrice <= 200;
                } else if (priceRange === '200+') {
                    priceMatch = productPrice > 200;
                }
                
                if (categoryMatch && priceMatch) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>

<?php
CloseCon($conn);
?>