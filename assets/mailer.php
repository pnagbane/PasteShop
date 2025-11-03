<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require('vendor/autoload.php');

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'mail.roncloud.com.ng';
    $mail->SMTPAuth = true;
    $mail->Username = 'pasteshop@roncloud.com.ng';
    $mail->Password = 'Pasteshop@25';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
} catch(\Exception $e) {
    echo "Mail not Sent: {$mail->ErrorInfo}";
}