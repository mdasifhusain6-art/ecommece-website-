<?php
/**
 * Homepage
 * E-Commerce Website
 */

require_once 'includes/config.php';

$page_title = 'Home';

$db = getDB();

// Get featured products (limit 4)
$featured_products = [];
$stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.stock_quantity > 0 ORDER BY RAND() LIMIT 4");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $featured_products[] = $row;
}
$stmt->close();

// Get category counts
$categories = [];
$stmt = $db->prepare("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id AND p.stock_quantity > 0 GROUP BY c.id");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
$stmt->close();

// Get total products and orders counts
$total_products = 0;
$stmt = $db->prepare("SELECT COUNT(*) as count FROM products WHERE stock_quantity > 0");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $total_products = $row['count'];
}
$stmt->close();

include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-3 fw-bold mb-4">Shop the Best Products</h1>
        <p class="lead mb-4">Discover amazing deals on top-quality items from our curated collection.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="products.php" class="btn btn-light btn-lg px-4 py-3">
                <i class="fas fa-shopping-bag me-2"></i>Shop Now
            </a>
            <a href="products.php" class="btn btn-outline-light btn-lg px-4 py-3">
                <i class="fas fa-tag me-2"></i>View Deals
            </a>
        </div>
    </div>
</div>

<!-- Categories Section -->
<div class="container mb-5">
    <h2 class="text-center mb-4 display-5 fw-bold">Shop by Category</h2>
    <div class="row g-4">
        <?php foreach ($categories as $category): ?>
            <?php if ($category['product_count'] > 0): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card category-card h-100 shadow">
                        <div class="card-body text-center p-4">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-<?php 
                                    $icons = ['box', 'tshirt', 'book', 'home', 'basketball'];
                                    echo $icons[$category['id'] % 5];
                                ?> fa-2x text-primary"></i>
                            </div>
                            <h4 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h4>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
                            <span class="badge bg-primary fs-6 py-2 px-3">
                                <?php echo $category['product_count']; ?> products
                            </span>
                        </div>
                        <a href="products.php?category=<?php echo $category['id']; ?>" class="stretched-link"></a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<!-- Featured Products Section -->
<div class="container mb-5">
    <h2 class="text-center mb-4 display-5 fw-bold">Featured Products</h2>
    <div class="row g-4">
        <?php foreach ($featured_products as $product): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card product-card h-100 shadow">
                    <div class="position-relative">
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-box-open fa-4x text-muted"></i>
                        </div>
                        <?php if ($product['stock_quantity'] <= 10 && $product['stock_quantity'] > 0): ?>
                            <span class="badge bg-warning position-absolute top-0 start-0 m-2">Low Stock</span>
                        <?php elseif ($product['stock_quantity'] == 0): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <span class="text-muted small mb-1"><?php echo htmlspecialchars($product['category_name']); ?></span>
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>...</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <h4 class="text-primary mb-0">$<?php echo number_format($product['price'], 2); ?></h4>
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Stats Section -->
<div class="bg-light py-5 mb-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="p-4">
                    <h2 class="display-4 fw-bold text-primary"><?php echo $total_products; ?>+</h2>
                    <p class="text-muted mb-0">Products Available</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="p-4">
                    <h2 class="display-4 fw-bold text-primary"><?php echo count($categories); ?></h2>
                    <p class="text-muted mb-0">Categories</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <h2 class="display-4 fw-bold text-primary">100%</h2>
                    <p class="text-muted mb-0">Satisfaction</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>