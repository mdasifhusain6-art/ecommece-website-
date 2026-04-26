<?php
/**
 * Contact Page
 * E-Commerce Website
 */

require_once 'includes/config.php';

$page_title = 'Contact Us';

errors = [];
success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        errors[] = 'Invalid form submission.';
    } else {
        name = sanitize($_POST['name'] ?? '');
        email = sanitize($_POST['email'] ?? '');
        subject = sanitize($_POST['subject'] ?? '');
        message = sanitize($_POST['message'] ?? '');
        
        if (empty(name)) {
            errors[] = 'Name is required.';
        }
        
        if (empty(email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            errors[] = 'Valid email is required.';
        }
        
        if (empty(subject)) {
            errors[] = 'Subject is required.';
        }
        
        if (empty(message)) {
            errors[] = 'Message is required.';
        }
        
        if (empty(errors)) {
            // In production, you would send an email here
            // mail('admin@ecommerce.com', $subject, $message, 'From: ' . $email);
            
            setFlash('success', 'Thank you for your message! We will get back to you soon.');
            redirect('contact.php');
        }
    }
}

include 'includes/header.php';
?>

<div class="container my-5">
    <!-- Page Header -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item active">Contact Us</li>
        </ol>
    </nav>
    
    <h1 class="text-center mb-5"><i class="fas fa-envelope me-2"></i>Contact Us</h1>
    
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h3 class="card-title mb-4"><i class="fas fa-paper-plane me-2"></i>Send us a Message</h3>
                    
                    <?php 
                    $flash = getFlash();
                    if ($flash): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="contact.php">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" class="form-control" id="subject" name="subject" required
                                   value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h3 class="card-title mb-4"><i class="fas fa-info-circle me-2"></i>Contact Information</h3>
                    
                    <div class="mb-4">
                        <h5 class="text-primary"><i class="fas fa-map-marker-alt me-2"></i>Address</h5>
                        <p class="text-muted">
                            123 Shopping Street<br>
                            Commerce City, CC 12345<br>
                            United States
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="text-primary"><i class="fas fa-phone me-2"></i>Phone</h5>
                        <p class="text-muted">
                            <a href="tel:+15551234567" class="text-decoration-none">(555) 123-4567</a><br>
                            <small class="text-muted">Mon-Fri, 9am - 6pm EST</small>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="text-primary"><i class="fas fa-envelope me-2"></i>Email</h5>
                        <p class="text-muted">
                            <a href="mailto:support@ecommerce.com" class="text-decoration-none">support@ecommerce.com</a><br>
                            <a href="mailto:sales@ecommerce.com" class="text-decoration-none text-muted">sales@ecommerce.com</a>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="text-primary"><i class="fas fa-clock me-2"></i>Business Hours</h5>
                        <p class="text-muted">
                            Monday - Friday: 9:00 AM - 6:00 PM<br>
                            Saturday: 10:00 AM - 4:00 PM<br>
                            Sunday: Closed
                        </p>
                    </div>
                    
                    <hr>
                    
                    <h5 class="text-primary">Follow Us</h5>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="btn btn-outline-primary btn-lg rounded-circle" title="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-lg rounded-circle" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-lg rounded-circle" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-lg rounded-circle" title="LinkedIn">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- FAQ Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="text-center mb-4"><i class="fas fa-question-circle me-2"></i>Frequently Asked Questions</h3>
            
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq1">
                            <i class="fas fa-shipping-fast me-2 text-primary"></i>How long does shipping take?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                        <div class="accordion-body">
                            Standard shipping takes 3-5 business days. Express shipping takes 1-2 business days. Free shipping may take 5-7 business days for orders over $50.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq2">
                            <i class="fas fa-undo me-2 text-success"></i>What is your return policy?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                        <div class="accordion-body">
                            We offer a 30-day return policy on most items. Products must be in their original condition with all tags attached. Some items (like final sale products) may not be eligible for return.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq3">
                            <i class="fas fa-shield-alt me-2 text-info"></i>Is my payment information secure?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes! We use industry-standard SSL encryption to protect your payment information. We never store your credit card details on our servers.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq4">
                            <i class="fas fa-gift me-2 text-warning"></i>Do you offer gift wrapping?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes! We offer complimentary gift wrapping for all orders. Simply select the gift wrapping option during checkout and include a personalized message.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Map Placeholder -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <div class="bg-light rounded d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                        <i class="fas fa-map-marked-alt fa-3x text-primary"></i>
                    </div>
                    <h4>Visit Our Store</h4>
                    <p class="text-muted mb-4">123 Shopping Street, Commerce City</p>
                    <p class="text-muted small">
                        <em>Google Maps integration would go here in production</em>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>