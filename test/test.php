<?php
if(!isset($_SERVER['REMOTE_ADDR']))
    exit;
if($_SERVER['REMOTE_ADDR']!='190.186.245.10' && $_SERVER['REMOTE_ADDR']!='127.0.0.1' && $_SERVER['REMOTE_ADDR']!='2803:1920::2:10')
    exit;

$failedcount=0;

require_once '../config.php';
require_once '../libs/class.smtp.php';
require_once '../libs/class.phpmailer.php';

$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPSecure='tls';
$mail->Host = $smtp_server;
$mail->Port = $smtp_port;
$mail->SMTPAuth = true;
$mail->Username = $smtp_login;
$mail->Password = $smtp_password;
$mail->setFrom($admin_email, 'CatchChallenger');
$mail->addReplyTo($admin_email, 'CatchChallenger');
$mail->isHTML(false);

$mail->addAddress('alpha_one_x86@first-world.info','Test cron');
$mail->Subject = 'Cron test mail via CatchChallenger';
$mail->Body = 'Cron test mail via CatchChallenger';
if (!$mail->send())
{
    echo 'Send error on '.$smtp_server.': ' . $mail->ErrorInfo;
    $failedcount++;
}

if($failedcount==0)
{ ?><center><div style="color:#00AD2B;">OK</div></center><?php } ?>
