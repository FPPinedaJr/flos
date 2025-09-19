<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// If using Composer (recommended)
require '../php/vendor/autoload.php';

// If NOT using Composer, comment the line above and uncomment below:
// require 'PHPMailer/src/Exception.php';
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars($_POST["name"] ?? '');
    $email = htmlspecialchars($_POST["email"] ?? '');
    $org = htmlspecialchars($_POST["organization"] ?? '');
    $message = htmlspecialchars($_POST["message"] ?? '');

    $to = "202280193@psu.palawan.edu.ph"; // where messages go
    $subject = "New Contact Form Message from $name";
    $body = "
        <h2>New Contact Form Submission</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Organization:</strong> $org</p>
        <p><strong>Message:</strong><br>" . nl2br($message) . "</p>
    ";

    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'quarsi@miceff.com';   // your Hostinger email
        $mail->Password = '@tt3nDanc3';          // your Hostinger email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // 'ssl'
        $mail->Port = 465; // 587 for TLS

        // Recipients
        $mail->setFrom('quarsi@miceff.com', 'Website Contact Form');
        $mail->addAddress($to); // destination email
        if (!empty($email)) {
            $mail->addReplyTo($email, $name); // let you reply directly
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        echo 'OK';
    } catch (Exception $e) {
        echo "Message could not be sent. Error: {$mail->ErrorInfo}";
    }
}
