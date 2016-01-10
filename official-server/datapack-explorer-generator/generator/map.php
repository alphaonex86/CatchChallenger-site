<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator map'."\n");

$map_to_function=array();
$zone_to_function=array();
$zone_to_bot_count=array();
foreach($temp_maps as $maindatapackcode=>$map_list)
foreach($map_list as $map)
{
    $map_current_object=$maps_list[$maindatapackcode][$map];
	$map_html=$maindatapackcode.'/'.str_replace('.tmx','.html',$map);
	$map_image=$maindatapackcode.'/'.str_replace('.tmx','.png',$map);
	$map_folder='';
	if(preg_match('#/#isU',$map))
	{
		$map_folder=$maindatapackcode.'/'.preg_replace('#/[^/]+$#','',$map).'/';
		if(!is_dir($datapack_explorer_local_path.$translation_list[$current_lang]['maps/'].$map_folder))
			mkpath($datapack_explorer_local_path.$translation_list[$current_lang]['maps/'].$map_folder);
	}
    else
    {
        $map_folder=$maindatapackcode.'/';
        if(!is_dir($datapack_explorer_local_path.$translation_list[$current_lang]['maps/'].$map_folder))
            mkpath($datapack_explorer_local_path.$translation_list[$current_lang]['maps/'].$map_folder);
    }
	$map_descriptor='';

	$map_descriptor.='<div class="map map_type_'.$map_current_object['type'].'">';
		$map_descriptor.='<div class="subblock"><h1>'.$map_current_object['name'][$current_lang].'</h1>';
		if($map_current_object['type']!='')
			$map_descriptor.='<h3>('.$map_current_object['type'].')</h3>';
		if($map_current_object['shortdescription'][$current_lang]!='')
			$map_descriptor.='<h2>'.$map_current_object['shortdescription'][$current_lang].'</h2>';
		$map_descriptor.='</div>';
		if(file_exists($datapack_explorer_local_path.$translation_list[$current_lang]['maps/'].$map_image))
		{
            $size=@getimagesize($datapack_explorer_local_path.$translation_list[$current_lang]['maps/'].$map_image);
            if($size!==NULL)
            {
                $map_current_object['pixelwidth']=$size[0];
                $map_current_object['pixelheight']=$size[1];
                if($map_current_object['pixelwidth']>1600 || $map_current_object['pixelheight']>800)
                    $ratio=4;
                elseif($map_current_object['pixelwidth']>800 || $map_current_object['pixelheight']>400)
                    $ratio=2;
                else
                    $ratio=1;
                $map_descriptor.='<div class="value mapscreenshot datapackscreenshot"><a href="'.$base_datapack_explorer_site_path.'maps/'.$map_image.'"><center><img src="'.$base_datapack_explorer_site_path.'maps/'.$map_image.'" alt="Screenshot of '.$map_current_object['name'][$current_lang].'" title="Screenshot of '.$map_current_object['name'][$current_lang].'" width="'.($map_current_object['pixelwidth']/$ratio).'" height="'.($map_current_object['pixelheight']/$ratio).'" /></center></a></div>';
            }
		}
		if($map_current_object['description'][$current_lang]!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Map description'].'</div><div class="value">'.$map_current_object['description'][$current_lang].'</div></div>';

		if(isset($zone_meta[$maindatapackcode][$map_current_object['zone']]))
			$zone_name=$zone_meta[$maindatapackcode][$map_current_object['zone']]['name'][$current_lang];
		elseif(!isset($map['zone']) || $map_current_object['zone']=='')
			$zone_name='Unknown zone';
		else
			$zone_name=$map['zone'];
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Zone'].'</div><div class="value"><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['zones/'].$maindatapackcode.'/'.text_operation_do_for_url($zone_name).'.html" title="'.$zone_name.'">';
		$map_descriptor.=$zone_name.'</a></div></div>';

		if(count($map_current_object['borders'])>0 || count($map_current_object['doors'])>0 || count($map_current_object['tp'])>0)
		{
			$duplicate=array();
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Linked locations'].'</div><div class="value"><ul>';
			foreach($map_current_object['borders'] as $bordertype=>$border)
			{
				if(!isset($duplicate[$border]))
				{
					$duplicate[$border]='';
					if(isset($maps_list[$maindatapackcode][$border]))
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Border '.$bordertype].': <a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].$maindatapackcode.'/'.str_replace('.tmx','.html',$border).'">'.$maps_list[$maindatapackcode][$border]['name'][$current_lang].'</a></li>';
					else
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Border '.$bordertype].': <span class="mapnotfound">'.$border.'</span></li>';
				}
			}
			foreach($map_current_object['doors'] as $door)
			{
				if(!isset($duplicate[$door['map']]))
				{
					$duplicate[$door['map']]='';
					if(isset($maps_list[$maindatapackcode][$door['map']]))
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Door'].': <a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].$maindatapackcode.'/'.str_replace('.tmx','.html',$door['map']).'">'.$maps_list[$maindatapackcode][$door['map']]['name'][$current_lang].'</a></li>';
					else
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Door'].': <span class="mapnotfound">'.$door['map'].'</span></li>';
				}
			}
			foreach($map_current_object['tp'] as $tp)
			{
				if(!isset($duplicate[$tp]))
				{
					$duplicate[$tp]='';
					if(isset($maps_list[$maindatapackcode][$tp]))
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Teleporter'].': <a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].$maindatapackcode.'/'.str_replace('.tmx','.html',$tp).'">'.$maps_list[$maindatapackcode][$tp]['name'][$current_lang].'</a></li>';
					else
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Teleporter'].': <span class="mapnotfound">'.$tp.'</span></li>';
				}
			}
			$map_descriptor.='</ul></div></div>';
		}
	$map_descriptor.='</div>';

    if($map_current_object['dropcount']>0 || count($map_current_object['items'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$map_current_object['type'].'">
		<tr class="item_list_title item_list_title_type_'.$map_current_object['type'].'">
			<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
			<th>'.$translation_list[$current_lang]['Location'].'</th>
		</tr>';
        $droplist=array();
		$monster_list=$map_current_object['monsters_list'];
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
                $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html';
                $name=$item_meta[$item]['name'][$current_lang];
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
                    $map_descriptor.=$quantity_text.$translation_list[$current_lang]['Unknown item'];
                if($link!='')
                    $map_descriptor.='</a>';
                $map_descriptor.='</td>';
                $map_descriptor.='<td>'.$translation_list[$current_lang]['Drop on '];
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
                        $monster_html[]='<a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$monster]['name'][$current_lang]).'.html" title="'.$monster_meta[$monster]['name'][$current_lang].'">'.$monster_meta[$monster]['name'][$current_lang].'</a>';
                    $monster_drops_html[]=implode(', ',$monster_html).' '.str_replace('[luck]',$luck,$translation_list[$current_lang]['with luck of [luck]%']);
                }
                $map_descriptor.=implode(', ',$monster_drops_html);
                $map_descriptor.='</td>
                </tr>';
            }
        }

        foreach($map_current_object['items'] as $item)
        {
            $visible=$item['visible'];
            $item=$item['item'];
            if(isset($item_meta[$item]))
            {
                $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html';
                $name=$item_meta[$item]['name'][$current_lang];
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
                    $map_descriptor.=$translation_list[$current_lang]['Unknown ite'].'m';
                if($link!='')
                    $map_descriptor.='</a>';
                $map_descriptor.='</td>';
                if($visible)
                    $map_descriptor.='<td>'.$translation_list[$current_lang]['On the map'].'</td>';
                else
                    $map_descriptor.='<td>'.$translation_list[$current_lang]['Hidden on the map'].'</td>';
                $map_descriptor.='</tr>';
            }
        }

		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>
		</tr>
		</table>';
	}

	if(count($map_current_object['monsters'])>0)
	{
        //test if have sub type
        $subTypeDuplicate=array('');
        $subTypeCount=1;
        foreach($map_current_object['monsters'] as $monsterType=>$monster_list_temp)
            foreach($monster_list_temp as $subdatapackcode=>$monster_list)
                if($subdatapackcode!='' && !in_array($subdatapackcode,$subTypeDuplicate))
                {
                    $subTypeDuplicate[]=$subdatapackcode;
                    $subTypeCount++;
                }
        sort($subTypeDuplicate);
        $specversion=false;
        if($subTypeCount>1 && isset($informations_meta['main'][$maindatapackcode]['sub']) && count($informations_meta['main'][$maindatapackcode]['sub'])==($subTypeCount-1))
        {
            $specversion=false;
            foreach($map_current_object['monsters'] as $monsterType=>$monster_list_temp)
            {
                $deduplicated_monster_list=array();
                foreach($monster_list_temp as $subdatapackcode=>$monster_list)
                {
                    if($subdatapackcode!='')
                    {
                        if(count($monster_list_temp[''])!=count($monster_list_temp[$subdatapackcode]))
                        {
                            $specversion=true;
                            break;
                            break;
                        }
                        foreach($monster_list as $index=>$monster)
                        {
                            if($monster_list_temp[''][$index]['id']==$monster_list_temp[$subdatapackcode][$index]['id'] && 
                            $monster_list_temp[''][$index]['minLevel']==$monster_list_temp[$subdatapackcode][$index]['minLevel'] && 
                            $monster_list_temp[''][$index]['maxLevel']==$monster_list_temp[$subdatapackcode][$index]['maxLevel'] && 
                            $monster_list_temp[''][$index]['luck']==$monster_list_temp[$subdatapackcode][$index]['luck'])
                            {
                            }
                            else
                            {
                                $specversion=true;
                                break;
                                break;
                                break;
                            }
                        }
                    }
                }
            }
/*
                if(count($monster_list_temp)!=$subTypeCount)
                {

                    $specversion=true;
                    break;
                }*/
        }
		$map_descriptor.='<table class="item_list item_list_type_'.$map_current_object['type'].'">
		<tr class="item_list_title item_list_title_type_'.$map_current_object['type'].'">
			<th colspan="2">'.$translation_list[$current_lang]['Monster'].'</th>
			<th>'.$translation_list[$current_lang]['Location'].'</th>';
            if($specversion)
                $map_descriptor.='<th>'.$translation_list[$current_lang]['Variations'].'</th>';
			$map_descriptor.='<th>'.$translation_list[$current_lang]['Levels'].'</th>
			<th colspan="3">'.$translation_list[$current_lang]['Rate'].'</th>
		</tr>';
        foreach($map_current_object['monsters'] as $monsterType=>$monster_list_temp)
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
            $map_descriptor.='<tr class="item_list_title_type_'.$map_current_object['type'].'">
                    <th colspan="';
            if($specversion)
                $map_descriptor.='8';
            else
                $map_descriptor.='7';
            $map_descriptor.='">';
            $link='';
            $name='';
            $image='';
            if(isset($layer_meta[$monsterType_top]['item']) && $item_meta[$layer_meta[$monsterType_top]['item']])
            {
                $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang]).'.html';
                $name=$item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang];
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
                $map_descriptor.=$item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang];
                if($link!='')
                    $map_descriptor.='</a></td>';

                $map_descriptor.='</tr></table></center>';
            }
            else
                $map_descriptor.=$translation_list[$current_lang][$full_monsterType_name];
            if(isset($layer_event[$monsterType]))
            {
                if($layer_event[$monsterType]['id']=='day' && $layer_event[$monsterType]['value']=='night')
                    $map_descriptor.=' '.$translation_list[$current_lang]['at night'];
                else
                    $map_descriptor.=str_replace('[value]',$layer_event[$monsterType]['value'],str_replace('[condition]',$layer_event[$monsterType]['id'],$translation_list[$current_lang][' condition [condition] at [value]']));
            }
            $map_descriptor.='</th>
                </tr>';
            // group the monster by sub + deduplicate
            $deduplicated_monster_list=array();
            foreach($monster_list_temp as $subdatapackcode=>$monster_list)
            {
                if(!is_array($monster_list))
                {
                    print_r($monster_list);
                    die('not an array, abort');
                }
                foreach($monster_list as $monster)
                {
                    $monsterfound=false;
                    foreach($deduplicated_monster_list as $temp_id_deduplicate=>$monster2)
                    {
                        if($monster['id']==$monster2['id'] && $monster['minLevel']==$monster2['minLevel'] && $monster['maxLevel']==$monster2['maxLevel'] && $monster['luck']==$monster2['luck'])
                        {
                            if(!in_array($subdatapackcode,$deduplicated_monster_list[$temp_id_deduplicate]['sub']))
                            {
                                $deduplicated_monster_list[$temp_id_deduplicate]['sub'][]=$subdatapackcode;
                                sort($deduplicated_monster_list[$temp_id_deduplicate]['sub']);
                            }
                            $monsterfound=true;
                            break;
                        }
                    }
                    if($monsterfound==false)
                    {
                        $monster['sub']=array($subdatapackcode);
                        $deduplicated_monster_list[]=$monster;
                    }
                }
            }
            uasort($deduplicated_monster_list,'monsterMapOrderGreater');
            foreach($deduplicated_monster_list as $monster)
            {
                if(isset($monster_meta[$monster['id']]))
                {
                    $name=$monster_meta[$monster['id']]['name'][$current_lang];
                    $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html';
                    $map_descriptor.='<tr class="value">
                        <td>';
                        if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.png'))
                            $map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.png" width="32" height="32" alt="'.$name.'" title="'.$name.'" /></a></div>';
                        else if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.gif'))
                            $map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.gif" width="32" height="32" alt="'.$name.'" title="'.$name.'" /></a></div>';
                        $map_descriptor.='</td>
                        <td><a href="'.$link.'">'.$name.'</a></td>
                        <td>';
                        $map_descriptor.='<img src="/images/datapack-explorer/'.$full_monsterType_name_top.'.png" alt="" class="locationimg">'.$translation_list[$current_lang][$full_monsterType_name_top];
                        $map_descriptor.='</td>';
                        
                        if($specversion)
                        {
                            $map_descriptor.='<td>';
                            foreach($subTypeDuplicate as $tempSub)
                            {
                                $text_temp_sub='?';
                                $color_temp_sub='';
                                if($tempSub=='')
                                {
                                    if(isset($informations_meta['main'][$maindatapackcode]))
                                    {
                                        $text_temp_sub=$informations_meta['main'][$maindatapackcode]['initial'];
                                        $color_temp_sub=$informations_meta['main'][$maindatapackcode]['color'];
                                    }
                                }
                                else
                                {
                                    if(isset($informations_meta['main'][$maindatapackcode]['sub'][$tempSub]))
                                    {
                                        $text_temp_sub=$informations_meta['main'][$maindatapackcode]['sub'][$tempSub]['initial'];
                                        $color_temp_sub=$informations_meta['main'][$maindatapackcode]['sub'][$tempSub]['color'];
                                    }
                                }
                                if($color_temp_sub!='')
                                {
                                    if(in_array($tempSub,$monster['sub']))
                                        $map_descriptor.='<span style="background-color:'.$color_temp_sub.';" class="datapackinital">'.$text_temp_sub.'</span>';
                                    else
                                        $map_descriptor.='<span style="color:'.$color_temp_sub.';" class="datapackinital">'.$text_temp_sub.'</span>';
                                }
                                else
                                {
                                    if(in_array($tempSub,$monster['sub']))
                                        $map_descriptor.='<span class="datapackinital">'.$text_temp_sub.'</span>';
                                    else
                                        $map_descriptor.='<span style="text-decoration:line-through;" class="datapackinital">'.$text_temp_sub.'</span>';
                                }
                            }
                            $map_descriptor.='</td>';
                        }

                        $map_descriptor.='<td>';
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
			<td colspan="';
        if($specversion)
            $map_descriptor.='8';
        else
            $map_descriptor.='7';
        $map_descriptor.='" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>
		</tr>
		</table>';
	}
    if(isset($map_current_object['bots']) && count($map_current_object['bots'])>0)
	{
		$map_descriptor.='<center><table class="item_list item_list_type_'.$map_current_object['type'].'">
		<tr class="item_list_title item_list_title_type_'.$map_current_object['type'].'">
			<th colspan="2">'.$translation_list[$current_lang]['Bot'].'</th>
			<th>'.$translation_list[$current_lang]['Type'].'</th>
			<th>'.$translation_list[$current_lang]['Content'].'</th>
		</tr>';
		foreach($map_current_object['bots'] as $bot_on_map)
		{
            if(isset($bot_id_to_skin[$bot_id][$maindatapackcode]))
            {
                if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png'))
                    $have_skin=true;
                elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png'))
                    $have_skin=true;
                elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif'))
                    $have_skin=true;
                elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif'))
                    $have_skin=true;
                else
                    $have_skin=false;
            }
            else
                $have_skin=false;
            if($have_skin)
            {
                if(!isset($zone_to_bot_count[$maindatapackcode][$map_current_object['zone']]))
                    $zone_to_bot_count[$maindatapackcode][$map_current_object['zone']]=1;
                else
                    $zone_to_bot_count[$maindatapackcode][$map_current_object['zone']]++;
            }

            if(isset($bot_start_to_quests[$bot_id]))
            {
                if(!isset($map_to_function[$maindatapackcode][$map]))
                    $map_to_function[$maindatapackcode][$map]=array();
                if(!isset($map_to_function[$maindatapackcode][$map]['quests']))
                    $map_to_function[$maindatapackcode][$map]['quests']=count($bot_start_to_quests[$bot_id]);
                else
                    $map_to_function[$maindatapackcode][$map]['quests']+=count($bot_start_to_quests[$bot_id]);

                if(!isset($zone_to_function[$maindatapackcode][$map_current_object['zone']]))
                    $zone_to_function[$maindatapackcode][$map_current_object['zone']]=array();
                if(!isset($zone_to_function[$maindatapackcode][$map_current_object['zone']]['quests']))
                    $zone_to_function[$maindatapackcode][$map_current_object['zone']]['quests']=count($bot_start_to_quests[$bot_id]);
                else
                    $zone_to_function[$maindatapackcode][$map_current_object['zone']]['quests']+=count($bot_start_to_quests[$bot_id]);
            }

			if(isset($bots_meta[$maindatapackcode][$bot_on_map['id']]))
			{
                $bot_id=$bot_on_map['id'];
                $bot=$bots_meta[$maindatapackcode][$bot_id];
				if($bot['name'][$current_lang]=='')
					$link=text_operation_do_for_url('bot '.$bot_on_map['id']);
				else if($bots_name_count[$maindatapackcode][$current_lang][$bot['name'][$current_lang]]==1)
					$link=text_operation_do_for_url($bot['name'][$current_lang]);
				else
					$link=text_operation_do_for_url($bot_on_map['id'].'-'.$bot['name'][$current_lang]);
				if($bot['onlytext']==true)
				{
					$map_descriptor.='<tr class="value">';
					$have_skin=true;
					if(isset($bot_id_to_skin[$bot_id][$maindatapackcode]))
					{
						if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>';
						elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>';
						elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>';
						elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>';
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
						$map_descriptor.='><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['bots/'].$maindatapackcode.'/'.$link.'.html" title="Bot #'.$bot_id.'">Bot #'.$bot_id.'</a></td>';
					else
						$map_descriptor.='><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['bots/'].$maindatapackcode.'/'.$link.'.html" title="'.$bot['name'][$current_lang].'">'.$bot['name'][$current_lang].'</a></td>';
                    if(!isset($bot_start_to_quests[$bot_id]))
                        $map_descriptor.='<td>'.$translation_list[$current_lang]['Text only'].'</td>';
                    else
                    {
                        $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Quests'].'
                        <div style="background-position:-32px 0px;" class="flags flags32"></div></center></td>';
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
                                if(!isset($map_to_function[$maindatapackcode][$map]))
                                    $map_to_function[$maindatapackcode][$map]=array();
                                if(!isset($map_to_function[$maindatapackcode][$map][$step['type']]))
                                    $map_to_function[$maindatapackcode][$map][$step['type']]=1;
                                else
                                    $map_to_function[$maindatapackcode][$map][$step['type']]++;

                                if(!isset($zone_to_function[$maindatapackcode][$map_current_object['zone']]))
                                    $zone_to_function[$maindatapackcode][$map_current_object['zone']]=array();
                                if(!isset($zone_to_function[$maindatapackcode][$map_current_object['zone']][$step['type']]))
                                    $zone_to_function[$maindatapackcode][$map_current_object['zone']][$step['type']]=1;
                                else
                                    $zone_to_function[$maindatapackcode][$map_current_object['zone']][$step['type']]++;
                            }

                            $map_descriptor.='<tr class="value">';
                            $have_skin=true;
                            if(isset($bot_id_to_skin[$bot_id][$maindatapackcode]))
                            {
                                if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png'))
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
                                elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png'))
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
                                elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif'))
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
                                elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif'))
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
                                else
                                    $have_skin=false;
                            }
                            else
                                $have_skin=false;
                            $map_descriptor.='<td';
                            if(!$have_skin)
                                $map_descriptor.=' colspan="2"';
                            if($bot['name'][$current_lang]=='')
                                $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['bots/'].$maindatapackcode.'/'.$link.'.html" title="Bot #'.$bot_id.'">Bot #'.$bot_id.'</a></td>';
                            else
                                $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['bots/'].$maindatapackcode.'/'.$link.'.html" title="'.$bot['name'][$current_lang].'">'.$bot['name'][$current_lang].'</a></td>';
                        }
                        if($step['type']=='text')
                        {}
                        else if($step['type']=='shop')
                        {
                            $map_descriptor.='<td><center>Shop<div style="background-position:-32px 0px;" class="flags flags16"></div></center></td><td>';
                            $map_descriptor.='<center><table class="item_list item_list_type_'.$map_current_object['type'].'">
                            <tr class="item_list_title item_list_title_type_'.$map_current_object['type'].'">
                                <th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
                                <th>'.$translation_list[$current_lang]['Price'].'</th>
                            </tr>';
                            foreach($shop_meta[$maindatapackcode][$step['shop']]['products'] as $item=>$price)
                            {
                                if(isset($item_meta[$item]))
                                {
                                    $map_descriptor.='<tr class="value">';
                                    if(isset($item_meta[$item]))
                                    {
                                        $link_item=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html';
                                        $name=$item_meta[$item]['name'][$current_lang];
                                        if($item_meta[$item]['image']!='')
                                            $image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
                                        else
                                            $image='';
                                    }
                                    else
                                    {
                                        $link_item='';
                                        $name='';
                                        $image='';
                                    }
                                    $map_descriptor.='<tr class="value">
                                    <td>';
                                    if($image!='')
                                    {
                                        if($link_item!='')
                                            $map_descriptor.='<a href="'.$link_item.'">';
                                        $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                        if($link_item!='')
                                            $map_descriptor.='</a>';
                                    }
                                    $map_descriptor.='</td>
                                    <td>';
                                    if($link_item!='')
                                        $map_descriptor.='<a href="'.$link_item.'">';
                                    if($name!='')
                                        $map_descriptor.=$name;
                                    else
                                        $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                                    if($link_item!='')
                                        $map_descriptor.='</a>';
                                    $map_descriptor.='</td>';
                                    $map_descriptor.='<td>'.$price.'$</td>';
                                    $map_descriptor.='</tr>';
                                }
                            }
                            $map_descriptor.='<tr>
                                <td colspan="3" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>
                            </tr>
                            </table>';
                            $map_descriptor.='</center></td>';
                        }
                        else if($step['type']=='fight')
                        {
                            if(isset($fight_meta[$maindatapackcode][$step['fightid']]))
                            {
                                $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Fight'].'<div style="background-position:-16px -16px;" class="flags flags16"></div></center></td><td>';
                                if($step['leader'])
                                {
                                    $map_descriptor.='<b>'.$translation_list[$current_lang]['Leader'].'</b><br />';
                                    if(isset($bot_id_to_skin[$bot_id][$maindatapackcode]))
                                    {
                                        if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.png'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.png" width="80" height="80" alt="" /></center>';
                                        else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.png'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.png" width="80" height="80" alt="" /></center>';
                                        elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.gif'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.gif" width="80" height="80" alt="" /></center>';
                                        else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.gif'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.gif" width="80" height="80" alt="" /></center>';
                                    }
                                }
                                if($fight_meta[$maindatapackcode][$step['fightid']]['cash']>0)
                                    $map_descriptor.=$translation_list[$current_lang]['Rewards'].': <b>'.$fight_meta[$maindatapackcode][$step['fightid']]['cash'].'$</b><br />';

                                if(count($fight_meta[$maindatapackcode][$step['fightid']]['items'])>0)
                                {
                                    $map_descriptor.='<center><table class="item_list item_list_type_'.$map_current_object['type'].'">
                                    <tr class="item_list_title item_list_title_type_'.$map_current_object['type'].'">
                                        <th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
                                    </tr>';
                                    foreach($fight_meta[$maindatapackcode][$step['fightid']]['items'] as $item)
                                    {
                                        if(isset($item_meta[$item['item']]))
                                        {
                                            $link_item=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item['item']]['name'][$current_lang]).'.html';
                                            $name=$item_meta[$item['item']]['name'][$current_lang];
                                            if($item_meta[$item['item']]['image']!='')
                                                $image=$base_datapack_site_path.'/items/'.$item_meta[$item['item']]['image'];
                                            else
                                                $image='';
                                        }
                                        else
                                        {
                                            $link_item='';
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
                                                if($link_item!='')
                                                    $map_descriptor.='<a href="'.$link_item.'">';
                                                $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                                if($link_item!='')
                                                    $map_descriptor.='</a>';
                                            }
                                            $map_descriptor.='</td>
                                            <td>';
                                            if($link_item!='')
                                                $map_descriptor.='<a href="'.$link_item.'">';
                                            if($name!='')
                                                $map_descriptor.=$quantity_text.$name;
                                            else
                                                $map_descriptor.=$quantity_text.$translation_list[$current_lang]['Unknown item'];
                                            if($link_item!='')
                                                $map_descriptor.='</a>';
                                            $map_descriptor.='</td>';
                                            $map_descriptor.='</tr>';
                                    }
                                    $map_descriptor.='<tr>
                                        <td colspan="2" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>
                                    </tr>
                                    </table></center>';
                                }

                                foreach($fight_meta[$maindatapackcode][$step['fightid']]['monsters'] as $monster)
                                    $map_descriptor.=monsterAndLevelToDisplay($monster,$step['leader']);
                                $map_descriptor.='<br style="clear:both;" />';

                                $map_descriptor.='</td>';
                            }
                        }
                        else if($step['type']=='heal')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Heal'].'</center></td>
                            <td><center><div style="background-position:0px 0px;" class="flags flags64"></div></center></td>';
                        }
                        else if($step['type']=='learn')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Learn'].'</center></td>
                            <td><center><div style="background-position:-192px 0px;" class="flags flags64"></div></center></td>';
                        }
                        else if($step['type']=='warehouse')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Warehouse'].'</center></td>
                            <td><center><div style="background-position:0px -64px;" class="flags flags64"></div></center></td>';
                        }
                        else if($step['type']=='market')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Market'].'</center></td>
                            <td><center><div style="background-position:0px -64px;" class="flags flags64"></div></center></td>';
                        }
                        else if($step['type']=='quests' && isset($bot_start_to_quests[$bot_id]))
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Quests'].'</center></td>
                            <td><center><div style="background-position:-32px 0px;" class="flags flags32"></div></center></td>';
                        }
                        else if($step['type']=='clan')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Clan'].'</center></td>
                            <td><center><div style="background-position:-192px -64px;" class="flags flags64"></div></center></td>';
                        }
                        else if($step['type']=='sell')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Sell'].'</center></td>
                            <td><center><div style="background-position:-128px 0px;" class="flags flags64"></div></center></td>';
                        }
                        else if($step['type']=='zonecapture')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Zone capture'].'</center></td>
                            <td><center><div style="background-position:-128px -64px;" class="flags flags64"></div></center></td>';
                        }
                        else if($step['type']=='industry')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Industry'].'<div style="background-position:0px -32px;" class="flags flags16"></div></center></td><td>';

                            if(!isset($industrie_meta[$step['industry']]))
                            {
                                $map_descriptor.='Industry '.$step['industry'].' not found for map '.$bot_id.'!</td>';
                                echo 'Industry '.$step['industry'].' not found for map '.$bot_id.'!'."\n";
                            }
                            else
                            {
                                $map_descriptor.='<center><table class="item_list item_list_type_'.$map_current_object['type'].'">
                                <tr class="item_list_title item_list_title_type_'.$map_current_object['type'].'">
                                    <th>'.$translation_list[$current_lang]['Industry'].'</th>
                                    <th>'.$translation_list[$current_lang]['Resources'].'</th>
                                    <th>'.$translation_list[$current_lang]['Products'].'</th>
                                </tr>';
                                $industry=$industrie_meta[$step['industry']];
                                $map_descriptor.='<tr class="value">';
                                $map_descriptor.='<td>';
                                $map_descriptor.='<a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['industries/'].$step['industry'].'.html">#'.$step['industry'].'</a>';
                                $map_descriptor.='</td>';
                                $map_descriptor.='<td>';
                                foreach($industry['resources'] as $resources)
                                {
                                    $item=$resources['item'];
                                    $quantity=$resources['quantity'];
                                    if(isset($item_meta[$item]))
                                    {
                                        $link_industry=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html';
                                        $name=$item_meta[$item]['name'][$current_lang];
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
                                            $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                                        if($link_industry!='')
                                            $map_descriptor.='</a>';
                                        $map_descriptor.='</div>';
                                    }
                                    else
                                        $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                                }
                                $map_descriptor.='</td>';
                                $map_descriptor.='<td>';
                                foreach($industry['products'] as $products)
                                {
                                    $item=$products['item'];
                                    $quantity=$products['quantity'];
                                    if(isset($item_meta[$item]))
                                    {
                                        $link_industry=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html';
                                        $name=$item_meta[$item]['name'][$current_lang];
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
                                            $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                                        if($link_industry!='')
                                            $map_descriptor.='</a>';
                                        $map_descriptor.='</div>';
                                    }
                                    else
                                        $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                                }
                            }
                            $map_descriptor.='</td>';
                            $map_descriptor.='</tr>';
                            $map_descriptor.='<tr>
                                <td colspan="3" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>
                            </tr>
                            </table></center>';

                            $map_descriptor.='</td>';
                        }
                        else
                            $map_descriptor.='<td>'.$step['type'].'</td><td>'.$translation_list[$current_lang]['Unknown type'].' ('.$step['type'].')</td>';
                        if($step['type']!='text')
                            $map_descriptor.='</tr>';
                    }
                }
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>
		</tr>
		</table></center>';
	}
	
	$content=$template;
	$content=str_replace('${TITLE}',$map_current_object['name'][$current_lang],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=clean_html($content);
	filewrite($datapack_explorer_local_path.$translation_list[$current_lang]['maps/'].$map_html,$content);
}

$map_descriptor='';
ksort($zone_to_map);

$mapoverviewindex=1;
while(file_exists($datapack_explorer_local_path.'maps/overview-'.$mapoverviewindex.'.png') && file_exists($datapack_explorer_local_path.'maps/preview-'.$mapoverviewindex.'.png'))
{
    $size=getimagesize($datapack_explorer_local_path.'maps/preview-'.$mapoverviewindex.'.png');
    $map_current_object['pixelwidth']=$size[0];
    $map_current_object['pixelheight']=$size[1];
    if($map_current_object['pixelwidth']>1600 || $map_current_object['pixelheight']>800)
        $ratio=4;
    elseif($map_current_object['pixelwidth']>800 || $map_current_object['pixelheight']>400)
        $ratio=2;
    else
        $ratio=1;
    $map_descriptor.='<div class="value datapackscreenshot"><a href="'.$base_datapack_explorer_site_path.'maps/overview-'.$mapoverviewindex.'.png"><center><img src="'.$base_datapack_explorer_site_path.'maps/preview-'.$mapoverviewindex.'.png" alt="Map overview" title="Map overview" width="'.($map_current_object['pixelwidth']/$ratio).'" height="'.($map_current_object['pixelheight']/$ratio).'" />
    <b>'.$translation_list[$current_lang]['Size'].': '.round(filesize($datapack_explorer_local_path.'maps/overview-'.$mapoverviewindex.'.png')/1000000,1).$translation_list[$current_lang]['MB'].'</b>
    </center></a></div>';
    $mapoverviewindex++;
}

foreach($zone_to_map as $maindatapackcode=>$zonetempthis)
foreach($zonetempthis as $zone=>$map_by_zone)
{
	if(isset($zone_meta[$maindatapackcode][$zone]))
		$zone_name=$zone_meta[$maindatapackcode][$zone]['name'][$current_lang];
	elseif($zone=='')
		$zone_name=$translation_list[$current_lang]['Unknown zone'];
	else
		$zone_name=$zone;

	$map_descriptor.='<table class="item_list item_list_type_outdoor map_list"><tr class="item_list_title item_list_title_type_outdoor">
	<th><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['zones/'].$maindatapackcode.'/'.text_operation_do_for_url($zone_name).'.html" title="'.$zone_name.'">';
	$map_descriptor.=$zone_name;
	$map_descriptor.='</a></th>';
    if(isset($zone_to_function[$maindatapackcode][$zone]))
        $additionnal_function=count($zone_to_function[$maindatapackcode][$zone])>0;
    else
        $additionnal_function=false;
    if($additionnal_function)
    {
        $map_descriptor.='<th>';
        if(isset($zone_to_function[$maindatapackcode][$zone]['shop']))
            $map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>';
        if(isset($zone_to_function[$maindatapackcode][$zone]['fight']))
            $map_descriptor.='<div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>';
        if(isset($zone_to_function[$maindatapackcode][$zone]['heal']))
            $map_descriptor.='<div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>';
        if(isset($zone_to_function[$maindatapackcode][$zone]['learn']))
            $map_descriptor.='<div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>';
        if(isset($zone_to_function[$maindatapackcode][$zone]['warehouse']))
            $map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>';
        if(isset($zone_to_function[$maindatapackcode][$zone]['market']))
            $map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>';
        if(isset($zone_to_function[$maindatapackcode][$zone]['clan']))
            $map_descriptor.='<div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>';
        if(isset($zone_to_function[$maindatapackcode][$zone]['sell']))
            $map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>';
        if(isset($zone_to_function[$maindatapackcode][$zone]['zonecapture']))
            $map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>';
        if(isset($zone_to_function[$maindatapackcode][$zone]['industry']))
            $map_descriptor.='<div style="float:left;background-position:0px -32px;" class="flags flags16" title="Industry"></div>';
        if(isset($zone_to_function[$maindatapackcode][$zone]['quests']))
            $map_descriptor.='<div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>';
        $map_descriptor.='</th>';
    }
    $map_descriptor.='</tr>';
	asort($map_by_zone);
	foreach($map_by_zone as $map=>$name)
	{
		$map_descriptor.='<tr class="value"><td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].$maindatapackcode.'/'.str_replace('.tmx','.html',$map).'" title="'.$name[$current_lang].'">'.$name[$current_lang].'</a></td>';
        if($additionnal_function)
        {
            $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].$maindatapackcode.'/'.str_replace('.tmx','.html',$map).'" title="'.$name[$current_lang].'">';
            if(isset($map_to_function[$maindatapackcode][$map]['shop']))
                $map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>';
            if(isset($map_to_function[$maindatapackcode][$map]['fight']))
                $map_descriptor.='<div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>';
            if(isset($map_to_function[$maindatapackcode][$map]['heal']))
                $map_descriptor.='<div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>';
            if(isset($map_to_function[$maindatapackcode][$map]['learn']))
                $map_descriptor.='<div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>';
            if(isset($map_to_function[$maindatapackcode][$map]['warehouse']))
                $map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>';
            if(isset($map_to_function[$maindatapackcode][$map]['market']))
                $map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>';
            if(isset($map_to_function[$maindatapackcode][$map]['clan']))
                $map_descriptor.='<div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>';
            if(isset($map_to_function[$maindatapackcode][$map]['sell']))
                $map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>';
            if(isset($map_to_function[$maindatapackcode][$map]['zonecapture']))
                $map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>';
            if(isset($map_to_function[$maindatapackcode][$map]['industry']))
                $map_descriptor.='<div style="float:left;background-position:0px -32px;" class="flags flags16" title="Industry"></div>';
            if(isset($map_to_function[$maindatapackcode][$map]['quests']))
                $map_descriptor.='<div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>';
            $map_descriptor.='</a></td>';
        }
        $map_descriptor.='</tr>';
	}
	$map_descriptor.='<tr>
	<td ';
    if($additionnal_function)
        $map_descriptor.=' colspan="2"';
    $map_descriptor.=' class="item_list_endline item_list_title_type_outdoor"></td>
	</tr></table>';
}
$content=$template;
$content=str_replace('${TITLE}',$translation_list[$current_lang]['Maps list'],$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=clean_html($content);
filewrite($datapack_explorer_local_path.$translation_list[$current_lang]['maps.html'],$content);
