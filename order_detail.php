<?php
/**
 * Order Detail Page
 * E-Commerce Website
 */

require_once 'includes/config.php';

$page_title = 'Order Details';

requireLogin();

db = getDB();

// Get order ID
order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (order_id <= 0) {
    setFlash('error', 'Invalid order ID.');
    redirect('orders.php');
}

// Get order details
stmt = db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
stmt->bind_param("ii", order_id, $_SESSION['user_id']);
stmt->execute();
result = stmt->get_result();

if (result->num_rows === 0) {
    setFlash('error', 'Order not found.');
    redirect('orders.php');
}

order = result->fetch_assoc();
stmt->close();

// Get order items
items = [];
stmt = db->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
stmt->bind_param("i", order_id);
stmt->execute();
result = stmt->get_result();
while (row = result->fetch_assoc()) {
    items[] = row;
}
stmt->close();

include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4"><i class="fas fa-receipt me-2"></i>Order #<?php echo order['id']; ?></h1>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Order Status -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-info-circle me-2"></i>Order Status</h4>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-<?php 
                            echo order['status'] == 'delivered' ? 'success' : 
                                 (order['status'] == 'shipped' ? 'info' : 
                                 (order['status'] == 'processing' ? 'warning' : 
                                 (order['status'] == 'cancelled' ? 'danger' : 'secondary'))); ?> fs-4 py-3 px-4">
                            <i class="fas fa-<?php 
                                echo order['status'] == 'delivered' ? 'check-circle' : 
                                     (order['status'] == 'shipped' ? 'truck' : 
                                     (order['status'] == 'processing' ? 'cog' : 
                                     (order['status'] == 'cancelled' ? 'times-circle' : 'clock'))); ?> me-2"></i>
                            <?php echo ucfirst(order['status']); ?>
                        </span>
                        <span class="text-muted ms-3">
                            Placed on <?php echo date('F d, Y H:i', strtotime(order['created_at'])); ?>
                        </span>
                    </div>
                    
                    <!-- Status Timeline -->
                    <div class="mt-4">
                        <div class="row">
                            <div class="col text-center">
                                <div class="position-relative">
                                    <div class="position-absolute top-0 start-50 translate-middle-x bg-primary" style="width: 2px; height: 100%;"></div>
                                    <div class="position-relative bg-white px-2">
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    </div>
                                </div>
                                <p class="mt-3 fw-bold">Order Placed</p>
                                <small class="text-muted"><?php echo date('M d', strtotime(order['created_at'])); ?></small>
                            </div>
                            <div class="col text-center">
                                <?php if (in_array(order['status'], ['processing', 'shipped', 'delivered'])): ?>
                                    <div class="position-relative">
                                        <div class="position-absolute top-0 start-50 translate-middle-x bg-primary" style="width: 2px; height: 100%;"></div>
                                        <div class="position-relative bg-white px-2">
                                            <i class="fas fa-cog text-primary fa-2x"></i>
                                        </div>
                                    </div>
                                    <p class="mt-3 fw-bold">Processing</p>
                                    <small class="text-muted">In Progress</small>
                                <?php else: ?>
                                    <div class="position-relative">
                                        <div class="position-absolute top-0 start-50 translate-middle-x bg-secondary" style="width: 2px; height: 100%;"></div>
                                        <div class="position-relative bg-white px-2">
                                            <i class="fas fa-cog text-secondary fa-2x"></i>
                                        </div>
                                    </div>
                                    <p class="mt-3 fw-bold text-muted">Processing</p>
                                    <small class="text-muted">Pending</small>
                                <?php endif; ?>
                            </div>
                            <div class="col text-center">
                                <?php if (in_array(order['status'], ['shipped', 'delivered'])): ?>
                                    <div class="position-relative">
                                        <div class="position-absolute top-0 start-50 translate-middle-x bg-primary" style="width: 2px; height: 100%;"></div>
                                        <div class="position-relative bg-white px-2">
                                            <i class="fas fa-truck text-primary fa-2x"></i>
                                        </div>
                                    </div>
                                    <p class="mt-3 fw-bold">Shipped</p>
                                    <small class="text-muted">In Transit</small>
                                <?php else: ?>
                                    <div class="position-relative">
                                        <div class="position-absolute top-0 start-50 translate-middle-x bg-secondary" style="width: 2px; height: 100%;"></div>
                                        <div class="position-relative bg-white px-2">
                                            <i class="fas fa-truck text-secondary fa-2x"></i>
                                        </div>
                                    </div>
                                    <p class="mt-3 fw-bold text-muted">Shipped</p>
                                    <small class="text-muted">Pending</small>
                                <?php endif; ?>
                            </div>
                            <div class="col text-center">
                                <?php if (order['status'] == 'delivered'): ?>
                                    <div class="position-relative bg-white px-2">
                                        <i class="fas fa-box-open text-success fa-2x"></i>
                                    </div>
                                    <p class="mt-3 fw-bold text-success">Delivered</p>
                                    <small class="text-muted">Completed</small>
                                <?php else: ?>
                                    <div class="position-relative bg-white px-2">
                                        <i class="fas fa-box-open text-secondary fa-2x"></i>
                                    </div>
                                    <p class="mt-3 fw-bold text-muted">Delivered</p>
                                    <small class="text-muted">Pending</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4"><i class="fas fa-shopping-bag me-2"></i>Items</h4>
                    <?php foreach (items as item): ?>
                        <div class="d-flex align-items-center border-bottom py-3">
                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 70px; height: 70px;">
                                <i class="fas fa-box text-muted fa-lg"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?php echo htmlspecialchars(item['name']); ?></h6>
                                <p class="text-muted small mb-1">$<?php echo number_format(item['price'], 2); ?> × <?php echo item['quantity']; ?></p>
                            </div>
                            <div class="text-end">
                                <h5 class="text-primary mb-0">$<?php echo number_format(item['subtotal'], 2); ?></h5>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-file-invoice-dollar me-2"></i>Order Summary</h4>
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>$<?php 
                            subtotal = 0;
                            foreach (items as item):
                                subtotal += item['subtotal'];
                            endforeach;
                            echo number_format(subtotal, 2); 
                        ?></span>
                    </div>
                    
                    <?php 
                    tax = subtotal * 0.08;
                    shipping = subtotal > 50 ? 0 : 5.99;
                    ?>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax (8%)</span>
                        <span>$<?php echo number_format(tax, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span>
                            <?php if (shipping == 0): ?>
                                <span class="text-success">FREE</span>
                            <?php else: ?>
                                $<?php echo number_format(shipping, 2); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="d-flex justify-content-between h4 fw-bold text-primary">
                        <span>Total</span>
                        <span>$<?php echo number_format(order['total_amount'], 2); ?></span>
                    </div>
                    
                    <hr>
                    
                    <h6 class="fw-bold"><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</h6>
                    <p class="text-muted small"><?php echo nl2br(htmlspecialchars(order['shipping_address'])); ?></p>
                    
                    <?php if (order['status'] == 'delivered'): ?>
                        <a href="#" class="btn btn-outline-primary w-100 mt-3" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Receipt
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-question-circle me-2"></i>Need Help?</h6>
                    <p class="text-muted small">
                        Contact our support team at <a href="mailto:support@ecommerce.com">support@ecommerce.com</a>
                    </p>
                    <p class="text-muted small mb-0">
                        Order ID: #<?php echo order['id']; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>