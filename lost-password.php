<?php
$is_up=true;
require 'config.php';

if($postgres_db_login['host']!='localhost')
    $postgres_link_login = @pg_connect('dbname='.$postgres_db_login['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_db_login['host']);
else
    $postgres_link_login = @pg_connect('dbname='.$postgres_db_login['database'].' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link_login===FALSE)
    $is_up=false;

require_once 'libs/class.smtp.php';
require_once 'libs/class.phpmailer.php';

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
$mail->isHTML(false);

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
            $mail->Body = 'To change your password on http://'.$_SERVER['HTTP_HOST'].', click here: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?id='.$id.'&oldpass='.$data['password'];
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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Lost password for CatchChallenger account</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF8"/>
		<meta name="robots" content="index,follow"/>
		<meta name="description" content="Register catchchallenger account" />
		<meta name="keywords" content="catchchallenger,pokemon,minecraft,crafting,official server" />
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css" />
        <meta name="viewport" content="width=device-width" />
	</head>
	<body>
		<div id="container">
			<div id="header">
				<div id="logo"></div>
				<div id="back_menu">
					<table>
					<tr>
						<td><a href="/">Home</a></td>
						<td><a href="/official-server.html">Official server</a></td>
						<td><a href="/download.html">Download</a></td>
						<td><a href="/screenshot.html">Screenshot</a></td>
						<td><a href="/shop/">Shop</a></td>
						<td><a href="/community.html">Community</a></td>
						<td><a href="/contact.html">Contact</a></td>
					</tr>
					</table>
				</div>
			</div>
			<div id="body">
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
						if(isset($_POST['new_password']) && isset($_POST['login']))
						{
                            $login_hash=hash("sha224",hash("sha224",$_POST['login'].'RtR3bm9Z1DFMfAC3',true));
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
                                else if($_POST['password']==$_POST['login'])
                                    echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your password can\'t be same as your login</b></span><br />';
                                else if(!preg_match('#[a-z]#',$_POST['password']) || !preg_match('#[A-Z]#',$_POST['password']) || !preg_match('#[0-9]#',$_POST['password']) || strlen($_POST['password'])<6 || strpos($_POST['password'],' ')!==false)
                                    echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your password need be composed of upper and lower char and number. And need be more than 6 of lenght and without space</b></span><br />';
                                else
                                {
                                    $reply = pg_execute($postgres_link_login,'SELECTencode',array($_GET['id'])) or die(pg_last_error());
                                    if($data = pg_fetch_array($reply))
                                    {
                                        if($data['password']==$_GET['oldpass'])
                                        {
                                            pg_execute($postgres_link_login,'UPDATEaccount',array($login_hash,hash("sha224",$_POST['new_password'].'AwjDvPIzfJPTTgHs'.$_POST['login']),$_GET['id'])) or die(pg_last_error());
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
						echo '<form name="input" method="post">New login: <script type="text/javascript"><!--
                        document.write("<input name=\"login\" type=\"text\">");
                        --></script><br />
						New password: <script type="text/javascript"><!--
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
						Login or email: <script type="text/javascript"><!--
                        document.write("<input name=\"login_or_email\" type=\"text\">");
                        --></script>';
						echo '<input type="submit" value="Ok"></form>';
					}
				}
				?>
				</p>
			</div>
			<br />
			<div id="footer">
				<div id="copyright">CatchChallenger - <span style="color:#777;font-size:80%">Donate Bitcoin: </span><span style="color:#999;font-size:70%">1C4VLs16HX5YBoUeCLxEMJq8TpP24dcUJN</span> <span style="color:#777;font-size:80%">Nextcoin: </span><span style="color:#999;font-size:70%">NXT-MY96-548U-A5V5-BSR7R</span></div>
			</div>
		</div>
<script type="text/javascript">
var _paq=_paq || [];_paq.push(["trackPageView"]);_paq.push(["enableLinkTracking"]);
(function() {
var u=(("https:"==document.location.protocol)?"https":"http")+"://stat.first-world.info/";_paq.push(["setTrackerUrl",u+"piwik.php"]);_paq.push(["setSiteId","22"]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.type="text/javascript";g.defer=true;g.async=true;g.src=u+"piwik.js";s.parentNode.insertBefore(g,s);
})();
</script>
	</body>
</html>
