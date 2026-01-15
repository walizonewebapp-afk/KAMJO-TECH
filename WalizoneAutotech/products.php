<?php
// Include database connection
require_once 'config/db.php';

// Get product categories
$categories = [];
$categoryQuery = "SELECT * FROM product_categories ORDER BY name";
$categoryResult = $conn->query($categoryQuery);
if ($categoryResult && $categoryResult->num_rows > 0) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Get products (with optional category filter)
$products = [];
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

if ($category_id > 0) {
    $productQuery = "SELECT * FROM products WHERE category = ? AND in_stock = 1 ORDER BY name";
    $stmt = $conn->prepare($productQuery);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $productResult = $stmt->get_result();
} else {
    $productQuery = "SELECT * FROM products WHERE in_stock = 1 ORDER BY name";
    $productResult = $conn->query($productQuery);
}

if ($productResult && $productResult->num_rows > 0) {
    while ($row = $productResult->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Walizone Autotech</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Product Page Specific Styles */
        .products-container {
            padding: 2rem 0;
        }
        
        .product-filters {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            background: #f4f4f4;
            border: none;
            padding: 0.5rem 1rem;
            margin: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: #f7c08a;
            color: #333;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .product-card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            height: 200px;
            background-color: #f4f4f4;
            background-size: cover;
            background-position: center;
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-name {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .product-price {
            font-weight: bold;
            color: #f7c08a;
            margin-bottom: 1rem;
        }
        
        .product-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .product-actions {
            display: flex;
            justify-content: space-between;
        }
        
        .no-products {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">Walizone Autotech</div>
            <nav>
                <ul>
                    <li><a href="index.php#home">Home</a></li>
                    <li><a href="index.php#about">About</a></li>
                    <li><a href="index.php#services">Services</a></li>
                    <li><a href="products.php" class="active">Products</a></li>
                    <li><a href="index.php#dealership">Dealership</a></li>
                    <li><a href="index.php#contact">Contact Us</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="products-container">
        <div class="container">
            <h1>Our Products</h1>
            
            <!-- Product Filters -->
            <div class="product-filters">
                <a href="products.php" class="filter-btn <?php echo !isset($_GET['category']) ? 'active' : ''; ?>">All</a>
                <?php foreach ($categories as $category): ?>
                    <a href="products.php?category=<?php echo $category['id']; ?>" 
                       class="filter-btn <?php echo (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Product Grid -->
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <p>No products found in this category. Please check back later or browse other categories.</p>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image" style="background-image: url('<?php echo !empty($product['image_path']) ? htmlspecialchars($product['image_path']) : 'images/product-placeholder.jpg'; ?>')"></div>
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <div class="product-price">
                                    <?php echo !empty($product['price']) ? 'KSh ' . number_format($product['price'], 2) : 'Price on request'; ?>
                                </div>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . (strlen($product['description']) > 100 ? '...' : ''); ?>
                                </p>
                                <div class="product-actions">
                                    <a href="#" class="btn">Details</a>
                                    <a href="#" class="btn">Inquire</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Walizone Autotech and General Dealers. All rights reserved.</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>