<?php
$is_up=true;
require '../config.php';

if($postgres_db_base['host']!='localhost')
    $postgres_link_base = pg_connect('dbname='.$postgres_db_base['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_db_base['host']);
else
    $postgres_link_base = pg_connect('dbname='.$postgres_db_base['database'].' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link_base===FALSE)
    $is_up=false;

if($is_up)
{
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
}
?>
<?php
$title='Players - CatchChallenger MMORPG';
$description='Players - CatchChallenger, Independent Old school Opensource MMORPG/Single player game';
$keywords='player,catchchallenger,catch challenger,catch challenger,community';
include '../template/top.php';
include '../template/top2.php';
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
					$reply = pg_query($postgres_link_common,'SELECT COUNT(id) as count FROM character;') or die(pg_last_error());
					if($data = pg_fetch_array($reply))
                        echo 'Number of player registred: '.$data['count'].'<br />';
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
					$reply = pg_query($postgres_link_common,'SELECT * FROM character ORDER BY id LIMIT 30') or die(pg_last_error());
					while($data = pg_fetch_array($reply))
					{
						echo '<tr><td>';
						if($index<=3)
							echo '<img src="/official-server/images/top-'.$index.'.png" alt="Top '.$index.'" title="Top '.$index.'" width="16" height="16" style="float:left" />';
                        if(isset($skin_list[$data['skin']]))
                            $skin=$skin_list[$data['skin']];
                        else
                        {
                            $reply_skin = pg_query($postgres_link_base,'SELECT skin FROM dictionary_skin WHERE id='.$data['skin']) or die(pg_last_error());
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
						$reply_clan = pg_query($postgres_link_common,'SELECT name FROM clan WHERE id='.$data['clan']) or die(pg_last_error());
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
				?><?php
include '../template/bottom2.php';
include '../template/bottom.php';
