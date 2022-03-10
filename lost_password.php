<?php
$is_up=true;
require 'config.php';

if($postgres_db_login['host']!='localhost')
    $postgres_link_login = @pg_connect('dbname='.$postgres_db_login['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_db_login['host']);
else
    $postgres_link_login = @pg_connect('dbname='.$postgres_db_login['database'].' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link_login===FALSE)
    $is_up=false;

require_once 'libs/PHPMailer/Exception.php';
require_once 'libs/PHPMailer/OAuth.php';
require_once 'libs/PHPMailer/PHPMailer.php';
require_once 'libs/PHPMailer/POP3.php';
require_once 'libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$https=false;
if(isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT']=='443')
    $https=true;
if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']=='https')
    $https=true;
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')
    $https=true;
if(isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME']=='https')
    $https=true;
if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']=='443')
    $https=true;
if($https)
    $httpproto='https';
else
    $httpproto='http';

$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPSecure='tls';
$mail->Host = $smtp_server;
$mail->Port = 25;
$mail->SMTPAuth = true;
$mail->Username = $smtp_login;
$mail->Password = $smtp_password;
$mail->setFrom($admin_email, 'CatchChallenger');
$mail->addReplyTo($admin_email, 'CatchChallenger');
$mail->isHTML(true);

$reply = pg_prepare($postgres_link_login,'SELECTaccount','SELECT email,encode(password,\'hex\') as password FROM account WHERE id=$1') or die(pg_last_error());
$reply = pg_prepare($postgres_link_login,'SELECTencode','SELECT encode(login,\'hex\') as login,encode(password,\'hex\') as password,date,email FROM account WHERE id=$1') or die(pg_last_error());
$reply = pg_prepare($postgres_link_login,'UPDATEaccount','UPDATE account SET login=decode($1,\'hex\'),password=decode($2,\'hex\') WHERE id=$3') or die(pg_last_error());
$reply = pg_prepare($postgres_link_login,'SELECTemail','SELECT encode(login,\'hex\') as login,encode(password,\'hex\') as password,date,email,id FROM account WHERE email=$1') or die(pg_last_error());
$reply = pg_prepare($postgres_link_login,'SELECTlogin','SELECT encode(login,\'hex\') as login,encode(password,\'hex\') as password,date,email,id FROM account WHERE login=decode($1,\'hex\')') or die(pg_last_error());

function send_mail($title,$text,$to,$type,$from)
{
	$headers = 'From: '.$from."\r\n";
	$headers .= 'MIME-Version: 1.0'."\r\n";
	$headers .= 'Content-type: '.$type.'; charset=UTF-8'."\r\n";
	$return=@mail($to,'=?UTF-8?B?'.base64_encode($title).'?=',$text,$headers);
	return $return;
}

function send_change_password($id)
{
    if(!preg_match('#^[0-9]+$#',$id))
        return false;
	global $admin_email,$mail,$smtp_server,$postgres_link_login;
	$reply = pg_execute($postgres_link_login,'SELECTaccount',array($id)) or die(pg_last_error());
	if($data = pg_fetch_array($reply))
	{
        if(strpos($data['email'],'@')===FALSE)
            return false;
        if($smtp_server!='')
        {
            $mail->addAddress($data['email'], '');
            $mail->Subject = 'Change your password on '.$_SERVER['HTTP_HOST'];
            $body='<div style="margin:40px;">To change your password on http://'.$_SERVER['HTTP_HOST'].', click here: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?id='.$id.'&oldpass='.$data['password'].'</div>';
            $mail->Body = str_replace('XXXBODYXXX',$body,file_get_contents($_SERVER['DOCUMENT_ROOT'].'/template/mail/en.template'));
            return $mail->send();
        }
        else
        {
            send_mail('Change your password on '.$_SERVER['HTTP_HOST'],'To change your password on http://'.$_SERVER['HTTP_HOST'].', click here: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?id='.$id.'&oldpass='.$data['password'],$data['email'],'text/plain',$admin_email);
            return true;
        }
	}
	else
		return false;
}

$title='CatchChallenger lost password';
$description='CatchChallenger lost password';
$keywords='catchchallenger,catch challenger,catch challenger,lost password';
include 'template/top.php';
include 'template/top2.php';
?>

<div id="title">Lost password for CatchChallenger account</div>
<br />
<br />
<img src="/images/pixel.png" width="96" height="96" style="float:left; margin-right:7px;" class="tiers_img" alt="" />
<p class="text">
<?php
if(!$is_up)
    echo 'Password change is actually <span style="color:red;"><b>closed</b></span>.<br />';
else
{
    if(isset($_GET['id']) && isset($_GET['oldpass']))
    {
        if(!preg_match('#^[0-9]+$#',$_GET['id']))
            exit;
        if(isset($_POST['new_password']) && isset($_POST['email']))
        {
            $login_hash=hash("sha224",hash("sha224",$_POST['email'].'RtR3bm9Z1DFMfAC3',true));
            $reply = pg_execute($postgres_link_login,'SELECTlogin',array($login_hash)) or die(pg_last_error());
            if($data = pg_fetch_array($reply) && $data['id']!=$_GET['id'])
                echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Other account already found with same login</b></span><br />';
            else
            {
                $handle = @fopen("blacklisted-passwords.txt", "r");
                $arrayofpass=array();
                if ($handle)
                {
                    while (($buffer = fgets($handle, 4096)) !== false) {
                        $arrayofpass[]=$buffer;
                    }
                    /*if (!feof($handle)) {
                        echo "Error: unexpected fgets() fail\n";
                    }*/
                    fclose($handle);
                }
                if(in_array(strtolower($_POST['password']),$arrayofpass))
                    echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your password is into the most common password, it need be unique</b></span><br />';
                else if($_POST['password']==$_POST['email'])
                    echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your password can\'t be same as your login</b></span><br />';
                else if(!preg_match('#[a-z]#',$_POST['password']) || !preg_match('#[A-Z]#',$_POST['password']) || !preg_match('#[0-9]#',$_POST['password']) || strlen($_POST['password'])<6)
                    echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your password need be composed of upper and lower char and number. And need be more than 6 of lenght and without space</b></span><br />';
                else
                {
                    $pos=strpos($_POST['email'],'@');
                    if($_POST['password']==substr($_POST['email'],0,$pos))
                        echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your password can\'t be same as your first part of your email</b></span><br />';
                    else
                    {
                        $reply = pg_execute($postgres_link_login,'SELECTencode',array($_GET['id'])) or die(pg_last_error());
                        if($data = pg_fetch_array($reply))
                        {
                            if($data['password']==$_GET['oldpass'])
                            {
                                pg_execute($postgres_link_login,'UPDATEaccount',array($login_hash,hash("sha224",$_POST['new_password'].'AwjDvPIzfJPTTgHs'.$_POST['email']),$_GET['id'])) or die(pg_last_error());
                                echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Password changed</b></span><br />';
                            }
                            else
                                echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Wrong key for password change</b></span><br />';
                        }
                        else
                            echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Account not found</b></span><br />';
                    }
                }
            }
        }
        echo '<form name="input" method="post">New password: <script type="text/javascript"><!--
        document.write("<input name=\"new_password\" type=\"password\">");
        --></script><br />';
        echo '<br /><input type="submit" value="Ok"></form>';
    }
    else
    {
        if(isset($_POST['login_or_email']))
        {
            $reply = pg_execute($postgres_link_login,'SELECTemail',array($_POST['login_or_email'])) or die(pg_last_error());
            if($data = pg_fetch_array($reply))
            {
                if(send_change_password($data['id']))
                    echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Email to change your password send, check your email</b></span><br />';
                else
                    echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Missing data or meta data to send the email</b></span><br />';
            }
            else
            {
                $login_hash=hash("sha224",hash("sha224",$_POST['login_or_email'].'RtR3bm9Z1DFMfAC3',true));
                $reply = pg_execute($postgres_link_login,'SELECTlogin',array($login_hash)) or die(pg_last_error());
                if($data = pg_fetch_array($reply))
                {
                    if(send_change_password($data['id']))
                        echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Email to change your password send, check your email</b></span><br />';
                    else
                        echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Missing data or meta data to send the email</b></span><br />';
                }
                else
                    echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Login or email not found</b></span><br />';
            }
        }
        echo 'Password change is actually <span style="color:green;"><b>open</b></span>.<br />';
        echo '<form name="input" method="post">
        Email: <script type="text/javascript"><!--
        document.write("<input name=\"login_or_email\" type=\"text\">");
        --></script>';
        echo '<input type="submit" value="Ok"></form>';
    }
}
?>
</p>
<?php
include 'template/bottom2.php';
include 'template/bottom.php';
