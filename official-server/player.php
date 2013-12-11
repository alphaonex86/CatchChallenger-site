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
		<title>Player on Catchchallenger</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF8"/>
		<meta name="robots" content="index,follow"/>
		<meta name="description" content="Player on Catchchallenger list" />
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
				<div id="title">Player on Catchchallenger</div>
				<br />
				<br />
				<img src="/images/pixel.png" width="96" height="96" style="float:left; margin-right:7px;" class="tiers_img" alt="" />
				<?php
				if(!$is_up)
					echo '<p class="text">The player list is actually <span style="color:red;"><b>closed</b></span>.<br /></p>';
				else
				{
					echo '<p class="text">The player list:<br />';
					echo '<img src="/official-server/images/top-1.png" alt="Top 1" title="Top 1" width="16" height="16" style="float:left" /> Top 1<br />';
					echo '<img src="/official-server/images/top-2.png" alt="Top 2" title="Top 2" width="16" height="16" style="float:left" /> Top 2<br />';
					echo '<img src="/official-server/images/top-3.png" alt="Top 3" title="Top 3" width="16" height="16" style="float:left" /> Top 3<br />';
					echo '<img src="/official-server/images/leader.png" alt="" width="16" height="16" alt="Clan leader" title="Clan leader" style="float:left" /> Clan leader<br />';
					echo '<table>';
					echo '<tr class="tiers_img">';
					echo '<td></td>';
					echo '<td>Player</td>';
					echo '<td>Date</td>';
					echo '<td></td>';
					echo '<td>Clan</td>';
					echo '</tr>';
					$index=1;
					$reply = mysql_query('SELECT * FROM  `character` LIMIT 0,30') or die(mysql_error());
					while($data = mysql_fetch_array($reply))
					{
						echo '<tr><td>';
						if($index<=3)
							echo '<img src="/official-server/images/top-'.$index.'.png" alt="Top '.$index.'" title="Top '.$index.'" width="16" height="16" style="float:left" />';
						if(file_exists('../datapack/skin/fighter/'.$data['skin'].'/trainer.png'))
							echo '<div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($data['skin']).'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div>';
						elseif(file_exists('../datapack/skin/fighter/'.$data['skin'].'/trainer.gif'))
							echo '<div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($data['skin']).'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div>';
						echo '</td><td>'.htmlspecialchars($data['pseudo']).'</td>';
						echo '<td>'.date('jS \of F Y',$data['date']).'</td>';
						$reply_clan = mysql_query('SELECT `name` FROM `clan` WHERE `id`='.$data['clan']) or die(mysql_error());
						if($data_clan = mysql_fetch_array($reply_clan))
						{
							if($data['clan_leader']==1)
								echo '<td><img src="/official-server/images/leader.png" alt="" width="16" height="16" alt="Clan leader" title="Clan leader" /></td><td>'.htmlspecialchars($data_clan['name']).'</td>';
							else
								echo '<td></td><td>'.htmlspecialchars($data_clan['name']).'</td>';
						}
						else
							echo '<td></td><td></td>';
						echo '</tr>';
						$index++;
					}
					echo '</table></p>';
				}
				?>
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