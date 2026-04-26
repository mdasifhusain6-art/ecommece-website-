<?php
/**
 * Orders History
 * E-Commerce Website
 */

require_once 'includes/config.php';

$page_title = 'My Orders';

requireLogin();

db = getDB();

// Pagination
page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
per_page = 10;
offset = (page - 1) * per_page;

// Get total count
total_stmt = db->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
total_stmt->bind_param("i", $_SESSION['user_id']);
total_stmt->execute();
total_result = total_stmt->get_result();
total_orders = total_result->fetch_assoc()['total'];
total_stmt->close();

total_pages = ceil(total_orders / per_page);

// Get orders
stmt = db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
stmt->bind_param("iii", $_SESSION['user_id'], per_page, offset);
stmt->execute();
result = stmt->get_result();
orders = [];
while (row = result->fetch_assoc()) {
    orders[] = row;
}
stmt->close();

include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4"><i class="fas fa-box me-2"></i>My Orders</h1>
    
    <?php if (empty(orders)): ?>
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No orders yet</h4>
            <p class="text-muted mb-4">You haven't placed any orders yet.</p>
            <a href="products.php" class="btn btn-primary px-4 py-2">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (orders as order): ?>
                                <?php 
                                // Get order items count
                                items_stmt = db->prepare("SELECT COUNT(*) as count FROM order_items WHERE order_id = ?");
                                items_stmt->bind_param("i", order['id']);
                                items_stmt->execute();
                                items_result = items_stmt->get_result();
                                items_count = items_result->fetch_assoc()['count'];
                                items_stmt->close();
                                ?>
                                <tr>
                                    <td>#<?php echo order['id']; ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime(order['created_at'])); ?></td>
                                    <td><?php echo items_count; ?> item(s)</td>
                                    <td class="fw-bold">$<?php echo number_format(order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo order['status'] == 'delivered' ? 'success' : 
                                                 (order['status'] == 'shipped' ? 'info' : 
                                                 (order['status'] == 'processing' ? 'warning' : 
                                                 (order['status'] == 'cancelled' ? 'danger' : 'secondary'))); ?>">
                                            <?php echo ucfirst(order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order_detail.php?id=<?php echo order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if (total_pages > 1): ?>
                    <div class="card-footer bg-white d-flex justify-content-center">
                        <nav>
                            <ul class="pagination mb-0">
                                <?php if (page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo page - 1; ?>">Previous</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for (i = 1; i <= total_pages; i++): ?>
                                    <li class="page-item <?php echo i == page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo i; ?>"><?php echo i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if (page < total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo page + 1; ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>