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
	$map_html=$maindatapackcode.'/'.str_replace('.tmx','',$map);
	$map_image=$maindatapackcode.'/'.str_replace('.tmx','.png',$map);
	$map_folder='';
	if(preg_match('#/#isU',$map))
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
	$map_descriptor='';

	$map_descriptor.='<div class="map map_type_'.$map_current_object['type'].'">'."\n";
		$map_descriptor.='<div class="subblock"><h1>'.$map_current_object['name'][$current_lang].'</h1>'."\n";
		if($map_current_object['type']!='')
			$map_descriptor.='<h3>('.$map_current_object['type'].')</h3>'."\n";
		if($map_current_object['shortdescription'][$current_lang]!='')
			$map_descriptor.='<h2>'.$map_current_object['shortdescription'][$current_lang].'</h2>'."\n";
		$map_descriptor.='</div>'."\n";
		if(file_exists($datapack_explorer_local_path.'maps/'.$map_image))
		{
            $size=getimagesize($datapack_explorer_local_path.'maps/'.$map_image);
            $map_current_object['pixelwidth']=$size[0];
            $map_current_object['pixelheight']=$size[1];
			if($map_current_object['pixelwidth']>1600 || $map_current_object['pixelheight']>800)
				$ratio=4;
			elseif($map_current_object['pixelwidth']>800 || $map_current_object['pixelheight']>400)
				$ratio=2;
			else
				$ratio=1;
			$map_descriptor.='<div class="value mapscreenshot datapackscreenshot"><center>[['.$base_datapack_site_http.$base_datapack_explorer_site_path.'maps/'.$map_image.' <img src="'.$base_datapack_site_http.$base_datapack_explorer_site_path.'maps/'.$map_image.'" alt="Screenshot of '.$map_current_object['name'][$current_lang].'" title="Screenshot of '.$map_current_object['name'][$current_lang].'" width="'.($map_current_object['pixelwidth']/$ratio).'" height="'.($map_current_object['pixelheight']/$ratio).'" />]]</center></div>'."\n";
		}
		if($map_current_object['description'][$current_lang]!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Map description</div><div class="value">'.$map_current_object['description'][$current_lang].'</div></div>'."\n";

		if(isset($zone_meta[$maindatapackcode][$map_current_object['zone']]))
			$zone_name=$zone_meta[$maindatapackcode][$map_current_object['zone']]['name'][$current_lang];
		elseif(!isset($map['zone']) || $map_current_object['zone']=='')
			$zone_name='Unknown zone';
		else
			$zone_name=$map['zone'];
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Zone'].'</div><div class="value">[['.$translation_list[$current_lang]['Zones:'].$zone_name.'|';
		$map_descriptor.=$zone_name.']]</div></div>'."\n";

		if(count($map_current_object['borders'])>0 || count($map_current_object['doors'])>0 || count($map_current_object['tp'])>0)
		{
			$duplicate=array();
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Linked locations'].'</div><div class="value"><ul>'."\n";
			foreach($map_current_object['borders'] as $bordertype=>$border)
			{
				if(!isset($duplicate[$border]))
				{
					$duplicate[$border]='';
					if(isset($maps_list[$maindatapackcode][$border]))
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Border '.$bordertype].': [['.$translation_list[$current_lang]['Maps:'].$maindatapackcode.'-'.map_to_wiki_name($border).'|'.$maps_list[$maindatapackcode][$border]['name'][$current_lang].']]</li>'."\n";
					else
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Border '.$bordertype].': <span class="mapnotfound">'.$maindatapackcode.'-'.$border.'</span></li>'."\n";
				}
			}
			foreach($map_current_object['doors'] as $door)
			{
				if(!isset($duplicate[$door['map']]))
				{
					$duplicate[$door['map']]='';
					if(isset($maps_list[$maindatapackcode][$door['map']]))
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Door'].': [['.$translation_list[$current_lang]['Maps:'].$maindatapackcode.'-'.map_to_wiki_name($door['map']).'|'.$maps_list[$maindatapackcode][$door['map']]['name'][$current_lang].']]</li>'."\n";
					else
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Door'].': <span class="mapnotfound">'.$maindatapackcode.'-'.$door['map'].'</span></li>'."\n";
				}
			}
			foreach($map_current_object['tp'] as $tp)
			{
				if(!isset($duplicate[$tp]))
				{
					$duplicate[$tp]='';
					if(isset($maps_list[$maindatapackcode][$tp]))
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Teleporter'].': [['.$translation_list[$current_lang]['Maps:'].$maindatapackcode.'-'.map_to_wiki_name($tp).'|'.$maps_list[$maindatapackcode][$tp]['name'][$current_lang].']]</li>'."\n";
					else
						$map_descriptor.='<li>'.$translation_list[$current_lang]['Teleporter'].': <span class="mapnotfound">'.$maindatapackcode.'-'.$tp.'</span></li>'."\n";
				}
			}
			$map_descriptor.='</ul></div></div>'."\n";
		}
	$map_descriptor.='</div>';
    savewikipage('Template:Maps/'.$map_html.'_HEADER',$map_descriptor,false);$map_descriptor='';

    if($map_current_object['dropcount']>0 || count($map_current_object['items'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$map_current_object['type'].'">
		<tr class="item_list_title item_list_title_type_'.$map_current_object['type'].'">
			<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
			<th>'.$translation_list[$current_lang]['Location'].'</th>
		</tr>'."\n";
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
                        $monster_html[]='[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$monster]['name'][$current_lang].'|'.$monster_meta[$monster]['name'][$current_lang].']]';
                    $monster_drops_html[]=implode(', ',$monster_html).' '.str_replace('[luck]',$luck,$translation_list[$current_lang]['with luck of [luck]%']);
                }
                $map_descriptor.=implode(', ',$monster_drops_html);
                $map_descriptor.='</td>'."\n".'
                </tr>'."\n";
            }
        }

        foreach($map_current_object['items'] as $item)
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
                    $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                if($link!='')
                    $map_descriptor.=']]';
                $map_descriptor.='</td>'."\n";
                if($visible)
                    $map_descriptor.='<td>'.$translation_list[$current_lang]['On the map'].'</td>'."\n";
                else
                    $map_descriptor.='<td>'.$translation_list[$current_lang]['Hidden on the map'].'</td>'."\n";
                $map_descriptor.='</tr>'."\n";
            }
        }

		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>'."\n".'
		</tr>
		</table>'."\n";
        savewikipage('Template:Maps/'.$map_html.'_ITEM',$map_descriptor,false);$map_descriptor='';
	}

	if(count($map_current_object['monsters'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$map_current_object['type'].'">
		<tr class="item_list_title item_list_title_type_'.$map_current_object['type'].'">
			<th colspan="2">'.$translation_list[$current_lang]['Monster'].'</th>
			<th>'.$translation_list[$current_lang]['Location'].'</th>
			<th>'.$translation_list[$current_lang]['Levels'].'</th>
			<th colspan="3">'.$translation_list[$current_lang]['Rate'].'</th>
		</tr>'."\n";
        foreach($map_current_object['monsters'] as $monsterType=>$monster_list)
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
                $map_descriptor.=$translation_list[$current_lang][$full_monsterType_name];
            if(isset($layer_event[$monsterType]))
            {
                if($layer_event[$monsterType]['id']=='day' && $layer_event[$monsterType]['value']=='night')
                    $map_descriptor.=' '.$translation_list[$current_lang]['at night'];
                else
                    $map_descriptor.=str_replace('[value]',$layer_event[$monsterType]['value'],str_replace('[condition]',$layer_event[$monsterType]['id'],$translation_list[$current_lang][' condition [condition] at [value]']));
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
                        $map_descriptor.='<img src="'.$base_datapack_site_http.'/images/datapack-explorer/'.$full_monsterType_name_top.'.png" alt="" class="locationimg">'.$translation_list[$current_lang][$full_monsterType_name_top];
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
			<td colspan="7" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>'."\n".'
		</tr>
		</table>'."\n";
        savewikipage('Template:Maps/'.$map_html.'_MONSTER',$map_descriptor,false);$map_descriptor='';
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
					$map_descriptor.='<tr class="value">'."\n";
					$have_skin=true;
					if(isset($bot_id_to_skin[$bot_id][$maindatapackcode]))
					{
						if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
						elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
						elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
						elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif'))
							$map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
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
						$map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$maindatapackcode.'-'.$link.'|Bot #'.$bot_id.']]</td>'."\n";
					else
						$map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$maindatapackcode.'-'.$link.'|'.$bot['name'][$current_lang].']]</td>'."\n";
                    if(!isset($bot_start_to_quests[$bot_id]))
                        $map_descriptor.='<td>'.$translation_list[$current_lang]['Text only'].'</td>'."\n";
                    else
                    {
                        $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Quests'].'
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
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>'."\n";
                                elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png'))
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>'."\n";
                                elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif'))
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>'."\n";
                                elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif'))
                                    $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>'."\n";
                                else
                                    $have_skin=false;
                            }
                            else
                                $have_skin=false;
                            $map_descriptor.='<td';
                            if(!$have_skin)
                                $map_descriptor.=' colspan="2"';
                            if($bot['name'][$current_lang]=='')
                                $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$maindatapackcode.'-'.$link.'|Bot #'.$bot_id.']]</td>'."\n";
                            else
                                $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$maindatapackcode.'-'.$link.'|'.$bot['name'][$current_lang].']]</td>'."\n";
                        }
                        if($step['type']=='text')
                        {}
                        else if($step['type']=='shop')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Shop'].'<div style="background-position:-32px 0px;" class="flags flags16"></div></center></td>'."\n".'<td>';
                            $map_descriptor.='<center><table class="item_list item_list_type_'.$map_current_object['type'].'">
                            <tr class="item_list_title item_list_title_type_'.$map_current_object['type'].'">
                                <th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
                                <th>'.$translation_list[$current_lang]['Price'].'</th>
                            </tr>'."\n";
                            foreach($shop_meta[$maindatapackcode][$step['shop']]['products'] as $item=>$price)
                            {
                                if(isset($item_meta[$item]))
                                {
                                    $map_descriptor.='<tr class="value">'."\n";
                                    if(isset($item_meta[$item]))
                                    {
                                        $link_item=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]);
                                        $name=$item_meta[$item]['name'][$current_lang];
                                        if($item_meta[$item]['image']!='')
                                            $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
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
                                    <td>'."\n";
                                    if($image!='')
                                    {
                                        if($link_item!='')
                                            $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item]['name'][$current_lang].'|';
                                        $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                        if($link_item!='')
                                            $map_descriptor.=']]';
                                    }
                                    $map_descriptor.='</td>'."\n".'
                                    <td>'."\n";
                                    if($link_item!='')
                                        $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item]['name'][$current_lang].'|';
                                    if($name!='')
                                        $map_descriptor.=$name;
                                    else
                                        $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                                    if($link_item!='')
                                        $map_descriptor.=']]';
                                    $map_descriptor.='</td>'."\n";
                                    $map_descriptor.='<td>'.$price.'$</td>'."\n";
                                    $map_descriptor.='</tr>'."\n";
                                }
                            }
                            $map_descriptor.='<tr>
                                <td colspan="3" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>'."\n".'
                            </tr>
                            </table>'."\n";
                            $map_descriptor.='</center></td>'."\n";
                        }
                        else if($step['type']=='fight')
                        {
                            if(isset($fight_meta[$maindatapackcode][$step['fightid']]))
                            {
                                $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Fight'].'<div style="background-position:-16px -16px;" class="flags flags16"></div></center></td>'."\n".'<td>'."\n";
                                if($step['leader'])
                                {
                                    $map_descriptor.='<b>'.$translation_list[$current_lang]['Leader'].'</b><br />'."\n";
                                    if(isset($bot_id_to_skin[$bot_id][$maindatapackcode]))
                                    {
                                        if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.png'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.png" width="80" height="80" alt="" /></center>'."\n";
                                        else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.png'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.png" width="80" height="80" alt="" /></center>'."\n";
                                        elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.gif'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.gif" width="80" height="80" alt="" /></center>'."\n";
                                        else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.gif'))
                                            $map_descriptor.='<center><img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id][$maindatapackcode].'/front.gif" width="80" height="80" alt="" /></center>'."\n";
                                    }
                                }
                                if($fight_meta[$maindatapackcode][$step['fightid']]['cash']>0)
                                    $map_descriptor.=$translation_list[$current_lang]['Rewards'].': <b>'.$fight_meta[$maindatapackcode][$step['fightid']]['cash'].'$</b><br />'."\n";

                                if(count($fight_meta[$maindatapackcode][$step['fightid']]['items'])>0)
                                {
                                    $map_descriptor.='<center><table class="item_list item_list_type_'.$map_current_object['type'].'">
                                    <tr class="item_list_title item_list_title_type_'.$map_current_object['type'].'">
                                        <th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
                                    </tr>'."\n";
                                    foreach($fight_meta[$maindatapackcode][$step['fightid']]['items'] as $item)
                                    {
                                        if(isset($item_meta[$item['item']]))
                                        {
                                            $link_item=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item['item']]['name'][$current_lang]);
                                            $name=$item_meta[$item['item']]['name'][$current_lang];
                                            if($item_meta[$item['item']]['image']!='')
                                                $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item['item']]['image'];
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
                                            <td>'."\n";
                                            if($image!='')
                                            {
                                                if($link_item!='')
                                                    $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item['item']]['name'][$current_lang].'|';
                                                $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                                if($link_item!='')
                                                    $map_descriptor.=']]';
                                            }
                                            $map_descriptor.='</td>'."\n".'
                                            <td>';
                                            if($link_item!='')
                                                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item['item']]['name'][$current_lang].'|';
                                            if($name!='')
                                                $map_descriptor.=$quantity_text.$name;
                                            else
                                                $map_descriptor.=$quantity_text.'Unknown item';
                                            if($link_item!='')
                                                $map_descriptor.=']]';
                                            $map_descriptor.='</td>'."\n";
                                            $map_descriptor.='</tr>'."\n";
                                    }
                                    $map_descriptor.='<tr>
                                        <td colspan="2" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>'."\n".'
                                    </tr>
                                    </table></center>'."\n";
                                }

                                foreach($fight_meta[$maindatapackcode][$step['fightid']]['monsters'] as $monster)
                                    $map_descriptor.=monsterAndLevelToDisplay($monster,$step['leader'],true);
                                $map_descriptor.='<br style="clear:both;" />'."\n";

                                $map_descriptor.='</td>'."\n";
                            }
                        }
                        else if($step['type']=='heal')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Heal'].'</center></td>'."\n".'
                            <td><center><div style="background-position:0px 0px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='learn')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Learn'].'</center></td>'."\n".'
                            <td><center><div style="background-position:-192px 0px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='warehouse')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Warehouse'].'</center></td>'."\n".'
                            <td><center><div style="background-position:0px -64px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='market')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Market'].'</center></td>'."\n".'
                            <td><center><div style="background-position:0px -64px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='quests' && isset($bot_start_to_quests[$bot_id]))
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Quests'].'</center></td>'."\n".'
                            <td><center><div style="background-position:-32px 0px;" class="flags flags32"></div></center></td>'."\n";
                        }
                        else if($step['type']=='clan')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Clan'].'</center></td>'."\n".'
                            <td><center><div style="background-position:-192px -64px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='sell')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Sell'].'</center></td>'."\n".'
                            <td><center><div style="background-position:-128px 0px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='zonecapture')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Zone capture'].'</center></td>'."\n".'
                            <td><center><div style="background-position:-128px -64px;" class="flags flags64"></div></center></td>'."\n";
                        }
                        else if($step['type']=='industry')
                        {
                            $map_descriptor.='<td><center>'.$translation_list[$current_lang]['Industry'].'<div style="background-position:0px -32px;" class="flags flags16"></div></center></td><td>'."\n";

                            if(!isset($industrie_meta[$step['industry']]))
                            {
                                $map_descriptor.='Industry '.$step['industry'].' not found for map '.$bot_id.'!</td>'."\n";
                                echo 'Industry '.$step['industry'].' not found for map '.$bot_id.'!'."\n";
                            }
                            else
                            {
                                $map_descriptor.='<center><table class="item_list item_list_type_'.$map_current_object['type'].'">
                                <tr class="item_list_title item_list_title_type_'.$map_current_object['type'].'">
                                    <th>'.$translation_list[$current_lang]['Industry'].'</th>
                                    <th>'.$translation_list[$current_lang]['Resources'].'</th>
                                    <th>'.$translation_list[$current_lang]['Products'].'</th>
                                </tr>'."\n";
                                $industry=$industrie_meta[$step['industry']];
                                $map_descriptor.='<tr class="value">'."\n";
                                $map_descriptor.='<td>'."\n";
                                $map_descriptor.='[['.$translation_list[$current_lang]['Industries:'].str_replace('[id]',$step['industry'],$translation_list[$current_lang]['Industry [id]']).'|'.str_replace('[id]',$step['industry'],$translation_list[$current_lang]['Industry [id]']).']]';
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
                                            $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                                        if($link_industry!='')
                                            $map_descriptor.=']]';
                                        $map_descriptor.='</div>'."\n";
                                    }
                                    else
                                        $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
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
                                            $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                                        if($link_industry!='')
                                            $map_descriptor.=']]';
                                        $map_descriptor.='</div>'."\n";
                                    }
                                    else
                                        $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                                }
                            }
                            $map_descriptor.='</td>'."\n";
                            $map_descriptor.='</tr>'."\n";
                            $map_descriptor.='<tr>
                                <td colspan="3" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>'."\n".'
                            </tr>
                            </table></center>'."\n";

                            $map_descriptor.='</td>'."\n";
                        }
                        else
                            $map_descriptor.='<td>'.$step['type'].'</td>'."\n".'<td>'.$translation_list[$current_lang]['Unknown type'].' ('.$step['type'].')</td>'."\n";
                        if($step['type']!='text')
                            $map_descriptor.='</tr>'."\n";
                    }
                }
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_'.$map_current_object['type'].'"></td>'."\n".'
		</tr>
		</table></center>'."\n";
        savewikipage('Template:Maps/'.$map_html.'_BOT',$map_descriptor,false);$map_descriptor='';
	}
	
    $lang_template='';
    if(count($wikivarsapp)>1)
    {
        $temp_current_lang=$current_lang;
        foreach($wikivarsapp as $wikivars2)
            if($wikivars2['lang']!=$temp_current_lang)
            {
                $current_lang=$wikivars2['lang'];
                $lang_template.='[['.$current_lang.':'.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($map).']]'."\n";
            }
        savewikipage('Template:Maps/'.$map_html.'_LANG',$lang_template,false);$lang_template='';
        $current_lang=$temp_current_lang;
        $map_descriptor.='{{Template:Maps/'.$map_html.'_LANG}}'."\n";
    }

    $map_descriptor.='{{Template:Maps/'.$map_html.'_HEADER}}'."\n";
    if($map_current_object['dropcount']>0 || count($map_current_object['items'])>0)
        $map_descriptor.='{{Template:Maps/'.$map_html.'_ITEM}}'."\n";
    if(count($map_current_object['monsters'])>0)
        $map_descriptor.='{{Template:Maps/'.$map_html.'_MONSTER}}'."\n";
    if(isset($map_current_object['bots']) && count($map_current_object['bots'])>0)
        $map_descriptor.='{{Template:Maps/'.$map_html.'_BOT}}'."\n";
    savewikipage($translation_list[$current_lang]['Maps:'].map_to_wiki_name($map),$map_descriptor,!$wikivars['generatefullpage']);
}

$map_descriptor='';
ksort($zone_to_map);

if(file_exists($datapack_explorer_local_path.'maps/overview.png') && file_exists($datapack_explorer_local_path.'maps/preview.png'))
{
    $size=getimagesize($datapack_explorer_local_path.'maps/preview.png');
    $map_current_object['pixelwidth']=$size[0];
    $map_current_object['pixelheight']=$size[1];
    if($map_current_object['pixelwidth']>1600 || $map_current_object['pixelheight']>800)
        $ratio=4;
    elseif($map_current_object['pixelwidth']>800 || $map_current_object['pixelheight']>400)
        $ratio=2;
    else
        $ratio=1;
    $map_descriptor.='<div class="value datapackscreenshot"><center>[['.$base_datapack_site_http.$base_datapack_explorer_site_path.'maps/overview.png <img src="'.$base_datapack_site_http.$base_datapack_explorer_site_path.'maps/preview.png" alt="Map overview" title="Map overview" width="'.($map_current_object['pixelwidth']/$ratio).'" height="'.($map_current_object['pixelheight']/$ratio).'" />]]
    <b>'.$translation_list[$current_lang]['Size'].': '.round(filesize($datapack_explorer_local_path.'maps/overview.png')/1000000,1).$translation_list[$current_lang]['MB'].'</b>
    </center></div>';
    savewikipage('Template:maps_preview',$map_descriptor,false);
    $map_descriptor='';
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
	<th>[['.$translation_list[$current_lang]['Zones:'].$zone_name.'|'.$zone_name.']]'."\n";
	$map_descriptor.='</th>'."\n";
    if(isset($zone_to_function[$maindatapackcode][$zone]))
        $additionnal_function=count($zone_to_function[$maindatapackcode][$zone])>0;
    else
        $additionnal_function=false;
    if($additionnal_function)
    {
        $map_descriptor.='<th>'."\n";
        if(isset($zone_to_function[$maindatapackcode][$zone]['shop']))
            $map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>'."\n";
        if(isset($zone_to_function[$maindatapackcode][$zone]['fight']))
            $map_descriptor.='<div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>'."\n";
        if(isset($zone_to_function[$maindatapackcode][$zone]['heal']))
            $map_descriptor.='<div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>'."\n";
        if(isset($zone_to_function[$maindatapackcode][$zone]['learn']))
            $map_descriptor.='<div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>'."\n";
        if(isset($zone_to_function[$maindatapackcode][$zone]['warehouse']))
            $map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>'."\n";
        if(isset($zone_to_function[$maindatapackcode][$zone]['market']))
            $map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>'."\n";
        if(isset($zone_to_function[$maindatapackcode][$zone]['clan']))
            $map_descriptor.='<div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>'."\n";
        if(isset($zone_to_function[$maindatapackcode][$zone]['sell']))
            $map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>'."\n";
        if(isset($zone_to_function[$maindatapackcode][$zone]['zonecapture']))
            $map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>'."\n";
        if(isset($zone_to_function[$maindatapackcode][$zone]['industry']))
            $map_descriptor.='<div style="float:left;background-position:0px -32px;" class="flags flags16" title="Industry"></div>'."\n";
        if(isset($zone_to_function[$maindatapackcode][$zone]['quests']))
            $map_descriptor.='<div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>'."\n";
        $map_descriptor.='</th>'."\n";
    }
    $map_descriptor.='</tr>'."\n";
	asort($map_by_zone);
	foreach($map_by_zone as $map=>$name)
	{
		$map_descriptor.='<tr class="value"><td>[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($map).'|'.$name[$current_lang].']]</td>'."\n";
        if($additionnal_function)
        {
            $map_descriptor.='<td>'."\n";
            if(isset($map_to_function[$maindatapackcode][$map]['shop']))
                $map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>'."\n";
            if(isset($map_to_function[$maindatapackcode][$map]['fight']))
                $map_descriptor.='<div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>'."\n";
            if(isset($map_to_function[$maindatapackcode][$map]['heal']))
                $map_descriptor.='<div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>'."\n";
            if(isset($map_to_function[$maindatapackcode][$map]['learn']))
                $map_descriptor.='<div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>'."\n";
            if(isset($map_to_function[$maindatapackcode][$map]['warehouse']))
                $map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>'."\n";
            if(isset($map_to_function[$maindatapackcode][$map]['market']))
                $map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>'."\n";
            if(isset($map_to_function[$maindatapackcode][$map]['clan']))
                $map_descriptor.='<div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>'."\n";
            if(isset($map_to_function[$maindatapackcode][$map]['sell']))
                $map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>'."\n";
            if(isset($map_to_function[$maindatapackcode][$map]['zonecapture']))
                $map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>'."\n";
            if(isset($map_to_function[$maindatapackcode][$map]['industry']))
                $map_descriptor.='<div style="float:left;background-position:0px -32px;" class="flags flags16" title="Industry"></div>'."\n";
            if(isset($map_to_function[$maindatapackcode][$map]['quests']))
                $map_descriptor.='<div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>'."\n";
            $map_descriptor.='</td>'."\n";
        }
        $map_descriptor.='</tr>'."\n";
	}
	$map_descriptor.='<tr>
	<td';
    if($additionnal_function)
        $map_descriptor.=' colspan="2"';
    $map_descriptor.=' class="item_list_endline item_list_title_type_outdoor"></td>'."\n".'
	</tr></table>'."\n";
}
savewikipage('Template:maps_list',$map_descriptor,false);$map_descriptor='';

$lang_template='';
if(count($wikivarsapp)>1)
{
    foreach($wikivarsapp as $wikivars2)
        if($wikivars2['lang']!=$current_lang)
            $lang_template.='[['.$wikivars2['lang'].':'.$translation_list[$wikivars2['lang']]['Maps list'].']]'."\n";
    savewikipage('Template:maps_LANG',$lang_template,false);$lang_template='';
    $map_descriptor.='{{Template:maps_LANG}}'."\n";
}

if(file_exists($datapack_explorer_local_path.'maps/overview.png') && file_exists($datapack_explorer_local_path.'maps/preview.png'))
    $map_descriptor.='{{Template:maps_preview}}'."\n";
$map_descriptor.='{{Template:maps_list}}'."\n";
savewikipage($translation_list[$current_lang]['Maps list'],$map_descriptor,!$wikivars['generatefullpage']);
