<?php
$is_up=true;
require 'config.php';
$datapackexplorergeneratorinclude=true;
require 'official-server/datapack-explorer-generator/function.php';

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
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->Username = $smtp_login;
$mail->Password = $smtp_password;
$mail->setFrom($admin_email, 'CatchChallenger');
$mail->addReplyTo($admin_email, 'CatchChallenger');
$mail->isHTML(false);

$reply = pg_prepare($postgres_link_login,'SELECTaccount','SELECT * FROM account WHERE login=decode($1,\'hex\') OR email=$2') or die(pg_last_error());
$reply = pg_prepare($postgres_link_login,'SELECTaccount_register','SELECT * FROM account_register WHERE login=decode($1,\'hex\') OR email=$2') or die(pg_last_error());
$reply = pg_prepare($postgres_link_login,'INSERTaccount_register','INSERT INTO account_register(login,password,email,key,date) VALUES(decode($1,\'hex\'),decode($2,\'hex\'),$3,$4,$5);') or die(pg_last_error());
$reply = pg_prepare($postgres_link_login,'SELECTencode','SELECT encode(login,\'hex\') as login,encode(password,\'hex\') as password,date,email,key FROM account_register WHERE email=$1') or die(pg_last_error());
$reply = pg_prepare($postgres_link_login,'DELETEaccount_register','DELETE FROM account_register WHERE login=decode($1,\'hex\')') or die(pg_last_error());
$reply = pg_prepare($postgres_link_login,'INSERTaccount','INSERT INTO account(id,login,password,date,email) VALUES($1,decode($2,\'hex\'),decode($3,\'hex\'),$4,$5);') or die(pg_last_error());

function send_mail($title,$text,$to,$type,$from)
{
	$headers = 'From: '.$from."\r\n";
	$headers .= 'MIME-Version: 1.0'."\r\n";
	$headers .= 'Content-type: '.$type.'; charset=UTF-8'."\r\n";
	$return=@mail($to,'=?UTF-8?B?'.base64_encode($title).'?=',$text,$headers);
	return $return;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Register catchchallenger account</title>
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
				<div id="title">Register catchchallenger account</div>
				<br />
				<br />
				<img src="/images/pixel.png" width="96" height="96" style="float:left; margin-right:7px;" class="tiers_img" alt="" />
				<p class="text">
				<?php
				if(!$is_up)
					echo 'The registration is actually <span style="color:red;"><b>closed</b></span><br />';
				else
				{
					if(isset($_POST['login']) && isset($_POST['password']) && isset($_POST['email']))
					{
						if($_POST['login']=='')
							echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your login can\'t be empty</b></span><br />';
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
                            else if(!preg_match('#[a-z]#',$_POST['password']) || !preg_match('#[A-Z]#',$_POST['password']) || !preg_match('#[0-9]#',$_POST['password']) || strlen($_POST['password'])<6)
                                echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your password need be composed of upper and lower char and number. And need be more than 6 of lenght</b></span><br />';
                            else if(!preg_match('#^[a-z0-9\.\-_\+]+@[a-z0-9\.\-_\+]+\.[a-z]{2,4}$#',$_POST['email']))
                                echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your email seam wrong</b></span><br />';
                            else
                            {
                                $login_hash=hash("sha224",hash("sha224",$_POST['login'].'RtR3bm9Z1DFMfAC3',true));
                                $reply = pg_execute($postgres_link_login,'SELECTaccount',array($login_hash,$_POST['email'])) or die(pg_last_error());
                                if($data = pg_fetch_array($reply))
                                    echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Login already taken</b></span><br />';
                                else
                                {
                                    $reply = pg_execute($postgres_link_login,'SELECTaccount_register',array($login_hash,$_POST['email'])) or die(pg_last_error());
                                    if($data = pg_fetch_array($reply))
                                        echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Login already taken (into register)</b></span><br />';
                                    else
                                    {
                                        $key=rand(10000,99999);
                                        if($smtp_server!='')
                                        {
                                            $mail->addAddress($_POST['email'], $_POST['login']);
                                            $mail->Subject = $_POST['login'].' enable your account into '.$_SERVER['HTTP_HOST'];
                                            $mail->Body = 'Hello '.$_POST['login'].', to enable your account into http://'.$_SERVER['HTTP_HOST'].', click here: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?key='.$key.'&email='.$_POST['email'];

                                            //$mail->SMTPDebug = 2;
                                            if (!$mail->send())
                                                echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;;"><b>Mailer error: '.$mail->ErrorInfo.', contact the admin at '.$admin_email.'</b></span><br />';
                                            else
                                            {
                                                $postgres_return=pg_execute($postgres_link_login,'INSERTaccount_register',array($login_hash,hash("sha224",$_POST['password'].'AwjDvPIzfJPTTgHs'.$_POST['login']),$_POST['email'],$key,time())) or die(pg_last_error());
                                                echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Registered, check your email</b></span><br />';
                                            }
                                        }
                                        else
                                        {
                                            send_mail($_POST['login'].' enable your account into '.$_SERVER['HTTP_HOST'],'Hello '.$_POST['login'].', to enable your account into http://'.$_SERVER['HTTP_HOST'].', click here: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?key='.$key.'&email='.$_POST['email'],$_POST['email'],'text/plain',$admin_email);
                                            $postgres_return=pg_execute($postgres_link_login,'INSERTaccount_register',array($login_hash,hash("sha224",$_POST['password'].'AwjDvPIzfJPTTgHs'.$_POST['login']),$_POST['email'],$key,time())) or die(pg_last_error());
                                            echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Registered, check your email</b></span><br />';
                                        }
                                    }
                                }
                            }
                        }
					}
					else if(isset($_GET['key']) && isset($_GET['email']))
					{
						$reply = pg_execute($postgres_link_login,'SELECTencode',array($_GET['email'])) or die(pg_last_error());
						if($data = pg_fetch_array($reply))
						{
							if($data['key']==$_GET['key'])
							{
                                $reply_max_id = pg_query($postgres_link_login,'SELECT id FROM account ORDER BY id DESC LIMIT 1') or die(pg_last_error());
                                if($data_max_id = pg_fetch_array($reply_max_id))
                                    $max_id=$data_max_id['id']+1;
                                else
                                    $max_id=1;
                                pg_execute($postgres_link_login,'DELETEaccount_register',array($data['login'])) or die(pg_last_error());
                                pg_execute($postgres_link_login,'INSERTaccount',array($max_id,$data['login'],$data['password'],$data['date'],$data['email'])) or die(pg_last_error());
                                echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Registered, thanks for your validation</b></span><br /><script type="text/JavaScript">'."\n";
                                echo '<!--'."\n";
                                echo 'setTimeout("location.href = \'/\';",1500);'."\n";
                                echo '-->'."\n";
                                echo '</script>'."\n";
							}
							else
								echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Wrong key for the registration</b></span><br />';
						}
						else
							echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Registration not found, already validated?</b></span><br />';
					}
					echo 'The registration is actually <span style="color:green;"><b>open</b></span>.<br />';
					echo '<a href="lost-password.html"><span style="font-size:0.7em;">Lost password</span></a><br />';
					echo '<form name="input" method="post">
					Login: <script type="text/javascript"><!--
					document.write("<input name=\"login\" type=\"text\">");
					--></script><br />
					Password: <input name="password" type="password"><br />
					Email: <input name="email" type="text"> (needed for confirmation)<br />
					<input type="submit" value="Register"></form>';
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
<?php
if($is_up)
    pg_query($postgres_link_login,'DELETE FROM account_register WHERE date < '.(time()-24*3600).';') or die(pg_last_error());
?>