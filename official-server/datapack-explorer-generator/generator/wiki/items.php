<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator items'."\n");

foreach($item_meta as $id=>$item)
{
	$map_descriptor='';

    //shop
    if(isset($item_to_shop[$id]))
    {
        $bot_list=array();
        foreach($item_to_shop[$id] as $shop)
            if(isset($shop_to_bot[$shop]))
                $bot_list=array_merge($bot_list,$shop_to_bot[$shop]);
        if(count($bot_list)>0)
        {
            $map_descriptor.='<table class="item_list item_list_type_normal">
            <tr class="item_list_title item_list_title_type_normal">
                <th colspan="4">'.$translation_list[$current_lang]['Shop'].'</th>
            </tr>'."\n";

            foreach($bot_list as $bot_id)
            {
                $map_descriptor.='<tr class="value">'."\n";
                if(isset($bots_meta[$bot_id]))
                {
                    $bot=$bots_meta[$bot_id];

                    $skin_found=true;
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
                            $skin_found=false;
                    }
                    else
                        $skin_found=false;
                    $map_descriptor.='<td';
                    if(!$skin_found)
                        $map_descriptor.=' colspan="2"';

                    $link=bot_to_wiki_name($bot_id);
                    if($bot['name'][$current_lang]=='')
                        $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link.'|Bot #'.$bot_id.']]</td>'."\n";
                    else
                        $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link.'|'.$bot['name'][$current_lang].']]</td>'."\n";

                    if(isset($bot_id_to_map[$bot_id]))
                    {
                        $entry=$bot_id_to_map[$bot_id];
                        if(isset($maps_list[$entry]))
                        {
                            if(isset($zone_meta[$maindatapackcode][$maps_list[$entry]['zone']]))
                            {
                                $map_descriptor.='<td>[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($entry).'|'.map_to_wiki_name($entry).']]</td>'."\n";
                                $map_descriptor.='<td>[['.$translation_list[$current_lang]['Zones:'].$zone_meta[$maindatapackcode][$maps_list[$entry]['zone']]['name'][$current_lang].'|'.$zone_meta[$maindatapackcode][$maps_list[$entry]['zone']]['name'][$current_lang].']]</td>'."\n";
                            }
                            else
                                $map_descriptor.='<td colspan="2">[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($entry).'|'.map_to_wiki_name($entry).']]</td>'."\n";
                        }
                        else
                            $map_descriptor.='<td colspan="2">'.$translation_list[$current_lang]['Unknown map'].'</td>'."\n";
                    }
                    else
                        $map_descriptor.='<td colspan="2">&nbsp;</td>'."\n";
                }
                else
                    $map_descriptor.='<td colspan="4"></td>'."\n";
                $map_descriptor.='</tr>'."\n";
            }

            $map_descriptor.='<tr>
                <td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
            </tr>
            </table>'."\n";
            savewikipage('Template:Items/'.$id.'_SHOP',$map_descriptor,false);$map_descriptor='';
        }
    }

	$map_descriptor.='<div class="map item_details">'."\n";
		$map_descriptor.='<div class="subblock"><h1>'.$item['name'][$current_lang].'</h1>'."\n";
		$map_descriptor.='<h2>#'.$id.'</h2>'."\n";
		if($item['group']!='')
			$map_descriptor.='<h3>'.$item_group[$item['group']]['name'][$current_lang].'</h3>'."\n";
		$map_descriptor.='</div>'."\n";
		$map_descriptor.='<div class="value datapackscreenshot"><center>'."\n";
		if($item['image']!='' && file_exists($datapack_path.'items/'.$item['image']))
			$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item['image'].'" width="24" height="24" alt="'.$item['name'][$current_lang].'" title="'.$item['name'][$current_lang].'" />'."\n";
		$map_descriptor.='</center></div>'."\n";
		if($item['price']>0)
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Price'].'</div><div class="value">'.$item['price'].'$</div></div>'."\n";
		else
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Price'].'</div><div class="value">'.$translation_list[$current_lang]['Can\'t be sold'].'</div></div>'."\n";
		if($item['description'][$current_lang]!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Description'].'</div><div class="value">'.$item['description'][$current_lang].'</div></div>'."\n";
		if(isset($item['trap']))
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Trap'].'</div><div class="value">'.$translation_list[$current_lang]['Bonus rate:'].' '.$item['trap'].'x</div></div>'."\n";
		if(isset($item['repel']))
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Repel'].'</div><div class="value">Repel the monsters during '.$item['repel'].' steps</div></div>'."\n";
		if(isset($item_to_plant[$id]) && isset($plant_meta[$item_to_plant[$id]]))
		{
			$image='';
			if(file_exists($datapack_path.'plants/'.$item_to_plant[$id].'.png'))
				$image.=$base_datapack_site_http.$base_datapack_site_path.'plants/'.htmlspecialchars($item_to_plant[$id]).'.png'."\n";
			elseif(file_exists($datapack_path.'plants/'.$item_to_plant[$id].'.gif'))
				$image.=$base_datapack_site_http.$base_datapack_site_path.'plants/'.htmlspecialchars($item_to_plant[$id]).'.gif'."\n";
			if($image!='')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Plant'].'</div><div class="value">'."\n";
                $map_descriptor.=str_replace('[fruits]',$plant_meta[$item_to_plant[$id]]['quantity'],str_replace('[mins]',($plant_meta[$item_to_plant[$id]]['fruits']/60),$translation_list[$current_lang]['After <b>[mins]</b> minutes you will have <b>[fruits]</b> fruits']))."\n";
				$map_descriptor.='<table class="item_list item_list_type_normal">
				<tr class="item_list_title item_list_title_type_normal">
					<th>'.$translation_list[$current_lang]['Seed'].'</th>
					<th>'.$translation_list[$current_lang]['Sprouted'].'</th>
					<th>'.$translation_list[$current_lang]['Taller'].'</th>
					<th>'.$translation_list[$current_lang]['Flowering'].'</th>
					<th>'.$translation_list[$current_lang]['Fruits'].'</th>
				</tr><tr class="value">'."\n";
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:0px 0px;"></div></center></td>'."\n";
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-16px 0px;"></div></center></td>'."\n";
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-32px 0px;"></div></center></td>'."\n";
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-48px 0px;"></div></center></td>'."\n";
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-64px 0px;"></div></center></td>'."\n";
				$map_descriptor.='</tr><tr>
				<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
				</tr>
				</table>'."\n";
				$map_descriptor.='</div></div>'."\n";
			}

            if(count($plant_meta[$item_to_plant[$id]]['requirements'])>0)
            {
                $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Requirements'].'</div><div class="value">'."\n";
                if(isset($plant_meta[$item_to_plant[$id]]['requirements']['quests']))
                {
                    foreach($plant_meta[$item_to_plant[$id]]['requirements']['quests'] as $quest_id)
                    {
                        $map_descriptor.=$translation_list[$current_lang]['Quest'].': [['.$translation_list[$current_lang]['Quests:'].$quest_id.' '.$quests_meta[$quest_id]['name'][$current_lang].'|'.$quests_meta[$quest_id]['name'][$current_lang].']]'."\n";
                        $map_descriptor.='<br />'."\n";
                    }
                }
                if(isset($plant_meta[$item_to_plant[$id]]['requirements']['reputation']))
                    foreach($plant_meta[$item_to_plant[$id]]['requirements']['reputation'] as $reputation)
                        $map_descriptor.=reputationLevelToText($reputation['type'],$reputation['level']).'<br />'."\n";
                $map_descriptor.='</div></div>'."\n";
            }
            if(count($plant_meta[$item_to_plant[$id]]['rewards'])>0)
            {
                $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Rewards'].'</div><div class="value">'."\n";
                if(isset($plant_meta[$item_to_plant[$id]]['rewards']['items']))
                {
                    $map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">
                    <th colspan="2">'.$translation_list[$current_lang]['Item'].'</th></tr>'."\n";
                    foreach($plant_meta[$item_to_plant[$id]]['rewards']['items'] as $item)
                    {
                        $map_descriptor.='<tr class="value"><td>'."\n";
                        if(isset($item_meta[$item['item']]))
                        {
                            $link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item['item']]['name'][$current_lang]).'.html'."\n";
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
                            $quantity_text=$item['quantity'].' '."\n";
                        
                        if($image!='')
                        {
                            if($link!='')
                                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                            $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />'."\n";
                            if($link!='')
                                $map_descriptor.=']]'."\n";
                        }
                        $map_descriptor.='</td><td>'."\n";
                        if($link!='')
                            $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                        if($name!='')
                            $map_descriptor.=$quantity_text.$name;
                        else
                            $map_descriptor.=$quantity_text.$translation_list[$current_lang]['Unknown item']."\n";
                        if($link!='')
                            $map_descriptor.=']]'."\n";
                        $map_descriptor.='</td></tr>'."\n";
                    }
                    $map_descriptor.='<tr>
                    <td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
                    </tr></table>'."\n";
                }
                if(isset($plant_meta[$item_to_plant[$id]]['rewards']['reputation']))
                    foreach($plant_meta[$item_to_plant[$id]]['rewards']['reputation'] as $reputation)
                    {
                        if($reputation['point']<0)
                            $map_descriptor.=$translation_list[$current_lang]['Less reputation in:'].' '.reputationToText($reputation['type']);
                        else
                            $map_descriptor.=$translation_list[$current_lang]['More reputation in:'].' '.reputationToText($reputation['type']);
                    }
                if(isset($plant_meta[$item_to_plant[$id]]['rewards']['allow']))
                    foreach($plant_meta[$item_to_plant[$id]]['rewards']['allow'] as $allow)
                    {
                        if($allow=='clan')
                            $map_descriptor.=$translation_list[$current_lang]['Able to create clan']."\n";
                        else
                            $map_descriptor.=$translation_list[$current_lang]['Allow'].' '.$allow;
                    }
                $map_descriptor.='</div></div>'."\n";
            }
		}
		if(isset($item['effect']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Effect'].'</div><div class="value"><ul>'."\n";
			if(isset($item['effect']['regeneration']))
			{
				if($item['effect']['regeneration']=='all')
					$map_descriptor.='<li>'.$translation_list[$current_lang]['Regenerate all the hp'].'</li>'."\n";
				else
					$map_descriptor.='<li>Regenerate '.$item['effect']['regeneration'].' hp</li>'."\n";
			}
			if(isset($item['effect']['buff']))
			{
				if($item['effect']['buff']=='all')
					$map_descriptor.='<li>'.$translation_list[$current_lang]['Remove all the buff and debuff'].'</li>'."\n";
				else
				{
					$buff_id=$item['effect']['buff'];
					$map_descriptor.='<li>'.$translation_list[$current_lang]['Remove the buff:']."\n";
					$map_descriptor.='<center><table><td>'."\n";
					if(file_exists($datapack_path.'/monsters/buff/'.$buff_id.'.png'))
						$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/buff/'.$buff_id.'.png" alt="" width="16" height="16" />'."\n";
					else
						$map_descriptor.='&nbsp;'."\n";
					$map_descriptor.='</td>'."\n";
					if(isset($buff_meta[$buff_id]))
						$map_descriptor.='<td>'.$translation_list[$current_lang]['Unknown buff'].'</td>'."\n";
					else
						$map_descriptor.='<td>[['.$translation_list[$current_lang]['Buffs:'].$buff_meta[$buff_id]['name'][$current_lang].'|'.$buff_meta[$buff_id]['name'][$current_lang].']]</td>'."\n";
					$map_descriptor.='</table></center>'."\n";
					$map_descriptor.='</li>'."\n";
				}
			}
			$map_descriptor.='</ul></div></div>'."\n";
		}

        if(isset($item_to_crafting[$id]))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Do the item'].'</div><div class="value">'."\n";
                $map_descriptor.='<table><tr><td>'."\n";
                if($item_meta[$item_to_crafting[$id]['doItemId']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$item_to_crafting[$id]['doItemId']]['image']))
                    $map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$item_to_crafting[$id]['doItemId']]['image'].'" width="24" height="24" alt="'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'][$current_lang].'" title="'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'][$current_lang].'" />'."\n";
                $map_descriptor.='</td><td>[['.$translation_list[$current_lang]['Items:'].$item_meta[$item_to_crafting[$id]['doItemId']]['name'][$current_lang].'|'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'][$current_lang].']]</td></tr></table>'."\n";
            $map_descriptor.='</div></div>'."\n";

            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Material'].'</div><div class="value">'."\n";
            foreach($item_to_crafting[$id]['material'] as $material=>$quantity)
            {
                    $map_descriptor.='<table><tr><td>'."\n";
                    if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
                        $map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'][$current_lang].'" title="'.$item_meta[$material]['name'][$current_lang].'" />'."\n";
                    $map_descriptor.='</td><td>'."\n";
                if($quantity>1)
                    $map_descriptor.=$quantity.'x '."\n";
                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$material]['name'][$current_lang].'|'.$item_meta[$material]['name'][$current_lang].']]</td></tr></table>'."\n";
            }
            $map_descriptor.='</div></div>'."\n";
        }

        if(isset($doItemId_to_crafting[$id]))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Product by crafting'].'</div><div class="value">'."\n";
            foreach($doItemId_to_crafting[$id] as $material)
            {
                    $map_descriptor.='<table><tr><td>'."\n";
                    if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
                        $map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'][$current_lang].'" title="'.$item_meta[$material]['name'][$current_lang].'" />'."\n";
                    $map_descriptor.='</td><td>'."\n";
                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$material]['name'][$current_lang].'|'.$item_meta[$material]['name'][$current_lang].']]</td></tr></table>'."\n";
            }
            $map_descriptor.='</div></div>'."\n";
        }

        if(isset($material_to_crafting[$id]))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Used into crafting'].'</div><div class="value">'."\n";
            foreach($material_to_crafting[$id] as $material)
            {
                    $map_descriptor.='<table><tr><td>'."\n";
                    if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
                        $map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'][$current_lang].'" title="'.$item_meta[$material]['name'][$current_lang].'" />'."\n";
                    $map_descriptor.='</td><td>'."\n";
                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$material]['name'][$current_lang].'|'.$item_meta[$material]['name'][$current_lang].']]</td></tr></table>'."\n";
            }
            $map_descriptor.='</div></div>'."\n";
        }

        //shop
        if(isset($item_to_shop[$id]))
        {
            $bot_list=array();
            foreach($item_to_shop[$id] as $shop)
                if(isset($shop_to_bot[$shop]))
                    $bot_list=array_merge($bot_list,$shop_to_bot[$shop]);
            if(count($bot_list)>0)
            {
                $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Shop'].'</div><div class="value">'."\n";
                $map_descriptor.='{{Template:Items/'.$id.'_SHOP}}'."\n";
                $map_descriptor.='</div></div>'."\n";
            }
        }

		if(isset($item_to_evolution[$id]) && count($item_to_evolution[$id])>0)
		{
			$count_evol=0;
			foreach($item_to_evolution[$id] as $evolution)
			{
				if(isset($monster_meta[$evolution['from']]) && isset($monster_meta[$evolution['to']]))
					$count_evol++;
			}
			foreach($item_to_evolution[$id] as $evolution)
			{
				if(isset($monster_meta[$evolution['from']]) && isset($monster_meta[$evolution['to']]))
				{
					$map_descriptor.='<table class="item_list item_list_type_normal map_list">
					<tr class="item_list_title item_list_title_type_normal">
						<th colspan="'.$count_evol.'">'.$translation_list[$current_lang]['Evolve from'].'</th>
					</tr>'."\n";
					$map_descriptor.='<tr class="value">'."\n";
					$map_descriptor.='<td>'."\n";
					$map_descriptor.='<table class="monsterforevolution">'."\n";
					if(file_exists($datapack_path.'monsters/'.$evolution['from'].'/front.png'))
						$map_descriptor.='<tr><td>[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['from']]['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$evolution['from'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['from']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['from']]['name'][$current_lang].'" />]]</td></tr>'."\n";
					else if(file_exists($datapack_path.'monsters/'.$evolution['from'].'/front.gif'))
						$map_descriptor.='<tr><td>[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['from']]['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$evolution['from'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['from']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['from']]['name'][$current_lang].'" />]]</td></tr>'."\n";
					$map_descriptor.='<tr><td class="evolution_name">[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['from']]['name'][$current_lang].'|'.$monster_meta[$evolution['from']]['name'][$current_lang].']]</td></tr>'."\n";
					$map_descriptor.='</table>'."\n";
					$map_descriptor.='</td>'."\n";
					$map_descriptor.='</tr>'."\n";

					$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['Evolve with'].'<br />'."\n";
					if($item_meta[$id]['image']!='')
						$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$id]['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$id]['image'].'" alt="'.$item_meta[$id]['name'][$current_lang].'" title="'.$item_meta[$id]['name'][$current_lang].'" style="float:left;" />]]'."\n";
					$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$id]['name'][$current_lang].'|'.$item_meta[$id]['name'][$current_lang].']]</td></tr>'."\n";

					$map_descriptor.='<tr class="value">'."\n";
					$map_descriptor.='<td>'."\n";
					$map_descriptor.='<table class="monsterforevolution">'."\n";
					if(file_exists($datapack_path.'monsters/'.$evolution['to'].'/front.png'))
						$map_descriptor.='<tr><td>[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['to']]['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$evolution['to'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['to']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['to']]['name'][$current_lang].'" />]]</td></tr>'."\n";
					else if(file_exists($datapack_path.'monsters/'.$evolution['to'].'/front.gif'))
						$map_descriptor.='<tr><td>[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['to']]['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$evolution['to'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['to']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['to']]['name'][$current_lang].'" />]]</td></tr>'."\n";
					$map_descriptor.='<tr><td class="evolution_name">[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['to']]['name'][$current_lang].'|'.$monster_meta[$evolution['to']]['name'][$current_lang].']]</td></tr>'."\n";
					$map_descriptor.='</table>'."\n";
					$map_descriptor.='</td>'."\n";
					$map_descriptor.='</tr>'."\n";

					$map_descriptor.='<tr>
						<th colspan="'.$count_evol.'" class="item_list_endline item_list_title item_list_title_type_normal">'.$translation_list[$current_lang]['Evolve to'].'</th>
					</tr>
					</table>'."\n";
				}
			}
			$map_descriptor.='<br style="clear:both" />'."\n";
		}

	$map_descriptor.='</div>'."\n";

    savewikipage('Template:Items/'.$id.'_HEADER',$map_descriptor,false);$map_descriptor='';

	if(isset($item_to_monster[$id]))
	{
        $only_one=true;
        foreach($item_to_monster[$id] as $item_to_monster_list)
            if($item_to_monster_list['quantity_min']!=1 || $item_to_monster_list['quantity_max']!=1)
                $only_one=false;
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th colspan="2">'.$translation_list[$current_lang]['Monster'].'</th>';
            if(!$only_one)
                $map_descriptor.='<th>'.$translation_list[$current_lang]['Quantity'].'</th>';
            $map_descriptor.='<th>'.$translation_list[$current_lang]['Luck'].'</th>
		</tr>'."\n";
		foreach($item_to_monster[$id] as $item_to_monster_list)
		{
			if(isset($monster_meta[$item_to_monster_list['monster']]))
			{
				if($item_to_monster_list['quantity_min']!=$item_to_monster_list['quantity_max'])
					$quantity_text=$item_to_monster_list['quantity_min'].' to '.$item_to_monster_list['quantity_max'];
				else
					$quantity_text=$item_to_monster_list['quantity_min'];
				$name=$monster_meta[$item_to_monster_list['monster']]['name'][$current_lang];
				$link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html'."\n";
				$map_descriptor.='<tr class="value">'."\n";
				$map_descriptor.='<td>'."\n";
				if(file_exists($datapack_path.'monsters/'.$item_to_monster_list['monster'].'/small.png'))
					$map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$item_to_monster_list['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'][$current_lang].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'][$current_lang].'" />]]</div>'."\n";
				else if(file_exists($datapack_path.'monsters/'.$item_to_monster_list['monster'].'/small.gif'))
					$map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$item_to_monster_list['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'][$current_lang].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'][$current_lang].'" />]]</div>'."\n";
				$map_descriptor.='</td>
				<td>[['.$translation_list[$current_lang]['Monsters:'].$name.'|'.$name.']]</td>'."\n";
                if(!$only_one)
                    $map_descriptor.='<td>'.$quantity_text.'</td>'."\n";
				$map_descriptor.='<td>'.$item_to_monster_list['luck'].'%</td>'."\n";
				$map_descriptor.='</tr>'."\n";
			}
		}
		$map_descriptor.='<tr>';
        if(!$only_one)
            $map_descriptor.='<td colspan="4" class="item_list_endline item_list_title_type_normal"></td>';
        else
            $map_descriptor.='<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>';
        $map_descriptor.='</tr>
		</table>'."\n";
        savewikipage('Template:Items/'.$id.'_MONSTER',$map_descriptor,false);$map_descriptor='';
	}

	if(isset($items_to_quests[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>'.$translation_list[$current_lang]['Quests'].'</th>
			<th>'.$translation_list[$current_lang]['Quantity rewarded'].'</th>
		</tr>'."\n";
		foreach($items_to_quests[$id] as $quest_id=>$quantity)
		{
			if(isset($quests_meta[$quest_id]))
			{
				$map_descriptor.='<tr class="value">'."\n";
				$map_descriptor.='<td>[['.$translation_list[$current_lang]['Quests:'].$quest_id.' '.$quests_meta[$quest_id]['name'][$current_lang].'|'.$quests_meta[$quest_id]['name'][$current_lang].']]'."\n";
				$map_descriptor.='</td>'."\n";
				$map_descriptor.='<td>'.$quantity.'</td>'."\n";
				$map_descriptor.='</tr>'."\n";
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>'."\n";
        savewikipage('Template:Items/'.$id.'_QUEST',$map_descriptor,false);$map_descriptor='';
	}
	if(isset($items_to_quests_for_step[$id]))
	{
		$full_details=false;
		foreach($items_to_quests_for_step[$id] as $items_to_quests_for_step_details)
		{
			if(isset($quests_meta[$items_to_quests_for_step_details['quest']]))
				if(isset($items_to_quests_for_step_details['monster']) && isset($monster_meta[$items_to_quests_for_step_details['monster']]))
					$full_details=true;
		}
		if($full_details)
			$map_descriptor.='<table class="item_list item_list_type_normal">
			<tr class="item_list_title item_list_title_type_normal">
				<th>'.$translation_list[$current_lang]['Quests'].'</th>
				<th>'.$translation_list[$current_lang]['Quantity needed'].'</th>
				<th colspan="2">'.$translation_list[$current_lang]['Monster'].'</th>
				<th>'.$translation_list[$current_lang]['Luck'].'</th>
			</tr>'."\n";
		else
			$map_descriptor.='<table class="item_list item_list_type_normal">
			<tr class="item_list_title item_list_title_type_normal">
				<th>'.$translation_list[$current_lang]['Quests'].'</th>
				<th>'.$translation_list[$current_lang]['Quantity needed'].'</th>
			</tr>'."\n";
		foreach($items_to_quests_for_step[$id] as $items_to_quests_for_step_details)
		{
			if(isset($quests_meta[$items_to_quests_for_step_details['quest']]))
			{
				$map_descriptor.='<tr class="value">'."\n";
				$map_descriptor.='<td>[['.$translation_list[$current_lang]['Quests:'].$items_to_quests_for_step_details['quest'].' '.$quests_meta[$items_to_quests_for_step_details['quest']]['name'][$current_lang].'|'.$quests_meta[$items_to_quests_for_step_details['quest']]['name'][$current_lang].']]'."\n";
				$map_descriptor.='</td>'."\n";
				$map_descriptor.='<td>'.$items_to_quests_for_step_details['quantity'].'</td>'."\n";
				if(isset($items_to_quests_for_step_details['monster']) && isset($monster_meta[$items_to_quests_for_step_details['monster']]))
				{
					$name=$monster_meta[$items_to_quests_for_step_details['monster']]['name'][$current_lang];
					$link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html'."\n";
					$map_descriptor.='<td>'."\n";
					if(file_exists($datapack_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.png'))
						$map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'][$current_lang].'" title="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'][$current_lang].'" />]]</div>'."\n";
					else if(file_exists($datapack_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.gif'))
						$map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'][$current_lang].'" title="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'][$current_lang].'" />]]</div>'."\n";
					$map_descriptor.='</td>
					<td>[['.$translation_list[$current_lang]['Monsters:'].$name.'|'.$name.']]</td>'."\n";
					$map_descriptor.='<td>'.$items_to_quests_for_step_details['rate'].'%</td>'."\n";
				}
				else if($full_details)
					$map_descriptor.='<td></td><td></td><td></td>'."\n";
				$map_descriptor.='</tr>'."\n";
			}
		}
		if($full_details)
			$map_descriptor.='<tr>
				<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
			</tr>
			</table>'."\n";
		else
			$map_descriptor.='<tr>
				<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
			</tr>
			</table>'."\n";
        savewikipage('Template:Items/'.$id.'_QUEST2',$map_descriptor,false);$map_descriptor='';
	}

	if(isset($item_consumed_by[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>'.$translation_list[$current_lang]['Product of the industry'].'</th>
			<th>'.$translation_list[$current_lang]['Quantity'].'</th>
		</tr>'."\n";
		foreach($item_consumed_by[$id] as $industry_id=>$quantity)
		{
			if(isset($industrie_meta[$industry_id]))
			{
				$map_descriptor.='<tr class="value">'."\n";
				$map_descriptor.='<td>[['.$translation_list[$current_lang]['Industries:'].str_replace('[id]',$industry_id,$translation_list[$current_lang]['Industry [id]']).'|'.str_replace('[id]',$industry_id,$translation_list[$current_lang]['Industry [id]']).']]</td>'."\n";
				$map_descriptor.='<td>'.$quantity.'</td>'."\n";
				$map_descriptor.='</tr>'."\n";
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>'."\n";
        savewikipage('Template:Items/'.$id.'_CONSUMED',$map_descriptor,false);$map_descriptor='';
	}

	if(isset($item_produced_by[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>'.$translation_list[$current_lang]['Product of the industry'].'</th>
			<th>'.$translation_list[$current_lang]['Quantity'].'</th>
		</tr>'."\n";
		foreach($item_produced_by[$id] as $industry_id=>$quantity)
		{
			if(isset($industrie_meta[$industry_id]))
			{
				$map_descriptor.='<tr class="value">'."\n";
				$map_descriptor.='<td>[['.$translation_list[$current_lang]['Industries:'].str_replace('[id]',$industry_id,$translation_list[$current_lang]['Industry [id]']).'|'.str_replace('[id]',$industry_id,$translation_list[$current_lang]['Industry [id]']).']]</td>'."\n";
				$map_descriptor.='<td>'.$quantity.'</td>'."\n";
				$map_descriptor.='</tr>'."\n";
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>'."\n";
        savewikipage('Template:Items/'.$id.'_PRODUCED',$map_descriptor,false);$map_descriptor='';
	}

    if(isset($item_to_skill_of_monster[$id]))
    {
        $map_descriptor.='<table class="item_list item_list_type_normal">
        <tr class="item_list_title item_list_title_type_normal">
            <th colspan="3">'.$translation_list[$current_lang]['Monster'].'</th>
            <th>'.$translation_list[$current_lang]['Skill'].'</th>
            <th>'.$translation_list[$current_lang]['Type'].'</th>
        </tr>'."\n";
        foreach($item_to_skill_of_monster[$id] as $entry)
        {
            $map_descriptor.='<tr class="value">'."\n";
            if(isset($monster_meta[$entry['monster']]))
            {
                $name=$monster_meta[$entry['monster']]['name'][$current_lang];
                $link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html'."\n";
                $map_descriptor.='<td>'."\n";
                if(file_exists($datapack_path.'monsters/'.$entry['monster'].'/small.png'))
                    $map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$entry['monster'].'/small.png" width="32" height="32" alt="'.$name.'" title="'.$name.'" />]]</div>'."\n";
                else if(file_exists($datapack_path.'monsters/'.$entry['monster'].'/small.gif'))
                    $map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$entry['monster'].'/small.gif" width="32" height="32" alt="'.$name.'" title="'.$name.'" />]]</div>'."\n";
                $map_descriptor.='</td>
                <td>[['.$translation_list[$current_lang]['Monsters:'].$name.'|'.$name.']]</td>'."\n";
                $type_list=array();
                foreach($monster_meta[$entry['monster']]['type'] as $type_monster)
                    if(isset($type_meta[$type_monster]))
                        $type_list[]='<span class="type_label type_label_'.$type_monster.'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type_monster]['name'][$current_lang].'|'.$type_meta[$type_monster]['name'][$current_lang].']]</span>'."\n";
                $map_descriptor.='<td><div class="type_label_list">'.implode(' ',$type_list).'</div></td>'."\n";
            }
            if(isset($skill_meta[$entry['id']]))
            {
                $map_descriptor.='<td>[['.$translation_list[$current_lang]['Skills:'].$skill_meta[$entry['id']]['name'][$current_lang].'|'.$skill_meta[$entry['id']]['name'][$current_lang];
                if($entry['attack_level']>1)
                    $map_descriptor.=' at level '.$entry['attack_level'];
                $map_descriptor.=']]</td>'."\n";
                if(isset($type_meta[$skill_meta[$entry['id']]['type']]))
                    $map_descriptor.='<td><span class="type_label type_label_'.$skill_meta[$entry['id']]['type'].'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$skill_meta[$entry['id']]['type']]['name'][$current_lang].'|'.$type_meta[$skill_meta[$entry['id']]['type']]['name'][$current_lang].']]</span></td>'."\n";
                else
                    $map_descriptor.='<td>&nbsp;</td>'."\n";
            }
            $map_descriptor.='</tr>'."\n";
        }
        $map_descriptor.='<tr>
            <td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
        </tr>
        </table>'."\n";
        savewikipage('Template:Items/'.$id.'_SKILL',$map_descriptor,false);$map_descriptor='';
    }

    if(isset($item_to_map[$id]))
    {
        $map_descriptor.='<table class="item_list item_list_type_normal">
        <tr class="item_list_title item_list_title_type_normal">
            <th colspan="2">'.$translation_list[$current_lang]['On the map'].'</th>
        </tr>'."\n";
        foreach($item_to_map[$id] as $entry)
        {
            $map_descriptor.='<tr class="value">'."\n";
                if(isset($maps_list[$entry]))
                {
                    if(isset($zone_meta[$maindatapackcode][$maps_list[$entry]['zone']]))
                    {
                        $map_descriptor.='<td>[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($entry).'|'.map_to_wiki_name($entry).']]</td>'."\n";
                        $map_descriptor.='<td>[['.$translation_list[$current_lang]['Zones:'].$zone_meta[$maindatapackcode][$maps_list[$entry]['zone']]['name'][$current_lang].'|'.$zone_meta[$maindatapackcode][$maps_list[$entry]['zone']]['name'][$current_lang].']]</td>'."\n";
                    }
                    else
                        $map_descriptor.='<td colspan="2">[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($entry).'|'.map_to_wiki_name($entry).']]</td>'."\n";
                }
                else
                    $map_descriptor.='<td colspan="2">'.$translation_list[$current_lang]['Unknown map'].'</td>'."\n";
                $map_descriptor.='</tr>'."\n";
        }
        $map_descriptor.='<tr>
            <td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
        </tr>
        </table>'."\n";
        savewikipage('Template:Items/'.$id.'_MAP',$map_descriptor,false);$map_descriptor='';
    }

    $fights_for_items_list=array();
    if(isset($item_to_fight[$id]))
        foreach($item_to_fight[$id] as $fight)
            if(isset($fight_to_bot[$fight]))
                foreach($fight_to_bot[$fight] as $bot)
                    $fights_for_items_list[]=$bot;
    if(count($fights_for_items_list)>0)
    {
        $map_descriptor.='<table class="item_list item_list_type_normal">
        <tr class="item_list_title item_list_title_type_normal">
            <th colspan="2">'.$translation_list[$current_lang]['Fight'].'</th>
            <th>'.$translation_list[$current_lang]['Monster'].'</th>
        </tr>'."\n";
        foreach($fights_for_items_list as $bot)
        {
            if($bots_meta[$bot]['name'][$current_lang]=='')
                $link=text_operation_do_for_url('bot '.$bot);
            else if($bots_name_count[$current_lang][$bots_meta[$bot]['name'][$current_lang]]==1)
                $link=text_operation_do_for_url($bots_meta[$bot]['name'][$current_lang]);
            else
                $link=text_operation_do_for_url($bot.'-'.$bots_meta[$bot]['name'][$current_lang]);
            $bot_id=$bot;
            $bot=$bots_meta[$bot_id];
            foreach($bot['step'] as $step_id=>$step)
            {
                if($step['type']=='fight')
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

                    $map_descriptor.='<tr class="value">'."\n";
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
                    $map_descriptor.='<td'."\n";
                    if(!$have_skin)
                        $map_descriptor.=' colspan="2"'."\n";
                    if($bot['name'][$current_lang]=='')
                        $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link.'|Bot #'.$bot_id.']]</td>'."\n";
                    else
                        $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link.'|'.$bot['name'][$current_lang].']]</td>'."\n";
                    
                    $map_descriptor.='<td>'."\n";
                    foreach($fight_meta[$step['fightid']]['monsters'] as $monster)
                        $map_descriptor.=monsterAndLevelToDisplay($monster,$step['leader'],true);

                    $map_descriptor.='</td>'."\n";
                }
                $map_descriptor.='</tr>'."\n";
            }
        }
        $map_descriptor.='<tr>
            <td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
        </tr>
        </table>'."\n";
        savewikipage('Template:Items/'.$id.'_FIGHT',$map_descriptor,false);$map_descriptor='';
    }

    $lang_template='';
    if(count($wikivarsapp)>1)
    {
        $temp_current_lang=$current_lang;
        foreach($wikivarsapp as $wikivars2)
            if($wikivars2['lang']!=$temp_current_lang)
            {
                $current_lang=$wikivars2['lang'];
                $lang_template.='[['.$current_lang.':'.$translation_list[$current_lang]['Items:'].$item['name'][$current_lang].']]'."\n";
            }
        savewikipage('Template:tems/'.$id.'_LANG',$lang_template,false);$lang_template='';
        $current_lang=$temp_current_lang;
        $map_descriptor.='{{Template:tems/'.$id.'_LANG}}'."\n";
    }

    $map_descriptor.='{{Template:Items/'.$id.'_HEADER}}'."\n";
    if(isset($item_to_monster[$id]))
        $map_descriptor.='{{Template:Items/'.$id.'_MONSTER}}'."\n";
    if(isset($items_to_quests[$id]))
        $map_descriptor.='{{Template:Items/'.$id.'_QUEST}}'."\n";
    if(isset($items_to_quests_for_step[$id]))
        $map_descriptor.='{{Template:Items/'.$id.'_QUEST2}}'."\n";
    if(isset($item_consumed_by[$id]))
        $map_descriptor.='{{Template:Items/'.$id.'_CONSUMED}}'."\n";
    if(isset($item_produced_by[$id]))
        $map_descriptor.='{{Template:Items/'.$id.'_PRODUCED}}'."\n";
    if(isset($item_to_skill_of_monster[$id]))
        $map_descriptor.='{{Template:Items/'.$id.'_SKILL}}'."\n";
    if(isset($item_to_map[$id]))
        $map_descriptor.='{{Template:Items/'.$id.'_MAP}}'."\n";
    if(count($fights_for_items_list)>0)
        $map_descriptor.='{{Template:Items/'.$id.'_FIGHT}}'."\n";
    if(!isset($item['name'][$current_lang]))
    {
        echo $id;
        print_r($item);
        exit;
    }
    savewikipage($translation_list[$current_lang]['Items:'].$item['name'][$current_lang],$map_descriptor,!$wikivars['generatefullpage']);
}