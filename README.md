# E-Commerce Store

A full-featured e-commerce website built with PHP, MySQL, HTML, CSS, JavaScript, and MDBootstrap.

## About

This e-commerce website provides a complete online shopping experience for customers and comprehensive management tools for administrators. Users can browse products by category, perform advanced searches, manage shopping carts, and complete secure checkouts. The platform includes user authentication, order tracking, and profile management. Administrators have access to dashboards with sales analytics, product inventory management, category organization, and order processing capabilities. The site emphasizes security with features like password hashing, CSRF protection, and input validation.

## Features

### User Features
- User Registration & Login with secure password hashing
- Browse products by category
- Advanced product search and filtering
- Shopping cart with session management
- Secure checkout process
- Order history and tracking
- User profile management

### Admin Features
- Admin dashboard with statistics
- Product management (CRUD)
- Category management
- Order management with status updates
- Low stock alerts
- Sales analytics

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript, MDBootstrap
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Server**: XAMPP (Apache)

## Installation

### Prerequisites
- XAMPP installed (Apache & MySQL)
- PHP 7.4 or higher
- Web browser

### Setup Instructions

1. **Install XAMPP**
   - Download from https://www.apachefriends.org/index.html
   - Install with default settings
   - Start Apache and MySQL from XAMPP Control Panel

2. **Deploy Project Files**
    - Copy the `ecommerce_web` folder to `C:\xampp\htdocs\`
   - Or use your preferred web server directory

3. **Import Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `ecommerce_db`
   - Import the SQL file from `database/schema.sql`
   - Or run the SQL commands directly

4. **Configure Database Connection**
   - Edit `includes/config.php` if needed
   - Update database credentials (DB_HOST, DB_USER, DB_PASS, DB_NAME)

5. **Set Permissions (if needed)**
   - Ensure PHP has write permissions to session directory
   - Configure upload directories if adding image uploads

## Project Structure

```
ecommerce_web/
├── index.php                    # Homepage
├── products.php                 # Product listing
├── product_detail.php          # Product details
├── cart.php                     # Shopping cart
├── checkout.php                 # Checkout process
├── dashboard.php                # User dashboard
├── orders.php                   # Order history
├── order_detail.php            # Order details
├── profile.php                  # Profile management
├── login.php                    # Login page
├── register.php                 # Registration page
├── about.php                    # About page
├── contact.php                  # Contact page
│
├── includes/
│   ├── config.php              # Database config & functions
│   ├── header.php              # Main header
│   └── footer.php              # Main footer
│
├── components/
│   ├── header_auth.php         # Auth page header
│   └── footer_auth.php         # Auth page footer
│
├── auth/
│   ├── login.php               # Login logic
│   ├── register.php            # Registration logic
│   └── logout.php              # Logout logic
│
├── admin/
│   ├── index.php               # Admin dashboard
│   ├── login.php               # Admin login
│   ├── products.php            # Product management
│   ├── product_form.php        # Add/edit product
│   └── orders.php              # Order management
│
├── css/
│   └── style.css               # Custom styles
│
├── js/
│   └── script.js               # Client-side JavaScript
│
├── database/
│   └── schema.sql              # Database schema
│
└── README.md                   # This file
```

## Default Admin Account

After installing, create an admin account by:
1. Registering a new account through the website
2. Or directly updating the database:

```sql
UPDATE users SET role='admin' WHERE username='your_admin_username';
```

Or insert directly:

```sql
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@ecommerce.com', '$2y$10$YourHashedPassword', 'Admin User', 'admin');
```

## Features Detail

### User Features

1. **Authentication System**
   - Secure registration with password hashing (bcrypt)
   - Session-based login
   - CSRF protection on all forms
   - Password validation (minimum 6 characters)

2. **Product Browsing**
   - Browse all products
   - Filter by category
   - Search functionality
   - Sort by name, price, or newest
   - Stock availability indicators

3. **Shopping Cart**
   - Add/remove products
   - Update quantities
   - Session-based persistence
   - Real-time price calculations
   - Stock validation

4. **Checkout Process**
   - User info pre-filled
   - Address validation
   - Order summary
   - Multiple payment options (simulated)
   - Order confirmation

5. **User Account**
   - View order history
   - Track order status
   - Profile management
   - Edit personal information

### Admin Features

1. **Dashboard**
   - Sales statistics
   - Order counts
   - Revenue tracking
   - Low stock alerts
   - Recent orders

2. **Product Management**
   - Add new products
   - Edit existing products
   - Delete products
   - Set prices and stock levels
   - Category assignment

3. **Order Management**
   - View all orders
   - Update order status
   - Filter by status
   - Pagination support
   - Customer information

## Security Features

- **Password Security**: bcrypt hashing with cost factor 10
- **SQL Injection Prevention**: Prepared statements for all queries
- **XSS Prevention**: Input sanitization with htmlspecialchars
- **CSRF Protection**: Token-based verification
- **Session Security**: HttpOnly cookies, secure session handling
- **Input Validation**: Server-side validation for all user inputs

## Database Schema

### Tables

1. **users**: User accounts
2. **categories**: Product categories
3. **products**: Product listings
4. **orders**: Customer orders
5. **order_items**: Individual order line items

## Customization

### Adding New Categories

1. Access admin panel or phpMyAdmin
2. Insert into `categories` table:
   ```sql
   INSERT INTO categories (name, description) VALUES ('New Category', 'Description');
   ```

### Adding Products

1. Login to admin panel
2. Navigate to "Manage Products"
3. Click "Add New Product"
4. Fill in product details
5. Set price and stock quantity

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Verify MySQL is running in XAMPP
   - Check database credentials in config.php
   - Ensure ecommerce_db database exists

2. **Session Not Working**
   - Check PHP session settings
   - Verify session.save_path is writable
   - Ensure cookies are enabled in browser

3. **Page Not Found (404)**
   - Verify files are in correct directory
   - Check Apache is running
   - Ensure .htaccess rules if using URL rewriting

4. **Images Not Loading**
   - Check image URLs in database
   - Verify image files exist in specified paths
   - Update default image paths as needed

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

This project is for educational purposes.

## Contributing

Contributions are welcome! Please feel free to submit pull requests.

## Support

For issues and questions, please contact developer@example.com

---

**Built with ❤️ for learning and demonstration purposes**
