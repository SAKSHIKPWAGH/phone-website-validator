<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $phone_number = $_POST['phone_number'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "security_check");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert the user data into the database
    $stmt = $conn->prepare("INSERT INTO users (username, phone_number, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $phone_number, $password);
    if ($stmt->execute()) {
        echo "Registration successful! <a href='login.html'>Login here</a>";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
