<?php
session_start();
require_once 'config.php';
require_once 'auth_functions.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$page_title = "Reset Password - GalaGo Events";
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['reset_password'])) {
        $email = trim($_POST['email']);
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        $errors = [];
        
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!validateEmail($email)) {
            $errors[] = 'Please enter a valid email address';
        } elseif (!emailExists($email)) {
            $errors[] = 'No account found with this email address';
        }
        
        if (empty($newPassword)) {
            $errors[] = 'New password is required';
        } elseif (!validatePassword($newPassword)) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        if (empty($errors)) {
            $result = resetPasswordDirect($email, $newPassword);
            if ($result['success']) {
                $_SESSION['success'] = 'Password reset successfully! You can now log in with your new password.';
                header('Location: login.php');
                exit();
            } else {
                $error = $result['error'];
            }
        } else {
            $error = implode(', ', $errors);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href=".\style2.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<title><?php echo $page_title; ?></title>
<style>
:root {
    --purple-primary: #8B5CF6;
    --purple-light: #A78BFA;
    --purple-dark: #7C3AED;
    --purple-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --purple-gradient-2: linear-gradient(135deg, #8B5CF6 0%, #3B82F6 100%);
    --purple-gradient-3: linear-gradient(135deg, #EC4899 0%, #8B5CF6 100%);
    --purple-gradient-4: linear-gradient(135deg, #10B981 0%, #059669 100%);
    --success: #10B981;
    --warning: #F59E0B;
    --danger: #EF4444;
    --info: #06B6D4;
    --white: #FFFFFF;
    --black: #1F2937;
    --gray-100: #F3F4F6;
    --gray-200: #E5E7EB;
    --gray-300: #D1D5DB;
    --gray-700: #374151;
    --shadow-purple: 0 20px 25px -5px rgba(139, 92, 246, 0.1), 0 10px 10px -5px rgba(139, 92, 246, 0.04);
    --shadow-glow: 0 0 50px rgba(139, 92, 246, 0.3);
    --glass-bg: rgba(255, 255, 255, 0.1);
    --glass-border: rgba(255, 255, 255, 0.2);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: var(--purple-gradient);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    overflow-x: hidden;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 50%, rgba(139, 92, 246, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(236, 72, 153, 0.3) 0%, transparent 50%);
    z-index: -1;
    animation: backgroundPulse 15s ease-in-out infinite;
}

@keyframes backgroundPulse {
    0%, 100% { opacity: 0.6; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.1); }
}

.auth-container {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
    backdrop-filter: blur(25px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 30px;
    padding: 3rem;
    width: 100%;
    max-width: 500px;
    margin: 2rem;
    box-shadow: 
        0 25px 50px rgba(139, 92, 246, 0.15),
        0 8px 16px rgba(0, 0, 0, 0.1),
        inset 0 2px 4px rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
    transform-style: preserve-3d;
    animation: slideInScale 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes slideInScale {
    0% {
        opacity: 0;
        transform: translateY(50px) scale(0.8);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.auth-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #EC4899 0%, #8B5CF6 100%);
    border-radius: 30px 30px 0 0;
}

.brand-header {
    text-align: center;
    margin-bottom: 2.5rem;
    position: relative;
}

.brand-icon {
    width: 80px;
    height: 80px;
    border-radius: 25px;
    background: linear-gradient(145deg, #EC4899 0%, #8B5CF6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: var(--white);
    font-size: 2.5rem;
    box-shadow: 
        0 15px 30px rgba(236, 72, 153, 0.4),
        inset 0 2px 4px rgba(255, 255, 255, 0.2);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    animation: iconFloat 3s ease-in-out infinite;
}

@keyframes iconFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.brand-title {
    color: var(--white);
    font-size: 2rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #FFFFFF 0%, #E0E7FF 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.brand-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
    font-weight: 500;
}

.form-group {
    margin-bottom: 1.5rem;
    position: relative;
}

.form-label {
    color: var(--white);
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
    font-size: 0.95rem;
}

.form-control {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    color: var(--white);
    padding: 0.875rem 1.25rem;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%;
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(236, 72, 153, 0.8);
    box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.2);
    color: var(--white);
    transform: translateY(-2px);
    outline: none;
}

.password-field {
    position: relative;
}

.password-field .form-control {
    padding-right: 3.5rem;
}

.password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.1rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.password-toggle:hover {
    color: var(--white);
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.password-toggle:active {
    transform: translateY(-50%) scale(0.95);
}

.password-toggle i {
    transition: all 0.3s ease;
}

.btn {
    border: none;
    border-radius: 15px;
    font-weight: 700;
    padding: 0.875rem 2rem;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    text-transform: none;
    letter-spacing: 0.5px;
    width: 100%;
    margin-bottom: 1rem;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn:hover {
    transform: translateY(-3px) scale(1.02);
}

.btn-warning {
    background: var(--purple-gradient-4);
    color: var(--white);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

.alert {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 15px;
    font-weight: 500;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-purple);
    color: var(--white);
    border-left: 4px solid;
}

.alert-success {
    border-left-color: var(--success);
    background: rgba(16, 185, 129, 0.1);
}

.alert-danger {
    border-left-color: var(--danger);
    background: rgba(239, 68, 68, 0.1);
}

.auth-links {
    text-align: center;
    margin-top: 1.5rem;
}

.auth-links a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    margin: 0 0.25rem;
}

.auth-links a:hover {
    color: var(--white);
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.separator {
    text-align: center;
    margin: 1.5rem 0;
    position: relative;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

.separator::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
}

.separator span {
    background: var(--glass-bg);
    padding: 0 1rem;
}

@media (max-width: 576px) {
    .auth-container {
        margin: 1rem;
        padding: 2rem;
    }
    
    .brand-title {
        font-size: 1.5rem;
    }
    
    .brand-icon {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
}
</style>
</head>
<body>

<div class="auth-container">
    <div class="brand-header">
        <div class="brand-icon">
            <i class="bi bi-shield-lock"></i>
        </div>
        <h1 class="brand-title">Reset Password</h1>
        <p class="brand-subtitle">Enter your email and new password</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label class="form-label" for="email">
                <i class="bi bi-envelope me-1"></i>Email Address
            </label>
            <input type="email" class="form-control" id="email" name="email" 
                   placeholder="Enter your email address" required 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="form-group">
            <label class="form-label" for="new_password">
                <i class="bi bi-lock me-1"></i>New Password
            </label>
            <div class="password-field">
                <input type="password" class="form-control" id="new_password" name="new_password" 
                       placeholder="Enter your new password" required>
                <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                    <i class="bi bi-eye" id="new_password_icon"></i>
                </button>
            </div>
            <small class="text-white-50">Password must be at least 8 characters long</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="confirm_password">
                <i class="bi bi-lock-fill me-1"></i>Confirm New Password
            </label>
            <div class="password-field">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                       placeholder="Confirm your new password" required>
                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                    <i class="bi bi-eye" id="confirm_password_icon"></i>
                </button>
            </div>
        </div>

        <button type="submit" name="reset_password" class="btn btn-warning">
            <i class="bi bi-shield-check me-2"></i>Reset Password
        </button>
    </form>

    <div class="separator">
        <span>or</span>
    </div>

    <div class="auth-links">
        <a href="login.php">
            <i class="bi bi-arrow-left me-1"></i>Back to Login
        </a>
        <a href="register.php">
            <i class="bi bi-person-plus me-1"></i>Create Account
        </a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(fieldId + '_icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePasswords() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
    
    form.addEventListener('submit', function(e) {
        validatePasswords();
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

</body>
</html>