<?php
/**
 * About Us Page
 * E-Commerce Website
 */

require_once 'includes/config.php';

$page_title = 'About Us';

include 'includes/header.php';
?>

<div class="container my-5">
    <!-- Page Header -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item active">About Us</li>
        </ol>
    </nav>
    
    <h1 class="text-center mb-5"><i class="fas fa-info-circle me-2"></i>About Our Store</h1>
    
    <div class="row mb-5">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-history fa-2x text-primary"></i>
                        </div>
                        <h3>Our Story</h3>
                    </div>
                    <p class="lead">Founded in 2024, E-Commerce Store has been dedicated to providing quality products at affordable prices to customers worldwide.</p>
                    <p>We started as a small online shop with a simple mission: to make shopping convenient and enjoyable for everyone. Today, we serve thousands of satisfied customers with a diverse range of products across multiple categories.</p>
                    <p>Our commitment to quality, customer service, and innovation has helped us grow into the trusted online retailer we are today.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-bullseye fa-2x text-success"></i>
                        </div>
                        <h3>Our Mission</h3>
                    </div>
                    <p class="lead">To provide an exceptional shopping experience with quality products, competitive prices, and outstanding customer service.</p>
                    <ul class="fs-5">
                        <li class="mb-3">
                            <i class="fas fa-check text-success me-2"></i>
                            Curate high-quality products from trusted suppliers
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check text-success me-2"></i>
                            Offer competitive pricing without compromising quality
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check text-success me-2"></i>
                            Provide excellent customer support and fast shipping
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            Build lasting relationships with our customers
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Why Choose Us -->
    <h2 class="text-center mb-4">Why Choose Us?</h2>
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-shipping-fast fa-2x text-primary"></i>
                    </div>
                    <h4>Fast Shipping</h4>
                    <p class="text-muted">Quick and reliable shipping options to get your products to you faster.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-shield-alt fa-2x text-success"></i>
                    </div>
                    <h4>Secure Shopping</h4>
                    <p class="text-muted">Your payment information is protected with industry-standard security measures.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-headset fa-2x text-info"></i>
                    </div>
                    <h4>24/7 Support</h4>
                    <p class="text-muted">Our customer service team is always ready to help you with any questions.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Team Section -->
    <h2 class="text-center mb-4">Meet Our Team</h2>
    <div class="row justify-content-center mb-5">
        <div class="col-md-3 col-6 mb-4">
            <div class="card text-center">
                <div class="card-body p-3">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 60px; height: 60px;">
                        <i class="fas fa-user fa-2x text-primary"></i>
                    </div>
                    <h6 class="card-title">Alex Johnson</h6>
                    <p class="text-muted small mb-0">CEO</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card text-center">
                <div class="card-body p-3">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 60px; height: 60px;">
                        <i class="fas fa-user fa-2x text-success"></i>
                    </div>
                    <h6 class="card-title">Sarah Williams</h6>
                    <p class="text-muted small mb-0">COO</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card text-center">
                <div class="card-body p-3">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 60px; height: 60px;">
                        <i class="fas fa-user fa-2x text-info"></i>
                    </div>
                    <h6 class="card-title">Mike Chen</h6>
                    <p class="text-muted small mb-0">CTO</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="row text-center mt-5">
        <div class="col-md-3 mb-4">
            <h2 class="display-4 fw-bold text-primary">50K+</h2>
            <p class="text-muted">Happy Customers</p>
        </div>
        <div class="col-md-3 mb-4">
            <h2 class="display-4 fw-bold text-success">1M+</h2>
            <p class="text-muted">Products Delivered</p>
        </div>
        <div class="col-md-3 mb-4">
            <h2 class="display-4 fw-bold text-info">4.9/5</h2>
            <p class="text-muted">Customer Rating</p>
        </div>
        <div class="col-md-3 mb-4">
            <h2 class="display-4 fw-bold text-warning">15+</h2>
            <p class="text-muted">Countries Served</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>