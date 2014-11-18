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
    if(is_executable('/usr/bin/mogrify'))
    {
        $before = microtime(true);
        exec('/usr/bin/find ./ -name \'*.png\' -exec /usr/bin/ionice -c 3 /usr/bin/nice -n 19 /usr/bin/mogrify -trim +repage {} \;');
        $after = microtime(true);
        echo 'Png trim and repage into '.(int)($after-$before)."s\n";
    }
    else
        echo 'no /usr/bin/mogrify found, install imagemagick';
    if(isset($png_compress_zopfli) && is_executable($png_compress_zopfli))
    {
        if(!isset($png_compress_zopfli_level))
            $png_compress_zopfli_level=100;
        $before = microtime(true);
        exec('/usr/bin/find ./ -name \'*.png\' -print -exec /usr/bin/ionice -c 3 /usr/bin/nice -n 19 '.$png_compress_zopfli.' --png --i'.$png_compress_zopfli_level.' {} \;');
        exec('/usr/bin/find ./ -name \'*.png\' -and ! -name \'*.png.png\' -exec mv {}.png {} \;');
        $after = microtime(true);
        echo 'Png trim and repage into '.(int)($after-$before)."s\n";
    }
    else
        echo 'zopfli for png don\'t installed, prefed install it';
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
		{
            $size=getimagesize($datapack_explorer_local_path.'maps/'.$map_image);
            $maps_list[$map]['pixelwidth']=$size[0];
            $maps_list[$map]['pixelheight']=$size[1];
			if($maps_list[$map]['pixelwidth']>1600 || $maps_list[$map]['pixelheight']>800)
				$ratio=4;
			elseif($maps_list[$map]['pixelwidth']>800 || $maps_list[$map]['pixelheight']>400)
				$ratio=2;
			else
				$ratio=1;
			$map_descriptor.='<div class="value mapscreenshot datapackscreenshot"><a href="'.$base_datapack_explorer_site_path.'maps/'.$map_image.'"><center><img src="'.$base_datapack_explorer_site_path.'maps/'.$map_image.'" alt="Screenshot of '.$maps_list[$map]['name'].'" title="Screenshot of '.$maps_list[$map]['name'].'" width="'.($maps_list[$map]['pixelwidth']/$ratio).'" height="'.($maps_list[$map]['pixelheight']/$ratio).'" /></center></a></div>';
		}
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

    if($maps_list[$map]['dropcount']>0 || count($maps_list[$map]['items'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
		<tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
			<th colspan="2">Item</th>
			<th>Location</th>
		</tr>';
        $droplist=array();
		$monster_list=$maps_list[$map]['monsters_list'];
        foreach($monster_list as $monster)
        {
            if(isset($monster_meta[$monster]))
            {
                $drops=$monster_meta[$monster]['drops'];
                foreach($drops as $drop)
                {
                    if(isset($item_meta[$drop['item']]))
                    {
                        if(!isset($droplist[$drop['item']]))
                            $droplist[$drop['item']]=array();
                        $droplist[$drop['item']][$monster]=$drop;
                    }
                }
            }
        }
		foreach($droplist as $item=>$monster_list)
		{
            if(isset($item_meta[$item]))
            {
                $link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
                $name=$item_meta[$item]['name'];
                if($item_meta[$item]['image']!='')
                    $image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
                else
                    $image='';
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
                $map_descriptor.='<td>Drop on ';
                $monster_drops_html=array();
                $luck_to_monster=array();
                foreach($monster_list as $monster=>$content)
                    if(isset($monster_meta[$monster]))
                    {
                        if(!isset($luck_to_monster[$content['luck']]))
                            $luck_to_monster[$content['luck']]=array();
                        $luck_to_monster[$content['luck']][]=$monster;
                    }
                krsort($luck_to_monster);
                foreach($luck_to_monster as $luck=>$content)
                {
                    $monster_html=array();
                    foreach($content as $monster)
                        $monster_html[]='<a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$monster]['name']).'.html" title="'.$monster_meta[$monster]['name'].'">'.$monster_meta[$monster]['name'].'</a>';
                    $monster_drops_html[]=implode(', ',$monster_html).' with luck of '.$luck.'%';
                }
                $map_descriptor.=implode(', ',$monster_drops_html);
                $map_descriptor.='</td>
                </tr>';
            }
        }

        foreach($maps_list[$map]['items'] as $item)
        {
            $visible=$item['visible'];
            $item=$item['item'];
            if(isset($item_meta[$item]))
            {
                $link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
                $name=$item_meta[$item]['name'];
                if($item_meta[$item]['image']!='')
                    $image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
                else
                    $image='';
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
                if($visible)
                    $map_descriptor.='<td>On the map</td>';
                else
                    $map_descriptor.='<td>Hidden on the map</td>';
                $map_descriptor.='</tr>';
            }
        }

		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>
		</tr>
		</table>';
	}

	if(count($maps_list[$map]['monsters'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
		<tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
			<th colspan="2">Monster</th>
			<th>Location</th>
			<th>Levels</th>
			<th colspan="3">Rate</th>
		</tr>';
        foreach($maps_list[$map]['monsters'] as $monsterType=>$monster_list)
        {
            $full_monsterType_name='Cave';
            if(isset($layer_event[$monsterType]))
            {
                if($layer_event[$monsterType]['layer']!='')
                    $full_monsterType_name=$layer_event[$monsterType]['layer'];
                $monsterType_top=$layer_event[$monsterType]['monsterType'];
                $full_monsterType_name_top='Cave';
                if(isset($layer_meta[$monsterType_top]))
                    if($layer_meta[$monsterType_top]['layer']!='')
                        $full_monsterType_name_top=$layer_meta[$monsterType_top]['layer'];
            }
            elseif(isset($layer_meta[$monsterType]))
            {
                if($layer_meta[$monsterType]['layer']!='')
                    $full_monsterType_name=$layer_meta[$monsterType]['layer'];
                $monsterType_top=$monsterType;
                $full_monsterType_name_top=$full_monsterType_name;
            }
            $map_descriptor.='<tr class="item_list_title_type_'.$maps_list[$map]['type'].'">
                    <th colspan="7">';
            $link='';
            $name='';
            $image='';
            if(isset($layer_meta[$monsterType_top]['item']) && $item_meta[$layer_meta[$monsterType_top]['item']])
            {
                $link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$layer_meta[$monsterType_top]['item']]['name']).'.html';
                $name=$item_meta[$layer_meta[$monsterType_top]['item']]['name'];
                if($item_meta[$layer_meta[$monsterType_top]['item']]['image']!='')
                    $image=$base_datapack_site_path.'/items/'.$item_meta[$layer_meta[$monsterType_top]['item']]['image'];
                else
                    $image='';
                $map_descriptor.='<center><table><tr>';
                
                if($link!='')
                    $map_descriptor.='<td><a href="'.$link.'">';
                if($image!='')
                    $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                if($link!='')
                    $map_descriptor.='</a></td>';

                if($link!='')
                    $map_descriptor.='<td><a href="'.$link.'">';
                $map_descriptor.=$item_meta[$layer_meta[$monsterType_top]['item']]['name'];
                if($link!='')
                    $map_descriptor.='</a></td>';

                $map_descriptor.='</tr></table></center>';
            }
            else
                $map_descriptor.=$full_monsterType_name;
            if(isset($layer_event[$monsterType]))
            {
                if($layer_event[$monsterType]['id']=='day' && $layer_event[$monsterType]['value']=='night')
                    $map_descriptor.=' at night';
                else
                    $map_descriptor.=' condition '.$layer_event[$monsterType]['id'].' at '.$layer_event[$monsterType]['value'];
            }
            $map_descriptor.='</th>
                </tr>';
            foreach($monster_list as $monster)
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
                        <td>';
                        $map_descriptor.='<img src="/images/datapack-explorer/'.$full_monsterType_name_top.'.png" alt="" class="locationimg">'.$full_monsterType_name_top;
                        $map_descriptor.='</td>
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
            if(isset($bot_id_to_skin[$bot_id]))
            {
                if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
                    $have_skin=true;
                elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
                    $have_skin=true;
                elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                    $have_skin=true;
                elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                    $have_skin=true;
                else
                    $have_skin=false;
            }
            else
                $have_skin=false;
            if($have_skin)
            {
                if(!isset($zone_to_bot_count[$maps_list[$map]['zone']]))
                    $zone_to_bot_count[$maps_list[$map]['zone']]=1;
                else
                    $zone_to_bot_count[$maps_list[$map]['zone']]++;
            }

            if(isset($bot_start_to_quests[$bot_id]))
            {
                if(!isset($map_to_function[$map]))
                    $map_to_function[$map]=array();
                if(!isset($map_to_function[$map]['quests']))
                    $map_to_function[$map]['quests']=count($bot_start_to_quests[$bot_id]);
                else
                    $map_to_function[$map]['quests']+=count($bot_start_to_quests[$bot_id]);

                if(!isset($zone_to_function[$maps_list[$map]['zone']]))
                    $zone_to_function[$maps_list[$map]['zone']]=array();
                if(!isset($zone_to_function[$maps_list[$map]['zone']]['quests']))
                    $zone_to_function[$maps_list[$map]['zone']]['quests']=count($bot_start_to_quests[$bot_id]);
                else
                    $zone_to_function[$maps_list[$map]['zone']]['quests']+=count($bot_start_to_quests[$bot_id]);
            }

			if(isset($bots_meta[$bot_on_map['id']]))
			{
				if($bots_meta[$bot_on_map['id']]['name']=='')
					$link=text_operation_do_for_url('bot '.$bot_on_map['id']);
				else if($bots_name_count[$bots_meta[$bot_on_map['id']]['name']]==1)
					$link=text_operation_do_for_url($bots_meta[$bot_on_map['id']]['name']);
				else
					$link=text_operation_do_for_url($bot_on_map['id'].'-'.$bots_meta[$bot_on_map['id']]['name']);
				$bot_id=$bot_on_map['id'];
				$bot=$bots_meta[$bot_id];
				if($bot['onlytext']==true)
				{
					$map_descriptor.='<tr class="value">';
					$have_skin=true;
					if(isset($bot_id_to_skin[$bot_id]))
					{
						if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>';
						elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>';
						elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>';
						elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>';
						else
							$have_skin=false;
					}
					else
						$have_skin=false;
					$map_descriptor.='<td';
					if(!$have_skin)
						$map_descriptor.=' colspan="3"';
					else
						$map_descriptor.=' colspan="2"';
					if($bot['name']=='')
						$map_descriptor.='><a href="'.$base_datapack_explorer_site_path.'bots/'.$link.'.html" title="Bot #'.$bot_id.'">Bot #'.$bot_id.'</a></td>';
					else
						$map_descriptor.='><a href="'.$base_datapack_explorer_site_path.'bots/'.$link.'.html" title="'.$bot['name'].'">'.$bot['name'].'</a></td>';
                    if(!isset($bot_start_to_quests[$bot_id]))
                        $map_descriptor.='<td>Text only</td>';
                    else
                    {
                        $map_descriptor.='<td><center>Quests
                        <div style="width:32px;height:32px;background-image:url(\'/official-server/images/flags-128.png\');background-repeat:no-repeat;background-position:-32px 0px;"></center></td>';
                    }
					$map_descriptor.='</tr>';
				}
				else
                {
                    foreach($bot['step'] as $step_id=>$step)
                    {
                        if($step['type']!='text')
                        {
                            if($step['type']!='quests')
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
                            }

                            $map_descriptor.='<tr class="value">';
                            $have_skin=true;
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
                                    $have_skin=false;
                            }
                            else
                                $have_skin=false;
                            $map_descriptor.='<td';
                            if(!$have_skin)
                                $map_descriptor.=' colspan="2"';
                            if($bot['name']=='')
                                $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.'bots/'.$link.'.html" title="Bot #'.$bot_id.'">Bot #'.$bot_id.'</a></td>';
                            else
                                $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.'bots/'.$link.'.html" title="'.$bot['name'].'">'.$bot['name'].'</a></td>';
                        }
                        if($step['type']=='text')
                        {}
                        else if($step['type']=='shop')
                        {
                            $map_descriptor.='<td><center>Shop<div style="width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;"></center></td><td>';
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
                                $map_descriptor.='<td><center>Fight<div style="width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-16px -16px;"></center></td><td>';
                                if($step['leader'])
                                {
                                    $map_descriptor.='<b>Leader</b><br />';
                                    if(isset($bot_id_to_skin[$bot_id]))
                                    {
                                        if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.png'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.png" width="80" height="80" alt="" /></center>';
                                        else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.png'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.png" width="80" height="80" alt="" /></center>';
                                        elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.gif'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.gif" width="80" height="80" alt="" /></center>';
                                        else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.gif'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.gif" width="80" height="80" alt="" /></center>';
                                    }
                                }
                                if($fight_meta[$step['fightid']]['cash']>0)
                                    $map_descriptor.='Rewards: <b>'.$fight_meta[$step['fightid']]['cash'].'$</b><br />';

                                if(count($fight_meta[$step['fightid']]['items'])>0)
                                {
                                    $map_descriptor.='<center><table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
                                    <tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
                                        <th colspan="2">Item</th>
                                    </tr>';
                                    foreach($fight_meta[$step['fightid']]['items'] as $item)
                                    {
                                        if(isset($item_meta[$item['item']]))
                                        {
                                            $link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item['item']]['name']).'.html';
                                            $name=$item_meta[$item['item']]['name'];
                                            if($item_meta[$item['item']]['image']!='')
                                                $image=$base_datapack_site_path.'/items/'.$item_meta[$item['item']]['image'];
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
                                        if($item['quantity']>1)
                                            $quantity_text=$item['quantity'].' ';
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
                                            $map_descriptor.='</tr>';
                                    }
                                    $map_descriptor.='<tr>
                                        <td colspan="2" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>
                                    </tr>
                                    </table></center>';
                                }

                                foreach($fight_meta[$step['fightid']]['monsters'] as $monster)
                                    $map_descriptor.=monsterAndLevelToDisplay($monster,$step['leader']);
                                $map_descriptor.='<br style="clear:both;" />';

                                $map_descriptor.='</td>';
                            }
                        }
                        else if($step['type']=='heal')
                        {
                            $map_descriptor.='<td><center>Heal</center></td>
                            <td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:0px 0px;"></center></td>';
                        }
                        else if($step['type']=='learn')
                        {
                            $map_descriptor.='<td><center>Learn</center></td>
                            <td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:-192px 0px;"></center></td>';
                        }
                        else if($step['type']=='warehouse')
                        {
                            $map_descriptor.='<td><center>Warehouse</center></td>
                            <td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:0px -64px;"></center></td>';
                        }
                        else if($step['type']=='market')
                        {
                            $map_descriptor.='<td><center>Market</center></td>
                            <td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:0px -64px;"></center></td>';
                        }
                        else if($step['type']=='quests' && isset($bot_start_to_quests[$bot_id]))
                        {
                            $map_descriptor.='<td><center>Quests</center></td>
                            <td><center><div style="width:32px;height:32px;background-image:url(\'/official-server/images/flags-128.png\');background-repeat:no-repeat;background-position:-32px 0px;"></center></td>';
                        }
                        else if($step['type']=='clan')
                        {
                            $map_descriptor.='<td><center>Clan</center></td>
                            <td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:-192px -64px;"></center></td>';
                        }
                        else if($step['type']=='sell')
                        {
                            $map_descriptor.='<td><center>Sell</center></td>
                            <td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:-128px 0px;"></center></td>';
                        }
                        else if($step['type']=='zonecapture')
                        {
                            $map_descriptor.='<td><center>Zone capture</center></td>
                            <td><center><div style="width:64px;height:64px;background-image:url(\'/official-server/images/flags-256.png\');background-repeat:no-repeat;background-position:-128px -64px;"></center></td>';
                        }
                        else if($step['type']=='industry')
                        {
                            $map_descriptor.='<td><center>Industry<div style="width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -32px;"></center></td><td>';

                            if(!isset($industrie_meta[$step['industry']]))
                            {
                                $map_descriptor.='Industry '.$step['industry'].' not found for map '.$bot_id.'!</td>';
                                echo 'Industry '.$step['industry'].' not found for map '.$bot_id.'!'."\n";
                            }
                            else
                            {
                                $map_descriptor.='<center><table class="item_list item_list_type_normal">
                                <tr class="item_list_title item_list_title_type_normal">
                                    <th>Industry</th>
                                    <th>Resources</th>
                                    <th>Products</th>
                                </tr>';
                                $industry=$industrie_meta[$step['industry']];
                                $map_descriptor.='<tr class="value">';
                                $map_descriptor.='<td>';
                                $map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'industries/'.$step['industry'].'.html">#'.$step['industry'].'</a>';
                                $map_descriptor.='</td>';
                                $map_descriptor.='<td>';
                                foreach($industry['resources'] as $resources)
                                {
                                    $item=$resources['item'];
                                    $quantity=$resources['quantity'];
                                    if(isset($item_meta[$item]))
                                    {
                                        $link_industry=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
                                        $name=$item_meta[$item]['name'];
                                        if($item_meta[$item]['image']!='')
                                            $image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
                                        else
                                            $image='';
                                        $map_descriptor.='<div style="float:left;text-align:center;">';
                                        if($image!='')
                                        {
                                            if($link_industry!='')
                                                $map_descriptor.='<a href="'.$link_industry.'">';
                                            $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                            if($link_industry!='')
                                                $map_descriptor.='</a>';
                                        }
                                        if($link_industry!='')
                                            $map_descriptor.='<a href="'.$link_industry.'">';
                                        if($name!='')
                                            $map_descriptor.=$name;
                                        else
                                            $map_descriptor.='Unknown item';
                                        if($link_industry!='')
                                            $map_descriptor.='</a></div>';
                                    }
                                    else
                                        $map_descriptor.='Unknown item';
                                }
                                $map_descriptor.='</td>';
                                $map_descriptor.='<td>';
                                foreach($industry['products'] as $products)
                                {
                                    $item=$products['item'];
                                    $quantity=$products['quantity'];
                                    if(isset($item_meta[$item]))
                                    {
                                        $link_industry=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
                                        $name=$item_meta[$item]['name'];
                                        if($item_meta[$item]['image']!='')
                                            $image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
                                        else
                                            $image='';
                                        $map_descriptor.='<div style="float:left;text-align:middle;">';
                                        if($image!='')
                                        {
                                            if($link_industry!='')
                                                $map_descriptor.='<a href="'.$link_industry.'">';
                                            $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                            if($link_industry!='')
                                                $map_descriptor.='</a>';
                                        }
                                        if($link_industry!='')
                                            $map_descriptor.='<a href="'.$link_industry.'">';
                                        if($name!='')
                                            $map_descriptor.=$name;
                                        else
                                            $map_descriptor.='Unknown item';
                                        if($link_industry!='')
                                            $map_descriptor.='</a></div>';
                                    }
                                    else
                                        $map_descriptor.='Unknown item';
                                }
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
	$content=clean_html($content);
	filewrite($datapack_explorer_local_path.'maps/'.$map_html,$content);
}

$map_descriptor='';
ksort($zone_to_map);
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
    if(isset($zone_to_function[$zone]['quests']))
        $map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-16px 0px;" title="Quests"></div>';
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
        if(isset($map_to_function[$map]['quests']))
            $map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-16px 0px;" title="Quests"></div>';
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
$content=clean_html($content);
filewrite($datapack_explorer_local_path.'maps.html',$content);
