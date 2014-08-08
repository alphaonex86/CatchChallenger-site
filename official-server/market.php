<?php
$is_up=true;
require '../config.php';
$datapackexplorergeneratorinclude=true;
require 'datapack-explorer-generator/function.php';
if($postgres_host!='localhost')
    $postgres_link = @pg_connect('dbname='.$postgres_db.' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_host);
else
    $postgres_link = @pg_connect('dbname='.$postgres_db.' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link===FALSE)
    $is_up=false;

$skin_list=array();

$monster_meta=array();
if(file_exists('../datapack/monsters/monster.xml'))
{
	$content=file_get_contents('../datapack/monsters/monster.xml');
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
$temp_items=getXmlList($datapack_path.'items/');
foreach($temp_items as $item_file)
{
	$content=file_get_contents('../datapack/items/'.$item_file);
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
		<title>Market Catchchallenger</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF8"/>
		<meta name="robots" content="index,follow"/>
		<meta name="description" content="Market Catchchallenger list" />
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
				<div id="title">Market Catchchallenger</div>
				<br />
				<br />
				<img src="/images/pixel.png" width="96" height="96" style="float:left; margin-right:7px;" class="tiers_img" alt="" />
				<?php
				if(!$is_up)
					echo '<p class="text">The market is actually <span style="color:red;"><b>closed</b></span>.<br /></p>';
				else
				{
					echo '<p class="text">Item into the market:';
					echo '<table>';
					echo '<tr class="tiers_img">';
					echo '<td></td>';
					echo '<td>Item</td>';
					echo '<td>Quantity</td>';
					echo '<td></td>';
					echo '<td>Player</td>';
					echo '<td>Price</td>';
					echo '</tr>';
					$reply = pg_query('SELECT * FROM item_market LIMIT 30') or die(pg_last_error());
					while($data = pg_fetch_array($reply))
					{
						echo '<tr>';
						echo '<td>';
						if(array_key_exists($data['item'],$item_meta))
						{
							if($item_meta[$data['item']]['image']!='' && file_exists('../datapack/items/'.$item_meta[$data['item']]['image']))
								echo '<a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$data['item']]['name'])).'.html"><img src="../datapack/items/'.htmlspecialchars($item_meta[$data['item']]['image']).'" width="24" height="24" alt="'.htmlspecialchars($item_meta[$data['item']]['description']).'" title="'.htmlspecialchars($item_meta[$data['item']]['description']).'" style="float:left" /></a>';
						}
						echo '</td>';
						if(array_key_exists($data['item'],$item_meta))
                        {
                            echo '<td><a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$data['item']]['name'])).'.html">';
                            echo htmlspecialchars($item_meta[$data['item']]['name']);
                            echo '</a></td>';
                        }
						else
							echo '<td>Unknown item ('.htmlspecialchars($data['item']).')</td>';
						echo '<td>'.htmlspecialchars($data['quantity']).'</td>';
						$reply_clan_players = pg_query('SELECT pseudo,skin FROM character WHERE id='.$data['character']) or die(pg_last_error());
						if($data_clan_players = pg_fetch_array($reply_clan_players))
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
							if(file_exists('../datapack/skin/fighter/'.$skin.'/trainer.png'))
								echo '<td><div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div></td><td>'.htmlspecialchars($data_clan_players['pseudo']).'</td>';
							elseif(file_exists('../datapack/skin/fighter/'.$skin.'/trainer.gif'))
								echo '<td><div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div></td><td>'.htmlspecialchars($data_clan_players['pseudo']).'</td>';
							else
								echo '<td></td><td>'.htmlspecialchars($data_clan_players['pseudo']).'</td>';
						}
						else
							echo '<td></td><td></td>';
						if($data['market_price']==0)
							echo '<td>Free</td>';
						else
							echo '<td>'.htmlspecialchars($data['market_price']).'$</td>';
						echo '</tr>';
					}
					echo '</table></p>';
					echo '<p class="text">Monster into the market:';
					echo '<table>';
					echo '<tr class="tiers_img">';
					echo '<td></td>';
					echo '<td>Monster</td>';
					echo '<td>Level</td>';
					echo '<td></td>';
					echo '<td>Player</td>';
					echo '<td>Price</td>';
					echo '</tr>';
					$reply = pg_query('SELECT * FROM monster_market LIMIT 30') or die(pg_last_error());
					while($data = pg_fetch_array($reply))
					{
						echo '<tr>';
						echo '<td>';
						if(array_key_exists($data['monster'],$monster_meta))
						{
							if(file_exists('../datapack/monsters/'.$data['monster'].'/front.png'))
								echo '<a href="/official-server/datapack-explorer/monsters/'.str_replace(' ','-',strtolower($monster_meta[$data['monster']]['name'])).'.html"><img src="../datapack/monsters/'.$data['monster'].'/front.png" width="80" height="80" alt="'.htmlspecialchars($monster_meta[$data['monster']]['name']).'" title="'.htmlspecialchars($monster_meta[$data['monster']]['description']).'" /></a>';
							elseif(file_exists('../datapack/monsters/'.$data['monster'].'/front.gif'))
								echo '<a href="/official-server/datapack-explorer/monsters/'.str_replace(' ','-',strtolower($monster_meta[$data['monster']]['name'])).'.html"><img src="../datapack/monsters/'.$data['monster'].'/front.gif" width="80" height="80" alt="'.htmlspecialchars($monster_meta[$data['monster']]['name']).'" title="'.htmlspecialchars($monster_meta[$data['monster']]['description']).'" /></a>';
						}
						echo '</td>';
						if(array_key_exists($data['monster'],$monster_meta))
                        {
                            echo '<td><a href="/official-server/datapack-explorer/monsters/'.str_replace(' ','-',strtolower($monster_meta[$data['monster']]['name'])).'.html">';
							echo '<b>'.htmlspecialchars($monster_meta[$data['monster']]['name']).'</b>';
                            echo '</a></td>';
                        }
						else
							echo '<td>Unknown monster ('.htmlspecialchars($data['monster']).')</td>';
						echo '<td>'.$data['level'].'</td>';
						$reply_clan_players = pg_query('SELECT pseudo,skin FROM character WHERE id='.$data['character']) or die(pg_last_error());
						if($data_clan_players = pg_fetch_array($reply_clan_players))
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
							if(file_exists('../datapack/skin/fighter/'.$skin.'/trainer.png'))
								echo '<td><div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div></td><td>'.htmlspecialchars($data_clan_players['pseudo']).'</td>';
							elseif(file_exists('../datapack/skin/fighter/'.$skin.'/trainer.gif'))
								echo '<td><div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div></td><td>'.htmlspecialchars($data_clan_players['pseudo']).'</td>';
							else
								echo '<td></td><td>'.htmlspecialchars($data_clan_players['pseudo']).'</td>';
						}
						else
							echo '<td></td><td></td>';
						if($data['market_price']==0)
							echo '<td>Free</td>';
						else
							echo '<td>'.htmlspecialchars($data['market_price']).'$</td>';
						echo '</tr>';
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