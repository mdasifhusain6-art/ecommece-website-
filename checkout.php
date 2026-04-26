<?php
/**
 * Checkout Page
 * E-Commerce Website
 */

require_once 'includes/config.php';

$page_title = 'Checkout';

requireLogin();

$db = getDB();

// Initialize errors
$errors = [];

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    setFlash('error', 'Your cart is empty.');
    redirect('cart.php');
}

// Get cart items
$cart_items = [];
$subtotal = 0;

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

$tax = $subtotal * 0.08; // 8% tax
$shipping = $subtotal > 0 ? ($subtotal > 50 ? 0 : 5.99) : 0;
$total = $subtotal + $tax + $shipping;

// Get user details
$user_stmt = $db->prepare("SELECT full_name, email, address, phone FROM users WHERE id = ?");
$user_stmt->bind_param("i", $_SESSION['user_id']);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission.';
    } else {
        // Sanitize and validate input
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');

        if (empty($name)) {
            $errors[] = 'Name is required.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }

        if (empty($address)) {
            $errors[] = 'Address is required.';
        }

        if (empty($phone)) {
            $errors[] = 'Phone number is required.';
        }
        
        // Check stock availability
        $stock_ok = true;
        foreach ($cart_items as $item) {
            if ($item['quantity'] > $item['stock_quantity']) {
                $errors[] = 'Product "' . htmlspecialchars($item['name']) . '" is out of stock.';
                $stock_ok = false;
            }
        }

        // Process order if no errors
        if (empty($errors) && $stock_ok) {
            $db->autocommit(false);

            try {
                // Insert order
                $order_stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'pending')");
                $order_stmt->bind_param("ids", $_SESSION['user_id'], $total, $address);
                $order_stmt->execute();
                $order_id = $db->insert_id;
                $order_stmt->close();
                
                // Insert order items and reduce stock
                $item_stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stock_stmt = $db->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");

                foreach ($cart_items as $item) {
                    $item_stmt->bind_param("iiidd", $order_id, $item['id'], $item['quantity'], $item['price'], $item['subtotal']);
                    $item_stmt->execute();

                    $stock_stmt->bind_param("ii", $item['quantity'], $item['id']);
                    $stock_stmt->execute();
                }

                $item_stmt->close();
                $stock_stmt->close();

                // Commit transaction
                $db->commit();
                $db->autocommit(true);

                // Clear cart
                unset($_SESSION['cart']);

                setFlash('success', 'Order placed successfully! Order ID: ' . $order_id);
                redirect('orders.php');

            } catch (Exception $e) {
                $db->rollback();
                $db->autocommit(true);
                $errors[] = 'Failed to process order. Please try again.';
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
            <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0"><i class="fas fa-shopping-bag text-primary me-2"></i>Checkout</h1>
            <p class="text-muted mt-1">Complete your order securely</p>
        </div>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Shipping Form -->
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h4 class="card-title mb-0"><i class="fas fa-truck text-primary me-2"></i>Shipping Information</h4>
                </div>
                <div class="card-body p-4">
                    
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? $user['full_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? $user['email'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Shipping Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? $user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? $user['phone'] ?? ''); ?>">
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3"><i class="fas fa-lock text-primary me-2"></i>Payment Method</h5>

                        <div class="border rounded p-3 mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment" id="card" value="card" checked>
                                <label class="form-check-label fw-semibold" for="card">
                                    <i class="fas fa-credit-card text-primary me-2"></i>Credit/Debit Card
                                    <small class="text-muted d-block">Secure payment via Stripe</small>
                                </label>
                            </div>
                        </div>

                        <div class="border rounded p-3 mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment" id="paypal" value="paypal">
                                <label class="form-check-label fw-semibold" for="paypal">
                                    <i class="fab fa-paypal text-primary me-2"></i>PayPal
                                    <small class="text-muted d-block">Pay with your PayPal account</small>
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-info border-0" style="border-radius: 10px; background: linear-gradient(135deg, #dbeafe, #bfdbfe);">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <strong>Demo Mode:</strong> This is a demonstration. No actual charges will be processed.
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3 mt-4">
                            <i class="fas fa-lock me-2"></i>Place Order - $<?php echo number_format($total, 2); ?>
                        </button>
                        
                        <a href="cart.php" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Cart
                        </a>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-5">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-light">
                    <h4 class="card-title mb-0"><i class="fas fa-clipboard-list text-primary me-2"></i>Order Summary</h4>
                </div>
                <div class="card-body">
                    
                    <div class="mb-3" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                </div>
                                <span class="fw-bold">$<?php echo number_format($item['subtotal'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
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
                    <div class="d-flex justify-content-between mt-3 pt-3 border-top border-dark" style="background: linear-gradient(135deg, #f8fafc, #e2e8f0); padding: 1rem; border-radius: 8px; margin: -1rem -1rem 0 -1rem;">
                        <h5 class="mb-0 fw-bold">Total Amount</h5>
                        <h4 class="text-primary mb-0 fw-bold">$<?php echo number_format($total, 2); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>