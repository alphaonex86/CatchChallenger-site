<?php
$is_up=true;
require 'config.php';
if($postgres_db_site['host']!='localhost')
    $postgres_link_site = @pg_connect('dbname='.$postgres_db_site['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_db_site['host']);
else
    $postgres_link_site = @pg_connect('dbname='.$postgres_db_site['database'].' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link_site===FALSE)
    $is_up=false;

function filewrite($file,$content)
{
    if($filecurs=fopen($file, 'w'))
    {
        if(fwrite($filecurs,$content) === false)
            die('Unable to write the file: '.$file);
        fclose($filecurs);
    }
    else
        die('Unable to write or create the file: '.$file);
}

function playerwithunit($player)
{
    //return ceil($player/1000).'k';//to force into k unit
    return number_format($player,0,'.',' ');//to force into raw unit
    //automatic unit
    if($player>9000000)
        return ceil($player/1000000).'M';
    else if($player>9000)
        return ceil($player/1000).'k';
    else
        return $player;
}
?>
<?php
$title='CatchChallenger community and official server - Old school Opensource MMORPG/Single player with multiple gameplay';
$description='CatchChallenger community and official server, Independent Old school Opensource MMORPG/Single player game';
$keywords='catchchallenger,catch challenger,catch challenger,community, community';
$css_list=array('/css/official-server.min.css');
include 'template/top.php';
include 'template/top2.php';
?>
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
			
			
			
			
				<div id="title">Official server</div>
				<br />
				<?php
                require 'dynamic-part.php';

echo '<p class="text" id="compactserver">';
echo 'Status: ';
$total_up=$loginserver_up+$mirrorserver_up+$mirrorserver_up+$backup_up+$otherjson_up;
$total_down=$loginserver_down+$mirrorserver_down+$mirrorserver_down+$backup_down+$otherjson_down;
$total_corrupted=$mirrorserver_corrupted+$backup_corrupted+$otherjson_corrupted;
echo '<strong>'.$total_up.'</strong> <span style="color:green;">ok</span>';
if($total_down>0)
    echo ', <strong>'.$total_down.'</strong> <span style="color:brown;">bad</span>';
if($total_corrupted>0)
    echo ', <strong>'.$total_corrupted.'</strong> <span style="color:brown;">corrupted</span>';
echo ', <strong>'.playerwithunit($player_count).'</strong> players</p>';
                if(count($total_string_array)>0)
                    echo '<p class="text" id="fullserver">'.implode(', ',$total_string_array).'</p>';
                ?>
                <div class="droplowheight droplowwidth">
                <?php if($maxplayer_count>0) { ?><p class="text">Total: <!--<b><?php echo $server_count; ?></b> servers and --><b><?php echo playerwithunit($player_count); ?></b><!--/<?php echo playerwithunit($maxplayer_count); ?>-->/23M players. Internationnal cluster: <img src="/images/multiflags.png" alt="" width="108px" height="12px" /></p><?php } ?>
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
				
								<div id="title">Community</div>
				<br />
				<br />
				<div class="tiers_img bigbutton"><center><a href="/forum/"><img src="/images/pixel.png" width="96" height="96" alt="" />Forum</a></center></div>
				<div class="tiers_img bigbutton"><center><a href="/wiki/"><img src="/images/wiki.png" width="96" height="96" alt="" />Developer's wiki</a></center></div>
				<div class="tiers_img bigbutton"><center><a href="/wiki-en/"><img src="/images/user-wiki.png" width="96" height="96" alt="" />User's wiki (en)</a></center></div>
				<div class="tiers_img bigbutton"><center><a href="/wiki-fr/"><img src="/images/user-wiki.png" width="96" height="96" alt="" />User's wiki (fr)</a></center></div>
<?php
include 'template/bottom2.php';
include 'template/bottom.php';
