<?php
// DraftDesk Waitlist - Email Collector
// Save this as collect.php on your Hostinger hosting

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email address']);
    exit;
}

$csv_file = __DIR__ . '/waitlist.csv';
$timestamp = date('Y-m-d H:i:s');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Create CSV with header if it doesn't exist
if (!file_exists($csv_file)) {
    $file = fopen($csv_file, 'w');
    fputcsv($file, ['email', 'timestamp', 'ip']);
    fclose($file);
}

// Check if email already exists
$existing = file_get_contents($csv_file);
if (strpos($existing, $email) !== false) {
    echo json_encode(['message' => 'You are already on the waitlist!', 'status' => 'duplicate']);
    exit;
}

// Append email to CSV
$file = fopen($csv_file, 'a');
fputcsv($file, [$email, $timestamp, $ip]);
fclose($file);

// Optional: Send notification to your email
$to = 'your-email@example.com'; // Change this to your email
$subject = 'New DraftDesk Waitlist Signup';
$message = "New signup!\n\nEmail: $email\nTime: $timestamp\nIP: $ip";
$headers = 'From: noreply@neshama.pw';

// Uncomment to enable email notifications:
// mail($to, $subject, $message, $headers);

echo json_encode(['message' => 'You are on the list!', 'status' => 'success']);
?>
