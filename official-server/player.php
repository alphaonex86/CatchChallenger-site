<?php
$is_up=true;
require '../config.php';
if($postgres_host!='localhost')
    $postgres_link = @pg_connect('dbname='.$postgres_db.' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_host);
else
    $postgres_link = @pg_connect('dbname='.$postgres_db.' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link===FALSE)
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
                    $skin_list=array();
					$index=1;
					$reply = pg_query('SELECT * FROM character LIMIT 30') or die(pg_last_error());
					while($data = pg_fetch_array($reply))
					{
						echo '<tr><td>';
						if($index<=3)
							echo '<img src="/official-server/images/top-'.$index.'.png" alt="Top '.$index.'" title="Top '.$index.'" width="16" height="16" style="float:left" />';
                        if(isset($skin_list[$data['skin']]))
                            $skin=$skin_list[$data['skin']];
                        else
                        {
                            $reply_skin = pg_query('SELECT skin FROM dictionary_skin WHERE id='.$data['skin']) or die(pg_last_error());
                            if($data_skin = pg_fetch_array($reply_skin))
                                $skin=$data_skin['skin'];
                            else
                                $skin='default';
                            $skin_list[$data['skin']]=$skin;
                        }
						if(file_exists('../datapack/skin/fighter/'.$skin.'/trainer.png'))
							echo '<div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div>';
						elseif(file_exists('../datapack/skin/fighter/'.$skin.'/trainer.gif'))
							echo '<div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div>';
						echo '</td><td>'.htmlspecialchars($data['pseudo']).'</td>';
						echo '<td>'.date('jS \of F Y',$data['date']).'</td>';
						$reply_clan = pg_query('SELECT name FROM clan WHERE id='.$data['clan']) or die(pg_last_error());
						if($data_clan = pg_fetch_array($reply_clan))
						{
							if($data['clan_leader']==true)
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
<script type="text/javascript">
var _paq=_paq || [];_paq.push(["trackPageView"]);_paq.push(["enableLinkTracking"]);
(function() {
var u=(("https:"==document.location.protocol)?"https":"http")+"://stat.first-world.info/";_paq.push(["setTrackerUrl",u+"piwik.php"]);_paq.push(["setSiteId","22"]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.type="text/javascript";g.defer=true;g.async=true;g.src=u+"piwik.js";s.parentNode.insertBefore(g,s);
})();
</script>
	</body>
</html>