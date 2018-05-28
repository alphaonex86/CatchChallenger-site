<?php
$title='Datapack explorer of Catch Challenger';
$description='Datapack explorer of Catch Challenger';
$keywords='Datapack explorer,Catch Challenger, CatchChallenger,items,monster,map,quests';
include '../config.php';
include '../template/top.php';
include '../template/top2.php';
include '../template/topdatapack.php';
?>
				<div id="back_menu">
					<table>
					<tr>
						<td><a href="/official-server/datapack-explorer/maps.html">Maps</a></td>
						<td><a href="/official-server/datapack-explorer/monsters.html">Monsters</a></td>
						<td><a href="/official-server/datapack-explorer/items.html">Items</a></td>
						<td><a href="/official-server/datapack-explorer/crafting.html">Crafting</a></td>
						<td><a href="/official-server/datapack-explorer/industries.html">Industries</a></td>
						<td><a href="/official-server/datapack-explorer/quests.html">Quests</a></td>
					</tr>
					</table>
				</div>
				
				<div id="title">Datapack explorer</div>
				<br />
				<br />
				<img src="/images/pixel.png" width="96" height="96" style="float:left; margin-right:7px;" class="tiers_img" alt="" />
				<p class="text">You can access to the current link above thanks to the menu. But you have extra pages like:
				<ul>
					<li><a href="/official-server/datapack-explorer/bots.html">Bots</a></li>
					<li><a href="/official-server/datapack-explorer/plants.html">Plants</a></li>
					<li><a href="/official-server/datapack-explorer/types.html">Types</a></li>
					<li><a href="/official-server/datapack-explorer/start.html">Starts</a></li>
					<li><a href="/official-server/datapack-explorer/skills.html">Skills</a></li>
					<li><a href="/official-server/datapack-explorer/buffs.html">Buffs</a></li>
                    <li><a href="/official-server/datapack-explorer/tree.html">Datapack tree</a></li>
				</ul>
				</p>
				
				<?php
                if(file_exists('../'.$contentstatfile) && $filecurs=file_get_contents('../'.$contentstatfile))
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
				
				
				
				
				
<?php
include '../template/bottom2.php';
include '../template/bottom.php';
