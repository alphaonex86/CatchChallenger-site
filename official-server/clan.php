<?php
$is_up=true;
require '../config.php';
if($postgres_db_login['host']!='localhost')
    $postgres_link_login = @pg_connect('dbname='.$postgres_db_login['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_db_login['host']);
else
    $postgres_link_login = @pg_connect('dbname='.$postgres_db_login['database'].' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link_login===FALSE)
    $is_up=false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Clan on CatchChallenger</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF8"/>
		<meta name="robots" content="index,follow"/>
		<meta name="description" content="Clan on CatchChallenger list" />
		<meta name="keywords" content="clan,catchchallenger,pokemon,minecraft,crafting,official server" />
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
				<div id="title">Clan on CatchChallenger</div>
				<br />
				<br />
				<img src="/images/pixel.png" width="96" height="96" style="float:left; margin-right:7px;" class="tiers_img" alt="" />
				<p class="text">The clan list:<br />
				<?php
				if(!$is_up)
					echo 'The clan list is <span style="color:red;"><b>closed</b></span>.<br />';
				else
				{
					echo '<img src="/official-server/images/top-1.png" alt="Top 1" title="Top 1" width="16" height="16" style="float:left" /> Top 1<br />';
					echo '<img src="/official-server/images/top-2.png" alt="Top 2" title="Top 2" width="16" height="16" style="float:left" /> Top 2<br />';
					echo '<img src="/official-server/images/top-3.png" alt="Top 3" title="Top 3" width="16" height="16" style="float:left" /> Top 3<br />';
					echo '<table>';
					echo '<tr class="tiers_img">';
					echo '<td>Name</td>';
					echo '<td>Cash</td>';
					echo '<td>Date</td>';
					echo '<td>Member</td>';
					echo '<td>Leader</td>';
					echo '<td>City</td>';
					echo '</tr>';
                    $skin_list=array();
					$index=1;
					$reply = pg_query('SELECT * FROM  clan ORDER BY name LIMIT 30') or die(pg_last_error());
					while($data = pg_fetch_array($reply))
					{
						echo '<tr>';
						echo '<td>';
						if($index<=3)
							echo '<img src="/official-server/images/top-'.$index.'.png" alt="Top '.$index.'" title="Top '.$index.'" width="16" height="16" style="float:left" />';
						echo '<strong>'.htmlspecialchars($data['name']).'</strong></td>';
						echo '<td>'.htmlspecialchars($data['cash']).'$</td>';
						echo '<td>'.date('jS \of F Y',$data['date']).'</td>';
						$reply_clan_count = pg_query('SELECT COUNT(id) FROM character WHERE clan='.$data['id']) or die(pg_last_error());
						if($data_clan_count = pg_fetch_array($reply_clan_count))
							echo '<td><b>'.$data_clan_count['count'].'</b></td>';
						else
							echo '<td></td>';
						echo '<td>';
						$clan_players=array();
						$reply_clan_players = pg_query('SELECT pseudo,skin,clan_leader FROM character WHERE clan='.$data['id'].' AND clan_leader=true ORDER BY id') or die(pg_last_error());
						while($data_clan_players = pg_fetch_array($reply_clan_players))
                        {
                            if(isset($skin_list[$data_clan_players['skin']]))
                                $skin=$skin_list[$data_clan_players['skin']];
                            else
                            {
                                $reply_skin = pg_query('SELECT skin FROM dictionary_skin WHERE id='.$data_clan_players['skin']) or die(pg_last_error());
                                if($data_skin = pg_fetch_array($reply_skin))
                                    $skin=$data_skin['skin'];
                                else
                                    $skin='default';
                                $skin_list[$data_clan_players['skin']]=$skin;
                            }
                            if(file_exists('../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.png'))
                                $clan_players[]='<div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div>'.htmlspecialchars($data_clan_players['pseudo']);
                            elseif(file_exists('../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.gif'))
                                $clan_players[]='<div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div>'.htmlspecialchars($data_clan_players['pseudo']);
                            else
                                $clan_players[]=htmlspecialchars($data_clan_players['pseudo']);
                        }
						echo implode(', ',$clan_players);
						echo '</td>';
						echo '<td>';
						$city=array();
						$city_text=array();
						$reply_clan_city = pg_query('SELECT * FROM city WHERE clan='.$data['id'].' ORDER BY city') or die(pg_last_error());
						while($data_clan_city = pg_fetch_array($reply_clan_city))
							$city[]=$data_clan_city['city'];
						foreach($city as $entry)
						{
							$zone_text=$entry;
							$zone_meta='../datapack/map/zone/'.$entry.'.xml';
							if(file_exists($zone_meta))
							{
								$content=file_get_contents($zone_meta);
								if(preg_match('#<name( lang="en")?>.*</name>#isU',$content))
									$zone_text=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
							}
							$city_text[]=$zone_text;
						}
						echo htmlspecialchars(implode(', ',$city_text));
						echo '</td>';
						echo '</tr>';
						$index++;
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
<script type="text/javascript">
var _paq=_paq || [];_paq.push(["trackPageView"]);_paq.push(["enableLinkTracking"]);
(function() {
var u=(("https:"==document.location.protocol)?"https":"http")+"://stat.first-world.info/";_paq.push(["setTrackerUrl",u+"piwik.php"]);_paq.push(["setSiteId","22"]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.type="text/javascript";g.defer=true;g.async=true;g.src=u+"piwik.js";s.parentNode.insertBefore(g,s);
})();
</script>
	</body>
</html>