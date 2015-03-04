<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator map'."\n");

$map_to_function=array();
$zone_to_function=array();
$zone_to_bot_count=array();

foreach($temp_maps as $map)
{
	$map_html=str_replace('.tmx','',$map);
	$map_image=str_replace('.tmx','.png',$map);
	$map_folder='';
	if(preg_match('#/#isU',$map))
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
	$map_descriptor='';

	$map_descriptor.='<div class="map map_type_'.$maps_list[$map]['type'].'">'."\n";
		$map_descriptor.='<div class="subblock"><h1>'.$maps_list[$map]['name'][$current_lang].'</h1>'."\n";
		if($maps_list[$map]['type']!='')
			$map_descriptor.='<h3>('.$maps_list[$map]['type'].')</h3>'."\n";
		if($maps_list[$map]['shortdescription'][$current_lang]!='')
			$map_descriptor.='<h2>'.$maps_list[$map]['shortdescription'][$current_lang].'</h2>'."\n";
		$map_descriptor.='</div>'."\n";
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
			$map_descriptor.='<div class="value mapscreenshot datapackscreenshot"><center>[['.$base_datapack_site_http.$base_datapack_explorer_site_path.'maps/'.$map_image.' <img src="'.$base_datapack_site_http.$base_datapack_explorer_site_path.'maps/'.$map_image.'" alt="Screenshot of '.$maps_list[$map]['name'][$current_lang].'" title="Screenshot of '.$maps_list[$map]['name'][$current_lang].'" width="'.($maps_list[$map]['pixelwidth']/$ratio).'" height="'.($maps_list[$map]['pixelheight']/$ratio).'" />]]</center></div>'."\n";
		}
		if($maps_list[$map]['description'][$current_lang]!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Map description</div><div class="value">'.$maps_list[$map]['description'][$current_lang].'</div></div>'."\n";

		if(isset($zone_meta[$maps_list[$map]['zone']]))
			$zone_name=$zone_meta[$maps_list[$map]['zone']]['name'][$current_lang];
		elseif(!isset($map['zone']) || $maps_list[$map]['zone']=='')
			$zone_name='Unknown zone';
		else
			$zone_name=$map['zone'];
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Zone</div><div class="value">[['.$translation_list[$current_lang]['Zones:'].$zone_name.'|';
		$map_descriptor.=$zone_name.']]</div></div>'."\n";

		if(count($maps_list[$map]['borders'])>0 || count($maps_list[$map]['doors'])>0 || count($maps_list[$map]['tp'])>0)
		{
			$duplicate=array();
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Linked locations</div><div class="value"><ul>'."\n";
			foreach($maps_list[$map]['borders'] as $bordertype=>$border)
			{
				if(!isset($duplicate[$border]))
				{
					$duplicate[$border]='';
					if(isset($maps_list[$border]))
						$map_descriptor.='<li>Border '.$bordertype.': [['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($border).'|'.$maps_list[$border]['name'][$current_lang].']]</li>'."\n";
					else
						$map_descriptor.='<li>Border '.$bordertype.': <span class="mapnotfound">'.$border.'</span></li>'."\n";
				}
			}
			foreach($maps_list[$map]['doors'] as $door)
			{
				if(!isset($duplicate[$door['map']]))
				{
					$duplicate[$door['map']]='';
					if(isset($maps_list[$door['map']]))
						$map_descriptor.='<li>Door: [['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($door['map']).'|'.$maps_list[$door['map']]['name'][$current_lang].']]</li>'."\n";
					else
						$map_descriptor.='<li>Door: <span class="mapnotfound">'.$door['map'].'</span></li>'."\n";
				}
			}
			foreach($maps_list[$map]['tp'] as $tp)
			{
				if(!isset($duplicate[$tp]))
				{
					$duplicate[$tp]='';
					if(isset($maps_list[$tp]))
						$map_descriptor.='<li>Teleporter: [['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($tp).'|'.$maps_list[$tp]['name'][$current_lang].']]</li>'."\n";
					else
						$map_descriptor.='<li>Teleporter: <span class="mapnotfound">'.$tp.'</span></li>'."\n";
				}
			}
			$map_descriptor.='</ul></div></div>'."\n";
		}
	$map_descriptor.='</div>';
    savewikipage('Template:Maps/'.$map_html.'_HEADER',$map_descriptor);$map_descriptor='';

    if($maps_list[$map]['dropcount']>0 || count($maps_list[$map]['items'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
		<tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
			<th colspan="2">Item</th>
			<th>Location</th>
		</tr>'."\n";
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
                $link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]);
                $name=$item_meta[$item]['name'][$current_lang];
                if($item_meta[$item]['image']!='')
                    $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
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
                        $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                    $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                    if($link!='')
                        $map_descriptor.=']]';
                }
                $map_descriptor.='</td>'."\n".'
                <td>'."\n";
                if($link!='')
                    $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                if($name!='')
                    $map_descriptor.=$quantity_text.$name;
                else
                    $map_descriptor.=$quantity_text.'Unknown item';
                if($link!='')
                    $map_descriptor.=']]';
                $map_descriptor.='</td>'."\n";
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
                        $monster_html[]='[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$monster]['name'][$current_lang].'|'.$monster_meta[$monster]['name'][$current_lang].']]';
                    $monster_drops_html[]=implode(', ',$monster_html).' with luck of '.$luck.'%';
                }
                $map_descriptor.=implode(', ',$monster_drops_html);
                $map_descriptor.='</td>'."\n".'
                </tr>'."\n";
            }
        }

        foreach($maps_list[$map]['items'] as $item)
        {
            $visible=$item['visible'];
            $item=$item['item'];
            if(isset($item_meta[$item]))
            {
                $link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]);
                $name=$item_meta[$item]['name'][$current_lang];
                if($item_meta[$item]['image']!='')
                    $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
                else
                    $image='';
                $map_descriptor.='<tr class="value">
                <td>'."\n";
                if($image!='')
                {
                    if($link!='')
                        $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                    $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                    if($link!='')
                        $map_descriptor.=']]';
                }
                $map_descriptor.='</td>'."\n".'
                <td>'."\n";
                if($link!='')
                    $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                if($name!='')
                    $map_descriptor.=$name;
                else
                    $map_descriptor.='Unknown item';
                if($link!='')
                    $map_descriptor.=']]';
                $map_descriptor.='</td>'."\n";
                if($visible)
                    $map_descriptor.='<td>On the map</td>'."\n";
                else
                    $map_descriptor.='<td>Hidden on the map</td>'."\n";
                $map_descriptor.='</tr>'."\n";
            }
        }

		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>'."\n".'
		</tr>
		</table>'."\n";
        savewikipage('Template:Maps/'.$map_html.'_ITEM',$map_descriptor);$map_descriptor='';
	}

	if(count($maps_list[$map]['monsters'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
		<tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
			<th colspan="2">Monster</th>
			<th>Location</th>
			<th>Levels</th>
			<th colspan="3">Rate</th>
		</tr>'."\n";
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
                    <th colspan="7">'."\n";
            $link='';
            $name='';
            $image='';
            if(isset($layer_meta[$monsterType_top]['item']) && $item_meta[$layer_meta[$monsterType_top]['item']])
            {
                $link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang]);
                $name=$item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang];
                if($item_meta[$layer_meta[$monsterType_top]['item']]['image']!='')
                    $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$layer_meta[$monsterType_top]['item']]['image'];
                else
                    $image='';
                $map_descriptor.='<center><table><tr>';
                
                if($link!='')
                    $map_descriptor.='<td>[['.$translation_list[$current_lang]['Items:'].$item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang].'|';
                if($image!='')
                    $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                if($link!='')
                    $map_descriptor.=']]</td>'."\n";

                if($link!='')
                    $map_descriptor.='<td>[['.$translation_list[$current_lang]['Items:'].$item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang].'|';
                $map_descriptor.=$item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang];
                if($link!='')
                    $map_descriptor.=']]</td>'."\n";

                $map_descriptor.='</tr></table></center>'."\n";
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
                </tr>'."\n";
            foreach($monster_list as $monster)
            {
                if(isset($monster_meta[$monster['id']]))
                {
                    $name=$monster_meta[$monster['id']]['name'][$current_lang];
                    $link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name);
                    $map_descriptor.='<tr class="value">
                        <td>'."\n";
                        if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.png'))
                            $map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.png" width="32" height="32" alt="'.$name.'" title="'.$name.'" />]]</div>'."\n";
                        else if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.gif'))
                            $map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.gif" width="32" height="32" alt="'.$name.'" title="'.$name.'" />]]</div>'."\n";
                        $map_descriptor.='</td>'."\n".'
                        <td>[['.$translation_list[$current_lang]['Monsters:'].$name.'|'.$name.']]</td>'."\n".'
                        <td>'."\n";
                        $map_descriptor.='<img src="'.$base_datapack_site_http.'/images/datapack-explorer/'.$full_monsterType_name_top.'.png" alt="" class="locationimg">'.$full_monsterType_name_top;
                        $map_descriptor.='</td>'."\n".'
                        <td>'."\n";
                        if($monster['minLevel']==$monster['maxLevel'])
                            $map_descriptor.=$monster['minLevel'];
                        else
                            $map_descriptor.=$monster['minLevel'].'-'.$monster['maxLevel'];
                        $map_descriptor.='</td>'."\n";
                        $map_descriptor.='<td colspan="3">'.$monster['luck'].'%</td>'."\n".'
                    </tr>'."\n";
                }
            }
        }
		$map_descriptor.='<tr>
			<td colspan="7" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>'."\n".'
		</tr>
		</table>'."\n";
        savewikipage('Template:Maps/'.$map_html.'_MONSTER',$map_descriptor);$map_descriptor='';
	}

    if(isset($maps_list[$map]['bots']) && count($maps_list[$map]['bots'])>0)
	{
		$map_descriptor.='<center><table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
		<tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
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
				if($bots_meta[$bot_on_map['id']]['name'][$current_lang]=='')
					$link=text_operation_do_for_url('bot '.$bot_on_map['id']);
				else if($bots_name_count[$current_lang][$bots_meta[$bot_on_map['id']]['name'][$current_lang]]==1)
					$link=text_operation_do_for_url($bots_meta[$bot_on_map['id']]['name'][$current_lang]);
				else
					$link=text_operation_do_for_url($bot_on_map['id'].'-'.$bots_meta[$bot_on_map['id']]['name'][$current_lang]);
				$bot_id=$bot_on_map['id'];
				$bot=$bots_meta[$bot_id];
				if($bot['onlytext']==true)
				{
					$map_descriptor.='<tr class="value">'."\n";
					$have_skin=true;
					if(isset($bot_id_to_skin[$bot_id]))
					{
						if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
						elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
						elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
						elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
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
					if($bot['name'][$current_lang]=='')
						$map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link.'|Bot #'.$bot_id.']]</td>'."\n";
					else
						$map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link.'|'.$bot['name'][$current_lang].']]</td>'."\n";
                    if(!isset($bot_start_to_quests[$bot_id]))
                        $map_descriptor.='<td>Text only</td>'."\n";
                    else
                    {
                        $map_descriptor.='<td><center>Quests
                        <div style="background-position:-32px 0px;" class="flags flags32"></div></center></td>'."\n";
                    }
					$map_descriptor.='</tr>'."\n";
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
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>'."\n";
                                elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>'."\n";
                                elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>'."\n";
                                elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>'."\n";
                                else
                                    $have_skin=false;
                            }
                            else
                                $have_skin=false;
                            $map_descriptor.='<td';
                            if(!$have_skin)
                                $map_descriptor.=' colspan="2"';
                            if($bot['name'][$current_lang]=='')
                                $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link.'|Bot #'.$bot_id.']]</td>'."\n";
                            else
                                $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link.'|'.$bot['name'][$current_lang].']]</td>'."\n";
                        }
                        if($step['type']=='text')
                        {}
                        else if($step['type']=='shop')
                        {
                            $map_descriptor.='<td><center>Shop<div style="background-position:-32px 0px;" class="flags flags16"></div></center></td>'."\n".'<td>';
                            $map_descriptor.='<center><table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
                            <tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
                                <th colspan="2">Item</th>
                                <th>Price</th>
                            </tr>'."\n";
                            foreach($shop_meta[$step['shop']]['products'] as $item=>$price)
                            {
                                if(isset($item_meta[$item]))
                                {
                                    $map_descriptor.='<tr class="value">'."\n";
                                    if(isset($item_meta[$item]))
                                    {
                                        $link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]);
                                        $name=$item_meta[$item]['name'][$current_lang];
                                        if($item_meta[$item]['image']!='')
                                            $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
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
                                    <td>'."\n";
                                    if($image!='')
                                    {
                                        if($link!='')
                                            $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item]['name'][$current_lang].'|';
                                        $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                        if($link!='')
                                            $map_descriptor.=']]';
                                    }
                                    $map_descriptor.='</td>'."\n".'
                                    <td>'."\n";
                                    if($link!='')
                                        $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item]['name'][$current_lang].'|';
                                    if($name!='')
                                        $map_descriptor.=$name;
                                    else
                                        $map_descriptor.='Unknown item';
                                    if($link!='')
                                        $map_descriptor.=']]';
                                    $map_descriptor.='</td>'."\n";
                                    $map_descriptor.='<td>'.$price.'$</td>'."\n";
                                    $map_descriptor.='</tr>'."\n";
                                }
                            }
                            $map_descriptor.='<tr>
                                <td colspan="3" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>'."\n".'
                            </tr>
                            </table>'."\n";
                            $map_descriptor.='</center></td>'."\n";
                        }
                        else if($step['type']=='fight')
                        {
                            if(isset($fight_meta[$step['fightid']]))
                            {
                                $map_descriptor.='<td><center>Fight<div style="background-position:-16px -16px;" class="flags flags16"></div></center></td>'."\n".'<td>'."\n";
                                if($step['leader'])
                                {
                                    $map_descriptor.='<b>Leader</b><br />'."\n";
                                    if(isset($bot_id_to_skin[$bot_id]))
                                    {
                                        if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.png'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.png" width="80" height="80" alt="" /></center>'."\n";
                                        else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.png'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.png" width="80" height="80" alt="" /></center>'."\n";
                                        elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.gif'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.gif" width="80" height="80" alt="" /></center>'."\n";
                                        else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.gif'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.gif" width="80" height="80" alt="" /></center>'."\n";
                                    }
                                }
                                if($fight_meta[$step['fightid']]['cash']>0)
                                    $map_descriptor.='Rewards: <b>'.$fight_meta[$step['fightid']]['cash'].'$</b><br />'."\n";

                                if(count($fight_meta[$step['fightid']]['items'])>0)
                                {
                                    $map_descriptor.='<center><table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
                                    <tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
                                        <th colspan="2">Item</th>
                                    </tr>'."\n";
                                    foreach($fight_meta[$step['fightid']]['items'] as $item)
                                    {
                                        if(isset($item_meta[$item['item']]))
                                        {
                                            $link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item['item']]['name'][$current_lang]);
                                            $name=$item_meta[$item['item']]['name'][$current_lang];
                                            if($item_meta[$item['item']]['image']!='')
                                                $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item['item']]['image'];
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
                                            <td>'."\n";
                                            if($image!='')
                                            {
                                                if($link!='')
                                                    $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item['item']]['name'][$current_lang].'|';
                                                $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                                if($link!='')
                                                    $map_descriptor.=']]';
                                            }
                                            $map_descriptor.='</td>'."\n".'
                                            <td>';
                                            if($link!='')
                                                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item['item']]['name'][$current_lang].'|';
                                            if($name!='')
                                                $map_descriptor.=$quantity_text.$name;
                                            else
                                                $map_descriptor.=$quantity_text.'Unknown item';
                                            if($link!='')
                                                $map_descriptor.=']]';
                                            $map_descriptor.='</td>'."\n";
                                            $map_descriptor.='</tr>'."\n";
                                    }
                                    $map_descriptor.='<tr>
                                        <td colspan="2" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>'."\n".'
                                    </tr>
                                    </table></center>'."\n";
                                }

                                foreach($fight_meta[$step['fightid']]['monsters'] as $monster)
                                    $map_descriptor.=monsterAndLevelToDisplay($monster,$step['leader'],true);
                                $map_descriptor.='<br style="clear:both;" />'."\n";

                                $map_descriptor.='</td>'."\n";
                            }
                        }
                        else if($step['type']=='heal')
                        {
                            $map_descriptor.='<td><center>Heal</center></td>'."\n".'
                            <td><center><div style="background-position:0px 0px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='learn')
                        {
                            $map_descriptor.='<td><center>Learn</center></td>'."\n".'
                            <td><center><div style="background-position:-192px 0px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='warehouse')
                        {
                            $map_descriptor.='<td><center>Warehouse</center></td>'."\n".'
                            <td><center><div style="background-position:0px -64px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='market')
                        {
                            $map_descriptor.='<td><center>Market</center></td>'."\n".'
                            <td><center><div style="background-position:0px -64px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='quests' && isset($bot_start_to_quests[$bot_id]))
                        {
                            $map_descriptor.='<td><center>Quests</center></td>'."\n".'
                            <td><center><div style="background-position:-32px 0px;" class="flags flags32"></div></center></td>'."\n";
                        }
                        else if($step['type']=='clan')
                        {
                            $map_descriptor.='<td><center>Clan</center></td>'."\n".'
                            <td><center><div style="background-position:-192px -64px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='sell')
                        {
                            $map_descriptor.='<td><center>Sell</center></td>'."\n".'
                            <td><center><div style="background-position:-128px 0px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='zonecapture')
                        {
                            $map_descriptor.='<td><center>Zone capture</center></td>'."\n".'
                            <td><center><div style="background-position:-128px -64px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='industry')
                        {
                            $map_descriptor.='<td><center>Industry<div style="background-position:0px -32px;" class="flags flags16"></div></center></td><td>'."\n";

                            if(!isset($industrie_meta[$step['industry']]))
                            {
                                $map_descriptor.='Industry '.$step['industry'].' not found for map '.$bot_id.'!</td>'."\n";
                                echo 'Industry '.$step['industry'].' not found for map '.$bot_id.'!'."\n";
                            }
                            else
                            {
                                $map_descriptor.='<center><table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
                                <tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
                                    <th>Industry</th>
                                    <th>Resources</th>
                                    <th>Products</th>
                                </tr>'."\n";
                                $industry=$industrie_meta[$step['industry']];
                                $map_descriptor.='<tr class="value">'."\n";
                                $map_descriptor.='<td>'."\n";
                                $map_descriptor.='[['.$translation_list[$current_lang]['Industries:'].'Industry '.$step['industry'].'|Industry '.$step['industry'].']]';
                                $map_descriptor.='</td>'."\n";
                                $map_descriptor.='<td>'."\n";
                                foreach($industry['resources'] as $resources)
                                {
                                    $item=$resources['item'];
                                    $quantity=$resources['quantity'];
                                    if(isset($item_meta[$item]))
                                    {
                                        $link_industry=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]);
                                        $name=$item_meta[$item]['name'][$current_lang];
                                        if($item_meta[$item]['image']!='')
                                            $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
                                        else
                                            $image='';
                                        $map_descriptor.='<div style="float:left;text-align:center;">'."\n";
                                        if($image!='')
                                        {
                                            if($link_industry!='')
                                                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item]['name'][$current_lang].'|';
                                            $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                            if($link_industry!='')
                                                $map_descriptor.=']]';
                                        }
                                        if($link_industry!='')
                                            $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item]['name'][$current_lang].'|';
                                        if($name!='')
                                            $map_descriptor.=$name;
                                        else
                                            $map_descriptor.='Unknown item';
                                        if($link_industry!='')
                                            $map_descriptor.=']]';
                                        $map_descriptor.='</div>'."\n";
                                    }
                                    else
                                        $map_descriptor.='Unknown item';
                                }
                                $map_descriptor.='</td>'."\n";
                                $map_descriptor.='<td>'."\n";
                                foreach($industry['products'] as $products)
                                {
                                    $item=$products['item'];
                                    $quantity=$products['quantity'];
                                    if(isset($item_meta[$item]))
                                    {
                                        $link_industry=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]);
                                        $name=$item_meta[$item]['name'][$current_lang];
                                        if($item_meta[$item]['image']!='')
                                            $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
                                        else
                                            $image='';
                                        $map_descriptor.='<div style="float:left;text-align:middle;">'."\n";
                                        if($image!='')
                                        {
                                            if($link_industry!='')
                                                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item]['name'][$current_lang].'|';
                                            $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                            if($link_industry!='')
                                                $map_descriptor.=']]';
                                        }
                                        if($link_industry!='')
                                            $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item]['name'][$current_lang].'|';
                                        if($name!='')
                                            $map_descriptor.=$name;
                                        else
                                            $map_descriptor.='Unknown item';
                                        if($link_industry!='')
                                            $map_descriptor.=']]';
                                        $map_descriptor.='</div>'."\n";
                                    }
                                    else
                                        $map_descriptor.='Unknown item';
                                }
                            }
                            $map_descriptor.='</td>'."\n";
                            $map_descriptor.='</tr>'."\n";
                            $map_descriptor.='<tr>
                                <td colspan="3" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>'."\n".'
                            </tr>
                            </table></center>'."\n";

                            $map_descriptor.='</td>'."\n";
                        }
                        else
                            $map_descriptor.='<td>'.$step['type'].'</td>'."\n".'<td>Unknown type ('.$step['type'].')</td>'."\n";
                        if($step['type']!='text')
                            $map_descriptor.='</tr>'."\n";
                    }
                }
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>'."\n".'
		</tr>
		</table></center>'."\n";
        savewikipage('Template:Maps/'.$map_html.'_BOT',$map_descriptor);$map_descriptor='';
	}
	
    if($wikivars['generatefullpage'])
    {
        $map_descriptor.='{{Template:Maps/'.$map_html.'_HEADER}}'."\n";
        if($maps_list[$map]['dropcount']>0 || count($maps_list[$map]['items'])>0)
            $map_descriptor.='{{Template:Maps/'.$map_html.'_ITEM}}'."\n";
        if(count($maps_list[$map]['monsters'])>0)
            $map_descriptor.='{{Template:Maps/'.$map_html.'_MONSTER}}'."\n";
        if(isset($maps_list[$map]['bots']) && count($maps_list[$map]['bots'])>0)
            $map_descriptor.='{{Template:Maps/'.$map_html.'_BOT}}'."\n";
        savewikipage($translation_list[$current_lang]['Maps:'].map_to_wiki_name($map),$map_descriptor);
    }
}

$map_descriptor='';
ksort($zone_to_map);

if(file_exists($datapack_explorer_local_path.'maps/overview.png') && file_exists($datapack_explorer_local_path.'maps/preview.png'))
{
    $size=getimagesize($datapack_explorer_local_path.'maps/preview.png');
    $maps_list[$map]['pixelwidth']=$size[0];
    $maps_list[$map]['pixelheight']=$size[1];
    if($maps_list[$map]['pixelwidth']>1600 || $maps_list[$map]['pixelheight']>800)
        $ratio=4;
    elseif($maps_list[$map]['pixelwidth']>800 || $maps_list[$map]['pixelheight']>400)
        $ratio=2;
    else
        $ratio=1;
    $map_descriptor.='<div class="value datapackscreenshot"><center>[['.$base_datapack_site_http.$base_datapack_explorer_site_path.'maps/overview.png <img src="'.$base_datapack_site_http.$base_datapack_explorer_site_path.'maps/preview.png" alt="Map overview" title="Map overview" width="'.($maps_list[$map]['pixelwidth']/$ratio).'" height="'.($maps_list[$map]['pixelheight']/$ratio).'" />]]
    <b>Size: '.round(filesize($datapack_explorer_local_path.'maps/overview.png')/1000000,1).'MB</b>
    </center></div>';
    savewikipage('Template:maps_preview',$map_descriptor);
    $map_descriptor='';
}

foreach($zone_to_map as $zone=>$map_by_zone)
{
	if(isset($zone_meta[$zone]))
		$zone_name=$zone_meta[$zone]['name'][$current_lang];
	elseif($zone=='')
		$zone_name='Unknown zone';
	else
		$zone_name=$zone;

	$map_descriptor.='<table class="item_list item_list_type_outdoor map_list"><tr class="item_list_title item_list_title_type_outdoor">
	<th>[['.$translation_list[$current_lang]['Zones:'].$zone_name.'|'.$zone_name.']]'."\n";
	$map_descriptor.='</th><th>'."\n";
	if(isset($zone_to_function[$zone]['shop']))
		$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>'."\n";
	if(isset($zone_to_function[$zone]['fight']))
		$map_descriptor.='<div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>'."\n";
	if(isset($zone_to_function[$zone]['heal']))
		$map_descriptor.='<div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>'."\n";
	if(isset($zone_to_function[$zone]['learn']))
		$map_descriptor.='<div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>'."\n";
	if(isset($zone_to_function[$zone]['warehouse']))
		$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>'."\n";
	if(isset($zone_to_function[$zone]['market']))
		$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>'."\n";
	if(isset($zone_to_function[$zone]['clan']))
		$map_descriptor.='<div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>'."\n";
	if(isset($zone_to_function[$zone]['sell']))
		$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>'."\n";
	if(isset($zone_to_function[$zone]['zonecapture']))
		$map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>'."\n";
    if(isset($zone_to_function[$zone]['industry']))
        $map_descriptor.='<div style="float:left;background-position:0px -32px;" class="flags flags16" title="Industry"></div>'."\n";
    if(isset($zone_to_function[$zone]['quests']))
        $map_descriptor.='<div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>'."\n";
	$map_descriptor.='</th></tr>';
	asort($map_by_zone);
	foreach($map_by_zone as $map=>$name)
	{
		$map_descriptor.='<tr class="value"><td>[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($map).'|'.$name[$current_lang].']]</td>'."\n".'<td>'."\n";
		if(isset($map_to_function[$map]['shop']))
			$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>'."\n";
		if(isset($map_to_function[$map]['fight']))
			$map_descriptor.='<div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>'."\n";
		if(isset($map_to_function[$map]['heal']))
			$map_descriptor.='<div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>'."\n";
		if(isset($map_to_function[$map]['learn']))
			$map_descriptor.='<div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>'."\n";
		if(isset($map_to_function[$map]['warehouse']))
			$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>'."\n";
		if(isset($map_to_function[$map]['market']))
			$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>'."\n";
		if(isset($map_to_function[$map]['clan']))
			$map_descriptor.='<div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>'."\n";
		if(isset($map_to_function[$map]['sell']))
			$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>'."\n";
		if(isset($map_to_function[$map]['zonecapture']))
			$map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>'."\n";
        if(isset($map_to_function[$map]['industry']))
            $map_descriptor.='<div style="float:left;background-position:0px -32px;" class="flags flags16" title="Industry"></div>'."\n";
        if(isset($map_to_function[$map]['quests']))
            $map_descriptor.='<div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>'."\n";
		$map_descriptor.='</td>'."\n".'</tr>'."\n";
	}
	$map_descriptor.='<tr>
	<td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>'."\n".'
	</tr></table>'."\n";
}
savewikipage('Template:maps_list',$map_descriptor);$map_descriptor='';

if($wikivars['generatefullpage'])
{
    $map_descriptor.='{{Template:maps_preview}}'."\n";
    $map_descriptor.='{{Template:maps_list}}'."\n";
    savewikipage($translation_list[$current_lang]['Maps list'],$map_descriptor);
}
