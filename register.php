<?php
require_once 'config.php';
require_once 'auth.php';

redirectIfLoggedIn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $conn = getDBConnection();
            
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $error = 'Username or email already exists.';
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password]);
                
                $success = 'Registration successful! You can now login.';
            }
        } catch(PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - HKDSE Study Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <header>
        <nav>
            <h1>HKDSE Study Tracker</h1>
            <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">🌙 Dark Mode</button>
        </nav>
    </header>
    
    <main id="main-content" class="auth-container">
        <div class="auth-card">
            <h1>Create Account</h1>
            
            <?php if ($error): ?>
                <div class="message message-error" role="alert" aria-live="polite">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="message message-success" role="alert" aria-live="polite">
                    <?php echo htmlspecialchars($success); ?>
                    <br><a href="login.php">Click here to login</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="register.php" id="register-form">
                <div class="form-group">
                    <label for="username">Username <span aria-label="required">*</span></label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        minlength="3"
                        aria-required="true"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="email">Email <span aria-label="required">*</span></label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        aria-required="true"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Password <span aria-label="required">*</span></label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        minlength="6"
                        aria-required="true"
                        aria-describedby="password-help"
                    >
                    <small id="password-help">Minimum 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password <span aria-label="required">*</span></label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required 
                        minlength="6"
                        aria-required="true"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
            </form>
            
            <p style="text-align: center; margin-top: 1rem;">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </main>
    
    <script src="script.js"></script>
</body>
</html>
