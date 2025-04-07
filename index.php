<?php 
session_start();

if (isset($_POST['send_verification'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $verification_code = rand(100000, 999999);
        $_SESSION['verification_code'] = $verification_code;
        $_SESSION['verification_email'] = $email;
        
        $subject = "Password Reset Verification Code";
        $message = "Your verification code is: $verification_code";
        $headers = "From: noreply@benzshop.com";
        
        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['message'] = "Verification code sent to your email!";
        } else {
            $_SESSION['error'] = "Failed to send verification code. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Please enter a valid email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>productsite</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1a2a44 0%, #2c3e50 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #fff;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 200px;
            height: 200px;
            background: linear-gradient(45deg, rgba(52, 152, 219, 0.3), rgba(46, 204, 113, 0.3));
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .container {
            background: linear-gradient(135deg, #1a2a44 0%, #2c3e50 100%);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 90%;
            max-width: 400px;
            z-index: 1;
            color: #fff;
            margin: 1rem;
        }

        .logo {
            text-align: center;
            margin-bottom: 1rem;
        }

        .logo h1 {
            font-size: clamp(1.2rem, 4vw, 1.5rem);
            color: #fff;
        }

        .logo p {
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
            color: #bdc3c7;
            margin-top: 0.2rem;
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 1.5rem;
            font-size: clamp(1.5rem, 5vw, 1.8rem);
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #bdc3c7;
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        }

        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            font-size: clamp(0.9rem, 3vw, 1rem);
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        input:focus {
            outline: none;
            border-color: #2ecc71;
            box-shadow: 0 0 5px rgba(46, 204, 113, 0.3);
        }

        .form-group .icon {
            position: absolute;
            right: 10px;
            top: 65%;
            transform: translateY(-50%);
            color: #2ecc71;
            font-size: clamp(1rem, 3vw, 1.2rem);
            cursor: pointer;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: clamp(0.9rem, 3vw, 1rem);
            transition: background 0.3s;
        }

        button:hover {
            background: #27ae60;
        }

        .toggle {
            text-align: center;
            margin-top: 1rem;
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        }

        .toggle a {
            color: #3498db;
            text-decoration: none;
        }

        .toggle a:hover {
            text-decoration: underline;
        }

        .error, .message {
            text-align: center;
            margin-bottom: 1rem;
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        }

        .error {
            color: #e74c3c;
        }

        .message {
            color: #2ecc71;
        }

        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }

        .forgot-password a {
            color: #3498db;
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .forgot-password-form {
            display: none;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            text-align: center;
            width: 100%;
            font-size: clamp(0.7rem, 2vw, 0.8rem);
            padding: 0 1rem;
        }

        .footer a {
            color: #3498db;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 2;
        }

        .modal-content {
            background: #fff;
            color: #333;
            margin: 10% auto;
            padding: 1rem;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            float: right;
            font-size: clamp(1.2rem, 4vw, 1.5rem);
            cursor: pointer;
        }

        @media (max-width: 480px) {
            body::before {
                width: 150px;
                height: 150px;
            }
            .container {
                padding: 1rem;
                margin: 0.5rem;
            }
            .form-group .icon {
                top: 62%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>BENZ SHOP</h1>
            <p>ORDER NOW</p>
        </div>
        <h2>SIT BACK AND ORDER!</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>

        <div id="login-form">
            <form action="auth.php" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="login-email" required value=" ">
                    <span class="icon">✔</span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="login-password" required>
                    <span class="icon" onclick="togglePassword('login-password', this)">👁️</span>
                </div>
                <button type="submit" name="login">Sign in</button>
            </form>
            <div class="forgot-password">
                <a href="#" onclick="showForgotPassword()">Forgot My Password</a>
            </div>
            <div class="toggle">
                <p>Don't have an account? <a href="#" onclick="showSignup()">Sign Up</a></p>
            </div>
        </div>

        <div id="signup-form" style="display: none;">
            <form action="auth.php" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="signup-email" required>
                    <span class="icon">✔</span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="signup-password" required>
                    <span class="icon" onclick="togglePassword('signup-password', this)">👁️</span>
                </div>
                <button type="submit" name="signup">Sign Up</button>
            </form>
            <div class="toggle">
                <p>Already have an account? <a href="#" onclick="showLogin()">Login</a></p>
            </div>
        </div>

        <div id="forgot-password-form" class="forgot-password-form">
            <form action="" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="forgot-email" required>
                    <span class="icon">✔</span>
                </div>
                <button type="submit" name="send_verification">Send Verification Code</button>
            </form>
            <div class="toggle">
                <p>Back to <a href="#" onclick="showLogin()">Login</a></p>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <a href="#" onclick="showTerms()">Terms of Use</a> | <a href="#" onclick="showPrivacy()">Privacy Policy</a>
    </div>

    <div id="terms-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('terms-modal')">×</span>
            <h2>Terms of Use</h2>
            <p>Last Updated: April 6, 2025</p>
            <h3>1. Acceptance of Terms</h3>
            <p>By accessing or using BENZ Shop, you agree to be bound by these Terms of Use. If you do not agree, please do not use our services.</p>
            <h3>2. Use of Service</h3>
            <p>You may use our service only for lawful purposes and in accordance with these Terms. You agree not to use the service:</p>
            <ul>
                <li>In any way that violates any applicable law or regulation</li>
                <li>To transmit any advertising or promotional material without our prior consent</li>
            </ul>
            <h3>3. Account Responsibility</h3>
            <p>You are responsible for maintaining the confidentiality of your account and password.</p>
            <h3>4. Changes to Terms</h3>
            <p>We may modify these Terms at any time. Continued use of the service after changes constitutes acceptance of the new Terms.</p>
        </div>
    </div>

    <div id="privacy-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('privacy-modal')">×</span>
            <h2>Privacy Policy</h2>
            <p>Last Updated: April 6, 2025</p>
            <h3>1. Information We Collect</h3>
            <p>We collect information you provide directly to us, such as:</p>
            <ul>
                <li>Email address</li>
                <li>Password (encrypted)</li>
            </ul>
            <h3>2. How We Use Your Information</h3>
            <p>We use your information to:</p>
            <ul>
                <li>Provide and maintain our service</li>
                <li>Send verification codes</li>
                <li>Notify you about changes to our service</li>
            </ul>
            <h3>3. Data Security</h3>
            <p>We implement measures to protect your information, but no method of transmission over the Internet is 100% secure.</p>
            <h3>4. Contact Us</h3>
            <p>For privacy-related questions, contact us at: privacy@benzshop.com</p>
        </div>
    </div>

    <script>
        function showSignup() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('signup-form').style.display = 'block';
            document.getElementById('forgot-password-form').style.display = 'none';
        }

        function showLogin() {
            document.getElementById('signup-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('forgot-password-form').style.display = 'none';
        }

        function showForgotPassword() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('signup-form').style.display = 'none';
            document.getElementById('forgot-password-form').style.display = 'block';
        }

        function togglePassword(inputId, iconElement) {
            const passwordInput = document.getElementById(inputId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                iconElement.textContent = '👁️‍🗨️';
            } else {
                passwordInput.type = 'password';
                iconElement.textContent = '👁️';
            }
        }

        function showTerms() {
            document.getElementById('terms-modal').style.display = 'block';
        }

        function showPrivacy() {
            document.getElementById('privacy-modal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>