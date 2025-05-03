<?php 
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../config/config.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    
    function createMailerService() {
        $emailSenderName = "QCU Guidance and Counseling Office";
        
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = EMAIL_SERVICE_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_SERVICE_SENDER;
        $mail->Password = EMAIL_SERVICE_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = EMAIL_SERVICE_PORT;

        $mail->setFrom(EMAIL_SERVICE_SENDER,  $emailSenderName);
        $mail->addReplyTo(EMAIL_SERVICE_SENDER, $emailSenderName); 

        $mail->isHTML(true); 

        return $mail;
    }
?>