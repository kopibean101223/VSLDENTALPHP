<?php
require 'db_connect.php'; 
require 'PHPMailer/PHPMailerAutoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    //$email = "johndmiguel292004@gmail.com";

$stmtCheck = $conn->prepare("SELECT * FROM accounts WHERE email = :email");
$stmtCheck->bindParam(':email', $email);
$stmtCheck->execute();

if ($stmtCheck->rowCount() == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Email not found"
    ]);
    exit;
} else {

   try {
        
        $number = '0123456789';
        $randomPin = substr(str_shuffle($number), 0, 6);    

        
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jdmigz01@gmail.com';   
        $mail->Password = 'fjhv krey cmil toaa';
        $mail->SMTPSecure = 'tls';                
        $mail->Port = 587;


        $mail->setFrom('jdmigz01@gmail.com', 'VSL Dental Clinic');
        $mail->addAddress($email); 
        //$mail->addAddress('johndmiguel292004@gmail.com');

        $mail->Subject = 'PASSWORD RESET';
        $mail->Body = 'Hello ' . $email . ",\n\n" .
        "This is an auto-generated message. Your account password has been successfully changed.\n\n" .
        "If you made this change, no further action is needed.\n" .
        "If you did not request this change, please contact us immediately at [Support Email/Phone Number] so we can secure your account.\n\n" .
        "Thanks.\n\n" .
        "Your one time password is: " . $randomPin;

        if ($mail->send()) {
            echo json_encode([
                "status" => "success",
                "message" => "New password sent to your email",
                "pin" => $randomPin
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to send email"
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Error: " . $e->getMessage()
        ]);
    }









}
}
?>