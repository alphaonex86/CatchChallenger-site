<?php
$is_up=true;
require '../config.php';
$mysql_link=@mysql_connect($mysql_host,$mysql_login,$mysql_pass,true);
if($mysql_link===NULL)
	$is_up=false;
else if(!@mysql_select_db($mysql_db))
	$is_up=false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Captured city on CatchChallenger</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF8"/>
		<meta name="robots" content="index,follow"/>
		<meta name="description" content="Captured city on CatchChallenger list" />
		<meta name="keywords" content="clan,catchchallenger,pokemon,minecraft,crafting,official server" />
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
				<div id="back_menu">
					<table>
					<tr>
						<td><a href="/official-server.html">General</a></td>
						<td><a href="/official-server/clan.html">Clan</a></td>
						<td><a href="/official-server/captured-city.html">Captured city</a></td>
						<td><a href="/official-server/market.html">Market</a></td>
						<td><a href="/official-server/player.html">Player</a></td>
						<td><a href="/official-server/map-resources.html">Map resources</a></td>
						<td><a href="/official-server/datapack-explorer.html">Datapack explorer</a></td>
					</tr>
					</table>
				</div>
			</div>
			<div id="body">
				<div id="title">Captured city on CatchChallenger</div>
				<br />
				<br />
				<img src="/images/pixel.png" width="96" height="96" style="float:left; margin-right:7px;" class="tiers_img" alt="" />
				<p class="text">The city captured:<br />
				<?php
				if(!$is_up)
					echo 'The city captured is <span style="color:red;"><b>closed</b></span>.<br />';
				else
				{
					echo '<table>';
					echo '<tr class="tiers_img">';
					echo '<td></td>';
					echo '<td>City</td>';
					echo '<td>Clan</td>';
					echo '</tr>';
					$reply = mysql_query('SELECT * FROM  `city` LIMIT 0,30') or die(mysql_error());
					while($data = mysql_fetch_array($reply))
					{
						echo '<tr>';
						$reply_clan = mysql_query('SELECT `name` FROM `clan` WHERE `id`='.$data['clan']) or die(mysql_error());
						if($data_clan = mysql_fetch_array($reply_clan))
							echo '<td><img src="/official-server/images/flag.png" width="16" height="16" alt="" /></td>';
						else
							echo '<td></td>';
						$zone_text=$data['city'];
						$zone_meta='../datapack/map/zone/'.$data['city'].'.xml';
						if(file_exists($zone_meta))
						{
							$content=file_get_contents($zone_meta);
							if(preg_match('#<name( lang="en")?>.*</name>#isU',$content))
								$zone_text=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
						}
						echo '<td><strong>'.htmlspecialchars($zone_text).'</strong></td>';
						$reply_clan = mysql_query('SELECT `name` FROM `clan` WHERE `id`='.$data['clan']) or die(mysql_error());
						if($data_clan = mysql_fetch_array($reply_clan))
							echo '<td>'.htmlspecialchars($data_clan['name']).'</td>';
						else
							echo '<td></td>';
						echo '</tr>';
					}
					echo '</table>';
				}
				?>
				</p>
			</div>
			<br />
			<div id="footer">
				<div id="copyright">CatchChallenger - <span style="color:#777;font-size:80%">Donate Bitcoin: </span><span style="color:#999;font-size:70%">1C4VLs16HX5YBoUeCLxEMJq8TpP24dcUJN</span></div>
			</div>
		</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/register.js"></script>
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://stat.first-world.info/" : "http://stat.first-world.info/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 22);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script>
	</body>
</html>