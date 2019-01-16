
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpMailer/vendor/autoload.php';
 
//uses PhpMailer

//source:https://www.sitepoint.com/sending-emails-php-phpmailer/


class emailObj {
 
private $recipent;
private $type;
private $db;
 
public function __construct($to,$nature) {
$this->recipent = $to;
$this->type = $nature;
}
 



public function createBody($msg){
	$body = $msg;
	if($this->type == "forgot"){
	$this->sendEmail($body);
	}	



}

private function sendEmail($body){
$mail = new PHPMailer(true);                              
try {
    $mail->SMTPDebug = 2;    
    $mail->SMTPOptions = array(
'ssl' => array(
'verify_peer' => false,
'verify_peer_name' => false,
'allow_self_signed' => true
));   
    $mail->isSMTP();                                      
    $mail->Host = "smtp.gmail.com"; 
    $mail->SMTPAuth = true;                               
    $mail->Username = 'GymPlanner42@gmail.com';                
    $mail->Password = 'gymplanner';                           
    $mail->SMTPSecure = 'tls';                            
    $mail->Port = 587;                                    
    $mail->setFrom('scheduleprogram99@gmail.com', 'Mailer');
    $mail->addAddress($this->recipent);               
    $mail->isHTML(true);                                
    $mail->Subject = 'Password Recovery Link';
    $mail->Body    = $body;
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
}
}

?>



