<?php
session_start();
include 'config/db.php';

$message = '';
$error = '';

if ($_POST) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $confirm_password = md5($_POST['confirm_password']);
    
    if ($password !== $confirm_password) {
        $error = 'passwords_do_not_match';
    } else {
        // Check if username already exists
        $check_query = "SELECT * FROM Users WHERE UserName = '$username'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'username_already_exists';
        } else {
            $query = "INSERT INTO Users (UserName, Password) VALUES ('$username', '$password')";
            if (mysqli_query($conn, $query)) {
                $message = 'account_created_successfully';
            } else {
                $error = 'error_creating_account: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Camellia HR System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="login-brand">
                <h1><i class="fas fa-coffee"></i>camellia</h1>
                <p>join our professional hr management platform and experience the future of recruitment. create your account to access advanced candidate management tools.</p>
                
                <div class="login-features">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-rocket"></i></div>
                        <div class="feature-text">quick_setup</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-database"></i></div>
                        <div class="feature-text">secure_storage</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>
                        <div class="feature-text">mobile_responsive</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-support"></i></div>
                        <div class="feature-text">24/7_support</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-card">
                <h2><i class="fas fa-user-plus"></i>create_account</h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo $message; ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="login-form" id="registerForm">
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i>username
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="choose_a_username"
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
                            placeholder="create_secure_password"
                            autocomplete="new-password"
                            required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">
                            <i class="fas fa-check-double"></i>confirm_password
                        </label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            placeholder="confirm_your_password"
                            autocomplete="new-password"
                            required>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-user-plus"></i>create_account
                    </button>
                </form>
                
                <div class="login-divider">
                    <span>or</span>
                </div>
                
                <div class="login-footer">
                    <p><i class="fas fa-sign-in-alt"></i>already_have_an_account?</p>
                    <a href="index.php">
                        <i class="fas fa-arrow-left"></i>back_to_login
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
    <script>
        // Enhanced register form handling
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('passwords_do_not_match');
                return;
            }
            
            const submitBtn = this.querySelector('.btn-login');
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>creating_account...';
        });
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = getPasswordStrength(password);
            // Add visual feedback here if needed
        });
        
        function getPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        }
    </script>
</body>
</html>
