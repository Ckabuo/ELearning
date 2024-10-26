<?php
// Configuration
$to = "w74403584@gmail.com"; // Replace with actual recipient email
$subject = $_POST['subject'];
$name = $_POST['name'];
$message = $_POST['message'];
$email = $_POST['email'];

// Check CSRF token (if using one)
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== 'your_csrf_token_here') {
    die("Invalid request");
}

// Prepare email content
$headers = array(
    'From' => $email,
    'To' => $to,
    'Subject' => $subject,
    'MIME-Version' => '1.0',
    'Content-Type' => 'text/html; charset=UTF-8'
);

// Send email using Google SMTP server
$smtp_host = 'smtp.gmail.com';
$smtp_port = 587;
$smtp_user = 'w74403584@gmail.com';
$smtp_pass = 'your_app_password'; // Generate app password from Google Account settings

$connection = stream_socket_client("tcp://$smtp_host:$smtp_port", $error, $errno, 30);

if (!$connection) {
    die("Connection error");
}

stream_set_timeout($connection, 120);

fputs($connection, "EHLO " . $_SERVER['SERVER_NAME'] . "\r\n");

$login_result = fgets($connection);
if ($login_result !== "235 Authentication Successful") {
    die("Authentication failed");
}

fputs($connection, "AUTH PLAIN " . base64_encode("\x00\x01" . $smtp_user . "\x00" . $smtp_pass) . "\r\n");

$auth_result = fgets($connection);
if ($auth_result !== "235 Authentication Successful") {
    die("Authentication failed");
}

$message_body = "
<html>
<body>
<h2>Contact Form Submission</h2>
<p>Name: $name</p>
<p>Email: $email</p>
<p>Message:</p>
<pre>$message</pre>
</body>
</html>
";

fputs($connection, "MAIL FROM:<$email>\r\n");
fputs($connection, "RCPT TO:<$to>\r\n");
fputs($connection, "DATA\r\n");
fputs($connection, $headers[0] . "\r\n" .
         $headers[1] . "\r\n" .
         $headers[2] . "\r\n" .
         $headers[3] . "\r\n" .
         "\r\n" .
         $message_body . "\r\n.\r\n");
fputs($connection, "QUIT\r\n");

fclose($connection);

// Redirect to success page or display message
header("Location: thanks.php");
exit;
?>
