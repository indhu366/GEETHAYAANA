<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';
require __DIR__ . '/phpmailer/Exception.php';

function sendEmail($to, $subject, $message){

    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 2;

    try{
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        

        // 🔥 YOUR EMAIL
        $mail->Username = 'geethayaana2026@gmail.com';
        $mail->Password = 'beqvdicujtdruwct';

        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

        $mail->setFrom('geethayaana2026@gmail.com', 'Geethayaana');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;
        }catch(Exception $e){
    echo "Mailer Error: " . $mail->ErrorInfo;
    return false;
}
}
?>