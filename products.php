<?php
/**
 * Products Listing Page
 * E-Commerce Website
 */

require_once 'includes/config.php';

$page_title = 'Products';

$db = getDB();

// Get search and filter parameters
$search = trim($_GET['search'] ?? '');
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sort = $_GET['sort'] ?? 'name_asc';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 999999;

// Build query
$where_conditions = ['p.stock_quantity > 0'];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = '(p.name LIKE ? OR p.description LIKE ?)';
    $search_term = '%' . $search . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
}

if ($category > 0) {
    $where_conditions[] = 'p.category_id = ?';
    $params[] = $category;
    $types .= 'i';
}

$where_conditions[] = 'p.price >= ?';
$params[] = $min_price;
$types .= 'd';

$where_conditions[] = 'p.price <= ?';
$params[] = $max_price;
$types .= 'd';

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Sort order
$sort_orders = [
    'name_asc' => 'p.name ASC',
    'name_desc' => 'p.name DESC',
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'newest' => 'p.created_at DESC'
];
$order_by = $sort_orders[$sort] ?? 'p.name ASC';

// Get all categories for filter
$all_categories = [];
$stmt = $db->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $all_categories[] = $row;
}
$stmt->close();

// Get price range for filter
$price_range = [];
$stmt = $db->prepare("SELECT MIN(price) as min_price, MAX(price) as max_price FROM products WHERE stock_quantity > 0");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $price_range = $row;
}
$stmt->close();

// Count total products
$count_sql = "SELECT COUNT(*) as total FROM products p $where_clause";
$count_stmt = $db->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_products = $count_result->fetch_assoc()['total'];
$count_stmt->close();

// Get products
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $where_clause 
        ORDER BY $order_by";
$stmt = $db->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-filter me-2"></i>Filters</h5>
                    <hr>
                    
                    <form method="GET" action="products.php" id="filterForm">
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                        
                        <!-- Category Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold">Category</h6>
                            <div class="form-check">
                                <input class="form-check-input category-filter" type="radio" name="category" id="cat_all" value="0" <?php echo $category == 0 ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="cat_all">All Categories</label>
                            </div>
                            <?php foreach ($all_categories as $cat): ?>
                                <div class="form-check">
                                    <input class="form-check-input category-filter" type="radio" name="category" id="cat_<?php echo $cat['id']; ?>" value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="cat_<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Price Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold">Price Range</h6>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" class="form-control form-control-sm" name="min_price" placeholder="Min" value="<?php echo $min_price > 0 ? $min_price : ''; ?>" min="0" step="0.01" style="width: 80px;">
                                <span>-</span>
                                <input type="number" class="form-control form-control-sm" name="max_price" placeholder="Max" value="<?php echo $max_price < 999999 ? $max_price : ''; ?>" min="0" step="0.01" style="width: 80px;">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        <a href="products.php" class="btn btn-outline-secondary w-100 mt-2">Clear Filters</a>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-0"><?php echo $total_products; ?> Products Found</h2>
                    <?php if (!empty($search)): ?>
                        <small class="text-muted">for "<?php echo htmlspecialchars($search); ?>"</small>
                    <?php endif; ?>
                </div>
                
                <!-- Sort Dropdown -->
                <div class="d-flex gap-2">
                    <select class="form-select" id="sortSelect" onchange="window.location.href=this.value">
                        <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name_asc'])); ?>" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name: A-Z</option>
                        <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name_desc'])); ?>" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name: Z-A</option>
                        <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'price_asc'])); ?>" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'price_desc'])); ?>" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'newest'])); ?>" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    </select>
                </div>
            </div>
            
            <?php if (count($products) > 0): ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-6 col-lg-4">
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
                                    <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <h4 class="text-primary mb-0">$<?php echo number_format($product['price'], 2); ?></h4>
                                        <button class="btn btn-primary btn-sm add-to-cart" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo $product['price']; ?>" data-stock="<?php echo $product['stock_quantity']; ?>">
                                            <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No products found</h4>
                    <p class="text-muted">Try adjusting your filters or search terms.</p>
                    <a href="products.php" class="btn btn-primary">View All Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>