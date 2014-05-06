<?php
$is_up=true;
require 'config.php';
$mysql_link=@mysql_connect($mysql_host,$mysql_login,$mysql_pass,true);
if($mysql_link===NULL)
	$is_up=false;
else if(!@mysql_select_db($mysql_db))
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
$mail->setFrom('alpha_one_x86@first-world.info', 'alpha_one_x86');
$mail->addReplyTo('alpha_one_x86@first-world.info', 'alpha_one_x86');
$mail->isHTML(false);

$ADMINISTRATOR_EMAIL='alpha_one_x86@first-world.info';
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
	global $ADMINISTRATOR_EMAIL,$mail,$smtp_server;
	$reply = mysql_query('SELECT * FROM `player` WHERE `id`='.addslashes($id)) or die(mysql_error());
	if($data = mysql_fetch_array($reply))
	{
		$reply_meta_data = mysql_query('SELECT * FROM `player_meta` WHERE `id`='.addslashes($id)) or die(mysql_error());
		if($data_meta_data = mysql_fetch_array($reply_meta_data))
		{
			if($smtp_server!='')
			{
				$mail->addAddress($data_meta_data['email'], '');
				$mail->Subject = 'Change your password on '.$_SERVER['HTTP_HOST'];
				$mail->Body = 'To change your password on http://'.$_SERVER['HTTP_HOST'].', click here: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?id='.$id.'&oldpass='.$data['password'];
				return $mail->send();
			}
			else
			{
				send_mail('Change your password on '.$_SERVER['HTTP_HOST'],'To change your password on http://'.$_SERVER['HTTP_HOST'].', click here: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?id='.$id.'&oldpass='.$data['password'],$data_meta_data['email'],'text/plain',$ADMINISTRATOR_EMAIL);
				return true;
			}
		}
		else
			return false;
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
						if(isset($_POST['new_password']))
						{
							$reply = mysql_query('SELECT * FROM `player` WHERE `id`='.addslashes($_GET['id'])) or die(mysql_error());
							if($data = mysql_fetch_array($reply))
							{
								if($data['password']==$_GET['oldpass'])
								{
									mysql_query('UPDATE `player` SET `password`=\''.hash("sha512",hash("sha224",$_POST['password'])).'\' WHERE `id`='.addslashes($_GET['id'])) or die(mysql_error());
									echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Password changed</b></span><br />';
								}
								else
									echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Wrong key for password change</b></span><br />';
							}
							else
								echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Account not found</b></span><br />';
						}
						echo '<form name="input" method="post">
						Change password to: <script type="text/javascript"><!--
						document.write("<input name=\"new_password\" type=\"text\">");
						--></script>';
						echo '<input type="submit" value="Ok"></form>';
					}
					else
					{
						if(isset($_POST['login_or_email']))
						{
							$reply = mysql_query('SELECT * FROM `player_meta` WHERE `email`=\''.addslashes($_POST['login_or_email']).'\'') or die(mysql_error());
							if($data = mysql_fetch_array($reply))
							{
								if(send_change_password($data['id']))
									echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Email to change your password send, check your email</b></span><br />';
								else
									echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Missing data or meta data to send the email</b></span><br />';
							}
							else
							{
								$reply = mysql_query('SELECT * FROM `player` WHERE `login`=\''.addslashes($_POST['login_or_email']).'\'') or die(mysql_error());
								if($data = mysql_fetch_array($reply))
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
				<div id="copyright">CatchChallenger - <span style="color:#777;font-size:80%">Donate Bitcoin: </span><span style="color:#999;font-size:70%">1C4VLs16HX5YBoUeCLxEMJq8TpP24dcUJN</span> <span style="color:#777;font-size:80%">Nextcoin: </span><span style="color:#999;font-size:70%">15504326669229103049</span></div>
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