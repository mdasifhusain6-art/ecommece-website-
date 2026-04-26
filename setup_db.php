<?php
/**
 * Database Setup Script
 * Creates required tables if they don't exist
 */

require_once 'includes/config.php';

$conn = getDB();

// Create users table
$users_sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($users_sql) === TRUE) {
    echo "Users table ready.\n";
} else {
    echo "Error creating users table: " . $conn->error . "\n";
}

// Create categories table
$categories_sql = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($categories_sql) === TRUE) {
    echo "Categories table ready.\n";
} else {
    echo "Error creating categories table: " . $conn->error . "\n";
}

// Create products table
$products_sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    stock_quantity INT DEFAULT 0,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
)";

if ($conn->query($products_sql) === TRUE) {
    echo "Products table ready.\n";
} else {
    echo "Error creating products table: " . $conn->error . "\n";
}

// Create orders table
$orders_sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($orders_sql) === TRUE) {
    echo "Orders table ready.\n";
} else {
    echo "Error creating orders table: " . $conn->error . "\n";
}

// Create order_items table (assuming it's needed)
$order_items_sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

if ($conn->query($order_items_sql) === TRUE) {
    echo "Order items table ready.\n";
} else {
    echo "Error creating order items table: " . $conn->error . "\n";
}

// Insert sample admin user if not exists
$admin_check = $conn->prepare("SELECT id FROM users WHERE username = 'admin'");
$admin_check->execute();
if ($admin_check->get_result()->num_rows === 0) {
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $admin_insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES ('admin', 'admin@example.com', ?, 'admin')");
    $admin_insert->bind_param("s", $admin_password);
    if ($admin_insert->execute()) {
        echo "Admin user created. Username: admin, Password: admin123\n";
    } else {
        echo "Error creating admin user: " . $conn->error . "\n";
    }
    $admin_insert->close();
} else {
    echo "Admin user already exists.\n";
}
$admin_check->close();

// Insert sample category if not exists
$cat_check = $conn->prepare("SELECT id FROM categories WHERE name = 'Electronics'");
$cat_check->execute();
if ($cat_check->get_result()->num_rows === 0) {
    $cat_insert = $conn->prepare("INSERT INTO categories (name, description) VALUES ('Electronics', 'Electronic devices and gadgets')");
    $cat_insert->execute();
    $cat_insert->close();
}
$cat_check->close();

echo "Database setup complete.\n";

$conn->close();
?>