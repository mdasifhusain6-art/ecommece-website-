# Product Requirement Document (PRD)
## E-Commerce Website

### 1. Project Overview
A full-stack e-commerce platform built with HTML, CSS, JavaScript, MDBootstrap (Frontend), PHP (Backend), and MySQL (Database). The platform enables users to browse products, manage a shopping cart, complete purchases, and allows administrators to manage products and orders.

### 2. Target Audience
- **End Users**: Customers looking to purchase products online
- **Administrators**: Staff managing products, inventory, and orders

### 3. Core Features

#### 3.1 User Authentication
- User Registration with email validation
- User Login with secure password handling
- Session-based authentication
- Password reset capability
- User profile management
- Logout functionality

#### 3.2 Product Management
- Product listing with pagination
- Product categories and filtering
- Product search functionality
- Product detail pages with images
- Product ratings and reviews
- Inventory tracking

#### 3.3 Shopping Cart
- Add/remove products from cart
- Update product quantities
- Persistent cart (session-based)
- Cart summary with totals
- Apply discount codes

#### 3.4 Checkout & Orders
- Shipping information form
- Payment processing simulation
- Order confirmation
- Order history for users
- Order status tracking

#### 3.5 Admin Panel
- Dashboard with key metrics
- Product CRUD operations
- Category management
- Order management and status updates
- User management
- Sales reports

### 4. User Flows

#### 4.1 Customer Journey
1. User visits homepage
2. Browses products or uses search
3. Views product details
4. Adds items to cart
5. Proceeds to checkout
6. Enters shipping information
7. Confirms order
8. Receives order confirmation
9. Can view order history

#### 4.2 Admin Journey
1. Admin logs into admin panel
2. Views dashboard statistics
3. Manages products (add/edit/delete)
4. Manages orders (update status)
5. Views reports and analytics

### 5. Database Schema

#### 5.1 Users Table
- id (INT, PK, AUTO_INCREMENT)
- username (VARCHAR(50), UNIQUE)
- email (VARCHAR(100), UNIQUE)
- password (VARCHAR(255))
- full_name (VARCHAR(100))
- address (TEXT)
- phone (VARCHAR(20))
- role (ENUM('customer', 'admin'))
- created_at (DATETIME)
- updated_at (DATETIME)

#### 5.2 Categories Table
- id (INT, PK, AUTO_INCREMENT)
- name (VARCHAR(100))
- description (TEXT)
- created_at (DATETIME)

#### 5.3 Products Table
- id (INT, PK, AUTO_INCREMENT)
- name (VARCHAR(200))
- description (TEXT)
- price (DECIMAL(10,2))
- category_id (INT, FK)
- stock_quantity (INT)
- image_url (VARCHAR(255))
- created_at (DATETIME)
- updated_at (DATETIME)

#### 5.4 Orders Table
- id (INT, PK, AUTO_INCREMENT)
- user_id (INT, FK)
- total_amount (DECIMAL(10,2))
- shipping_address (TEXT)
- status (ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled'))
- created_at (DATETIME)
- updated_at (DATETIME)

#### 5.5 Order_Items Table
- id (INT, PK, AUTO_INCREMENT)
- order_id (INT, FK)
- product_id (INT, FK)
- quantity (INT)
- price (DECIMAL(10,2))
- subtotal (DECIMAL(10,2))

### 6. Technical Requirements

#### 6.1 Frontend
- HTML5 semantic structure
- CSS3 with custom styles
- JavaScript for interactivity
- MDBootstrap for responsive design
- Mobile-first approach
- Cross-browser compatibility

#### 6.2 Backend
- PHP 7.4+ with OOP approach
- MySQLi for database operations
- Prepared statements for security
- Session management
- Password hashing (password_hash)
- Input validation and sanitization
- Error handling and logging

#### 6.3 Security
- Password hashing with bcrypt
- Prepared statements to prevent SQL injection
- CSRF protection tokens
- XSS prevention (htmlspecialchars)
- Input validation and sanitization
- Secure session handling
- HTTPS enforcement (in production)

### 7. Page Structure

#### 7.1 Public Pages
- Homepage (index.php)
- Product Listing (products.php)
- Product Detail (product_detail.php)
- Login (login.php)
- Register (register.php)
- About Us (about.php)
- Contact (contact.php)

#### 7.2 User Pages
- User Dashboard (dashboard.php)
- Cart (cart.php)
- Checkout (checkout.php)
- Order History (orders.php)
- Order Detail (order_detail.php)
- Profile Settings (profile.php)

#### 7.3 Admin Pages
- Admin Login (admin/login.php)
- Admin Dashboard (admin/index.php)
- Product Management (admin/products.php)
- Add/Edit Product (admin/product_form.php)
- Order Management (admin/orders.php)
- Order Detail (admin/order_detail.php)
- Category Management (admin/categories.php)

### 8. Non-Functional Requirements

#### 8.1 Performance
- Page load time under 3 seconds
- Database query optimization
- Image optimization
- Caching strategies

#### 8.2 Scalability
- Modular code structure
- Separation of concerns
- Easy to add new features

#### 8.3 Maintainability
- Clean code with comments
- Consistent naming conventions
- Documentation

### 9. Success Metrics
- All pages load correctly on XAMPP localhost
- User registration and login work properly
- Products can be browsed and searched
- Cart functionality works end-to-end
- Checkout process completes successfully
- Admin panel allows full CRUD operations
- All security measures implemented
- No SQL injection or XSS vulnerabilities