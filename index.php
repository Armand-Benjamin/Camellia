<?php
session_start();
include 'config/db.php';

$error = '';

if ($_POST) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM Users WHERE UserName = '$username' AND Password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['UserId'];
        $_SESSION['username'] = $user['UserName'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'invalid_username_or_password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camellia HR System - Authentication</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="login-brand">
                <h1><i class="fas fa-coffee"></i>camellia</h1>
                <p>professional hr management system designed for modern coffee culture and hospitality excellence. streamline your recruitment process with advanced analytics and candidate management.</p>
                
                <div class="login-features">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-users"></i></div>
                        <div class="feature-text">candidate_management</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="feature-text">analytics_dashboard</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-briefcase"></i></div>
                        <div class="feature-text">job_post_management</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="feature-text">secure_authentication</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-card">
                <h2><i class="fas fa-lock"></i>authentication</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="login-form" id="loginForm">
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i>username
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="enter_your_username" 
                            autocomplete="username"
                            required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-key"></i>password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="enter_your_password"
                            autocomplete="current-password" 
                            required>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>authenticate
                    </button>
                </form>
                
                <div class="login-divider">
                    <span>or</span>
                </div>
                
                <div class="login-footer">
                    <p><i class="fas fa-info-circle"></i>need_access_to_the_system?</p>
                    <a href="register.php">
                        <i class="fas fa-user-plus"></i>create_new_account
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
    <script>
        // Enhanced login form handling
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-login');
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>authenticating...';
        });
        
        // Add focus effects
        document.querySelectorAll('.form-group input').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.form-group').querySelector('label').style.color = 'var(--primary-color)';
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.closest('.form-group').querySelector('label').style.color = 'var(--text-secondary)';
                }
            });
        });
    </script>
</body>
</html>
