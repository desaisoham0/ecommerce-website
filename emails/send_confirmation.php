<?php
require './contact/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

global $conn, $user_email, $items;

// Ensure variables are properly set
if (empty($user_email) || empty($items)) {
    error_log("User email or items not provided.");
    return;
}

// Email subjects and messages
$subjectUser = "Order Confirmation";
$subjectOwner = "New Order Placed";
$messageUser = "Thank you for your order!\n\nHere are your purchased items:\n$itemDetails\n\nWe appreciate your business!";
$messageOwner = "A new order has been placed by user email $user_email.\n\nOrder Details:\n$itemDetails";

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = getenv('DB_OWNER_EMAIL');
    $mail->Password = getenv('DB_OWNER_EMAIL_PASSWORD');
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Email to User
    $mail->setFrom(getenv('DB_OWNER_EMAIL'), 'Athletics Store');
    $mail->addAddress($user_email); // User's email
    $mail->Subject = $subjectUser;
    $mail->Body = $messageUser;
    $mail->send();

    // Clear recipients for next email
    $mail->clearAddresses();

    // Email to Owner
    $mail->addAddress(getenv('DB_OWNER_EMAIL'), 'Athletics Store'); // Owner's email
    $mail->Subject = $subjectOwner;
    $mail->Body = $messageOwner;
    $mail->send();

} catch (Exception $e) {
    error_log("Email sending failed: " . $mail->ErrorInfo);
    throw new Exception("Failed to send email: " . $mail->ErrorInfo);
}
?>
