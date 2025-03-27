<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Composer autoload for PHPMailer

function processEmail() {
    // Validate input
    $requiredFields = ['sender_name', 'sender_email', 'recipient_email', 'subject', 'body'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            http_response_code(400);
            die("Missing required field: $field");
        }
    }

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'your_smtp_host';  // Configure your SMTP host
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_username';   // SMTP username
        $mail->Password   = 'your_password';   // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom($_POST['sender_email'], $_POST['sender_name']);
        $mail->addAddress($_POST['recipient_email']);
        $mail->addReplyTo($_POST['sender_email'], $_POST['sender_name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $_POST['subject'];
        $mail->Body    = $_POST['body'];

        // Attachments
        if (!empty($_FILES['attachments'])) {
            $attachmentFiles = $_FILES['attachments'];
            for ($i = 0; $i < count($attachmentFiles['name']); $i++) {
                $mail->addAttachment(
                    $attachmentFiles['tmp_name'][$i], 
                    $attachmentFiles['name'][$i]
                );
            }
        }

        $mail->send();
        echo 'Email sent successfully';
    } catch (Exception $e) {
        http_response_code(500);
        echo "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}

// Only process if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    processEmail();
}
?>