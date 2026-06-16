<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// VirusTotal API key
$virusTotalApiKey = 'a0fe649b903422d648cd8863e58f266bcc4d250a6e398d1f2a2df0c524706569';

// Whois API key
$whoisApiKey = 'at_PUJk7mOjLPfjZ20TtMCAtLXihBDAF';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['website_url'])) {
    $url = $_POST['website_url'];
    $username = $_SESSION['username'];

    // VirusTotal API URL
    $virusTotalUrl = "https://www.virustotal.com/vtapi/v2/url/report?apikey=$virusTotalApiKey&resource=$url";

    // Call VirusTotal API
    $virusTotalResponse = file_get_contents($virusTotalUrl);
    $virusTotalData = json_decode($virusTotalResponse, true);

    // Check if the website is safe
    $is_safe = true;
    if (isset($virusTotalData['positives']) && $virusTotalData['positives'] > 0) {
        $is_safe = false;
        $message = "The website $url is potentially unsafe.";
    } else {
        $message = "The website $url is safe.";
    }

    // Call Whois API to get domain information
    $whoisUrl = "https://www.whoisxmlapi.com/whoisserver/WhoisService?apiKey=$whoisApiKey&domainName=$url";
    $whoisResponse = file_get_contents($whoisUrl);
    $whoisData = json_decode($whoisResponse, true);

    if (isset($whoisData['WhoisRecord'])) {
        $domainOwner = $whoisData['WhoisRecord']['registrant']['name'];
        $message .= " Domain Owner: " . htmlspecialchars($domainOwner);
    }

    // Store the result in the database
    $conn = new mysqli("localhost", "root", "", "security_check");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO website_checks (url, checked_by, is_safe) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $url, $username, $is_safe);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    // Redirect back to the index page with the message
    header("Location: index.php?website_check=" . urlencode($message));
}
?>
