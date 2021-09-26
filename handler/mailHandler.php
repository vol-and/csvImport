<?php require_once('../db.php');
if (isset($post['activity'])) {
    $uid = $post['uid'];
    $userClass = new User ($mysqli);
    $userData = $userClass->getUserById($uid);
    if ( ! isset($userData)) exit();
} else {
    exit();
}

$host = $_SERVER['HTTP_ORIGIN'];

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require '../libs/mail/Exception.php';
require '../libs/mail/PHPMailer.php';
require '../libs/mail/SMTP.php';

$mail = new PHPMailer(true);

try {
    //Server settings
//    $mail->isSMTP();                                              // Send using SMTP
//    $mail->Host = 'smtp.gmail.com';                               // Set the SMTP server to send through
//    $mail->SMTPAuth = true;                                       // Enable SMTP authentication
//    $mail->Username = 'your_email@gmail.com';                     // SMTP username
//    $mail->Password = 'secret';                                   // SMTP password
//    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
//    $mail->Port = 587;                                            // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom(ADMIN_MAIL, 'Admin');
    $mail->addAddress($userData['email']);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset';
    $mail->Body = 'To reset your password click <a href="' . $host . '/content/passwordchange.php?code='
                            . $userData['temp_hash'] . '">here </a>.';
    if ($mail->send()) return true;

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}