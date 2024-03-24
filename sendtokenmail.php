<pre><?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/PHPMailer/Exception.php';
require 'libs/PHPMailer/PHPMailer.php';
require 'libs/PHPMailer/SMTP.php';

require 'FGETNw3g7uSX3J9c.php';

$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = $smtp_server;
$mail->Port = $smtp_port;
$mail->SMTPAuth = $smtp_login!='';
$mail->Username = $smtp_login;
$mail->Password = $smtp_password;
$mail->setFrom($admin_email, 'CatchChallenger');
$mail->addReplyTo($admin_email, 'CatchChallenger');
$mail->SMTPAutoTLS = false;
$mail->SMTPSecure = $smtp_secure;
$mail->SMTPDebug  = 1;
$mail->addAddress('aavr9gwvhpj5x3gjzeg3u3d4@hotmail.com');
$mail->Subject = 'token: vddIbdMmacj7nipdsbCLW2BUVLOWkKoE';
$mail->Body = 'token: vddIbdMmacj7nipdsbCLW2BUVLOWkKoE';
if (!$mail->send())
    echo 'Mailer hotmail error: '.$mail->ErrorInfo."\n";
else
    echo 'Mailer hotmail ok'."\n";

$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = $smtp_server;
$mail->Port = $smtp_port;
$mail->SMTPAuth = $smtp_login!='';
$mail->Username = $smtp_login;
$mail->Password = $smtp_password;
$mail->setFrom($admin_email, 'CatchChallenger');
$mail->addReplyTo($admin_email, 'CatchChallenger');
$mail->SMTPAutoTLS = false;
$mail->SMTPSecure = $smtp_secure;
$mail->SMTPDebug  = 1;
$mail->addAddress('jg24atqtwrbnq6x3tyni4dg3@gmail.com');
$mail->Subject = 'token: vddIbdMmacj7nipdsbCLW2BUVLOWkKoE';
$mail->Body = 'token: vddIbdMmacj7nipdsbCLW2BUVLOWkKoE';
if (!$mail->send())
    echo 'Mailer gmail error: '.$mail->ErrorInfo."\n";
else
    echo 'Mailer gmail ok'."\n";
