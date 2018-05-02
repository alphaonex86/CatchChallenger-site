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
$title='CatchChallenger community - Old school Opensource MMORPG/Single player with multiple gameplay';
$description='CatchChallenger community, Independent Old school Opensource MMORPG/Single player game';
$keywords='catchchallenger,catch challenger,catch challenger,community';
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
			
			
			
			
				<div id="title">CatchChallenger official server</div>
				<br />
				<?php
                require 'dynamic-part.php';

                if(count($total_string_array)>0)
                    echo '<p class="text">'.implode(', ',$total_string_array).'</p>';
                ?>
                <?php if($maxplayer_count>0) { ?><p class="text">Total: <!--<b><?php echo $server_count; ?></b> servers and --><b><?php echo playerwithunit($player_count); ?></b><!--/<?php echo playerwithunit($maxplayer_count); ?>-->/23M players. Internationnal cluster: <img src="/images/multiflags.png" alt="" width="108px" height="12px" /></p><?php } ?>
				<p class="text">Download the <a href="http://files.first-world.info/catchchallenger/2.0.4.3/catchchallenger-single-server-windows-x86-2.0.4.3-setup.exe">client for Windows</a> or the <a href="http://files.first-world.info/catchchallenger/2.0.4.3/catchchallenger-single-server-mac-os-x-2.0.4.3.dmg">client for Mac</a> to play on it</p>
                <?php
                if(file_exists($contentstatfile) && $filecurs=file_get_contents($contentstatfile))
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
<?php
include 'template/bottom2.php';
include 'template/bottom.php';
