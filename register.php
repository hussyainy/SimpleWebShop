
<?php
require_once __DIR__ . '/config/dbConnect.php';

// Registration logic
$resultMsg = '';
$resultType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username or email exists
    $check = $conn->prepare('SELECT id FROM users WHERE username=? OR email=?');
    $check->bind_param('ss', $username, $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $resultMsg = 'Username or email already exists.';
        $resultType = 'error';
    } else {
    $stmt = $conn->prepare('INSERT INTO users (fullname, email, phone, address, gender, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssssss', $fullname, $email, $phone, $address, $gender, $username, $password);
        if ($stmt->execute()) {
            $resultMsg = 'Registration successful!';
            $resultType = 'success';
        } else {
            $resultMsg = 'Registration failed.';
            $resultType = 'error';
        }
        $stmt->close();
    }
    $check->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FonXpress</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
    .alert-success {
        background: #e5ffe5;
        color: #080;
        border: 1px solid #080;
        padding: 1em;
        margin-bottom: 1em;
        border-radius: 6px;
        text-align: center;
        font-weight: bold;
    }
    .alert-error {
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
    <h1>Member Account Registration</h1>
    </header>
    <main>
        <?php if (!empty($resultMsg)): ?>
            <div class="alert-<?php echo $resultType; ?>"><?php echo $resultMsg; ?></div>
        <?php endif; ?>
        <form class="form-box" action="register.php" method="post">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" pattern="0[1-9][0-9]{7,8}$" maxlength="12" placeholder="e.g. 0123456789" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <label for="gender">Gender:</label>
            <div id="gender" style="margin-bottom:1em;">
                <input type="radio" id="male" name="gender" value="male" required>
                <label for="male" style="margin-right:1em;">Male</label>
                <input type="radio" id="female" name="gender" value="female" required>
                <label for="female">Female</label>
            </div>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Reconfirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <!-- Simple captcha -->
            <label for="captcha">Captcha: What is <span id="captchaNum1"></span> + <span id="captchaNum2"></span>?</label>
            <input type="text" id="captcha" name="captcha" required>

            <button type="submit">Register</button>
        </form>
        <script>
        // Captcha setup
        var num1 = Math.floor(Math.random() * 10) + 1;
        var num2 = Math.floor(Math.random() * 10) + 1;
        document.getElementById('captchaNum1').textContent = num1;
        document.getElementById('captchaNum2').textContent = num2;

        document.querySelector('.form-box').addEventListener('submit', function(e) {
            var pwd = document.getElementById('password').value;
            var cpwd = document.getElementById('confirm_password').value;
            var captcha = document.getElementById('captcha').value;
            if (pwd !== cpwd) {
                alert('Passwords do not match!');
                e.preventDefault();
                return;
            }
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
