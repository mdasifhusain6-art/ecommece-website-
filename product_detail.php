<?php
/**
 * Product Detail Page
 * E-Commerce Website
 */

require_once 'includes/config.php';

$page_title = 'Product Details';

$db = getDB();

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    setFlash('error', 'Invalid product ID.');
    redirect('products.php');
}

// Get product details
$stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setFlash('error', 'Product not found.');
    redirect('products.php');
}

$product = $result->fetch_assoc();
$stmt->close();

// Get related products from same category
$related_products = [];
$stmt = $db->prepare("SELECT id, name, price, stock_quantity FROM products WHERE category_id = ? AND id != ? AND stock_quantity > 0 LIMIT 4");
$stmt->bind_param("ii", $product['category_id'], $product_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $related_products[] = $row;
}
$stmt->close();

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                <?php if ($product['category_name']): ?>
                    <li class="breadcrumb-item"><a href="products.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>
        
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
                    <?php if (!empty($product['image_url']) && file_exists($product['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             class="img-fluid" style="max-height: 400px; object-fit: contain;">
                    <?php else: ?>
                        <div class="text-center p-5">
                            <i class="fas fa-box-open fa-6x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Product Image</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($product['stock_quantity'] <= 10 && $product['stock_quantity'] > 0): ?>
                    <div class="card-body text-center">
                        <span class="badge bg-warning fs-6 py-2 px-4">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Only <?php echo $product['stock_quantity']; ?> left in stock!
                        </span>
                    </div>
                <?php elseif ($product['stock_quantity'] == 0): ?>
                    <div class="card-body text-center">
                        <span class="badge bg-danger fs-6 py-2 px-4">
                            <i class="fas fa-times-circle me-1"></i>
                            Out of Stock
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="mb-4">
                <span class="text-muted"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h1 class="display-5 fw-bold mt-2"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <h2 class="text-primary mb-0">$<?php echo number_format($product['price'], 2); ?></h2>
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <span class="badge bg-success fs-6 py-2">In Stock</span>
                    <?php else: ?>
                        <span class="badge bg-danger fs-6 py-2">Out of Stock</span>
                    <?php endif; ?>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Description</h5>
                        <p class="card-text lead"><?php echo htmlspecialchars($product['description']); ?></p>
                    </div>
                </div>
                
                <?php if ($product['stock_quantity'] > 0): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label"><i class="fas fa-sort-numeric-up me-2"></i>Quantity</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary" onclick="adjustQuantity(-1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                                        <button type="button" class="btn btn-outline-secondary" onclick="adjustQuantity(1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block"><i class="fas fa-money-bill-wave me-2"></i>Total</label>
                                    <h3 class="text-primary mb-0" id="totalPrice">$<?php echo number_format($product['price'], 2); ?></h3>
                                </div>
                            </div>
                            
                            <button class="btn btn-primary w-100 py-3 mt-4 add-to-cart-btn" 
                                    data-id="<?php echo $product['id']; ?>" 
                                    data-price="<?php echo $product['price']; ?>" 
                                    data-stock="<?php echo $product['stock_quantity']; ?>">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Add to Cart
                            </button>
                            
                            <?php if (isLoggedIn()): ?>
                                <button class="btn btn-outline-primary w-100 py-3 mt-2" onclick="buyNow()">
                                    <i class="fas fa-bolt me-2"></i>
                                    Buy Now
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        This product is currently out of stock. Please check back later.
                    </div>
                <?php endif; ?>
                
                <div class="d-flex align-items-center text-muted">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <small>Added: <?php echo date('M d, Y', strtotime($product['created_at'])); ?></small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
        <div class="mt-5">
            <h3 class="mb-4">Related Products</h3>
            <div class="row g-4">
                <?php foreach ($related_products as $related): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card product-card h-100 shadow">
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                <i class="fas fa-box fa-2x text-muted"></i>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?php echo htmlspecialchars($related['name']); ?></h6>
                                <h5 class="text-primary mb-0">$<?php echo number_format($related['price'], 2); ?></h5>
                                <div class="mt-auto pt-3">
                                    <a href="product_detail.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-primary btn-sm w-100">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<script>
const productPrice = <?php echo $product['price']; ?>;
const maxStock = <?php echo $product['stock_quantity']; ?>;

function adjustQuantity(change) {
    const qtyInput = document.getElementById('quantity');
    let newQty = parseInt(qtyInput.value) + change;
    
    if (newQty < 1) newQty = 1;
    if (newQty > maxStock) newQty = maxStock;
    
    qtyInput.value = newQty;
    updateTotal();
}

function updateTotal() {
    const qty = parseInt(document.getElementById('quantity').value);
    const total = productPrice * qty;
    document.getElementById('totalPrice').textContent = '$' + total.toFixed(2);
}

function addToCart() {
    const qty = parseInt(document.getElementById('quantity').value);
    
    if (qty > maxStock) {
        alert('Requested quantity exceeds available stock.');
        return;
    }
    
    // Add to cart using JavaScript cart system
    addToCartJS(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', productPrice, qty);
    
    // Show success message
    const btn = document.querySelector('.add-to-cart-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check me-2"></i>Added to Cart!';
    btn.classList.remove('btn-primary');
    btn.classList.add('btn-success');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-primary');
    }, 2000);
}

function buyNow() {
    addToCart();
    window.location.href = 'checkout.php';
}

// Attach event listener
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.querySelector('.add-to-cart-btn');
    if (addBtn) {
        addBtn.addEventListener('click', addToCart);
    }
    
    const qtyInput = document.getElementById('quantity');
    if (qtyInput) {
        qtyInput.addEventListener('change', function() {
            let val = parseInt(this.value);
            if (isNaN(val) || val < 1) val = 1;
            if (val > maxStock) val = maxStock;
            this.value = val;
            updateTotal();
        });
    }
});
</script>
