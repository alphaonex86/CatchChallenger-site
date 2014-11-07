<?php
$is_up=true;
require 'config.php';
$datapackexplorergeneratorinclude=true;
require 'official-server/datapack-explorer-generator/function.php';

if($postgres_host!='localhost')
    $postgres_link = @pg_connect('dbname='.$postgres_db.' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_host);
else
    $postgres_link = @pg_connect('dbname='.$postgres_db.' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link===FALSE)
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

function send_mail($title,$text,$to,$type,$from)
{
	$headers = 'From: '.$from."\r\n";
	$headers .= 'MIME-Version: 1.0'."\r\n";
	$headers .= 'Content-type: '.$type.'; charset=UTF-8'."\r\n";
	$return=@mail($to,'=?UTF-8?B?'.base64_encode($title).'?=',$text,$headers);
	return $return;
}

$reputation_meta=array();
if(file_exists('datapack/player/reputation.xml'))
{
	$content=file_get_contents('datapack/player/reputation.xml');
	preg_match_all('#<reputation type="[a-z]+".*</reputation>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		if(!preg_match('#<reputation type="[a-z]+".*</reputation>#isU',$entry))
			continue;
		$type=preg_replace('#^.*<reputation type="([a-z]+)".*</reputation>.*$#isU','$1',$entry);
		preg_match_all('#<level point="-?[0-9]+".*</level>#isU',$entry,$level_list);
		$reputation_meta_list=array();
		foreach($level_list[0] as $level)
		{
			if(!preg_match('#<level point="-?[0-9]+".*</level>#isU',$level))
				continue;
			$point=preg_replace('#^.*<level point="(-?[0-9]+)".*</level>.*$#isU','$1',$level);
			if(!preg_match('#<text( lang="en")?>.*</text>#isU',$level))
				continue;
			$text=preg_replace('#^.*<text( lang="en")?>(.*)</text>.*$#isU','$2',$level);
			$reputation_meta_list[(int)$point]=$text;
		}
		if(count($reputation_meta_list)>0)
		{
			ksort($reputation_meta_list);
			$level_offset=0;
			foreach($reputation_meta_list as $point=>$text)
			{
				if($point>=0)
					break;
				$level_offset++;
			}
			$reputation_meta_list_by_level=array();
			foreach($reputation_meta_list as $point=>$text)
			{
				$reputation_meta_list_by_level[-$level_offset]=$text;
				$level_offset--;
			}
			unset($reputation_meta_list);
			$reputation_meta[$type]=$reputation_meta_list_by_level;
			unset($reputation_meta_list_by_level);
		}
	}
}
$monster_meta=array();
if(file_exists('datapack/monsters/monster.xml'))
{
    $content=file_get_contents('datapack/monsters/monster.xml');
    preg_match_all('#<monster.*</monster>#isU',$content,$entry_list);
    foreach($entry_list[0] as $entry)
    {
        if(!preg_match('#id="[0-9]+".*</monster>#isU',$entry))
            continue;
        $id=preg_replace('#^.*id="([0-9]+)".*</monster>.*$#isU','$1',$entry);
        if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
            continue;
        $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
        if(preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
            $description=preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry);
        else
            $description='';
        $attack_list=array();
        preg_match_all('#<attack[^>]+/>#isU',$entry,$attack_text_list);
        foreach($attack_text_list[0] as $attack_text)
        {
            if(!preg_match('#<attack[^>]*id="[0-9]+"[^>]*>#isU',$attack_text))
                continue;
            $skill_id=preg_replace('#^.*<attack[^>]*id="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
            if(!preg_match('#<attack[^>]*level="[0-9]+"[^>]*>#isU',$attack_text))
                continue;
            $level=preg_replace('#^.*<attack[^>]*level="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
            if(preg_match('#<attack[^>]*attack_level="[0-9]+"[^>]*>#isU',$attack_text))
                $attack_level=preg_replace('#^.*<attack[^>]*attack_level="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
            else
                $attack_level='1';
            if(!isset($attack_list[$level]))
                $attack_list[$level]=array();
            $attack_list[$level][]=array('id'=>$skill_id,'attack_level'=>$attack_level);
        }
        krsort($attack_list);
        $monster_meta[$id]=array('name'=>$name,'description'=>$description,'attack_list'=>$attack_list);
    }
}

$item_meta=array();
$temp_items=getXmlList('datapack/items/');
foreach($temp_items as $item_file)
{
    $content=file_get_contents('datapack/items/'.$item_file);
    preg_match_all('#<item[^>]*>.*</item>#isU',$content,$entry_list);
    foreach($entry_list[0] as $entry)
    {
        if(!preg_match('#<item[^>]*id="[0-9]+".*</item>#isU',$entry))
            continue;
        $id=preg_replace('#^.*<item[^>]*id="([0-9]+)".*</item>.*$#isU','$1',$entry);
        $price=0;
        if(preg_match('#<item[^>]*price="[0-9]+".*</item>#isU',$entry))
            $price=preg_replace('#^.*<item[^>]*price="([0-9]+)".*</item>.*$#isU','$1',$entry);
        if(preg_match('#<item[^>]*image="[^"]+".*</item>#isU',$entry))
            $image=preg_replace('#^.*<item[^>]*image="([^"]+)".*</item>.*$#isU','$1',$entry);
        else
            $image=$id.'.png';
        $image=preg_replace('#[^/]+$#isU','',$item_file).$image;
        if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
            continue;
        $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
        if(preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
            $description=preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry);
        else
            $description='';
        $item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description);
    }
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
				$content=file_get_contents('datapack/player/start.xml');
				preg_match_all('#<start>.*</start>#isU',$content,$entry_list);
				$start=array();
				foreach($entry_list[0] as $entry)
				{
					if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
						continue;
					$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
					if(!preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
						continue;
					$description=preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry);
					if(!preg_match('#<map.*file="([^"]+)".*/>#isU',$entry))
						continue;
					if(!preg_match('#<map.*x="([0-9]+)".*/>#isU',$entry))
						continue;
					if(!preg_match('#<map.*y="([0-9]+)".*/>#isU',$entry))
						continue;
					$map=preg_replace('#^.*<map.*file="([^"]+)".*/>.*$#isU','$1',$entry);
					$x=preg_replace('#^.*<map.*x="([0-9]+)".*/>.*$#isU','$1',$entry);
					$y=preg_replace('#^.*<map.*y="([0-9]+)".*/>.*$#isU','$1',$entry);
					$forcedskin=array();
					if(preg_match('#<forcedskin.*value="([^"]+)".*/>#isU',$entry))
						$forcedskin=explode(';',preg_replace('#^.*<forcedskin.*value="([^"]+)".*/>.*$#isU','$1',$entry));
					$cash=0;
					if(preg_match('#<cash.*value="([^"]+)".*/>#isU',$entry))
						$cash=preg_replace('#^.*<cash.*value="([^"]+)".*/>.*$#isU','$1',$entry);
					
					preg_match_all('#<monster id="[0-9]+" level="[0-9]+" captured_with="[0-9]+" />#isU',$entry,$monster_list);
					$monsters=array();
					foreach($monster_list as $monster)
					{
						if(!preg_match('#<monster.*id="([0-9]+)".*/>#isU',$entry))
							continue;
						if(!preg_match('#<monster.*level="([0-9]+)".*/>#isU',$entry))
							continue;
						if(!preg_match('#<monster.*captured_with="([0-9]+)".*/>#isU',$entry))
							continue;
						$id=preg_replace('#^.*<monster.*id="([0-9]+)".*/>.*$#isU','$1',$entry);
						$level=preg_replace('#^.*<monster.*level="([0-9]+)".*/>.*$#isU','$1',$entry);
						$captured_with=preg_replace('#^.*<monster.*captured_with="([0-9]+)".*/>.*$#isU','$1',$entry);
						$skill_added=0;
						$attack_list=array();
						if(isset($monster_meta[$id]['attack_list']))
							foreach($monster_meta[$id]['attack_list'] as $learn_at_level=>$skill_list)
							{
								foreach($skill_list as $skill)
								{
									if($learn_at_level<=$level)
									{
										$attack_list[]=$skill;
										$skill_added++;
									}
									if(count($attack_list)>=4)
										break;
								}
								if(count($attack_list)>=4)
									break;
							}
						$monsters[]=array('id'=>$id,'level'=>$level,'captured_with'=>$captured_with,'attack_list'=>$attack_list);
					}
					if(count($monsters)<=0)
						continue;

					preg_match_all('#<reputation type="[a-z]+" level="[0-9]+" />#isU',$entry,$reputation_list);
					$reputations=array();
					foreach($reputation_list as $reputation)
					{
						if(!preg_match('#<reputation.*type="([a-z]+)".*/>#isU',$entry))
							continue;
						if(!preg_match('#<reputation.*level="([0-9]+)".*/>#isU',$entry))
							continue;
						$type=preg_replace('#^.*<reputation.*type="([a-z]+)".*/>.*$#isU','$1',$entry);
						$level=preg_replace('#^.*<reputation.*level="([0-9]+)".*/>.*$#isU','$1',$entry);
						$reputations[]=array('type'=>$type,'level'=>$level);
					}

					preg_match_all('#<item id="[0-9]+" quantity="[0-9]+" />#isU',$entry,$item_list);
					$items=array();
					foreach($item_list as $item)
					{
						if(!preg_match('#<item.*id="([0-9]+)".*/>#isU',$entry))
							continue;
						if(!preg_match('#<item.*quantity="([0-9]+)".*/>#isU',$entry))
							continue;
						$id=preg_replace('#^.*<item.*id="([^"]+)".*/>.*$#isU','$1',$entry);
						$quantity=preg_replace('#^.*<item.*quantity="([0-9]+)".*/>.*$#isU','$1',$entry);
						$items[]=array('id'=>$id,'quantity'=>$quantity);
					}
					
					if(!preg_match('#\.tmx$#',$map))
						$map=$map.'.tmx';
					$start[]=array('name'=>$name,'description'=>$description,'map'=>$map,'x'=>$x,'y'=>$y,'forcedskin'=>$forcedskin,'cash'=>$cash,'monsters'=>$monsters,'reputations'=>$reputations,'items'=>$items);
				}
				if(count($start)<=0 || !$is_up)
					echo 'The registration is actually <span style="color:red;"><b>closed</b></span>.<br />';
				else
				{
					if(isset($_POST['login']) && isset($_POST['password']) && isset($_POST['email']))
					{
						if($_POST['login']=='')
							echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your login can\'t be empty</b></span><br />';
						else if(!preg_match('#^[a-zA-Z0-9]{6,}$#',$_POST['password']))
							echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your password need be composed of upper and lower char and number. And need be more than 6 of lenght</b></span><br />';
						else if(!preg_match('#^[a-z0-9\.\-_]+@[a-z0-9\.\-_]+\.[a-z]{2,4}$#',$_POST['email']))
							echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Your email seam wrong</b></span><br />';
						else
						{
							pg_query('DELETE FROM account_register WHERE date < '.(time()-24*3600).';') or die(pg_last_error());
							$login_hash=hash("sha224",hash("sha224",$_POST['login'].'RtR3bm9Z1DFMfAC3',true));
							$reply = pg_query('SELECT * FROM account WHERE login=\''.$login_hash.'\'') or die(pg_last_error());
							if($data = pg_fetch_array($reply))
								echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;"><b>Login already taken</b></span><br />';
							else
							{
								$reply = pg_query('SELECT * FROM account_register WHERE login=\''.$login_hash.'\'') or die(pg_last_error());
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

										if (!$mail->send())
											echo '<span style="background-color:rgb(255,169,169);border:1px solid rgb(255,77,77);padding:2px;;"><b>Mailer error: '.$mail->ErrorInfo.', contact the admin at '.$admin_email.'</b></span><br />';
										else
										{
											$postgres_return=pg_query('INSERT INTO account_register(login,password,email,key,date) VALUES(\''.$login_hash.'\',\''.hash("sha224",$_POST['password'].'AwjDvPIzfJPTTgHs').'\',\''.addslashes($_POST['email']).'\',\''.addslashes($key).'\','.time().');') or die(pg_last_error());
											echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Registred, check your email</b></span><br />';
										}
									}
									else
									{
										send_mail($_POST['login'].' enable your account into '.$_SERVER['HTTP_HOST'],'Hello '.$_POST['login'].', to enable your account into http://'.$_SERVER['HTTP_HOST'].', click here: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?key='.$key.'&email='.$_POST['email'],$_POST['email'],'text/plain',$admin_email);
										$postgres_return=pg_query('INSERT INTO account_register(login,password,email,key,date) VALUES(\''.$login_hash.'\',\''.hash("sha224",$_POST['password'].'AwjDvPIzfJPTTgHs').'\',\''.addslashes($_POST['email']).'\',\''.addslashes($key).'\','.time().');') or die(pg_last_error());
										echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Registred, check your email</b></span><br />';
									}
								}
							}
						}
					}
					else if(isset($_GET['key']) && isset($_GET['email']))
					{
						$reply = pg_query('SELECT * FROM account_register WHERE email=\''.addslashes($_GET['email']).'\'') or die(pg_last_error());
						if($data = pg_fetch_array($reply))
						{
							if($data['key']==$_GET['key'])
							{
                                $reply_max_id = pg_query('SELECT * FROM account ORDER BY id DESC LIMIT 1') or die(pg_last_error());
                                if($data_max_id = pg_fetch_array($reply_max_id))
                                    $max_id=$data_max_id['id']+1;
                                else
                                    $max_id=1;
                                pg_query('DELETE FROM account_register WHERE login=\''.$data['login'].'\'') or die(pg_last_error());
                                pg_query('INSERT INTO account(id,login,password,date,email) VALUES('.$max_id.',\''.addslashes($data['login']).'\',\''.addslashes($data['password']).'\','.$data['date'].',\''.addslashes($data['email']).'\');') or die(pg_last_error());
                                echo '<span style="background-color:#FFCC83;border:1px solid #FF8000;padding:2px;"><b>Registred, thanks for your validation</b></span><br /><script type="text/JavaScript">'."\n";
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
?>
				<img src="/images/hr.png" width="632" height="19" class="separation" />
				<div id="title">Starter characters</div>
<?php
					$index=1;
					$loadSkinPreview=array();
					foreach($start as $entry)
					{
						echo '
						<fieldset>
						<legend><h2><strong>'.htmlspecialchars($entry['name']).'</strong></h2></legend>
						<b>'.htmlspecialchars($entry['description']).'</b><br />';
						$map_name='';
						$zone_code='';
						$map_meta='datapack/map/'.str_replace('.tmx','.xml',$entry['map']);
						if(file_exists($map_meta))
						{
							$content=file_get_contents($map_meta);
							if(preg_match('#<name( lang="en")?>.*</name>#isU',$content))
								$map_name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
							else if(preg_match('#<map[^>]*zone="[^"]+"#isU',$content))
							{
								$zone_code=preg_replace('#<map[^>]*zone="([^"]+)"#isU','$1',$content);
								$zone_meta='datapack/map/zone/'.$zone_code.'.xml';
								if(file_exists($zone_meta))
								{
									$content=file_get_contents($zone_meta);
									if(preg_match('#<name( lang="en")?>.*</name>#isU',$content))
										$map_name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
								}
							}
						}
						if($map_name!='')
							echo 'Map: <i>'.htmlspecialchars($map_name).'</i><br />';
						$skin_count=0;
						if ($handle = opendir('datapack/skin/fighter/')) {
							while (false !== ($inode = readdir($handle)))
							{
								if(file_exists('datapack/skin/fighter/'.$inode.'/front.png') || file_exists('datapack/skin/fighter/'.$inode.'/front.gif'))
									if(count($entry['forcedskin'])==0 || in_array($inode,$entry['forcedskin']))
										$skin_count++;
							}
							closedir($handle);
						}
						if($skin_count>0)
						{
							echo 'Skin: <div id="skin_preview_'.$index.'">';
							if ($handle = opendir('datapack/skin/fighter/')) {
								while (false !== ($inode = readdir($handle)))
								{
									if(file_exists('datapack/skin/fighter/'.$inode.'/front.png') || file_exists('datapack/skin/fighter/'.$inode.'/front.gif'))
										if(count($entry['forcedskin'])==0 || in_array($inode,$entry['forcedskin']))
										{
											if(file_exists('datapack/skin/fighter/'.$inode.'/front.png'))
												echo '<img src="datapack/skin/fighter/'.$inode.'/front.png" width="80" height="80" alt="Front" style="float:left" />';
											else
												echo '<img src="datapack/skin/fighter/'.$inode.'/front.gif" width="80" height="80" alt="Front" style="float:left" />';
										}
								}
								closedir($handle);
							}
							echo '</div><br style="clear:both" />';
						}
						else
							echo 'Skin: No skin found<br />';
						if($entry['cash']>0)
							echo 'Cash: <i>'.htmlspecialchars($entry['cash']).'$</i> <small>(game coin)</small><br />';
						echo 'Monster: <ul style="margin:0px;">';
						foreach($entry['monsters'] as $monster)
							if(array_key_exists($monster['id'],$monster_meta))
							{
								echo '<li>';
								echo '<a href="/official-server/datapack-explorer/monsters/'.str_replace(' ','-',strtolower($monster_meta[$monster['id']]['name'])).'.html">';
								if(file_exists('datapack/monsters/'.$monster['id'].'/front.png'))
									echo '<img src="datapack/monsters/'.$monster['id'].'/front.png" width="80" height="80" alt="'.htmlspecialchars($monster_meta[$monster['id']]['name']).'" title="'.htmlspecialchars($monster_meta[$monster['id']]['description']).'" /><br />';
								elseif(file_exists('datapack/monsters/'.$monster['id'].'/front.gif'))
									echo '<img src="datapack/monsters/'.$monster['id'].'/front.gif" width="80" height="80" alt="'.htmlspecialchars($monster_meta[$monster['id']]['name']).'" title="'.htmlspecialchars($monster_meta[$monster['id']]['description']).'" /><br />';
								else
									echo 'No skin found!';
								echo '<b>'.htmlspecialchars($monster_meta[$monster['id']]['name']).'</b> level <i>'.htmlspecialchars($monster['level']).'</i>';
								echo '</a>';
								echo '</li>';
							}
							else
								echo '<li>No monster information!</li>';
						echo '</ul>';
						if(count($entry['reputations'])>0)
						{
							echo 'Reputations: <ul style="margin:0px;">';
							foreach($entry['reputations'] as $reputation)
							{
								if(array_key_exists($reputation['type'],$reputation_meta))
								{
									if(array_key_exists($reputation['level'],$reputation_meta[$reputation['type']]))
										echo '<li>'.htmlspecialchars($reputation_meta[$reputation['type']][$reputation['level']]).'</li>';
									else
										echo '<li>Unknown reputation '.htmlspecialchars($reputation['type']).' level: '.htmlspecialchars($reputation['level']).'</li>';
								}
								else
									echo '<li>Unknown reputation type: '.htmlspecialchars($reputation['type']).'</li>';
							}
							echo '</ul>';
						}
						if(count($entry['items'])>0)
						{
							echo 'Items: <ul style="margin:0px;">';
							foreach($entry['items'] as $item)
							{
								if($item['quantity']<=1)
									$quantity='';
								else
									$quantity=htmlspecialchars($item['quantity']).' ';
								if(array_key_exists($item['id'],$item_meta))
								{
									echo '<li>';
									echo '<a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$item['id']]['name'])).'.html" title="'.$item_meta[$item['id']]['name'].'">';
									if($item_meta[$item['id']]['image']!='' && file_exists('datapack/items/'.$item_meta[$item['id']]['image']))
										echo '<img src="datapack/items/'.htmlspecialchars($item_meta[$item['id']]['image']).'" width="24" height="24" alt="'.htmlspecialchars($item_meta[$item['id']]['description']).'" title="'.htmlspecialchars($item_meta[$item['id']]['description']).'" />'.$quantity.htmlspecialchars($item_meta[$item['id']]['name']);
									else
										echo $quantity.htmlspecialchars($item_meta[$item['id']]['name']);
									echo '</a>';
									echo '</li>';
								}
								else
									echo '<li>'.$quantity.'unknown item ('.htmlspecialchars($item['id']).')</li>';
							}
							echo '</ul>';
						}
						echo '</fieldset>';
						$index++;
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