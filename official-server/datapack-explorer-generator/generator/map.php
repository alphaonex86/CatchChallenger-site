<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator map'."\n");

if(!is_dir($datapack_explorer_local_path.'maps/'))
	if(!mkdir($datapack_explorer_local_path.'maps/'))
		die('Unable to make: '.$datapack_explorer_local_path.'maps/');

foreach($temp_maps as $map)
{
	$map_html=str_replace('.tmx','.html',$map);
	$map_image=str_replace('.tmx','.png',$map);
	if(preg_match('#/#isU',$map))
	{
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
		if(!is_dir($datapack_explorer_local_path.'maps/'.$map_folder))
			if(!mkpath($datapack_explorer_local_path.'maps/'.$map_folder))
				die('Unable to make: '.$datapack_explorer_local_path.'maps/'.$map_folder);
	}
}

$temprand=rand(10000,99999);
if(isset($map_generator) && $map_generator!='')
{
	$pwd=getcwd();
	$return_var=0;
	echo 'cd '.$datapack_explorer_local_path.'maps/ && '.$map_generator.' -platform offscreen '.$pwd.'/'.$datapack_path.'map/';
	chdir($datapack_explorer_local_path.'maps/');
	exec($map_generator.' -platform offscreen '.$pwd.'/'.$datapack_path.'map/',$output,$return_var);
	if(isset($png_compress) && $png_compress!='')
	{
		$before = microtime(true);
		exec($png_compress);
		$after = microtime(true);
		echo 'Png compressed into '.(int)($after-$before)."s\n";
	}
	chdir($pwd);
}

$map_to_function=array();
$zone_to_function=array();
$zone_to_bot_count=array();
foreach($temp_maps as $map)
{
	$map_html=str_replace('.tmx','.html',$map);
	$map_image=str_replace('.tmx','.png',$map);
	$map_folder='';
	if(preg_match('#/#isU',$map))
	{
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
		if(!is_dir($datapack_explorer_local_path.'maps/'.$map_folder))
			mkdir($datapack_explorer_local_path.'maps/'.$map_folder);
	}
	$map_descriptor='';

	$map_descriptor.='<div class="map map_type_'.$maps_list[$map]['type'].'">';
		$map_descriptor.='<div class="subblock"><h1>'.$maps_list[$map]['name'].'</h1>';
		if($maps_list[$map]['type']!='')
			$map_descriptor.='<h3>('.$maps_list[$map]['type'].')</h3>';
		if($maps_list[$map]['shortdescription']!='')
			$map_descriptor.='<h2>'.$maps_list[$map]['shortdescription'].'</h2>';
		$map_descriptor.='</div>';
		if(file_exists($datapack_explorer_local_path.'maps/'.$map_image))
			$map_descriptor.='<center><div class="value mapscreenshot datapackscreenshot"><a href="'.$base_datapack_explorer_site_path.'maps/'.$map_image.'"><img src="'.$base_datapack_explorer_site_path.'maps/'.$map_image.'" alt="Screenshot of '.$maps_list[$map]['name'].'" title="Screenshot of '.$maps_list[$map]['name'].'" width="'.($maps_list[$map]['pixelwidth']/2).'" height="'.($maps_list[$map]['pixelheight']/2).'" /></a></div></center>';
		if($maps_list[$map]['description']!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Map description</div><div class="value">'.$maps_list[$map]['description'].'</div></div>';

		if(isset($zone_meta[$maps_list[$map]['zone']]))
			$zone_name=$zone_meta[$maps_list[$map]['zone']]['name'];
		elseif(!isset($map['zone']) || $maps_list[$map]['zone']=='')
			$zone_name='Unknown zone';
		else
			$zone_name=$map['zone'];
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Zone</div><div class="value"><a href="'.$base_datapack_explorer_site_path.'zones/'.text_operation_do_for_url($zone_name).'.html" title="'.$zone_name.'">';
		$map_descriptor.=$zone_name.'</a></div></div>';

		if(count($maps_list[$map]['borders'])>0 || count($maps_list[$map]['doors'])>0 || count($maps_list[$map]['tp'])>0)
		{
			$duplicate=array();
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Linked locations</div><div class="value"><ul>';
			foreach($maps_list[$map]['borders'] as $bordertype=>$border)
			{
				if(!isset($duplicate[$border]))
				{
					$duplicate[$border]='';
					if(isset($maps_list[$border]))
						$map_descriptor.='<li>Border '.$bordertype.': <a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$border).'">'.$maps_list[$border]['name'].'</a></li>';
					else
						$map_descriptor.='<li>Border '.$bordertype.': <span class="mapnotfound">'.$border.'</span></li>';
				}
			}
			foreach($maps_list[$map]['doors'] as $door)
			{
				if(!isset($duplicate[$door['map']]))
				{
					$duplicate[$door['map']]='';
					if(isset($maps_list[$door['map']]))
						$map_descriptor.='<li>Door: <a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$door['map']).'">'.$maps_list[$door['map']]['name'].'</a></li>';
					else
						$map_descriptor.='<li>Door: <span class="mapnotfound">'.$door['map'].'</span></li>';
				}
			}
			foreach($maps_list[$map]['tp'] as $tp)
			{
				if(!isset($duplicate[$tp]))
				{
					$duplicate[$tp]='';
					if(isset($maps_list[$tp]))
						$map_descriptor.='<li>Teleporter: <a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$tp).'">'.$maps_list[$tp]['name'].'</a></li>';
					else
						$map_descriptor.='<li>Teleporter: <span class="mapnotfound">'.$tp.'</span></li>';
				}
			}
			$map_descriptor.='</ul></div></div>';
		}
	$map_descriptor.='</div>';
	
	if($maps_list[$map]['dropcount']>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
		<tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
			<th colspan="2">Item</th>
			<th>Location</th>
		</tr>';
		$monster_list=array_merge($maps_list[$map]['grass'],$maps_list[$map]['water'],$maps_list[$map]['cave']);
		foreach($monster_list as $monster)
		{
			if(isset($monster_meta[$monster['id']]))
			{
				$drops=$monster_meta[$monster['id']]['drops'];
				foreach($drops as $drop)
				{
					if(isset($item_meta[$drop['item']]))
					{
						$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$drop['item']]['name']).'.html';
						$name=$item_meta[$drop['item']]['name'];
						if($item_meta[$drop['item']]['image']!='')
							$image=$base_datapack_site_path.'/items/'.$item_meta[$drop['item']]['image'];
						else
							$image='';
					}
					else
					{
						$link='';
						$name='';
						$image='';
					}
					$quantity_text='';
					if($drop['quantity_min']!=$drop['quantity_max'])
						$quantity_text=$drop['quantity_min'].' to '.$drop['quantity_max'].' ';
					elseif($drop['quantity_min']>1)
						$quantity_text=$drop['quantity_min'].' ';
					$map_descriptor.='<tr class="value">
						<td>';
						if($image!='')
						{
							if($link!='')
								$map_descriptor.='<a href="'.$link.'">';
							$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
							if($link!='')
								$map_descriptor.='</a>';
						}
						$map_descriptor.='</td>
						<td>';
						if($link!='')
							$map_descriptor.='<a href="'.$link.'">';
						if($name!='')
							$map_descriptor.=$quantity_text.$name;
						else
							$map_descriptor.=$quantity_text.'Unknown item';
						if($link!='')
							$map_descriptor.='</a>';
						$map_descriptor.='</td>';
						$map_descriptor.='<td>Drop on <a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$monster['id']]['name']).'.html" title="'.$monster_meta[$monster['id']]['name'].'">'.$monster_meta[$monster['id']]['name'].'</a> with luck of '.$drop['luck'].'%</td>
					</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>
		</tr>
		</table>';
	}

	if(count($maps_list[$map]['grass'])>0 || count($maps_list[$map]['water'])>0 || count($maps_list[$map]['cave'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
		<tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
			<th colspan="2">Monster</th>
			<th>Location</th>
			<th>Levels</th>
			<th colspan="3">Rate</th>
		</tr>';
		if(count($maps_list[$map]['grass'])>0)
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$maps_list[$map]['type'].'">
					<th colspan="7">Grass</th>
				</tr>';
			foreach($maps_list[$map]['grass'] as $monster)
			{
				if(isset($monster_meta[$monster['id']]))
				{
					$name=$monster_meta[$monster['id']]['name'];
					$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.png" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						else if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>
						<td><img src="/images/datapack-explorer/grass.png" alt="" class="locationimg">Grass</td>
						<td>';
						if($monster['minLevel']==$monster['maxLevel'])
							$map_descriptor.=$monster['minLevel'];
						else
							$map_descriptor.=$monster['minLevel'].'-'.$monster['maxLevel'];
						$map_descriptor.='</td>';
						$map_descriptor.='<td colspan="3">'.$monster['luck'].'%</td>
					</tr>';
				}
			}
		}
		if(count($maps_list[$map]['water'])>0)
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$maps_list[$map]['type'].'">
					<th colspan="7">Water</th>
				</tr>';
			foreach($maps_list[$map]['water'] as $monster)
			{
				if(isset($monster_meta[$monster['id']]))
				{
					$name=$monster_meta[$monster['id']]['name'];
					$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.png" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						else if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>
						<td><img src="/images/datapack-explorer/water.png" alt="" class="locationimg">Water</td>
						<td>';
						if($monster['minLevel']==$monster['maxLevel'])
							$map_descriptor.=$monster['minLevel'];
						else
							$map_descriptor.=$monster['minLevel'].'-'.$monster['maxLevel'];
						$map_descriptor.='</td>';
						$map_descriptor.='<td colspan="3">'.$monster['luck'].'%</td>
					</tr>';
				}
			}
		}
		if(count($maps_list[$map]['cave'])>0)
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$maps_list[$map]['type'].'">
					<th colspan="7">Cave</th>
				</tr>';
			foreach($maps_list[$map]['cave'] as $monster)
			{
				if(isset($monster_meta[$monster['id']]))
				{
					$name=$monster_meta[$monster['id']]['name'];
					$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.png" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						else if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>
						<td><img src="/images/datapack-explorer/cave.png" alt="" class="locationimg">Cave</td>
						<td>';
						if($monster['minLevel']==$monster['maxLevel'])
							$map_descriptor.=$monster['minLevel'];
						else
							$map_descriptor.=$monster['minLevel'].'-'.$monster['maxLevel'];
						$map_descriptor.='</td>';
						$map_descriptor.='<td colspan="3">'.$monster['luck'].'%</td>
					</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="7" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>
		</tr>
		</table>';
	}
	if(isset($maps_list[$map]['bots']) && count($maps_list[$map]['bots'])>0)
	{
		$map_descriptor.='<center><table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th colspan="2">Bot</th>
			<th>Type</th>
			<th>Content</th>
		</tr>';
		foreach($maps_list[$map]['bots'] as $bot_on_map)
		{
			if(!isset($zone_to_bot_count[$maps_list[$map]['zone']]))
				$zone_to_bot_count[$maps_list[$map]['zone']]=1;
			else
				$zone_to_bot_count[$maps_list[$map]['zone']]++;

			if(isset($bots_meta[$bot_on_map['id']]))
			{
				$bot_id=$bot_on_map['id'];
				$bot=$bots_meta[$bot_id];
				if($bot['onlytext']==true)
				{
					$map_descriptor.='<tr class="value">';
					$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'bots/'.$bot_id.'.html">Bot #'.$bot_id.'</a></td>';
					if(isset($bot_id_to_skin[$bot_id]))
					{
						if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
							$map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
						elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
							$map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
						elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
							$map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
						elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
							$map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
						else
							$map_descriptor.='<td>&nbsp;</td>';
					}
					else
						$map_descriptor.='<td>&nbsp;</td>';
					$map_descriptor.='<td>Text</td>';
					$map_descriptor.='<td>Text only</td>';
					$map_descriptor.='</tr>';
				}
				else
				foreach($bot['step'] as $step_id=>$step)
				{
					if($step['type']!='text')
					{
						if(!isset($map_to_function[$map]))
							$map_to_function[$map]=array();
						if(!isset($map_to_function[$map][$step['type']]))
							$map_to_function[$map][$step['type']]=1;
						else
							$map_to_function[$map][$step['type']]++;

						if(!isset($zone_to_function[$maps_list[$map]['zone']]))
							$zone_to_function[$maps_list[$map]['zone']]=array();
						if(!isset($zone_to_function[$maps_list[$map]['zone']][$step['type']]))
							$zone_to_function[$maps_list[$map]['zone']][$step['type']]=1;
						else
							$zone_to_function[$maps_list[$map]['zone']][$step['type']]++;

						$map_descriptor.='<tr class="value">';
						$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'bots/'.$bot_id.'.html">Bot #'.$bot_id.'</a></td>';
						if(isset($bot_id_to_skin[$bot_id]))
						{
							if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
								$map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
							elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
								$map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
							elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
								$map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
							elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
								$map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
							else
								$map_descriptor.='<td>&nbsp;</td>';
						}
						else
							$map_descriptor.='<td>&nbsp;</td>';
					}
					if($step['type']=='text')
					{}
					else if($step['type']=='shop')
					{
						$map_descriptor.='<td>Shop<div style="width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;"></td><td>';
						$map_descriptor.='<center><table class="item_list item_list_type_normal">
						<tr class="item_list_title item_list_title_type_normal">
							<th colspan="2">Item</th>
							<th>Price</th>
						</tr>';
						foreach($shop_meta[$step['shop']]['products'] as $item=>$price)
						{
							if(isset($item_meta[$item]))
							{
								$map_descriptor.='<tr class="value">';
								if(isset($item_meta[$item]))
								{
									$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
									$name=$item_meta[$item]['name'];
									if($item_meta[$item]['image']!='')
										$image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
									else
										$image='';
								}
								else
								{
									$link='';
									$name='';
									$image='';
								}
								$map_descriptor.='<tr class="value">
								<td>';
								if($image!='')
								{
									if($link!='')
										$map_descriptor.='<a href="'.$link.'">';
									$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
									if($link!='')
										$map_descriptor.='</a>';
								}
								$map_descriptor.='</td>
								<td>';
								if($link!='')
									$map_descriptor.='<a href="'.$link.'">';
								if($name!='')
									$map_descriptor.=$name;
								else
									$map_descriptor.='Unknown item';
								if($link!='')
									$map_descriptor.='</a>';
								$map_descriptor.='</td>';
								$map_descriptor.='<td>'.$price.'$</td>';
								$map_descriptor.='</tr>';
							}
						}
						$map_descriptor.='<tr>
							<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
						</tr>
						</table>';
						$map_descriptor.='</center></td>';
					}
					else if($step['type']=='fight')
					{
						if(isset($fight_meta[$step['fightid']]))
						{
							$map_descriptor.='<td>Fight<div style="width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-16px -16px;"></td><td>';
							if($fight_meta[$step['fightid']]['cash']>0)
								$map_descriptor.='Rewards: '.$fight_meta[$step['fightid']]['cash'].'$';

							foreach($fight_meta[$step['fightid']]['monsters'] as $monster)
							{
								if(isset($monster_meta[$monster['monster']]))
								{
									$monster_full=$monster_meta[$monster['monster']];
									$map_descriptor.='<table class="item_list item_list_type_'.$monster_full['type'][0].' map_list">
									<tr class="item_list_title item_list_title_type_'.$monster_full['type'][0].'">
										<th>';
									$map_descriptor.='</th>
									</tr>';
									$map_descriptor.='<tr class="value">';
									$map_descriptor.='<td>';
									$map_descriptor.='<table class="monsterforevolution">';
									if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/front.png'))
										$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$monster['monster'].'/front.png" width="80" height="80" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" /></a></td></tr>';
									else if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/front.gif'))
										$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$monster['monster'].'/front.gif" width="80" height="80" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" /></a></td></tr>';
									$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html">'.$monster_full['name'].'</a></td></tr>';
									$map_descriptor.='<tr><td>Level '.$monster['level'].'</td></tr>';
									$map_descriptor.='</table>';
									$map_descriptor.='</td>';
									$map_descriptor.='</tr>';
									$map_descriptor.='<tr>
										<th class="item_list_endline item_list_title item_list_title_type_'.$monster_full['type'][0].'">';
									$map_descriptor.='</th>
									</tr>
									</table>';
								}
							}
							$map_descriptor.='<br style="clear:both;" />';

							$map_descriptor.='</td>';
						}
					}
					else if($step['type']=='heal')
					{
						$map_descriptor.='<td>Heal</td>
						<td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:0px 0px;"></center></td>';
					}
					else if($step['type']=='learn')
					{
						$map_descriptor.='<td>Learn</td>
						<td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:-192px 0px;"></center></td>';
					}
					else if($step['type']=='warehouse')
					{
						$map_descriptor.='<td>Warehouse</td>
						<td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:0px -64px;"></center></td>';
					}
					else if($step['type']=='market')
					{
						$map_descriptor.='<td>Market</td>
						<td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:0px -64px;"></center></td>';
					}
					else if($step['type']=='clan')
					{
						$map_descriptor.='<td>Clan</td>
						<td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:-192px -64px;"></center></td>';
					}
					else if($step['type']=='sell')
					{
						$map_descriptor.='<td>Sell</td>
						<td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:-128px 0px;"></center></td>';
					}
					else if($step['type']=='zonecapture')
					{
						$map_descriptor.='<td>Zone capture</td>
						<td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:-128px -64px;"></center></td>';
					}
					else if($step['type']=='industry')
					{
						$map_descriptor.='<td>Industry<div style="width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -32px;"></td><td>';

						$map_descriptor.='<center><table class="item_list item_list_type_normal">
						<tr class="item_list_title item_list_title_type_normal">
							<th>Industry</th>
							<th>Resources</th>
							<th>Products</th>
						</tr>';
						$industry=$industries_meta[$step['industry']];
						$map_descriptor.='<tr class="value">';
						$map_descriptor.='<td>';
						$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'industries/'.$step['industry'].'.html">#'.$step['industry'].'</a>';
						$map_descriptor.='</td>';
						$map_descriptor.='<td>';
						foreach($industry['resources'] as $item=>$quantity)
						{
							if(isset($item_meta[$item]))
							{
								$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
								$name=$item_meta[$item]['name'];
								if($item_meta[$item]['image']!='')
									$image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
								else
									$image='';
								$map_descriptor.='<div style="float:left;text-align:center;">';
								if($image!='')
								{
									if($link!='')
										$map_descriptor.='<a href="'.$link.'">';
									$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
									if($link!='')
										$map_descriptor.='</a>';
								}
								if($link!='')
									$map_descriptor.='<a href="'.$link.'">';
								if($name!='')
									$map_descriptor.=$name;
								else
									$map_descriptor.='Unknown item';
								if($link!='')
									$map_descriptor.='</a></div>';
							}
							else
								$map_descriptor.='Unknown item';
						}
						$map_descriptor.='</td>';
						$map_descriptor.='<td>';
						foreach($industry['products'] as $item=>$quantity)
						{
							if(isset($item_meta[$item]))
							{
								$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
								$name=$item_meta[$item]['name'];
								if($item_meta[$item]['image']!='')
									$image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
								else
									$image='';
								$map_descriptor.='<div style="float:left;text-align:middle;">';
								if($image!='')
								{
									if($link!='')
										$map_descriptor.='<a href="'.$link.'">';
									$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
									if($link!='')
										$map_descriptor.='</a>';
								}
								if($link!='')
									$map_descriptor.='<a href="'.$link.'">';
								if($name!='')
									$map_descriptor.=$name;
								else
									$map_descriptor.='Unknown item';
								if($link!='')
									$map_descriptor.='</a></div>';
							}
							else
								$map_descriptor.='Unknown item';
						}
						$map_descriptor.='</td>';
						$map_descriptor.='</tr>';
						$map_descriptor.='<tr>
							<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
						</tr>
						</table></center>';

						$map_descriptor.='</td>';
					}
					else
						$map_descriptor.='<td>'.$step['type'].'</td><td>Unknown type ('.$step['type'].')</td>';
					if($step['type']!='text')
						$map_descriptor.='</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table></center>';
	}
	
	$content=$template;
	$content=str_replace('${TITLE}',$maps_list[$map]['name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'maps/'.$map_html,$content);
}

$map_descriptor='';
foreach($zone_to_map as $zone=>$map_by_zone)
{
	if(isset($zone_meta[$zone]))
		$zone_name=$zone_meta[$zone]['name'];
	elseif($zone=='')
		$zone_name='Unknown zone';
	else
		$zone_name=$zone;

	$map_descriptor.='<table class="item_list item_list_type_outdoor map_list"><tr class="item_list_title item_list_title_type_outdoor">
	<th><a href="'.$base_datapack_explorer_site_path.'zones/'.text_operation_do_for_url($zone_name).'.html" title="'.$zone_name.'">';
	$map_descriptor.=$zone_name;
	$map_descriptor.='</a></th><th>';
	if(isset($zone_to_function[$zone]['shop']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;" title="Shop"></div>';
	if(isset($zone_to_function[$zone]['fight']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-16px -16px;" title="Fight"></div>';
	if(isset($zone_to_function[$zone]['heal']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px 0px;" title="Heal"></div>';
	if(isset($zone_to_function[$zone]['learn']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-48px 0px;" title="Learn"></div>';
	if(isset($zone_to_function[$zone]['warehouse']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -16px;" title="Warehouse"></div>';
	if(isset($zone_to_function[$zone]['market']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -16px;" title="Market"></div>';
	if(isset($zone_to_function[$zone]['clan']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-48px -16px;" title="Clan"></div>';
	if(isset($zone_to_function[$zone]['sell']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;" title="Sell"></div>';
	if(isset($zone_to_function[$zone]['zonecapture']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px -16px;" title="Zone capture"></div>';
	if(isset($zone_to_function[$zone]['industry']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -32px;" title="Industry"></div>';
	$map_descriptor.='</th></tr>';
	asort($map_by_zone);
	foreach($map_by_zone as $map=>$name)
	{
		$map_descriptor.='<tr class="value"><td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$map).'" title="'.$name.'">'.$name.'</a></td><td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$map).'" title="'.$name.'">';
		if(isset($map_to_function[$map]['shop']))
			$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;" title="Shop"></div>';
		if(isset($map_to_function[$map]['fight']))
			$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-16px -16px;" title="Fight"></div>';
		if(isset($map_to_function[$map]['heal']))
			$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px 0px;" title="Heal"></div>';
		if(isset($map_to_function[$map]['learn']))
			$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-48px 0px;" title="Learn"></div>';
		if(isset($map_to_function[$map]['warehouse']))
			$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -16px;" title="Warehouse"></div>';
		if(isset($map_to_function[$map]['market']))
			$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -16px;" title="Market"></div>';
		if(isset($map_to_function[$map]['clan']))
			$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-48px -16px;" title="Clan"></div>';
		if(isset($map_to_function[$map]['sell']))
			$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;" title="Sell"></div>';
		if(isset($map_to_function[$map]['zonecapture']))
			$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px -16px;" title="Zone capture"></div>';
		if(isset($map_to_function[$map]['industry']))
			$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -32px;" title="Industry"></div>';
		$map_descriptor.='</a></td></tr>';
	}
	$map_descriptor.='<tr>
	<td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
	</tr></table>';
}
$content=$template;
$content=str_replace('${TITLE}','Map list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'maps.html',$content);