<?php
/**
 * Shopping Cart
 * E-Commerce Website
 */

require_once 'includes/config.php';

$page_title = 'Shopping Cart';

requireLogin();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$db = getDB();

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect('cart.php');
    }
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update':
            $product_id = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
            } else {
                // Check stock availability
                $stmt = $db->prepare("SELECT stock_quantity FROM products WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    if ($quantity <= $row['stock_quantity']) {
                        $_SESSION['cart'][$product_id] = $quantity;
                    } else {
                        setFlash('error', 'Quantity exceeds available stock for this product.');
                    }
                }
                $stmt->close();
            }
            break;
            
        case 'remove':
            $product_id = (int)$_POST['product_id'];
            unset($_SESSION['cart'][$product_id]);
            setFlash('success', 'Product removed from cart.');
            break;
            
        case 'clear':
            $_SESSION['cart'] = [];
            setFlash('success', 'Cart cleared successfully.');
            break;
    }
    
    redirect('cart.php');
}

// Get cart products details
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    
    $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $types = str_repeat('i', count($product_ids));
    $stmt->bind_param($types, ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $quantity = $_SESSION['cart'][$row['id']];
        $row['quantity'] = $quantity;
        $row['subtotal'] = $row['price'] * $quantity;
        $cart_items[] = $row;
        $subtotal += $row['subtotal'];
    }
    $stmt->close();
}

$tax = $subtotal * 0.08; // 8% tax
$shipping = $subtotal > 0 ? ($subtotal > 50 ? 0 : 5.99) : 0; // Free shipping over $50
$total = $subtotal + $tax + $shipping;

include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h1>
    
    <?php 
    $flash = getFlash();
    if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8">
            <?php if (empty($cart_items)): ?>
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted">Your cart is empty</h3>
                        <p class="text-muted">Start shopping to add items to your cart.</p>
                        <a href="products.php" class="btn btn-primary px-4 py-2 mt-3">
                            <i class="fas fa-box-open me-2"></i>Browse Products
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                                        <i class="fas fa-box text-muted"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                        <small class="text-muted">In stock: <?php echo $item['stock_quantity']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center" style="width: 120px;">
                                                <form method="POST" action="cart.php" class="d-flex align-items-center justify-content-center">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                    <div class="input-group input-group-sm" style="width: 100px;">
                                                        <button class="btn btn-outline-secondary" type="submit" onclick="this.form.querySelector('[name=quantity]').value = parseInt(this.form.querySelector('[name=quantity]').value) - 1">-
                                                        </button>
                                                        <input type="number" name="quantity" class="form-control form-control-sm text-center border-start-0 border-end-0" 
                                                               value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>" style="font-size: 14px;">
                                                        <button class="btn btn-outline-secondary" type="submit" onclick="this.form.querySelector('[name=quantity]').value = parseInt(this.form.querySelector('[name=quantity]').value) + 1">+
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-end">$<?php echo number_format($item['price'], 2); ?></td>
                                            <td class="text-end fw-bold">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                            <td class="text-end">
                                                <form method="POST" action="cart.php" style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" class="btn btn-sm text-danger" onclick="return confirm('Remove this item?');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0 p-4">
                            <div class="d-flex justify-content-between">
                                <form method="POST" action="cart.php" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="clear">
                                    <button type="submit" class="btn btn-outline-secondary" onclick="return confirm('Clear entire cart?');">
                                        <i class="fas fa-trash-alt me-1"></i>Clear Cart
                                    </button>
                                </form>
                                <a href="products.php" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-calculator me-2"></i>Order Summary</h4>
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (<?php echo count($cart_items); ?> items)</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax (8%)</span>
                        <span>$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span>
                            <?php if ($shipping == 0): ?>
                                <span class="text-success">FREE</span>
                            <?php else: ?>
                                $<?php echo number_format($shipping, 2); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if ($subtotal > 0 && $subtotal <= 50): ?>
                        <div class="alert alert-success small py-2 px-3 mb-2">
                            <i class="fas fa-truck me-1"></i>
                            Spend $<?php echo number_format(50 - $subtotal, 2); ?> more for free shipping!
                        </div>
                    <?php endif; ?>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between h4 fw-bold text-primary">
                        <span>Total</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <?php if (!empty($cart_items)): ?>
                        <a href="checkout.php" class="btn btn-primary w-100 py-3 mt-3">
                            <i class="fas fa-lock mr-2"></i>Proceed to Checkout
                        </a>
                        <?php if (!isLoggedIn()): ?>
                            <p class="text-muted small text-center mt-2 mb-0">
                                <a href="login.php" class="text-primary">Login</a> to save this cart
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt fa-2x text-success mb-3"></i>
                    <h6 class="fw-bold">Secure Checkout</h6>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-lock me-1"></i> Your payment information is secure
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>