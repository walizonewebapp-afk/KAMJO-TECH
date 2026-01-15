<?php
// Include database connection
require_once 'config/db.php';

// Get dealership listings
$listings = [];
$type = isset($_GET['type']) ? $_GET['type'] : '';

if (!empty($type) && in_array($type, ['car', 'motorcycle', 'agricultural'])) {
    $listingQuery = "SELECT * FROM dealership_listings WHERE type = ? AND status = 'active' ORDER BY created_at DESC";
    $stmt = $conn->prepare($listingQuery);
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $listingResult = $stmt->get_result();
} else {
    $listingQuery = "SELECT * FROM dealership_listings WHERE status = 'active' ORDER BY created_at DESC";
    $listingResult = $conn->query($listingQuery);
}

if ($listingResult && $listingResult->num_rows > 0) {
    while ($row = $listingResult->fetch_assoc()) {
        $listings[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealership - Walizone Autotech</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Dealership Page Specific Styles */
        .dealership-container {
            padding: 2rem 0;
        }
        
        .dealership-filters {
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
        
        .listing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .listing-card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .listing-card:hover {
            transform: translateY(-5px);
        }
        
        .listing-image {
            height: 200px;
            background-color: #f4f4f4;
            background-size: cover;
            background-position: center;
        }
        
        .listing-info {
            padding: 1.5rem;
        }
        
        .listing-title {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .listing-price {
            font-weight: bold;
            color: #f7c08a;
            margin-bottom: 0.5rem;
        }
        
        .listing-details {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        
        .listing-detail {
            margin-right: 1rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .listing-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .listing-actions {
            display: flex;
            justify-content: space-between;
        }
        
        .no-listings {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .post-listing {
            text-align: center;
            margin: 2rem 0;
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
                    <li><a href="products.php">Products</a></li>
                    <li><a href="dealership.php" class="active">Dealership</a></li>
                    <li><a href="index.php#contact">Contact Us</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="dealership-container">
        <div class="container">
            <h1>Dealership Portal</h1>
            
            <div class="post-listing">
                <p>Have a vehicle or equipment to sell? Join our dealership network!</p>
                <a href="post-listing.php" class="btn">Post a Listing</a>
            </div>
            
            <!-- Dealership Filters -->
            <div class="dealership-filters">
                <a href="dealership.php" class="filter-btn <?php echo !isset($_GET['type']) ? 'active' : ''; ?>">All</a>
                <a href="dealership.php?type=car" class="filter-btn <?php echo (isset($_GET['type']) && $_GET['type'] == 'car') ? 'active' : ''; ?>">Cars</a>
                <a href="dealership.php?type=motorcycle" class="filter-btn <?php echo (isset($_GET['type']) && $_GET['type'] == 'motorcycle') ? 'active' : ''; ?>">Motorcycles</a>
                <a href="dealership.php?type=agricultural" class="filter-btn <?php echo (isset($_GET['type']) && $_GET['type'] == 'agricultural') ? 'active' : ''; ?>">Agricultural</a>
            </div>
            
            <!-- Listings Grid -->
            <?php if (empty($listings)): ?>
                <div class="no-listings">
                    <p>No listings found in this category. Please check back later or browse other categories.</p>
                </div>
            <?php else: ?>
                <div class="listing-grid">
                    <?php foreach ($listings as $listing): ?>
                        <div class="listing-card">
                            <?php 
                            $imagePaths = !empty($listing['image_paths']) ? explode(',', $listing['image_paths']) : [];
                            $firstImage = !empty($imagePaths) ? trim($imagePaths[0]) : 'images/vehicle-placeholder.jpg';
                            ?>
                            <div class="listing-image" style="background-image: url('<?php echo htmlspecialchars($firstImage); ?>')"></div>
                            <div class="listing-info">
                                <h3 class="listing-title"><?php echo htmlspecialchars($listing['title']); ?></h3>
                                <div class="listing-price">KSh <?php echo number_format($listing['price'], 2); ?></div>
                                <div class="listing-details">
                                    <?php if (!empty($listing['year'])): ?>
                                        <span class="listing-detail"><i class="fas fa-calendar"></i> <?php echo htmlspecialchars($listing['year']); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($listing['make'])): ?>
                                        <span class="listing-detail"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($listing['make']); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($listing['model'])): ?>
                                        <span class="listing-detail"><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($listing['model']); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($listing['condition_status'])): ?>
                                        <span class="listing-detail"><i class="fas fa-star"></i> <?php echo htmlspecialchars($listing['condition_status']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="listing-description">
                                    <?php echo htmlspecialchars(substr($listing['description'], 0, 100)) . (strlen($listing['description']) > 100 ? '...' : ''); ?>
                                </p>
                                <div class="listing-actions">
                                    <a href="listing-details.php?id=<?php echo $listing['id']; ?>" class="btn">View Details</a>
                                    <a href="contact-seller.php?listing=<?php echo $listing['id']; ?>" class="btn">Contact Seller</a>
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