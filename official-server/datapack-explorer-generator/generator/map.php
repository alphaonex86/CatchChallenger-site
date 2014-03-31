<?php
if(!isset($datapackexplorergeneratorinclude))
	exit;

if(!is_dir($datapack_explorer_local_path.'maps/'))
	mkdir($datapack_explorer_local_path.'maps/');

foreach($temp_maps as $map)
{
	$map_html=str_replace('.tmx','.html',$map);
	$map_image=str_replace('.tmx','.png',$map);
	if(preg_match('#/#isU',$map))
	{
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
		if(!is_dir($datapack_explorer_local_path.'maps/'.$map_folder))
			mkdir($datapack_explorer_local_path.'maps/'.$map_folder);
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
			$map_descriptor.='<div class="value mapscreenshot datapackscreenshot"><a href="'.$base_datapack_explorer_site_path.'maps/'.$map_image.'"><img src="'.$base_datapack_explorer_site_path.'maps/'.$map_image.'" alt="Screenshot of '.$maps_list[$map]['name'].'" title="Screenshot of '.$maps_list[$map]['name'].'" width="'.($maps_list[$map]['pixelwidth']/2).'" height="'.($maps_list[$map]['pixelheight']/2).'" /></a></div>';
		if($maps_list[$map]['description']!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Map description</div><div class="value">'.$maps_list[$map]['description'].'</div></div>';
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
				if(!isset($duplicate[$door]))
				{
					$duplicate[$door]='';
					if(isset($maps_list[$door]))
						$map_descriptor.='<li>Door: <a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$door).'">'.$maps_list[$door]['name'].'</a></li>';
					else
						$map_descriptor.='<li>Door: <span class="mapnotfound">'.$door.'</span></li>';
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
	$map_descriptor.='<table class="item_list item_list_type_outdoor map_list"><tr class="item_list_title item_list_title_type_outdoor">
	<th>';
	if(isset($zone_meta[$zone]))
		$map_descriptor.=$zone_meta[$zone]['name'];
	elseif($zone=='')
		$map_descriptor.='Unknown zone';
	else
		$map_descriptor.=$zone;
	$map_descriptor.='</th></tr>';
	asort($map_by_zone);
	foreach($map_by_zone as $map=>$name)
		$map_descriptor.='<tr class="value"><td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$map).'" title="'.$name.'">'.$name.'</a></td></tr>';
	$map_descriptor.='<tr>
	<td colspan="1" class="item_list_endline item_list_title_type_outdoor"></td>
	</tr></table>';
}
$content=$template;
$content=str_replace('${TITLE}','Map list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'maps.html',$content);