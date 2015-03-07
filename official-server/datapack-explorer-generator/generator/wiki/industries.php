<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator industries'."\n");

foreach($industrie_meta as $id=>$industry)
{
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">'."\n";
		$map_descriptor.='<div class="subblock"><h1>'.str_replace('[id]',$id,$translation_list[$current_lang]['Industry [id]']).'</h1>'."\n";
		$map_descriptor.='</div>'."\n";
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Time to complet a cycle'].'</div><div class="value">'."\n";
		if($industry['time']<(60*2))
			$map_descriptor.=$industry['time'].$translation_list[$current_lang]['s'];
		elseif($industry['time']<(60*60*2))
			$map_descriptor.=($industry['time']/60).$translation_list[$current_lang]['mins'];
		elseif($industry['time']<(60*60*24*2))
			$map_descriptor.=($industry['time']/(60*60)).$translation_list[$current_lang]['hours'];
		else
			$map_descriptor.=($industry['time']/(60*60*24)).$translation_list[$current_lang]['days'];
		$map_descriptor.='</div></div>'."\n";
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Cycle to be full'].'</div><div class="value">'.$industry['cycletobefull'].'</div></div>'."\n";

		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Resources'].'</div><div class="value">'."\n";
		foreach($industry['resources'] as $resources)
		{
            $material=$resources['item'];
            $quantity=$resources['quantity'];
			if(isset($item_meta[$material]))
			{
				$map_descriptor.='<table><tr><td>'."\n";
				if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
                {
                    $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$material]['name'][$current_lang].'|';
					$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'][$current_lang].'" title="'.$item_meta[$material]['name'][$current_lang].'" />';
                    $map_descriptor.=']]';
                }
				$map_descriptor.='</td><td>'."\n";
                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$material]['name'][$current_lang].'|';
				if($quantity>1)
					$map_descriptor.=$quantity.'x ';
				$map_descriptor.=$item_meta[$material]['name'][$current_lang].']]</td></tr></table>'."\n";
			}
			else
				$map_descriptor.='Unknown material: '.$material;
		}
		$map_descriptor.='</div></div>'."\n";

        if(isset($industrie_link_meta[$id]))
            if(count($industrie_link_meta[$id]['requirements'])>0)
            {
                $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Requirements'].'</div><div class="value">'."\n";
                if(isset($industrie_link_meta[$id]['requirements']['quests']))
                    foreach($industrie_link_meta[$id]['requirements']['quests'] as $quest_id)
                        $map_descriptor.=$translation_list[$current_lang]['Quest:'].' [['.$quest_id.'_'.$quests_meta[$quest_id]['name'][$current_lang].'|'.$quests_meta[$quest_id]['name'][$current_lang].']]<br />'."\n";
                if(isset($industrie_link_meta[$id]['requirements']['reputation']))
                    foreach($industrie_link_meta[$id]['requirements']['reputation'] as $reputation)
                        $map_descriptor.=reputationLevelToText($reputation['type'],$reputation['level']).'<br />'."\n";
                $map_descriptor.='</div></div>'."\n";
            }

        if(isset($industry_to_bot[$id]))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Location'].'</div><div class="value">'."\n";
            $map_descriptor.='<table class="item_list item_list_type_normal map_list">
                    <tr class="item_list_title item_list_title_type_normal">
                        <th colspan="2">'.$translation_list[$current_lang]['Bot'].'</th>
                        <th colspan="2">'.$translation_list[$current_lang]['Map'].'</th>
                        </tr>'."\n";
            foreach($industry_to_bot[$id] as $bot_id)
            {
                $map_descriptor.='<tr class="value">'."\n";
                if(isset($bots_meta[$bot_id]))
                {
                    $bot=$bots_meta[$bot_id];
                    if($bot['name'][$current_lang]=='')
                        $final_name='Bot #'.$bot_id;
                    else
                        $final_name=$bot['name'][$current_lang];
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
                    if($bots_meta[$bot_id]['name'][$current_lang]=='')
                        $link_bot=text_operation_do_for_url('bot '.$bot_id);
                    else if($bots_name_count[$current_lang][$bots_meta[$bot_id]['name'][$current_lang]]==1)
                        $link_bot=text_operation_do_for_url($bots_meta[$bot_id]['name'][$current_lang]);
                    else
                        $link_bot=text_operation_do_for_url($bot_id.'-'.$bots_meta[$bot_id]['name'][$current_lang]);
                    if($bot['name'][$current_lang]=='')
                        $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link_bot.'|Bot #'.$bot_id.']]</td>'."\n";
                    else
                        $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link_bot.'|'.$bot['name'][$current_lang].']]</td>'."\n";
                    if(isset($bot_id_to_map[$bot_id]))
                    {
                        $entry=$bot_id_to_map[$bot_id];
                        if(isset($maps_list[$entry]))
                        {
                            if(isset($zone_meta[$maps_list[$entry]['zone']]))
                            {
                                $map_descriptor.='<td>[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($entry).'|'.$maps_list[$entry]['name'][$current_lang].']]</td>'."\n";
                                $map_descriptor.='<td>[['.$translation_list[$current_lang]['Zones:'].$zone_meta[$maps_list[$entry]['zone']]['name'][$current_lang].'|'.$zone_meta[$maps_list[$entry]['zone']]['name'][$current_lang].']]</td>'."\n";
                            }
                            else
                                $map_descriptor.='<td colspan="2">[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($entry).'|'.$maps_list[$entry]['name'][$current_lang].']]</td>'."\n";
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
            </table><br style="clear:both;" />'."\n";
            $map_descriptor.='</div></div>'."\n";
        }

		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Products'].'</div><div class="value">'."\n";
		foreach($industry['products'] as $products)
		{
            $material=$products['item'];
            $quantity=$products['quantity'];
			if(isset($item_meta[$material]))
			{
                $map_descriptor.='<table><tr><td>'."\n";
                if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
                {
                    $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$material]['name'][$current_lang].'|';
                    $map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'][$current_lang].'" title="'.$item_meta[$material]['name'][$current_lang].'" />';
                    $map_descriptor.=']]';
                }
                $map_descriptor.='</td><td>'."\n";
                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$material]['name'][$current_lang].'|';
                if($quantity>1)
                    $map_descriptor.=$quantity.'x ';
                $map_descriptor.=$item_meta[$material]['name'][$current_lang].']]</td></tr></table>'."\n";
			}
			else
				$map_descriptor.='Unknown material: '.$material;
		}
		$map_descriptor.='</div></div>'."\n";
	$map_descriptor.='</div>'."\n";
    
    savewikipage('Template:industry_'.$id,$map_descriptor,false);$map_descriptor='';

    $map_descriptor.='{{Template:industry_'.$id.'}}'."\n";
    savewikipage($translation_list[$current_lang]['Industries:'].str_replace('[id]',$id,$translation_list[$current_lang]['Industry [id]']),$map_descriptor,!$wikivars['generatefullpage']);
}

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>'.$translation_list[$current_lang]['Industry'].'</th>
	<th>'.$translation_list[$current_lang]['Resources'].'</th>
	<th>'.$translation_list[$current_lang]['Products'].'</th>
    <th>'.$translation_list[$current_lang]['Location'].'</th>
</tr>'."\n";
foreach($industrie_meta as $id=>$industry)
{
	$map_descriptor.='<tr class="value">'."\n";
	$map_descriptor.='<td>'."\n";
	$map_descriptor.='[['.$translation_list[$current_lang]['Industries:'].str_replace('[id]',$id,$translation_list[$current_lang]['Industry [id]']).'|'.str_replace('[id]',$id,$translation_list[$current_lang]['Industry [id]']).']]'."\n";
	$map_descriptor.='</td>'."\n";
	$map_descriptor.='<td><center>'."\n";
	foreach($industry['resources'] as $resources)
	{
        $item=$resources['item'];
        $quantity=$resources['quantity'];
		if(isset($item_meta[$item]))
		{
			$link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html';
			$name=$item_meta[$item]['name'][$current_lang];
			if($item_meta[$item]['image']!='')
				$image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
			else
				$image='';
			$map_descriptor.='<div style="float:left;text-align:middle;">'."\n";
			if($image!='')
			{
				if($link!='')
					$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
				$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
				if($link!='')
					$map_descriptor.=']]'."\n";
			}
			if($link!='')
				$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
			if($name!='')
				$map_descriptor.=$name;
			else
				$map_descriptor.='Unknown resources name ('.$item.')';
			if($link!='')
				$map_descriptor.=']]</div>'."\n";
		}
		else
			$map_descriptor.='Unknown resources ('.$item.')';
	}
	$map_descriptor.='</center></td>'."\n";
	$map_descriptor.='<td><center>'."\n";
	foreach($industry['products'] as $products)
	{
        $item=$products['item'];
        $quantity=$products['quantity'];
		if(isset($item_meta[$item]))
		{
			$link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html';
			$name=$item_meta[$item]['name'][$current_lang];
			if($item_meta[$item]['image']!='')
				$image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
			else
				$image='';
			$map_descriptor.='<div style="float:left;text-align:middle;">'."\n";
			if($image!='')
			{
				if($link!='')
					$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
				$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
				if($link!='')
					$map_descriptor.=']]'."\n";
			}
			if($link!='')
				$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
			if($name!='')
				$map_descriptor.=$name;
			else
				$map_descriptor.='Unknown products name ('.$item.')';
			if($link!='')
				$map_descriptor.=']]</div>'."\n";
		}
		else
			$map_descriptor.='Unknown products ('.$item.')';
	}
	$map_descriptor.='</center></td><td>'."\n";
    if(isset($industry_to_bot[$id]))
    {
        $map_descriptor.='<table class="item_list item_list_type_normal map_list">'."\n";
        foreach($industry_to_bot[$id] as $bot_id)
        {
            $map_descriptor.='<tr class="value">'."\n";
            if(isset($bots_meta[$bot_id]))
            {
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
                if($bots_meta[$bot_id]['name'][$current_lang]=='')
                    $link_bot=text_operation_do_for_url('bot '.$bot_id);
                else if($bots_name_count[$current_lang][$bots_meta[$bot_id]['name'][$current_lang]]==1)
                    $link_bot=text_operation_do_for_url($bots_meta[$bot_id]['name'][$current_lang]);
                else
                    $link_bot=text_operation_do_for_url($bot_id.'-'.$bots_meta[$bot_id]['name'][$current_lang]);
                    if($bot['name'][$current_lang]=='')
                        $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link_bot.'|Bot #'.$bot_id.']]</td>'."\n";
                    else
                        $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link_bot.'|'.$bot['name'][$current_lang].']]</td>'."\n";
                if(isset($bot_id_to_map[$bot_id]))
                {
                    $entry=$bot_id_to_map[$bot_id];
                    if(isset($maps_list[$entry]))
                    {
                        if(isset($zone_meta[$maps_list[$entry]['zone']]))
                        {
                            $map_descriptor.='<td>[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($entry).'|'.$maps_list[$entry]['name'][$current_lang].']]</td>'."\n";
                            $map_descriptor.='<td>[['.$translation_list[$current_lang]['Zones:'].$zone_meta[$maps_list[$entry]['zone']]['name'][$current_lang].'|'.$zone_meta[$maps_list[$entry]['zone']]['name'][$current_lang].']]</td>'."\n";
                        }
                        else
                            $map_descriptor.='<td colspan="2">[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($entry).'|'.$maps_list[$entry]['name'][$current_lang].']]</td>'."\n";
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
        $map_descriptor.='</table>'."\n";
    }
    $map_descriptor.='</td>'."\n";
	$map_descriptor.='</tr>'."\n";
}
$map_descriptor.='<tr>
	<td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>'."\n";

savewikipage('Template:industries_list',$map_descriptor,false);$map_descriptor='';

$map_descriptor.='{{Template:industries_list}}'."\n";
savewikipage($translation_list[$current_lang]['Industries list'],$map_descriptor,!$wikivars['generatefullpage']);
