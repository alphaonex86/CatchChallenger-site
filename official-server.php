<?php
$is_up=true;
require 'config.php';
if($postgres_db_site['host']!='localhost')
    $postgres_link_site = pg_connect('dbname='.$postgres_db_site['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_db_site['host']);
else
    $postgres_link_site = pg_connect('dbname='.$postgres_db_site['database'].' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link_site===FALSE)
    $is_up=false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>CatchChallenger official server</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF8"/>
		<meta name="robots" content="index,follow"/>
		<meta name="description" content="CatchChallenger official server" />
		<meta name="keywords" content="catchchallenger,pokemon,minecraft,crafting,official server" />
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css" />
        <meta name="viewport" content="width=device-width" />
        <meta name="Language" content="en" />
        <meta http-equiv="content-language" content="english" />
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
				<div id="title">CatchChallenger official server</div>
				<br />
				<br />
				<img src="/images/pixel.png" width="96" height="96" style="float:left; margin-right:7px;" class="tiers_img" alt="" />
				<ul><?php
                $server_count=0;
                $player_count=0;
                $file='testserver.json';
                if(file_exists($file) && $filecurs=file_get_contents($file))
                {
                    $arr=json_decode($filecurs,true);
                    if(is_array($arr))
                    {
                        ksort($arr);
                        if($is_up)
                        {
                            $db_server_found=array();
                            $reply = pg_query($postgres_link_site,'SELECT uniquekey,name,description FROM gameservers ORDER BY uniquekey') or die(pg_last_error());
                            while($data = pg_fetch_array($reply))
                            {
                                $db_server_found[]=$data['uniquekey'];
                                if(!isset($arr[$data['uniquekey']]))//not found
                                    echo '<li><div class="divBackground" title="'.htmlspecialchars($data['description']).'"><div class="labelDatapackMap"></div><strong>'.htmlspecialchars($data['name']).'</strong> - <span style="color:red;">down</span></div></li>';
                                else
                                {
                                    $server_count++;
                                    $server=$arr[$data['uniquekey']];
                                    if(isset($server['xml']) && isset($server['connectedPlayer']) && isset($server['maxPlayer']))
                                    {
                                        $name='';
                                        if(preg_match('#<name( lang="en")?>.*</name>#isU',$server['xml']))
                                        {
                                            $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$server['xml']);
                                            $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
                                        }
                                        if($name=='')
                                            $name='Default server';
                                        $description='';
                                        if(preg_match('#<description( lang="en")?>.*</description>#isU',$server['xml']))
                                        {
                                            $description=preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$server['xml']);
                                            $description=str_replace('<![CDATA[','',str_replace(']]>','',$description));
                                        }
                                        if($server['maxPlayer']<65534 && $server['maxPlayer']>0 && $server['connectedPlayer']<=$server['maxPlayer'])
                                        {
                                            echo '<li><div class="divBackground" title="'.htmlentities($description).'"><div class="labelDatapackMap"></div><strong>'.htmlentities($name).'</strong> - '.$server['connectedPlayer'].'/'.$server['maxPlayer'].' players</div></li>';
                                            $player_count+=$server['connectedPlayer'];
                                        }
                                        else
                                            echo '<li><div class="divBackground" title="'.htmlentities($description).'"><div class="labelDatapackMap"></div><strong>'.htmlentities($name).'</strong> - <span style="color:green;">online</span></div></li>';
                                        if($data['name']!=$name || $data['description']!=$description)
                                            pg_query($postgres_link_site,'UPDATE gameservers SET name=\''.addslashes($name).'\',description=\''.addslashes($description).'\' WHERE uniquekey='.$data['uniquekey']) or die(pg_last_error());
                                    }
                                    else
                                        echo '<li><div class="divBackground" title="'.htmlentities($data['description']).'"><div class="labelDatapackMap"></div><strong>'.htmlentities($data['name']).'</strong></div></li>';
                                }
                            }
                            foreach($arr as $uniqueKey=>$server)
                            {
                                if(!in_array($uniqueKey,$db_server_found))
                                {
                                    $server_count++;
                                    if(isset($server['xml']) && isset($server['connectedPlayer']) && isset($server['maxPlayer']))
                                    {
                                        $name='';
                                        if(preg_match('#<name( lang="en")?>.*</name>#isU',$server['xml']))
                                        {
                                            $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$server['xml']);
                                            $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
                                        }
                                        if($name=='')
                                            $name='Default server';
                                        $description='';
                                        if(preg_match('#<description( lang="en")?>.*</description>#isU',$server['xml']))
                                        {
                                            $description=preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$server['xml']);
                                            $description=str_replace('<![CDATA[','',str_replace(']]>','',$description));
                                        }
                                        if($server['maxPlayer']<65534 && $server['maxPlayer']>0 && $server['connectedPlayer']<=$server['maxPlayer'])
                                        {
                                            echo '<li><div class="divBackground" title="'.htmlentities($description).'"><div class="labelDatapackMap"></div><strong>'.htmlentities($name).'</strong> - '.$server['connectedPlayer'].'/'.$server['maxPlayer'].' players</div></li>';
                                            $player_count+=$server['connectedPlayer'];
                                        }
                                        else
                                            echo '<li><div class="divBackground" title="'.htmlentities($description).'"><div class="labelDatapackMap"></div><strong>'.htmlentities($name).'</strong> - <span style="color:green;">online</span></div></li>';
                                        pg_query($postgres_link_site,'INSERT INTO gameservers(uniquekey,name,description) VALUES ('.addslashes($uniqueKey).',\''.addslashes($name).'\',\''.addslashes($description).'\');') or die(pg_last_error());
                                    }
                                    else
                                        echo '<li><div class="divBackground"><div class="labelDatapackMap"></div><strong>Default server</strong></div></li>';
                                }
                            }
                        }
                        else
                            foreach($arr as $uniqueKey=>$server)
                            {
                                if(isset($server['xml']) && isset($server['connectedPlayer']) && isset($server['maxPlayer']))
                                {
                                    $name='';
                                    if(preg_match('#<name( lang="en")?>.*</name>#isU',$server['xml']))
                                    {
                                        $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$server['xml']);
                                        $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
                                    }
                                    if($name=='')
                                        $name='Default server';
                                    $description='';
                                    if(preg_match('#<description( lang="en")?>.*</description>#isU',$server['xml']))
                                    {
                                        $description=preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$server['xml']);
                                        $description=str_replace('<![CDATA[','',str_replace(']]>','',$description));
                                    }
                                    if($server['maxPlayer']<65534 && $server['maxPlayer']>0 && $server['connectedPlayer']<=$server['maxPlayer'])
                                    {
                                        echo '<li><div class="divBackground" title="'.htmlentities($description).'"><div class="labelDatapackMap"></div><strong>'.htmlentities($name).'</strong> - '.$server['connectedPlayer'].'/'.$server['maxPlayer'].' players</div></li>';
                                        $player_count+=$server['connectedPlayer'];
                                    }
                                    else
                                        echo '<li><div class="divBackground" title="'.htmlentities($description).'"><div class="labelDatapackMap"></div><strong>'.htmlentities($name).'</strong> - <span style="color:green;">online</span></div></li>';
                                }
                                else
                                    echo '<li><div class="divBackground"><div class="labelDatapackMap"></div><strong>Default server</strong></div></li>';
                            }
                    }
                    else
                        echo '<li><p class="text">The official server list is actually in <b>Unknown state</b>.</p></li>';
                }
                else
                    echo '<li><p class="text">The official server list is actually in <b>Unknown state</b>.</p></li>';

                $loginserver_up=0;
                $loginserver_down=0;
                $file='loginserver.json';
                if(file_exists($file) && $filecurs=file_get_contents($file))
                {
                    $arr=json_decode($filecurs,true);
                    if(is_array($arr))
                    {
                        ksort($arr);
                        foreach($arr as $ip=>$server)
                        {
                            if(isset($server['state']))
                            {
                                if($server['state']=='up')
                                    $loginserver_up++;
                                else
                                    $loginserver_down++;
                            }
                            else
                                $loginserver_down++;
                        }
                        echo '<li><p class="text">Login server: ';
                        if($loginserver_up>0)
                            echo '<strong>'.$loginserver_up.'</strong> <span style="color:green;">online</span>';
                        if($loginserver_down>0 && $loginserver_up>0)
                            echo ', ';
                        if($loginserver_down>0)
                            echo '<strong>'.$loginserver_up.'</strong> <span style="color:red;">offline</span>';
                        echo '</p></li>';
                    }
                }
                ?>
                </ul>
                <p class="text">Total: <b><?php echo $server_count; ?></b> servers and <b><?php echo $player_count; ?></b> players.</p>
				<p class="text">Download the <a href="http://files.first-world.info/catchchallenger/1.0.0.0/catchchallenger-single-server-windows-x86-1.0.0.0-setup.exe">client for Windows</a> or the <a href="http://files.first-world.info/catchchallenger/1.0.0.0/catchchallenger-single-server-mac-os-x-1.0.0.0.dmg">client for Mac</a> to play on it</p>
                <?php
                $file='official-server/datapack-explorer/contentstat.json';
                if(file_exists($file) && $filecurs=file_get_contents($file))
                {
                    $arr=json_decode($filecurs,true);
                    if(is_array($arr))
                    {
                        if(isset($arr['map_count']))
                            echo '<div class="labelDatapack"><div class="labelDatapackMap"></div><strong>'.$arr['map_count'].' maps</strong></div>';
                        if(isset($arr['bot_count']))
                            echo '<div class="labelDatapack"><div class="labelDatapackBot"></div><strong>'.$arr['bot_count'].' bots</strong></div>';
                        if(isset($arr['monster_count']))
                            echo '<div class="labelDatapack"><div class="labelDatapackMonster"></div><strong>'.$arr['monster_count'].' monsters</strong></div>';
                        if(isset($arr['item_count']))
                            echo '<div class="labelDatapack"><div class="labelDatapackItem"></div><strong>'.$arr['item_count'].' items</strong></div>';
                    }
                }
                ?>
                <br style="clear:both;" />
				<!--<p class="text">The premium user have this advantage:
				<ul>
					<li>Have better rates</li>
					<li>Have more luck to get shiny monster</li>
					<li>During event have more luck to have unique object</li>
				</ul>
				To be premium user you need support the server developping by donation of 5€/month. You can buy too object into the game via you bitcoin account.
				</p>
				<p class="text">We provide <a href="hosting.html"><strong>hosting</strong></a> for your private server. We can work on custom part (features, optimisation, security, ...) out of the roadmap, contact us to know the price.</p>-->
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