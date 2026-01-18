<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    require 'PHPMailer/PHPMailerAutoload.php';

try {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jdmigz01@gmail.com';   // your Gmail
    $mail->Password = 'fjhv krey cmil toaa';
    $mail->SMTPSecure = 'tls';                // important: Gmail requires TLS
    $mail->Port = 587;

    $mail->setFrom('jdmigz01@gmail.com', 'VSL Dental Clinic');
    $mail->addAddress('johndmiguel292004@gmail.com'); // recipient


    $mail->Subject = 'New Contact Form Submission';
    $mail->Body    = '<b>Hello!</b> This is a test message from Android.';
    $mail->AltBody = 'Hello! This is a test message from Android.';

    
            if ($mail->send()) {
        echo "Mail sent!";
    } else {
        echo "Mailer error: " . $mail->ErrorInfo;
    }
    } catch (Exception $e) {
        echo 'Exception: ' . $mail->ErrorInfo;
    }
}
?>