<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator industries'."\n");

foreach($industrie_meta as $id=>$industry)
{
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">'."\n";
		$map_descriptor.='<div class="subblock"><h1>Industry #'.$id.'</h1>'."\n";
		$map_descriptor.='</div>'."\n";
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Time to complet a cycle</div><div class="value">'."\n";
		if($industry['time']<(60*2))
			$map_descriptor.=$industry['time'].'s';
		elseif($industry['time']<(60*60*2))
			$map_descriptor.=($industry['time']/60).'mins';
		elseif($industry['time']<(60*60*24*2))
			$map_descriptor.=($industry['time']/(60*60)).'hours';
		else
			$map_descriptor.=($industry['time']/(60*60*24)).'days';
		$map_descriptor.='</div></div>'."\n";
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Cycle to be full</div><div class="value">'.$industry['cycletobefull'].'</div></div>'."\n";

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Resources</div><div class="value">'."\n";
		foreach($industry['resources'] as $resources)
		{
            $material=$resources['item'];
            $quantity=$resources['quantity'];
			if(isset($item_meta[$material]))
			{
				$map_descriptor.='<table><tr><td>'."\n";
				if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
                {
                    $map_descriptor.='[[Items:'.$item_meta[$material]['name'].'|';
					$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" />';
                    $map_descriptor.=']]';
                }
				$map_descriptor.='</td><td>'."\n";
                $map_descriptor.='[[Items:'.$item_meta[$material]['name'].'|';
				if($quantity>1)
					$map_descriptor.=$quantity.'x ';
				$map_descriptor.=$item_meta[$material]['name'].']]</td></tr></table>'."\n";
			}
			else
				$map_descriptor.='Unknown material: '.$material;
		}
		$map_descriptor.='</div></div>'."\n";

        if(isset($industrie_link_meta[$id]))
            if(count($industrie_link_meta[$id]['requirements'])>0)
            {
                $map_descriptor.='<div class="subblock"><div class="valuetitle">Requirements</div><div class="value">'."\n";
                if(isset($industrie_link_meta[$id]['requirements']['quests']))
                    foreach($industrie_link_meta[$id]['requirements']['quests'] as $quest_id)
                        $map_descriptor.='Quest: [['.$quest_id.'_'.$quests_meta[$quest_id]['name'].'|'.$quests_meta[$quest_id]['name'].']]<br />'."\n";
                if(isset($industrie_link_meta[$id]['requirements']['reputation']))
                    foreach($industrie_link_meta[$id]['requirements']['reputation'] as $reputation)
                        $map_descriptor.=reputationLevelToText($reputation['type'],$reputation['level']).'<br />'."\n";
                $map_descriptor.='</div></div>'."\n";
            }

        if(isset($industry_to_bot[$id]))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">Location</div><div class="value">'."\n";
            $map_descriptor.='<table class="item_list item_list_type_normal map_list">
                    <tr class="item_list_title item_list_title_type_normal">
                        <th colspan="2">Bot</th>
                        <th colspan="2">Map</th>
                        </tr>'."\n";
            foreach($industry_to_bot[$id] as $bot_id)
            {
                $map_descriptor.='<tr class="value">'."\n";
                if(isset($bots_meta[$bot_id]))
                {
                    $bot=$bots_meta[$bot_id];
                    if($bot['name']=='')
                        $final_name='Bot #'.$bot_id;
                    else
                        $final_name=$bot['name'];
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
                    if($bots_meta[$bot_id]['name']=='')
                        $link_bot=text_operation_do_for_url('bot '.$bot_id);
                    else if($bots_name_count[$bots_meta[$bot_id]['name']]==1)
                        $link_bot=text_operation_do_for_url($bots_meta[$bot_id]['name']);
                    else
                        $link_bot=text_operation_do_for_url($bot_id.'-'.$bots_meta[$bot_id]['name']);
                    if($bot['name']=='')
                        $map_descriptor.='>[[Bots:'.$link_bot.'|Bot #'.$bot_id.']]</td>'."\n";
                    else
                        $map_descriptor.='>[[Bots:'.$link_bot.'|'.$bot['name'].']]</td>'."\n";
                    if(isset($bot_id_to_map[$bot_id]))
                    {
                        $entry=$bot_id_to_map[$bot_id];
                        if(isset($maps_list[$entry]))
                        {
                            if(isset($zone_meta[$maps_list[$entry]['zone']]))
                            {
                                $map_descriptor.='<td>[[Maps:'.map_to_wiki_name($entry).'|'.$maps_list[$entry]['name'].']]</td>'."\n";
                                $map_descriptor.='<td>[[Zones:'.$zone_meta[$maps_list[$entry]['zone']]['name'].'|'.$zone_meta[$maps_list[$entry]['zone']]['name'].']]</td>'."\n";
                            }
                            else
                                $map_descriptor.='<td colspan="2">[[Maps:'.map_to_wiki_name($entry).'|'.$maps_list[$entry]['name'].']]</td>'."\n";
                        }
                        else
                            $map_descriptor.='<td colspan="2">Unknown map</td>'."\n";
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

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Products</div><div class="value">'."\n";
		foreach($industry['products'] as $products)
		{
            $material=$products['item'];
            $quantity=$products['quantity'];
			if(isset($item_meta[$material]))
			{
                $map_descriptor.='<table><tr><td>'."\n";
                if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
                {
                    $map_descriptor.='[[Items:'.$item_meta[$material]['name'].'|';
                    $map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" />';
                    $map_descriptor.=']]';
                }
                $map_descriptor.='</td><td>'."\n";
                $map_descriptor.='[[Items:'.$item_meta[$material]['name'].'|';
                if($quantity>1)
                    $map_descriptor.=$quantity.'x ';
                $map_descriptor.=$item_meta[$material]['name'].']]</td></tr></table>'."\n";
			}
			else
				$map_descriptor.='Unknown material: '.$material;
		}
		$map_descriptor.='</div></div>'."\n";
	$map_descriptor.='</div>'."\n";
    
    savewikipage('Template:industry_'.$id,$map_descriptor);$map_descriptor='';

    if($wikivarsapp['generatefullpage'])
    {
        $map_descriptor.='{{Template:industry_'.$id.'}}'."\n";
        savewikipage('Industries:Industry '.$id,$map_descriptor);
    }
}

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>Industry</th>
	<th>Resources</th>
	<th>Products</th>
    <th>Location</th>
</tr>'."\n";
foreach($industrie_meta as $id=>$industry)
{
	$map_descriptor.='<tr class="value">'."\n";
	$map_descriptor.='<td>'."\n";
	$map_descriptor.='[[Industries:Industry '.$id.'|Industry '.$id.']]'."\n";
	$map_descriptor.='</td>'."\n";
	$map_descriptor.='<td><center>'."\n";
	foreach($industry['resources'] as $resources)
	{
        $item=$resources['item'];
        $quantity=$resources['quantity'];
		if(isset($item_meta[$item]))
		{
			$link=$base_datapack_site_http.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
			$name=$item_meta[$item]['name'];
			if($item_meta[$item]['image']!='')
				$image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
			else
				$image='';
			$map_descriptor.='<div style="float:left;text-align:middle;">'."\n";
			if($image!='')
			{
				if($link!='')
					$map_descriptor.='[[Items:'.$name.'|';
				$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
				if($link!='')
					$map_descriptor.=']]'."\n";
			}
			if($link!='')
				$map_descriptor.='[[Items:'.$name.'|';
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
			$link=$base_datapack_site_http.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
			$name=$item_meta[$item]['name'];
			if($item_meta[$item]['image']!='')
				$image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
			else
				$image='';
			$map_descriptor.='<div style="float:left;text-align:middle;">'."\n";
			if($image!='')
			{
				if($link!='')
					$map_descriptor.='[[Items:'.$name.'|';
				$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
				if($link!='')
					$map_descriptor.=']]'."\n";
			}
			if($link!='')
				$map_descriptor.='[[Items:'.$name.'|';
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
                if($bot['name']=='')
                    $final_url_name='bot-'.$bot_id;
                else if($bots_name_count[$bot['name']]==1)
                    $final_url_name=$bot['name'];
                else
                    $final_url_name=$bot_id.'-'.$bot['name'];
                if($bot['name']=='')
                    $final_name='Bot #'.$bot_id;
                else
                    $final_name=$bot['name'];
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
                if($bots_meta[$bot_id]['name']=='')
                    $link_bot=text_operation_do_for_url('bot '.$bot_id);
                else if($bots_name_count[$bots_meta[$bot_id]['name']]==1)
                    $link_bot=text_operation_do_for_url($bots_meta[$bot_id]['name']);
                else
                    $link_bot=text_operation_do_for_url($bot_id.'-'.$bots_meta[$bot_id]['name']);
                    if($bot['name']=='')
                        $map_descriptor.='>[[Bots:'.$link_bot.'|Bot #'.$bot_id.']]</td>'."\n";
                    else
                        $map_descriptor.='>[[Bots:'.$link_bot.'|'.$bot['name'].']]</td>'."\n";
                if(isset($bot_id_to_map[$bot_id]))
                {
                    $entry=$bot_id_to_map[$bot_id];
                    if(isset($maps_list[$entry]))
                    {
                        if(isset($zone_meta[$maps_list[$entry]['zone']]))
                        {
                            $map_descriptor.='<td>[[Maps:'.map_to_wiki_name($entry).'|'.$maps_list[$entry]['name'].']]</td>'."\n";
                            $map_descriptor.='<td>[[Zones:'.$zone_meta[$maps_list[$entry]['zone']]['name'].'|'.$zone_meta[$maps_list[$entry]['zone']]['name'].']]</td>'."\n";
                        }
                        else
                            $map_descriptor.='<td colspan="2">[[Maps:'.map_to_wiki_name($entry).'|'.$maps_list[$entry]['name'].']]</td>'."\n";
                    }
                    else
                        $map_descriptor.='<td colspan="2">Unknown map</td>'."\n";
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

savewikipage('Template:industries_list',$map_descriptor);$map_descriptor='';

if($wikivarsapp['generatefullpage'])
{
    $map_descriptor.='{{Template:industries_list}}'."\n";
    savewikipage('Industries_list',$map_descriptor);
}
