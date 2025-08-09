
<?php
require_once 'dbConnect.php';

// Login logic
$errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT password FROM users WHERE username=?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            // Login successful! Update last_login
            $update = $conn->prepare('UPDATE users SET last_login=NOW() WHERE username=?');
            $update->bind_param('s', $username);
            $update->execute();
            $update->close();
            echo '<script>localStorage.setItem("isLoggedIn", "true"); localStorage.setItem("username", "' . htmlspecialchars($username) . '"); window.location.href = "index.html";</script>';
            exit();
        } else {
            $errorMsg = 'Invalid password.';
        }
    } else {
        $errorMsg = 'Username not found.';
    }
    $stmt->close();
}
$conn->close();
?>
<!-- You can redirect or show a message here -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FonXpress</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
    .alert {
        background: #ffe5e5;
        color: #c00;
        border: 1px solid #c00;
        padding: 1em;
        margin-bottom: 1em;
        border-radius: 6px;
        text-align: center;
        font-weight: bold;
    }
    </style>
</head>
<body>
    <nav>
        <div class="nav-left">
            <a href="index.html">Home</a>
            <a href="products.html">Products</a>
            <a href="contact.html">Contact Us</a>
        </div>
        <div class="nav-right">
            <a href="login.html">Login</a>
            <a href="register.html">Register</a>
        </div>
    </nav>
    <header>
        <h1>Login to Your Account</h1>
    </header>
    <main>
        <?php if (!empty($errorMsg)): ?>
            <div class="alert"><?php echo $errorMsg; ?></div>
        <?php endif; ?>
        <form class="form-box" action="login.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <!-- Simple captcha -->
            <label for="captcha">Captcha: What is <span id="captchaNum1"></span> + <span id="captchaNum2"></span>?</label>
            <input type="text" id="captcha" name="captcha" required>

            <button type="submit">Login</button>
        </form>
        <script>
        // Captcha setup
        var num1 = Math.floor(Math.random() * 10) + 1;
        var num2 = Math.floor(Math.random() * 10) + 1;
        document.getElementById('captchaNum1').textContent = num1;
        document.getElementById('captchaNum2').textContent = num2;

        document.querySelector('.form-box').addEventListener('submit', function(e) {
            var captcha = document.getElementById('captcha').value;
            if (parseInt(captcha) !== num1 + num2) {
                alert('Captcha answer is incorrect!');
                e.preventDefault();
            }
        });
        </script>
    </main>
    <footer>
    <p>&copy; 2025 FonXpress. All rights reserved.</p>
    </footer>
</body>
</html>
