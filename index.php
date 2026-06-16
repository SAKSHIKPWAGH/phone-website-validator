<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone & Website Checker</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

        <!-- Phone Number Checker -->
        <div>
            <h2>Check Phone Number</h2>
            <form method="POST" action="check_phone.php">
                <input type="tel" name="phone_number" placeholder="Enter phone number" required>
                <button type="submit">Check Phone</button>
            </form>
            <?php if (isset($_GET['phone_check'])): ?>
                <div class="result-output">
                    <?php echo htmlspecialchars($_GET['phone_check']); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Website URL Checker -->
        <div>
            <h2>Check Website</h2>
            <form method="POST" action="check_website.php">
                <input type="url" name="website_url" placeholder="Enter website URL" required>
                <button type="submit">Check Website</button>
            </form>
            <?php if (isset($_GET['website_check'])): ?>
                <div class="result-output">
                    <?php echo htmlspecialchars($_GET['website_check']); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
