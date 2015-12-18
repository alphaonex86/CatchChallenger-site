<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator items'."\n");

foreach($item_meta as $id=>$item)
{
	if(!is_dir($datapack_explorer_local_path.$translation_list[$current_lang]['items/']))
		mkdir($datapack_explorer_local_path.$translation_list[$current_lang]['items/']);
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>'.$item['name'][$current_lang].'</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		if($item['group']!='')
			$map_descriptor.='<h3>'.$item_group[$item['group']]['name'][$current_lang].'</h3>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="value datapackscreenshot"><center>';
		if($item['image']!='' && file_exists($datapack_path.'items/'.$item['image']))
			$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item['image'].'" width="24" height="24" alt="'.$item['name'][$current_lang].'" title="'.$item['name'][$current_lang].'" />';
		$map_descriptor.='</center></div>';
		if($item['price']>0)
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Price'].'</div><div class="value">'.$item['price'].'$</div></div>';
		else
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Price'].'</div><div class="value">'.$translation_list[$current_lang]['Can\'t be sold'].'</div></div>';
		if($item['description'][$current_lang]!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Description'].'</div><div class="value">'.$item['description'][$current_lang].'</div></div>';
		if(isset($item['trap']))
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Trap'].'</div><div class="value">'.$translation_list[$current_lang]['Bonus rate:'].' '.$item['trap'].'x</div></div>';
		if(isset($item['repel']))
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Repel'].'</div><div class="value">Repel the monsters during '.$item['repel'].' steps</div></div>';
		if(isset($item_to_plant[$id]) && isset($plant_meta[$item_to_plant[$id]]))
		{
			$image='';
			if(file_exists($datapack_path.'plants/'.$item_to_plant[$id].'.png'))
				$image.=$base_datapack_site_path.'plants/'.htmlspecialchars($item_to_plant[$id]).'.png';
			elseif(file_exists($datapack_path.'plants/'.$item_to_plant[$id].'.gif'))
				$image.=$base_datapack_site_path.'plants/'.htmlspecialchars($item_to_plant[$id]).'.gif';
			if($image!='')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Plant'].'</div><div class="value">';
				$map_descriptor.=str_replace('[fruits]',$plant_meta[$item_to_plant[$id]]['quantity'],str_replace('[mins]',($plant_meta[$item_to_plant[$id]]['fruits']/60),$translation_list[$current_lang]['After <b>[mins]</b> minutes you will have <b>[fruits]</b> fruits']))."\n";
				$map_descriptor.='<table class="item_list item_list_type_normal">
				<tr class="item_list_title item_list_title_type_normal">
					<th>'.$translation_list[$current_lang]['Seed'].'</th>
					<th>'.$translation_list[$current_lang]['Sprouted'].'</th>
					<th>'.$translation_list[$current_lang]['Taller'].'</th>
					<th>'.$translation_list[$current_lang]['Flowering'].'</th>
					<th>'.$translation_list[$current_lang]['Fruits'].'</th>
				</tr><tr class="value">';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:0px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-16px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-32px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-48px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-64px 0px;"></div></center></td>';
				$map_descriptor.='</tr><tr>
				<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
				</tr>
				</table>';
				$map_descriptor.='</div></div>';
			}

            if(count($plant_meta[$item_to_plant[$id]]['requirements'])>0)
            {
                $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Requirements'].'</div><div class="value">';
                if(isset($plant_meta[$item_to_plant[$id]]['requirements']['quests']))
                {
                    foreach($plant_meta[$item_to_plant[$id]]['requirements']['quests'] as $quest_id)
                    {
                        $map_descriptor.=$translation_list[$current_lang]['Quest:'].' <a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['quests/'].$maindatapackcode.'/'.$quest_id.'-'.text_operation_do_for_url($quests_meta[$maindatapackcode][$quest_id]['name'][$current_lang]).'.html" title="'.$quests_meta[$maindatapackcode][$quest_id]['name'][$current_lang].'">';
                        $map_descriptor.=$quests_meta[$maindatapackcode][$quest_id]['name'][$current_lang];
                        $map_descriptor.='</a><br />';
                    }
                }
                if(isset($plant_meta[$item_to_plant[$id]]['requirements']['reputation']))
                    foreach($plant_meta[$item_to_plant[$id]]['requirements']['reputation'] as $reputation)
                        $map_descriptor.=reputationLevelToText($reputation['type'],$reputation['level']).'<br />';
                $map_descriptor.='</div></div>';
            }
            if(count($plant_meta[$item_to_plant[$id]]['rewards'])>0)
            {
                $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Rewards'].'</div><div class="value">';
                if(isset($plant_meta[$item_to_plant[$id]]['rewards']['items']))
                {
                    $map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">
                    <th colspan="2">Item</th></tr>';
                    foreach($plant_meta[$item_to_plant[$id]]['rewards']['items'] as $item)
                    {
                        $map_descriptor.='<tr class="value"><td>';
                        if(isset($item_meta[$item['item']]))
                        {
                            $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item['item']]['name'][$current_lang]).'.html';
                            $name=$item_meta[$item['item']]['name'][$current_lang];
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
                        
                        if($image!='')
                        {
                            if($link!='')
                                $map_descriptor.='<a href="'.$link.'">';
                            $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                            if($link!='')
                                $map_descriptor.='</a>';
                        }
                        $map_descriptor.='</td><td>';
                        if($link!='')
                            $map_descriptor.='<a href="'.$link.'">';
                        if($name!='')
                            $map_descriptor.=$quantity_text.$name;
                        else
                            $map_descriptor.=$quantity_text.'Unknown item';
                        if($link!='')
                            $map_descriptor.='</a>';
                        $map_descriptor.='</td></tr>';
                    }
                    $map_descriptor.='<tr>
                    <td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
                    </tr></table>';
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
                            $map_descriptor.=$translation_list[$current_lang]['Able to create clan'];
                        else
                            $map_descriptor.=$translation_list[$current_lang]['Allow'].' '.$allow;
                    }
                $map_descriptor.='</div></div>';
            }
		}
		if(isset($item['effect']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Effect'].'</div><div class="value"><ul>';
			if(isset($item['effect']['regeneration']))
			{
				if($item['effect']['regeneration']=='all')
					$map_descriptor.='<li>'.$translation_list[$current_lang]['Regenerate all the hp'].'</li>';
				else
					$map_descriptor.='<li>Regenerate '.$item['effect']['regeneration'].' hp</li>';
			}
			if(isset($item['effect']['buff']))
			{
				if($item['effect']['buff']=='all')
					$map_descriptor.='<li>'.$translation_list[$current_lang]['Remove all the buff and debuff'].'</li>';
				else
				{
					$buff_id=$item['effect']['buff'];
					$map_descriptor.='<li>'.$translation_list[$current_lang]['Remove the buff:'];
					$map_descriptor.='<center><table><td>';
					if(file_exists($datapack_path.'/monsters/buff/'.$buff_id.'.png'))
						$map_descriptor.='<img src="'.$base_datapack_site_path.'monsters/buff/'.$buff_id.'.png" alt="" width="16" height="16" />';
					else
						$map_descriptor.='&nbsp;';
					$map_descriptor.='</td>';
					if(isset($buff_meta[$buff_id]))
						$map_descriptor.='<td>'.$translation_list[$current_lang]['Unknown buff'].'</td>';
					else
						$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'monsters/buffs/'.text_operation_do_for_url($buff_meta[$buff_id]['name'][$current_lang]).'.html">'.$buff_meta[$buff_id]['name'][$current_lang].'</a></td>';
					$map_descriptor.='</table></center>';
					$map_descriptor.='</li>';
				}
			}
			$map_descriptor.='</ul></div></div>';
		}

        if(isset($item_to_crafting[$id]))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Do the item'].'</div><div class="value">';
            $map_descriptor.='<a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item_to_crafting[$id]['doItemId']]['name'][$current_lang]).'.html" title="'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'][$current_lang].'">';
                $map_descriptor.='<table><tr><td>';
                if($item_meta[$item_to_crafting[$id]['doItemId']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$item_to_crafting[$id]['doItemId']]['image']))
                    $map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$item_to_crafting[$id]['doItemId']]['image'].'" width="24" height="24" alt="'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'][$current_lang].'" title="'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'][$current_lang].'" />';
                $map_descriptor.='</td><td>'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'][$current_lang].'</td></tr></table>';
            $map_descriptor.='</a>';
            $map_descriptor.='</div></div>';

            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Material'].'</div><div class="value">';
            foreach($item_to_crafting[$id]['material'] as $material=>$quantity)
            {
                $map_descriptor.='<a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$material]['name'][$current_lang]).'.html" title="'.$item_meta[$material]['name'][$current_lang].'">';
                    $map_descriptor.='<table><tr><td>';
                    if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
                        $map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'][$current_lang].'" title="'.$item_meta[$material]['name'][$current_lang].'" />';
                    $map_descriptor.='</td><td>';
                if($quantity>1)
                    $map_descriptor.=$quantity.'x ';
                $map_descriptor.=$item_meta[$material]['name'][$current_lang].'</td></tr></table>';
                $map_descriptor.='</a>';
            }
            $map_descriptor.='</div></div>';
        }

        if(isset($doItemId_to_crafting[$id]))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Product by crafting'].'</div><div class="value">';
            foreach($doItemId_to_crafting[$id] as $material)
            {
                $map_descriptor.='<a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$material]['name'][$current_lang]).'.html" title="'.$item_meta[$material]['name'][$current_lang].'">';
                    $map_descriptor.='<table><tr><td>';
                    if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
                        $map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'][$current_lang].'" title="'.$item_meta[$material]['name'][$current_lang].'" />';
                    $map_descriptor.='</td><td>';
                $map_descriptor.=$item_meta[$material]['name'][$current_lang].'</td></tr></table>';
                $map_descriptor.='</a>';
            }
            $map_descriptor.='</div></div>';
        }

        if(isset($material_to_crafting[$id]))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Used into crafting'].'</div><div class="value">';
            foreach($material_to_crafting[$id] as $material)
            {
                $map_descriptor.='<a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$material]['name'][$current_lang]).'.html" title="'.$item_meta[$material]['name'][$current_lang].'">';
                    $map_descriptor.='<table><tr><td>';
                    if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
                        $map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'][$current_lang].'" title="'.$item_meta[$material]['name'][$current_lang].'" />';
                    $map_descriptor.='</td><td>';
                $map_descriptor.=$item_meta[$material]['name'][$current_lang].'</td></tr></table>';
                $map_descriptor.='</a>';
            }
            $map_descriptor.='</div></div>';
        }

        //shop
        if(isset($item_to_shop[$id]))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Shop'].'</div><div class="value">';
            $map_descriptor.='<table class="item_list item_list_type_normal">
            <tr class="item_list_title item_list_title_type_normal">
                <th colspan="4">'.$translation_list[$current_lang]['Shop'].'</th>
            </tr>';
            foreach($item_to_shop[$id] as $maindatapackcode=>$shop_list)
            {
                $bot_list=array();
                foreach($shop_list as $shop)
                    if(isset($shop_to_bot[$shop][$maindatapackcode]))
                        $bot_list=array_merge($bot_list,$shop_to_bot[$shop][$maindatapackcode]);
                if(count($bot_list)>0)
                {
                    foreach($bot_list as $bot_id)
                    {
                        $map_descriptor.='<tr class="value">';
                        if(isset($bots_meta[$maindatapackcode][$bot_id]))
                        {
                            $bot=$bots_meta[$maindatapackcode][$bot_id];
                            if($bot['name'][$current_lang]=='')
                                $final_url_name='bot-'.$bot_id;
                            else if($bots_name_count[$maindatapackcode][$current_lang][$bot['name'][$current_lang]]==1)
                                $final_url_name=$bot['name'][$current_lang];
                            else
                                $final_url_name=$bot_id.'-'.$bot['name'][$current_lang];
                            if($bot['name'][$current_lang]=='')
                                $final_name='Bot #'.$bot_id;
                            else
                                $final_name=$bot['name'][$current_lang];
                            $skin_found=true;
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
                                    $skin_found=false;
                            }
                            else
                                $skin_found=false;
                            $map_descriptor.='<td';
                            if(!$skin_found)
                                $map_descriptor.=' colspan="2"';
                            $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['bots/'].$maindatapackcode.'/'.text_operation_do_for_url($final_url_name).'.html" title="'.$final_name.'">'.$final_name.'</a></td>';
                            if(isset($bot_id_to_map[$bot_id][$maindatapackcode]))
                            {
                                $entry=$bot_id_to_map[$bot_id][$maindatapackcode];
                                if(isset($maps_list[$maindatapackcode][$entry['map']]))
                                {
                                    $item_current_map=$maps_list[$maindatapackcode][$entry['map']];
                                    if(isset($zone_meta[$maindatapackcode][$item_current_map['zone']]))
                                    {
                                        $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].$maindatapackcode.'/'.str_replace('.tmx','.html',$entry['map']).'" title="'.$item_current_map['name'][$current_lang].'">'.$item_current_map['name'][$current_lang].'</a></td>';
                                        $map_descriptor.='<td>'.$zone_meta[$maindatapackcode][$item_current_map['zone']]['name'][$current_lang].'</td>';
                                    }
                                    else
                                        $map_descriptor.='<td colspan="2"><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].$maindatapackcode.'/'.str_replace('.tmx','.html',$entry['map']).'" title="'.$item_current_map['name'][$current_lang].'">'.$item_current_map['name'][$current_lang].'</a></td>';
                                }
                                else
                                    $map_descriptor.='<td colspan="2">'.$translation_list[$current_lang]['Unknown map'].'</td>';
                            }
                            else
                                $map_descriptor.='<td colspan="2">&nbsp;'.$final_name.' not found for '.$maindatapackcode.' for map</td>';
                        }
                        else
                        {
                            $map_descriptor.='<td colspan="4">Bot id not found: '.$bot_id.' for '.$maindatapackcode.'</td>';
                            echo 'Bot id not found: '.$bot_id.' for '.$maindatapackcode."\n";
                        }
                        $map_descriptor.='</tr>';
                    }
                }
            }

            $map_descriptor.='<tr>
                <td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
            </tr>
            </table>';
            $map_descriptor.='</div></div>';
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
					</tr>';
					$map_descriptor.='<tr class="value">';
					$map_descriptor.='<td>';
					$map_descriptor.='<table class="monsterforevolution">';
					if(file_exists($datapack_path.'monsters/'.$evolution['from'].'/front.png'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['from']]['name'][$current_lang]).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['from'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['from']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['from']]['name'][$current_lang].'" /></a></td></tr>';
					else if(file_exists($datapack_path.'monsters/'.$evolution['from'].'/front.gif'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['from']]['name'][$current_lang]).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['from'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['from']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['from']]['name'][$current_lang].'" /></a></td></tr>';
					$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['from']]['name'][$current_lang]).'.html">'.$monster_meta[$evolution['from']]['name'][$current_lang].'</a></td></tr>';
					$map_descriptor.='</table>';
					$map_descriptor.='</td>';
					$map_descriptor.='</tr>';

					$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['Evolve with'].'<br /><a href="'.$link.'" title="'.$item_meta[$id]['name'][$current_lang].'">';
					if($item_meta[$id]['image']!='')
						$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$id]['image'].'" alt="'.$item_meta[$id]['name'][$current_lang].'" title="'.$item_meta[$id]['name'][$current_lang].'" style="float:left;" />';
					$map_descriptor.=$item_meta[$id]['name'][$current_lang].'</a></td></tr>';

					$map_descriptor.='<tr class="value">';
					$map_descriptor.='<td>';
					$map_descriptor.='<table class="monsterforevolution">';
					if(file_exists($datapack_path.'monsters/'.$evolution['to'].'/front.png'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['to']]['name'][$current_lang]).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['to'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['to']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['to']]['name'][$current_lang].'" /></a></td></tr>';
					else if(file_exists($datapack_path.'monsters/'.$evolution['to'].'/front.gif'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['to']]['name'][$current_lang]).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['to'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['to']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['to']]['name'][$current_lang].'" /></a></td></tr>';
					$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['to']]['name'][$current_lang]).'.html">'.$monster_meta[$evolution['to']]['name'][$current_lang].'</a></td></tr>';
					$map_descriptor.='</table>';
					$map_descriptor.='</td>';
					$map_descriptor.='</tr>';

					$map_descriptor.='<tr>
						<th colspan="'.$count_evol.'" class="item_list_endline item_list_title item_list_title_type_normal">'.$translation_list[$current_lang]['Evolve to'].'</th>
					</tr>
					</table>';
				}
			}
			$map_descriptor.='<br style="clear:both" />';
		}

	$map_descriptor.='</div>';

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
		</tr>';
		foreach($item_to_monster[$id] as $item_to_monster_list)
		{
			if(isset($monster_meta[$item_to_monster_list['monster']]))
			{
				if($item_to_monster_list['quantity_min']!=$item_to_monster_list['quantity_max'])
					$quantity_text=$item_to_monster_list['quantity_min'].' to '.$item_to_monster_list['quantity_max'];
				else
					$quantity_text=$item_to_monster_list['quantity_min'];
				$name=$monster_meta[$item_to_monster_list['monster']]['name'][$current_lang];
				$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html';
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td>';
				if(file_exists($datapack_path.'monsters/'.$item_to_monster_list['monster'].'/small.png'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item_to_monster_list['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'][$current_lang].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'][$current_lang].'" /></a></div>';
				else if(file_exists($datapack_path.'monsters/'.$item_to_monster_list['monster'].'/small.gif'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item_to_monster_list['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'][$current_lang].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'][$current_lang].'" /></a></div>';
				$map_descriptor.='</td>
				<td><a href="'.$link.'">'.$name.'</a></td>';
                if(!$only_one)
                    $map_descriptor.='<td>'.$quantity_text.'</td>';
				$map_descriptor.='<td>'.$item_to_monster_list['luck'].'%</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>';
        if(!$only_one)
			$map_descriptor.='<td colspan="4" class="item_list_endline item_list_title_type_normal"></td>';
        else
            $map_descriptor.='<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>';
		$map_descriptor.='</tr>
		</table>';
	}

	if(isset($items_to_quests[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>'.$translation_list[$current_lang]['Quests'].'</th>
			<th>'.$translation_list[$current_lang]['Quantity rewarded'].'</th>
		</tr>';
		foreach($items_to_quests[$id] as $maindatapackcode=>$quest_list)
        foreach($quest_list as $quest_id=>$quantity)
		{
			if(isset($quests_meta[$maindatapackcode][$quest_id]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['quests/'].$quest_id.'-'.text_operation_do_for_url($quests_meta[$maindatapackcode][$quest_id]['name'][$current_lang]).'.html" title="'.$quests_meta[$maindatapackcode][$quest_id]['name'][$current_lang].'">';
				$map_descriptor.=$quests_meta[$maindatapackcode][$quest_id]['name'][$current_lang];
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$quantity.'</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}
	if(isset($items_to_quests_for_step[$id]))
	{
		$full_details=false;
        foreach($items_to_quests_for_step[$id] as $maindatapackcode=>$quest_list)
        foreach($quest_list as $items_to_quests_for_step_details)
		{
			if(isset($quests_meta[$maindatapackcode][$items_to_quests_for_step_details['quest']]))
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
			</tr>';
		else
			$map_descriptor.='<table class="item_list item_list_type_normal">
			<tr class="item_list_title item_list_title_type_normal">
				<th>'.$translation_list[$current_lang]['Quests'].'</th>
				<th>'.$translation_list[$current_lang]['Quantity needed'].'</th>
			</tr>';
		foreach($items_to_quests_for_step[$id] as $maindatapackcode=>$quest_list)
        foreach($quest_list as $items_to_quests_for_step_details)
		{
			if(isset($quests_meta[$maindatapackcode][$items_to_quests_for_step_details['quest']]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['quests/'].$items_to_quests_for_step_details['quest'].'-'.text_operation_do_for_url($quests_meta[$maindatapackcode][$items_to_quests_for_step_details['quest']]['name'][$current_lang]).'.html" title="'.$quests_meta[$maindatapackcode][$items_to_quests_for_step_details['quest']]['name'][$current_lang].'">';
				$map_descriptor.=$quests_meta[$maindatapackcode][$items_to_quests_for_step_details['quest']]['name'][$current_lang];
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$items_to_quests_for_step_details['quantity'].'</td>';
				if(isset($items_to_quests_for_step_details['monster']) && isset($monster_meta[$items_to_quests_for_step_details['monster']]))
				{
					$name=$monster_meta[$items_to_quests_for_step_details['monster']]['name'][$current_lang];
					$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html';
					$map_descriptor.='<td>';
					if(file_exists($datapack_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.png'))
						$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'][$current_lang].'" title="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'][$current_lang].'" /></a></div>';
					else if(file_exists($datapack_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.gif'))
						$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'][$current_lang].'" title="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'][$current_lang].'" /></a></div>';
					$map_descriptor.='</td>
					<td><a href="'.$link.'">'.$name.'</a></td>';
					$map_descriptor.='<td>'.$items_to_quests_for_step_details['rate'].'%</td>';
				}
				else if($full_details)
					$map_descriptor.='<td></td><td></td><td></td>';
				$map_descriptor.='</tr>';
			}
		}
		if($full_details)
			$map_descriptor.='<tr>
				<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
			</tr>
			</table>';
		else
			$map_descriptor.='<tr>
				<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
			</tr>
			</table>';
	}

	if(isset($item_consumed_by[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>'.$translation_list[$current_lang]['Resource of the industry'].'</th>
			<th>'.$translation_list[$current_lang]['Quantity'].'</th>
		</tr>';
		foreach($item_consumed_by[$id] as $industry_id=>$quantity)
		{
			if(isset($industrie_meta[$industry_id]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['industries/'].$industry_id.'.html">';
				$map_descriptor.=str_replace('[industryid]',$industry_id,$translation_list[$current_lang]['Industry #[industryid]']);
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$quantity.'</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

	if(isset($item_produced_by[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>'.$translation_list[$current_lang]['Product of the industry'].'</th>
			<th>'.$translation_list[$current_lang]['Quantity'].'</th>
		</tr>';
		foreach($item_produced_by[$id] as $industry_id=>$quantity)
		{
			if(isset($industrie_meta[$industry_id]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['industries/'].$industry_id.'.html">';
				$map_descriptor.=str_replace('[industryid]',$industry_id,$translation_list[$current_lang]['Industry #[industryid]']);
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$quantity.'</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

    if(isset($item_to_skill_of_monster[$id]))
    {
        $map_descriptor.='<table class="item_list item_list_type_normal">
        <tr class="item_list_title item_list_title_type_normal">
            <th colspan="3">'.$translation_list[$current_lang]['Monster'].'</th>
            <th>'.$translation_list[$current_lang]['Skill'].'</th>
            <th>'.$translation_list[$current_lang]['Type'].'</th>
        </tr>';
        foreach($item_to_skill_of_monster[$id] as $entry)
        {
            $map_descriptor.='<tr class="value">';
            if(isset($monster_meta[$entry['monster']]))
            {
                $name=$monster_meta[$entry['monster']]['name'][$current_lang];
                $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html';
                $map_descriptor.='<td>';
                if(file_exists($datapack_path.'monsters/'.$entry['monster'].'/small.png'))
                    $map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$entry['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$entry['monster']]['name'][$current_lang].'" title="'.$monster_meta[$entry['monster']]['name'][$current_lang].'" /></a></div>';
                else if(file_exists($datapack_path.'monsters/'.$entry['monster'].'/small.gif'))
                    $map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$entry['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$entry['monster']]['name'][$current_lang].'" title="'.$monster_meta[$entry['monster']]['name'][$current_lang].'" /></a></div>';
                $map_descriptor.='</td>
                <td><a href="'.$link.'">'.$name.'</a></td>';
                $type_list=array();
                foreach($monster_meta[$entry['monster']]['type'] as $type_monster)
                    if(isset($type_meta[$type_monster]))
                        $type_list[]='<span class="type_label type_label_'.$type_monster.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_monster.'.html">'.$type_meta[$type_monster]['name'][$current_lang].'</a></span>';
                $map_descriptor.='<td><div class="type_label_list">'.implode(' ',$type_list).'</div></td>';
            }
            if(isset($skill_meta[$entry['id']]))
            {
                
                $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'monsters/skills/'.text_operation_do_for_url($skill_meta[$entry['id']]['name'][$current_lang]).'.html">'.$skill_meta[$entry['id']]['name'][$current_lang];
                if($entry['attack_level']>1)
                    $map_descriptor.=' at level '.$entry['attack_level'];
                $map_descriptor.='</a></td>';
                if(isset($type_meta[$skill_meta[$entry['id']]['type']]))
                    $map_descriptor.='<td><span class="type_label type_label_'.$skill_meta[$entry['id']]['type'].'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$skill_meta[$entry['id']]['type'].'.html">'.$type_meta[$skill_meta[$entry['id']]['type']]['name'][$current_lang].'</a></span></td>';
                else
                    $map_descriptor.='<td>&nbsp;</td>';
            }
            $map_descriptor.='</tr>';
        }
        $map_descriptor.='<tr>
            <td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
        </tr>
        </table>';
    }

    if(isset($item_to_map[$id]))
    {
        $map_descriptor.='<table class="item_list item_list_type_normal">
        <tr class="item_list_title item_list_title_type_normal">
            <th colspan="2">'.$translation_list[$current_lang]['On the map'].'</th>
        </tr>';
        foreach($item_to_map[$id] as $entry)
        {
            $map_descriptor.='<tr class="value">';
            $maindatapackcode=$entry['maindatapackcode'];
                if(isset($maps_list[$maindatapackcode][$entry['map']]))
                {
                    $item_current_map=$maps_list[$maindatapackcode][$entry['map']];
                    if(isset($zone_meta[$maindatapackcode][$item_current_map['zone']]))
                    {
                        $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].$maindatapackcode.'/'.str_replace('.tmx','.html',$entry['map']).'" title="'.$item_current_map['name'][$current_lang].'">'.$item_current_map['name'][$current_lang].'</a></td>';
                        $map_descriptor.='<td>'.$zone_meta[$maindatapackcode][$item_current_map['zone']]['name'][$current_lang].'</td>';
                    }
                    else
                        $map_descriptor.='<td colspan="2"><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].$maindatapackcode.'/'.str_replace('.tmx','.html',$entry['map']).'" title="'.$item_current_map['name'][$current_lang].'">'.$item_current_map['name'][$current_lang].'</a></td>';
                }
                else
                    $map_descriptor.='<td colspan="2">'.$translation_list[$current_lang]['Unknown map'].'</td>';
                $map_descriptor.='</tr>';
        }
        $map_descriptor.='<tr>
            <td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
        </tr>
        </table>';
    }

    $fights_for_items_list=array();
    if(isset($item_to_fight[$id]))
        foreach($item_to_fight[$id] as $maindatapackcode=>$fight_list)
        {
            foreach($fight_list as $fight)
                if(isset($fight_to_bot[$maindatapackcode][$fight]))
                    foreach($fight_to_bot[$maindatapackcode][$fight] as $bot)
                        $fights_for_items_list[]=$bot;
            if(count($fights_for_items_list)>0)
            {
                $map_descriptor.='<table class="item_list item_list_type_normal">
                <tr class="item_list_title item_list_title_type_normal">
                    <th colspan="2">'.$translation_list[$current_lang]['Fight'].'</th>
                    <th>'.$translation_list[$current_lang]['Monster'].'</th>
                </tr>';
                foreach($fights_for_items_list as $bot)
                {
                    if($bots_meta[$maindatapackcode][$bot]['name'][$current_lang]=='')
                        $link=text_operation_do_for_url('bot '.$bot);
                    else if($bots_name_count[$maindatapackcode][$current_lang][$bots_meta[$maindatapackcode][$bot]['name'][$current_lang]]==1)
                        $link=text_operation_do_for_url($bots_meta[$maindatapackcode][$bot]['name'][$current_lang]);
                    else
                        $link=text_operation_do_for_url($bot.'-'.$bots_meta[$maindatapackcode][$bot]['name'][$current_lang]);
                    $bot_id=$bot;
                    $bot=$bots_meta[$maindatapackcode][$bot_id];
                    foreach($bot['step'] as $step_id=>$step)
                    {
                        if($step['type']=='fight')
                        {
                            $map=$bot_id_to_map[$bot_id][$maindatapackcode]['map'];
                            if(!isset($map_to_function[$maindatapackcode][$map]))
                                $map_to_function[$maindatapackcode][$map]=array();
                            if(!isset($map_to_function[$maindatapackcode][$map][$step['type']]))
                                $map_to_function[$maindatapackcode][$map][$step['type']]=1;
                            else
                                $map_to_function[$maindatapackcode][$map][$step['type']]++;

                            if(!isset($zone_to_function[$maindatapackcode][$maps_list[$maindatapackcode][$map]['zone']]))
                                $zone_to_function[$maindatapackcode][$maps_list[$maindatapackcode][$map]['zone']]=array();
                            if(!isset($zone_to_function[$maindatapackcode][$maps_list[$maindatapackcode][$map]['zone']][$step['type']]))
                                $zone_to_function[$maindatapackcode][$maps_list[$maindatapackcode][$map]['zone']][$step['type']]=1;
                            else
                                $zone_to_function[$maindatapackcode][$maps_list[$maindatapackcode][$map]['zone']][$step['type']]++;

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
                            
                            $map_descriptor.='<td>';
                            if(isset($fight_meta[$maindatapackcode][$step['fightid']]['monsters']))
                            {
                                foreach($fight_meta[$maindatapackcode][$step['fightid']]['monsters'] as $monster)
                                    $map_descriptor.=monsterAndLevelToDisplay($monster,$step['leader']);
                            }
                            else
                                echo 'Unable to resolv: $fight_meta['.$maindatapackcode.']['.$step['fightid'].'][\'monsters\']'."\n";

                            $map_descriptor.='</td>';
                        }
                        $map_descriptor.='</tr>';
                    }
                }
                $map_descriptor.='<tr>
                    <td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
                </tr>
                </table>';
            }
    }

	$content=$template;
	$content=str_replace('${TITLE}',$item['name'][$current_lang],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=clean_html($content);
	filewrite($datapack_explorer_local_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item['name'][$current_lang]).'.html',$content);
}