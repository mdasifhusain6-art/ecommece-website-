<?php
/**
 * Test Setup Script
 * Verifies the installation is working correctly
 */

require_once 'includes/config.php';

echo "<h1>E-Commerce Store - Setup Test</h1>";
echo "<hr>";

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>";
try {
    $db = getDB();
    if ($db) {
        echo "✅ Database connection successful<br>";
        
        // Test 2: Check tables exist
        echo "<h2>Test 2: Database Tables</h2>";
        $tables = ['users', 'categories', 'products', 'orders', 'order_items'];
        foreach ($tables as $table) {
            $stmt = $db->prepare("SHOW TABLES LIKE ?");
            $stmt->bind_param("s", $table);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo "✅ Table '$table' exists<br>";
            } else {
                echo "❌ Table '$table' NOT found<br>";
            }
            $stmt->close();
        }
        
        // Test 3: Check data
        echo "<h2>Test 3: Sample Data</h2>";
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM categories");
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        echo "✅ Categories: $count<br>";
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM products");
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        echo "✅ Products: $count<br>";
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        echo "✅ Users: $count<br>";
        
        $stmt->close();
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 4: Check files
echo "<h2>Test 4: Required Files</h2>";
$files = [
    'index.php',
    'products.php',
    'cart.php',
    'checkout.php',
    'dashboard.php',
    'login.php',
    'register.php',
    'auth/login.php',
    'auth/register.php',
    'includes/config.php',
    'includes/header.php',
    'includes/footer.php',
    'css/style.css',
    'js/script.js',
];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file NOT found<br>";
    }
}

// Test 5: Admin check
echo "<h2>Test 5: Admin Account</h2>";
$stmt = $db->prepare("SELECT username, role FROM users WHERE username = 'admin'");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo "✅ Admin user exists: " . htmlspecialchars($admin['username']) . "<br>";
    echo "   Role: " . htmlspecialchars($admin['role']) . "<br>";
    if ($admin['role'] !== 'admin') {
        echo "⚠️  Note: Run this SQL to set admin role:<br>";
        echo "   UPDATE users SET role='admin' WHERE username='admin';<br>";
    }
} else {
    echo "❌ Admin user not found<br>";
}
$stmt->close();

echo "<hr>";
echo "<h2>Test Complete</h2>";
?>

<a href="index.php">Go to Homepage</a> | 
<a href="admin/login.php">Go to Admin Login</a>
