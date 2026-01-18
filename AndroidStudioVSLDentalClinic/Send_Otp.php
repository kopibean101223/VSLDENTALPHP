<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contactnumber = $_POST['contactnumber']; 
    
  
    $otp = rand(100000, 999999);

    
    session_start();
    $_SESSION['otp'] = $otp;
    $_SESSION['contactnumber'] = $contactnumber;

    
    $apiKey = 'YOUR_SEMAPHORE_API_KEY';
    $sender = 'VSLClinic'; 

    $message = "Your VSL Dental Clinic verification code is: $otp";

    $ch = curl_init();
    $parameters = array(
        'apikey' => $apiKey,
        'number' => $contactnumber,
        'message' => $message,
        'sendername' => $sender
    );
    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);

    echo json_encode(["status" => "success", "message" => "OTP sent to $contactnumber"]);
}
?>