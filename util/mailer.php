<?php
require '../../phpmailer/PHPMailerAutoload.php';

function send_mail($recipient, $subject, $body)
{
    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'asmtp.bilkent.edu.tr';                  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'grc@ee.bilkent.edu.tr';         // SMTP username
    $mail->Password = 'GRCGRC2020';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->From = 'grc@ee.bilkent.edu.tr';
    $mail->FromName = 'GRC';
    $mail->addAddress($recipient);                        // Add a recipient

    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body = $body;

    if (!$mail->send()) {
        return false;
    } else {
        return true;
    }
}

function send_password($firstname, $lastname, $password, $recipient)
{
    send_mail($recipient,
        "Your GRC poster submission password",
        "Dear " . $firstname . " " . $lastname .
        ". \nYour GRC poster submission password is: " . $password);
}

function send_confirmation($firstname, $lastname, $poster, $recipient)
{
    send_mail($recipient,
        "GRC poster submission confirmation",
        "Dear " . $firstname . " " . $lastname .
        ". \n Your GRC poster submission has been received. " .
        "\nYou can access your submission at: " . $poster);
}
