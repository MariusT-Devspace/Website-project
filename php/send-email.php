<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

$env = parse_ini_file('../.env');

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->Debugoutput = 'error_log';
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp-relay.sendinblue.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $env['USER'];                     //SMTP username
    $mail->Password   = $env['PASSWORD'];                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom($env['SENDFROM'], 'Construcciones Adan contacto');
    $mail->addReplyTo(filter_var($_POST['formEmail'], FILTER_SANITIZE_EMAIL));
    $mail->addAddress($env['SENDTO'], filter_var($_POST['formName'], FILTER_SANITIZE_STRING));     //Add a recipient

    //Content
    $mail->isHTML(false);                                  //Set email format to HTML
    $mail->Subject = filter_var($_POST['formSubject'], FILTER_SANITIZE_STRING);
    $mail->Body    = filter_var($_POST['formMessage'], FILTER_SANITIZE_STRING);

    $mail->send();
    echo 'Tu mensaje ha sido enviado';
} catch (Exception $e) {
    echo "El mensaje no ha podido ser enviado. Mailer Error: {$mail->ErrorInfo}";
}

?>