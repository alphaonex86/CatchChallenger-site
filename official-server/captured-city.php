<?php
$is_up=true;
require '../config.php';

$is_up=false;
foreach($postgres_db_tree as $common_server_content)
{
    $is_up=true;
    if($common_server_content['host']!='localhost')
        $postgres_link_common = pg_connect('dbname='.$common_server_content['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$common_server_content['host']);
    else
        $postgres_link_common = pg_connect('dbname='.$common_server_content['database'].' user='.$postgres_login.' password='.$postgres_pass);
    if($postgres_link_common===FALSE)
        $is_up=false;
    else
    {
        $is_up=false;
        foreach($common_server_content['servers'] as $server_content)
        {
            $is_up=true;
            if($server_content['host']!='localhost')
                $postgres_link_server = pg_connect('dbname='.$server_content['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$server_content['host']);
            else
                $postgres_link_server = pg_connect('dbname='.$server_content['database'].' user='.$postgres_login.' password='.$postgres_pass);
            if($postgres_link_server===FALSE)
                $is_up=false;
            break;
        }
    }
    break;
}
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
					$reply = pg_query($postgres_link_server,'SELECT * FROM  city ORDER BY city LIMIT 30') or die(pg_last_error());
					while($data = pg_fetch_array($reply))
					{
						echo '<tr>';
						$reply_clan = pg_query($postgres_link_common,'SELECT name FROM clan WHERE id='.$data['clan'].' ORDER BY id') or die(pg_last_error());
						if($data_clan = pg_fetch_array($reply_clan))
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
						$reply_clan = pg_query($postgres_link_common,'SELECT name FROM clan WHERE id='.$data['clan'].' ORDER BY name') or die(pg_last_error());
						if($data_clan = pg_fetch_array($reply_clan))
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
var _paq=_paq || [];_paq.push(["trackPageView"]);_paq.push(["enableLinkTracking"]);
(function() {
var u=(("https:"==document.location.protocol)?"https":"http")+"://stat.first-world.info/";_paq.push(["setTrackerUrl",u+"piwik.php"]);_paq.push(["setSiteId","22"]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.type="text/javascript";g.defer=true;g.async=true;g.src=u+"piwik.js";s.parentNode.insertBefore(g,s);
})();
</script>
	</body>
</html>