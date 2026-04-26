<?php
/**
 * User Dashboard
 * E-Commerce Website
 */

require_once 'includes/config.php';

$page_title = 'Dashboard';

requireLogin();

$db = getDB();

// Get user details
$user_stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param("i", $_SESSION['user_id']);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

// Get recent orders
$orders = [];
$order_stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$order_stmt->bind_param("i", $_SESSION['user_id']);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
while ($row = $order_result->fetch_assoc()) {
    $orders[] = $row;
}
$order_stmt->close();

// Get order statistics
$total_orders = count($orders);
$total_spent = 0;
foreach ($orders as $order) {
    $total_spent += $order['total_amount'];
}

include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4"><i class="fas fa-user-circle me-2"></i>My Dashboard</h1>
    
    <div class="row mb-5">
        <!-- Account Overview -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4"><i class="fas fa-user me-2"></i>Account Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p><strong>Member Since:</strong> <?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                            <p><strong>Role:</strong> <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?>"><?php echo strtoupper($user['role']); ?></span></p>
                        </div>
                    </div>
                    <a href="profile.php" class="btn btn-primary mt-3">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-chart-pie me-2"></i>Statistics</h4>
                    <hr>
                    <div class="text-center my-4">
                        <h2 class="text-primary"><?php echo count($orders); ?></h2>
                        <p class="text-muted mb-1">Total Orders</p>
                    </div>
                    <div class="text-center my-4">
                        <h2 class="text-success">$<?php echo number_format($total_spent, 2); ?></h2>
                        <p class="text-muted mb-1">Total Spent</p>
                    </div>
                    <div class="text-center my-4">
                        <h2 class="text-info"><?php echo isset($user['address']) && !empty($user['address']) ? 'Yes' : 'No'; ?></h2>
                        <p class="text-muted mb-1">Address Saved</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0"><i class="fas fa-box me-2"></i>Recent Orders</h4>
                <a href="orders.php" class="btn btn-outline-primary">
                    View All Orders <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            
            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No orders yet</h5>
                    <p class="text-muted">Start shopping to place your first order!</p>
                    <a href="products.php" class="btn btn-primary">Browse Products</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php
                                            echo $order['status'] == 'delivered' ? 'success' :
                                                 ($order['status'] == 'shipped' ? 'info' :
                                                 ($order['status'] == 'processing' ? 'warning' :
                                                 ($order['status'] == 'cancelled' ? 'danger' : 'secondary'))); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($db): ?>
        <div class="mt-4 text-center">
            <p class="text-muted">Database connection: <span class="text-success">Active</span></p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>