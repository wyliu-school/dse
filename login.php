<?php
require_once 'config.php';
require_once 'auth.php';

redirectIfLoggedIn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        try {
            $conn = getDBConnection();
            
            // Get user by username
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } catch(PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = 'Login failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HKDSE Study Tracker</title>
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
            <h1>Login</h1>
            
            <?php if ($error): ?>
                <div class="message message-error" role="alert" aria-live="polite">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" id="login-form">
                <div class="form-group">
                    <label for="username">Username <span aria-label="required">*</span></label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required
                        aria-required="true"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        autofocus
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Password <span aria-label="required">*</span></label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        aria-required="true"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            
            <p style="text-align: center; margin-top: 1rem;">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </main>
    
    <script src="script.js"></script>
</body>
</html>
