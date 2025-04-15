<?php
function OpenCon() {
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = ""; // Leave empty if no password
    $dbname = "decorcraft";
    
    // First connect without selecting a database
    $conn = new mysqli($dbhost, $dbuser, $dbpass);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if it doesn't exist
    $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->select_db($dbname);
    
    return $conn;
}

function CloseCon($conn) {
    $conn->close();
}

// Create tables if they don't exist
$conn = OpenCon();

// Products table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(100) NOT NULL
)";

if ($conn->query($sql) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

// Customization options table
$sql = "CREATE TABLE IF NOT EXISTS customization_options (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT(6) UNSIGNED NOT NULL,
    option_name VARCHAR(100) NOT NULL,
    option_type ENUM('select', 'color', 'size', 'text') NOT NULL,
    choices TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

// Insert sample data if tables are empty
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    // Sample products
    $products = [
        ["Custom Wooden Coffee Table", "Handcrafted wooden table with customizable dimensions and finish", 299.99, "furniture", "table.jpg"],
        ["Personalized Wall Art", "Choose from various designs and add your personal touch", 89.99, "wall-decor", "wall-art.jpg"],
        ["Designer Throw Pillows", "Premium quality pillows with customizable fabrics", 39.99, "textiles", "pillows.jpg"],
        ["Custom Curtains", "Tailor-made curtains to fit your windows perfectly", 129.99, "textiles", "curtains.jpg"],
        ["Adjustable Floor Lamp", "Modern lamp with adjustable height and light temperature", 149.99, "lighting", "lamp.jpg"],
        ["Rustic Bookshelf", "Customizable wooden bookshelf with multiple finish options", 249.99, "furniture", "bookshelf.jpg"],
        ["Photo Wall Display", "Create your own photo collage with customizable frames", 79.99, "wall-decor", "photo-wall.jpg"],
        ["Velvet Accent Chair", "Luxurious velvet chair with customizable colors", 199.99, "furniture", "accent-chair.jpg"],
        ["Geometric Wall Mirror", "Modern geometric mirror with frame customization", 129.99, "wall-decor", "wall-mirror.jpg"],
        ["Handwoven Area Rug", "Custom-sized rug with various pattern options", 179.99, "textiles", "area-rug.jpg"],
        ["Ceramic Table Vase", "Handcrafted ceramic vase with glaze options", 49.99, "decor", "table-vase.jpg"],
        ["Macrame Wall Hanging", "Boho-style macrame with customizable colors", 69.99, "wall-decor", "macrame.jpg"],
        ["Marble Side Table", "Elegant marble-top table with metal base options", 159.99, "furniture", "side-table.jpg"]
    ];
    
    foreach ($products as $product) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $product[0], $product[1], $product[2], $product[3], $product[4]);
        $stmt->execute();
        $product_id = $stmt->insert_id;
        $stmt->close();
        
        // Add customization options based on product
        // After inserting each product, add their customization options:
if ($product[0] == "Custom Wooden Coffee Table") {
    $options = [
        ["Wood Type", "select", "Oak,Walnut,Maple,Cherry"],
        ["Finish", "select", "Natural,Stained,Painted"],
        ["Size", "size", "Small (36\"),Medium (48\"),Large (60\")"],
        ["Edge Style", "select", "Straight,Beveled,Round"]
    ];
} elseif ($product[0] == "Personalized Wall Art") {
    $options = [
        ["Design", "select", "Abstract,Geometric,Nature,Cityscape"],
        ["Background Color", "color", "#FFFFFF,#000000,#F5F5F5,#E0E0E0,#B71C1C,#0D47A1,#1B5E20,#FFD600"],
        ["Text", "text", ""],
        ["Text Color", "color", "#000000,#FFFFFF,#B71C1C,#0D47A1"]
    ];
} elseif ($product[0] == "Designer Throw Pillows") {
    $options = [
        ["Fabric", "select", "Linen,Cotton,Velvet,Silk"],
        ["Color", "color", "#FFFFFF,#000000,#B71C1C,#0D47A1,#1B5E20,#FFD600,#6A1B9A,#E65100"],
        ["Size", "size", "18x18\",20x20\",22x22\""]
    ];
} elseif ($product[0] == "Custom Curtains") {
    $options = [
        ["Fabric", "select", "Linen,Sheer,Blackout,Silk"],
        ["Color", "color", "#FFFFFF,#000000,#F5F5F5,#E0E0E0,#B71C1C,#0D47A1,#1B5E20"],
        ["Length", "select", "63\",84\",95\",108\""]
    ];
} elseif ($product[0] == "Adjustable Floor Lamp") {
    $options = [
        ["Base Color", "color", "#000000,#FFFFFF,#C0C0C0,#B71C1C"],
        ["Shade Color", "color", "#FFFFFF,#000000,#F5F5F5,#B71C1C"],
        ["Height", "select", "60\",65\",70\""]
    ];
} elseif ($product[0] == "Rustic Bookshelf") {
    $options = [
        ["Wood Type", "select", "Oak,Walnut,Pine,Reclaimed Wood"],
        ["Finish", "select", "Natural,Whitewash,Distressed,Stained"],
        ["Size", "size", "Small (24\"),Medium (36\"),Large (48\")"],
        ["Number of Shelves", "select", "3,4,5,6"]
    ];
} elseif ($product[0] == "Photo Wall Display") {
    $options = [
        ["Frame Style", "select", "Modern,Rustic,Vintage,Minimalist"],
        ["Frame Color", "color", "#FFFFFF,#000000,#6d4c41,#E0E0E0,#B71C1C"],
        ["Layout", "select", "Grid,Collage,Linear,Freeform"],
        ["Number of Frames", "select", "4,6,8,10,12"]
    ];
} elseif ($product[0] == "Velvet Accent Chair") {
    $options = [
        ["Fabric Color", "color", "#B71C1C,#0D47A1,#1B5E20,#6A1B9A,#000000,#FFFFFF"],
        ["Leg Finish", "select", "Brass,Chrome,Black,Gold,Wood"],
        ["Back Height", "select", "Standard,Tall"]
    ];
} elseif ($product[0] == "Geometric Wall Mirror") {
    $options = [
        ["Frame Shape", "select", "Hexagon,Circle,Square,Rectangle"],
        ["Frame Color", "color", "#000000,#FFFFFF,#6d4c41,#C0C0C0"],
        ["Size", "size", "Small (12\"),Medium (18\"),Large (24\")"]
    ];
} elseif ($product[0] == "Handwoven Area Rug") {
    $options = [
        ["Pattern", "select", "Geometric,Tribal,Floral,Striped,Solid"],
        ["Primary Color", "color", "#000000,#FFFFFF,#6d4c41,#B71C1C,#0D47A1,#1B5E20"],
        ["Size", "size", "3x5 ft,5x7 ft,8x10 ft,9x12 ft"]
    ];
} elseif ($product[0] == "Ceramic Table Vase") {
    $options = [
        ["Shape", "select", "Cylinder,Bud,Urn,Asymmetric"],
        ["Glaze", "select", "Matte,Glossy,Crackle,Metallic"],
        ["Color", "color", "#FFFFFF,#000000,#6d4c41,#B71C1C,#0D47A1"]
    ];
} elseif ($product[0] == "Macrame Wall Hanging") {
    $options = [
        ["Design", "select", "Simple,Detailed,Geometric,Bohemian"],
        ["Color", "color", "#FFFFFF,#000000,#6d4c41,#E0E0E0"],
        ["Size", "size", "Small (12\"),Medium (18\"),Large (24\")"]
    ];
} elseif ($product[0] == "Marble Side Table") {
    $options = [
        ["Marble Type", "select", "Carrara,Calacatta,Emperador,Travertine"],
        ["Base Material", "select", "Brass,Chrome,Black Metal,Wood"],
        ["Size", "size", "Small (16\"),Medium (20\"),Large (24\")"]
    ];
}
        // Continue for other new products...
        if (isset($options)) {
            foreach ($options as $option) {
                $stmt = $conn->prepare("INSERT INTO customization_options (product_id, option_name, option_type, choices) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $product_id, $option[0], $option[1], $option[2]);
                $stmt->execute();
                $stmt->close();
            }
            unset($options);
        }
    }
}

CloseCon($conn);
?>

