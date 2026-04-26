<?php
/**
 * User Profile Edit
 * E-Commerce Website
 */

require_once 'includes/config.php';

$page_title = 'My Profile';

requireLogin();

$db = getDB();

// Get user details
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$errors = [];
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission.';
    } else {
        $full_name = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate inputs
        if (empty($full_name)) {
            $errors[] = 'Full name is required.';
        }

        // Update password if provided
        $password_updated = false;
        if (!empty($current_password) || !empty($new_password)) {
            if (empty($current_password) || empty($new_password)) {
                $errors[] = 'Please provide both current and new password.';
            } elseif (!password_verify($current_password, $user['password'])) {
                $errors[] = 'Current password is incorrect.';
            } elseif (strlen($new_password) < 6) {
                $errors[] = 'New password must be at least 6 characters long.';
            } elseif ($new_password !== $confirm_password) {
                $errors[] = 'New passwords do not match.';
            } else {
                $password_updated = true;
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            }
        }

        // If no errors, update profile
        if (empty($errors)) {
            if ($password_updated) {
                $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ?, address = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $full_name, $phone, $address, $hashed_password, $_SESSION['user_id']);
            } else {
                $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
                $stmt->bind_param("sssi", $full_name, $phone, $address, $_SESSION['user_id']);
            }
            
            if ($stmt->execute()) {
                // Update session data
                $_SESSION['full_name'] = $full_name;

                setFlash('success', 'Profile updated successfully!');
                redirect('dashboard.php');
            } else {
                $errors[] = 'Failed to update profile. Please try again.';
            }
            $stmt->close();
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0"><i class="fas fa-user-edit text-primary me-2"></i>Edit Profile</h1>
            <p class="text-muted mt-1">Update your account information and preferences</p>
        </div>
        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-user-cog text-primary me-2"></i>Profile Information</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 10px;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                            <small class="text-muted">Username cannot be changed.</small>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <small class="text-muted">Email cannot be changed.</small>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required
                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? $user['full_name'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? $user['phone'] ?? ''); ?>"
                                   placeholder="Enter phone number">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="4"
                                      placeholder="Enter your full address"><?php echo htmlspecialchars($_POST['address'] ?? $user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Change Password (optional)</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password"
                                       placeholder="Enter current password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                       placeholder="Enter new password (min 6 chars)" minlength="6">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                   placeholder="Confirm new password">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4 border-0" style="background: linear-gradient(135deg, #f8fafc, #e2e8f0);">
                <div class="card-body text-center p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px;">
                        <i class="fas fa-user fa-3x text-primary"></i>
                    </div>
                    <h4 class="fw-bold"><?php echo htmlspecialchars($user['full_name'] ?? 'Not provided'); ?></h4>
                    <p class="text-muted mb-2"><?php echo htmlspecialchars($user['username']); ?></p>
                    <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?> px-3 py-2 fs-6">
                        <i class="fas fa-<?php echo $user['role'] == 'admin' ? 'shield-alt' : 'user'; ?> me-1"></i>
                        <?php echo strtoupper($user['role']); ?>
                    </span>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-calendar-check text-primary me-2"></i>Account Information</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>
                        <h6 class="text-muted">Member Since</h6>
                        <p class="display-6 text-primary fw-bold"><?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="p-2">
                                <i class="fas fa-clock fa-2x text-info mb-2"></i>
                                <h4 class="text-info fw-bold">
                                    <?php
                                    $member_days = floor((time() - strtotime($user['created_at'])) / (60 * 60 * 24));
                                    echo $member_days;
                                    ?>
                                </h4>
                                <small class="text-muted">Days Active</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2">
                                <i class="fas fa-shopping-bag fa-2x text-success mb-2"></i>
                                <h4 class="text-success fw-bold">0</h4>
                                <small class="text-muted">Orders</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');

    if (newPassword && confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
                confirmPassword.classList.add('is-invalid');
            } else {
                confirmPassword.setCustomValidity('');
                confirmPassword.classList.remove('is-invalid');
                confirmPassword.classList.add('is-valid');
            }
        });

        newPassword.addEventListener('input', function() {
            if (confirmPassword.value && newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
                confirmPassword.classList.add('is-invalid');
                confirmPassword.classList.remove('is-valid');
            } else if (confirmPassword.value) {
                confirmPassword.setCustomValidity('');
                confirmPassword.classList.remove('is-invalid');
                confirmPassword.classList.add('is-valid');
            }
        });
    }

    // Show/hide password fields based on input
    const currentPassword = document.getElementById('current_password');
    const passwordSection = document.querySelector('.mb-4:has(#current_password)')?.parentElement;

    if (currentPassword) {
        currentPassword.addEventListener('input', function() {
            const passwordFields = document.querySelectorAll('#current_password, #new_password, #confirm_password');
            if (this.value.length > 0) {
                passwordFields.forEach(field => {
                    field.setAttribute('required', 'required');
                });
            } else {
                passwordFields.forEach(field => {
                    field.removeAttribute('required');
                });
            }
        });
    }

    // Form submission with loading state
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating Profile...';

                // Re-enable after 5 seconds if no redirect
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 5000);
            }
        });
    }

    // Auto-focus on first empty required field
    const requiredFields = document.querySelectorAll('input[required], textarea[required]');
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            field.focus();
            break;
        }
    }
});
</script>