<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator quests'."\n");

require_once 'datapack-explorer-generator/functions/quests.php';

foreach($quests_meta as $id=>$quest)
{
	if(!is_dir($datapack_explorer_local_path.$translation_list[$current_lang]['quests/']))
		mkdir($datapack_explorer_local_path.$translation_list[$current_lang]['quests/']);
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>'.$quest['name'][$current_lang];
		if($quest['repeatable'])
			$map_descriptor.=' ('.$translation_list[$current_lang]['repeatable'].')';
		else
			$map_descriptor.=' ('.$translation_list[$current_lang]['one time'].')';
		$map_descriptor.='</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';

        $bot_id=$quest['bot'];
        if(isset($bots_meta[$bot_id]))
        {
            $map_descriptor.='<center><table class="item_list item_list_type_normal"><tr class="value">';
            $bot=$bots_meta[$bot_id];
            if($bot['name'][$current_lang]=='')
                $final_url_name='bot-'.$bot_id;
            else if($bots_name_count[$current_lang][$bot['name'][$current_lang]]==1)
                $final_url_name=$bot['name'][$current_lang];
            else
                $final_url_name=$bot_id.'-'.$bot['name'][$current_lang];
            if($bot['name'][$current_lang]=='')
                $final_name='Bot #'.$bot_id;
            else
                $final_name=$bot['name'][$current_lang];
            $skin_found=true;
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
                    $skin_found=false;
            }
            else
                $skin_found=false;
            $map_descriptor.='<td';
            if(!$skin_found)
                $map_descriptor.=' colspan="2"';
            $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['bots/'].text_operation_do_for_url($final_url_name).'.html" title="'.$final_name.'">'.$final_name;
            if(isset($bot_id_to_map[$bot_id]))
            {
                $entry=$bot_id_to_map[$bot_id];
                if(isset($maps_list[$entry]))
                {
                    if(isset($zone_meta[$maps_list[$entry]['zone']]))
                    {
                        $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].str_replace('.tmx','.html',$entry).'" title="'.$maps_list[$entry]['name'][$current_lang].'">'.$maps_list[$entry]['name'][$current_lang].'</a></td>';
                        $map_descriptor.='<td>'.$zone_meta[$maps_list[$entry]['zone']]['name'][$current_lang].'</td>';
                    }
                    else
                        $map_descriptor.='<td colspan="2"><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].str_replace('.tmx','.html',$entry).'" title="'.$maps_list[$entry]['name'][$current_lang].'">'.$maps_list[$entry]['name'][$current_lang].'</a></td>';
                }
                else
                    $map_descriptor.='<td colspan="2">'.$translation_list[$current_lang]['Unknown map'].'</td>';
            }
            else
                $map_descriptor.='<td colspan="2">&nbsp;</td>';
            $map_descriptor.='</a></td>';
            $map_descriptor.='</tr></table></center>';
        }

		$map_descriptor.='</div>';

		if(count($quest['requirements'])>0)
		{
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Requirements'].'</div><div class="value">';
			if(isset($quest['requirements']['quests']))
			{
				foreach($quest['requirements']['quests'] as $quest_id)
				{
					$map_descriptor.=$translation_list[$current_lang]['Quest:'].' <a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['quests/'].$quest_id.'-'.text_operation_do_for_url($quests_meta[$quest_id]['name'][$current_lang]).'.html" title="'.$quests_meta[$quest_id]['name'][$current_lang].'">';
					$map_descriptor.=$quests_meta[$quest_id]['name'][$current_lang];
					$map_descriptor.='</a><br />';
				}
			}
            if(isset($quest['requirements']['reputation']))
                foreach($quest['requirements']['reputation'] as $reputation)
                    $map_descriptor.=reputationLevelToText($reputation['type'],$reputation['level']).'<br />';
            $map_descriptor.='</div></div>';
		}
		if(count($quest['steps'])>0)
		{
			foreach($quest['steps'] as $id_step=>$step)
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Step'].' #'.$id_step;

                if(isset($step))
                    $bot_id=$step['bot'];
                else
                    $bot_id=$quest['bot'];
                if(isset($bots_meta[$bot_id]) && $step['bot']!=$quest['bot'])
                {
                    $map_descriptor.='<center><table class="item_list item_list_type_normal"><tr class="value">';
                    $bot=$bots_meta[$bot_id];
                    if($bot['name'][$current_lang]=='')
                        $final_url_name='bot-'.$bot_id;
                    else if($bots_name_count[$current_lang][$bot['name'][$current_lang]]==1)
                        $final_url_name=$bot['name'][$current_lang];
                    else
                        $final_url_name=$bot_id.'-'.$bot['name'][$current_lang];
                    if($bot['name'][$current_lang]=='')
                        $final_name='Bot #'.$bot_id;
                    else
                        $final_name=$bot['name'][$current_lang];
                    $skin_found=true;
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
                            $skin_found=false;
                    }
                    else
                        $skin_found=false;
                    $map_descriptor.='<td';
                    if(!$skin_found)
                        $map_descriptor.=' colspan="2"';
                    $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['bots/'].text_operation_do_for_url($final_url_name).'.html" title="'.$final_name.'">'.$final_name.'</a></td>';
                    if(isset($bot_id_to_map[$bot_id]))
                    {
                        $entry=$bot_id_to_map[$bot_id];
                        if(isset($maps_list[$entry]))
                        {
                            if(isset($zone_meta[$maps_list[$entry]['zone']]))
                            {
                                $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].str_replace('.tmx','.html',$entry).'" title="'.$maps_list[$entry]['name'][$current_lang].'">'.$maps_list[$entry]['name'][$current_lang].'</a></td>';
                                $map_descriptor.='<td>'.$zone_meta[$maps_list[$entry]['zone']]['name'][$current_lang].'</td>';
                            }
                            else
                                $map_descriptor.='<td colspan="2"><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].str_replace('.tmx','.html',$entry).'" title="'.$maps_list[$entry]['name'][$current_lang].'">'.$maps_list[$entry]['name'][$current_lang].'</a></td>';
                        }
                        else
                            $map_descriptor.='<td colspan="2">'.$translation_list[$current_lang]['Unknown map'].'</td>';
                    }
                    else
                        $map_descriptor.='<td colspan="2">&nbsp;</td>';
                    $map_descriptor.='</tr></table></center>';
                }

                $map_descriptor.='</div><div class="value">';
				$map_descriptor.=$step['text'];
				if(count($step['items']))
				{
					$show_full=false;
					foreach($step['items'] as $item)
					{
						if(isset($item['monster']))
						{
							if(isset($monster_meta[$item['monster']]))
								$show_full=true;
						}
					}
					$map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">';
					if($show_full)
						$map_descriptor.='<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th><th colspan="2">'.$translation_list[$current_lang]['Monster'].'</th><th>Luck</th></tr>';
					else
						$map_descriptor.='<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th></tr>';
					foreach($step['items'] as $item)
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
							$map_descriptor.=$quantity_text.$translation_list[$current_lang]['Unknown item'];
						if($link!='')
							$map_descriptor.='</a>';
						$map_descriptor.='</td>';
						if(isset($item['monster']))
						{
							if(isset($monster_meta[$item['monster']]))
							{
								$name=$monster_meta[$item['monster']]['name'][$current_lang];
								$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html';
								$map_descriptor.='<td>';
								if(file_exists($datapack_path.'monsters/'.$item['monster'].'/small.png'))
									$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item['monster'].'/small.png" width="32" height="32" alt="'.$name.'" title="'.$name.'" /></a></div>';
								else if(file_exists($datapack_path.'monsters/'.$item['monster'].'/small.gif'))
									$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item['monster'].'/small.gif" width="32" height="32" alt="'.$name.'" title="'.$name.'" /></a></div>';
								$map_descriptor.='</td>
								<td><a href="'.$link.'">'.$name.'</a></td>';
								$map_descriptor.='<td>'.$item['rate'].'%</td>';
							}
							else if($show_full)
								$map_descriptor.='<td></td><td></td><td></td>';
						}
						else if($show_full)
							$map_descriptor.='<td></td><td></td><td></td>';
						$map_descriptor.='</tr>';
					}
					if($show_full)
						$map_descriptor.='<tr>
						<td colspan="5" class="item_list_endline item_list_title_type_outdoor"></td>
						</tr></table>';
					else
						$map_descriptor.='<tr>
						<td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
						</tr></table>';
					$map_descriptor.='<br />';
				}
				$map_descriptor.='</div></div>';
			}
		}
		if(count($quest['rewards'])>0)
		{
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Rewards'].'</div><div class="value">';
            if(isset($quest['rewards']['items']))
            {
                $map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">
                <th colspan="2">'.$translation_list[$current_lang]['Item'].'</th></tr>';
                foreach($quest['rewards']['items'] as $item)
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
                        $map_descriptor.=$quantity_text.$translation_list[$current_lang]['Unknown item'];
                    if($link!='')
                        $map_descriptor.='</a>';
                    $map_descriptor.='</td></tr>';
                }
                $map_descriptor.='<tr>
                <td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
                </tr></table>';
            }
            if(isset($quest['rewards']['reputation']))
                foreach($quest['rewards']['reputation'] as $reputation)
                {
                    if($reputation['point']<0)
                        $map_descriptor.=$translation_list[$current_lang]['Less reputation in:'].' '.reputationToText($reputation['type']);
                    else
                        $map_descriptor.=$translation_list[$current_lang]['More reputation in:'].' '.reputationToText($reputation['type']);
                }
            if(isset($quest['rewards']['allow']))
                foreach($quest['rewards']['allow'] as $allow)
                {
                    if($allow=='clan')
                        $map_descriptor.=$translation_list[$current_lang]['Able to create clan'];
                    else
                        $map_descriptor.=$translation_list[$current_lang]['Allow'].' '.$allow;
                }
            $map_descriptor.='</div></div>';
		}
	$map_descriptor.='</div>';

	$content=$template;
	$content=str_replace('${TITLE}',$quest['name'][$current_lang],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=clean_html($content);
	filewrite($datapack_explorer_local_path.$translation_list[$current_lang]['quests/'].$id.'-'.text_operation_do_for_url($quest['name'][$current_lang]).'.html',$content);
}

$map_descriptor='';

$map_descriptor.=questList(array_keys($quests_meta),true);

$content=$template;
$content=str_replace('${TITLE}',$translation_list[$current_lang]['Quests list'],$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=clean_html($content);
filewrite($datapack_explorer_local_path.$translation_list[$current_lang]['quests.html'],$content);