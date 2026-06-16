<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Numverify API key
$apiKey = '80a99911c58f6f1d60bd6b4bf75c6cee';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['phone_number'])) {
    $number = $_POST['phone_number'];
    $username = $_SESSION['username'];

    // Check if the phone number is registered in the system
    $conn = new mysqli("localhost", "root", "", "security_check");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the phone number exists in the database
    $stmt = $conn->prepare("SELECT username FROM users WHERE phone_number = ?");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Phone number belongs to a registered user
        $row = $result->fetch_assoc();
        $message = "This phone number belongs to user: " . htmlspecialchars($row['username']);
    } else {
        // Call Numverify API to validate the phone number
        $url = "http://apilayer.net/api/validate?access_key=$apiKey&number=$number";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            $message = "Error: Unable to connect to Numverify API.";
        } else {
            $data = json_decode($response, true);

            // Add error handling for the API response
            if (isset($data['success']) && !$data['success']) {
                $message = "Error: " . $data['error']['info']; // Show error from the API
            } else if (isset($data['valid']) && $data['valid']) {
                // Phone number is valid, but not in your system
                $message = "The phone number is valid. Location: " . $data['location'] . " Carrier: " . $data['carrier'];
            } else {
                $message = "Invalid phone number.";
            }
        }
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the index page with the message
    header("Location: index.php?phone_check=" . urlencode($message));
}
?>
