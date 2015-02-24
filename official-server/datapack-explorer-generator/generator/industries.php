<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator industries'."\n");

foreach($industrie_meta as $id=>$industry)
{
	if(!is_dir($datapack_explorer_local_path.'industries/'))
		mkdir($datapack_explorer_local_path.'industries/');
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>Industry #'.$id.'</h1>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Time to complet a cycle</div><div class="value">';
		if($industry['time']<(60*2))
			$map_descriptor.=$industry['time'].'s';
		elseif($industry['time']<(60*60*2))
			$map_descriptor.=($industry['time']/60).'mins';
		elseif($industry['time']<(60*60*24*2))
			$map_descriptor.=($industry['time']/(60*60)).'hours';
		else
			$map_descriptor.=($industry['time']/(60*60*24)).'days';
		$map_descriptor.='</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Cycle to be full</div><div class="value">'.$industry['cycletobefull'].'</div></div>';

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Resources</div><div class="value">';
		foreach($industry['resources'] as $resources)
		{
            $material=$resources['item'];
            $quantity=$resources['quantity'];
			if(isset($item_meta[$material]))
			{
				$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$material]['name']).'.html" title="'.$item_meta[$material]['name'].'">';
				$map_descriptor.='<table><tr><td>';
				if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
					$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" />';
				$map_descriptor.='</td><td>';
				if($quantity>1)
					$map_descriptor.=$quantity.'x ';
				$map_descriptor.=$item_meta[$material]['name'].'</td></tr></table>';
				$map_descriptor.='</a>';
			}
			else
				$map_descriptor.='Unknown material: '.$material;
		}
		$map_descriptor.='</div></div>';

        if(isset($industrie_link_meta[$id]))
            if(count($industrie_link_meta[$id]['requirements'])>0)
            {
                $map_descriptor.='<div class="subblock"><div class="valuetitle">Requirements</div><div class="value">';
                if(isset($industrie_link_meta[$id]['requirements']['quests']))
                {
                    foreach($industrie_link_meta[$id]['requirements']['quests'] as $quest_id)
                    {
                        $map_descriptor.='Quest: <a href="'.$base_datapack_explorer_site_path.'quests/'.$quest_id.'-'.text_operation_do_for_url($quests_meta[$quest_id]['name']).'.html" title="'.$quests_meta[$quest_id]['name'].'">';
                        $map_descriptor.=$quests_meta[$quest_id]['name'];
                        $map_descriptor.='</a><br />';
                    }
                }
                if(isset($industrie_link_meta[$id]['requirements']['reputation']))
                    foreach($industrie_link_meta[$id]['requirements']['reputation'] as $reputation)
                        $map_descriptor.=reputationLevelToText($reputation['type'],$reputation['level']).'<br />';
                $map_descriptor.='</div></div>';
            }

        if(isset($industry_to_bot[$id]))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">Location</div><div class="value">';
            $map_descriptor.='<table class="item_list item_list_type_normal map_list">
                    <tr class="item_list_title item_list_title_type_normal">
                        <th colspan="2">Bot</th>
                        <th colspan="2">Map</th>
                        </tr>';
            foreach($industry_to_bot[$id] as $bot_id)
            {
                $map_descriptor.='<tr class="value">';
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
                    if($bots_meta[$bot_id]['name']=='')
                        $link_bot=text_operation_do_for_url('bot '.$bot_id);
                    else if($bots_name_count[$bots_meta[$bot_id]['name']]==1)
                        $link_bot=text_operation_do_for_url($bots_meta[$bot_id]['name']);
                    else
                        $link_bot=text_operation_do_for_url($bot_id.'-'.$bots_meta[$bot_id]['name']);
                    $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.'bots/'.text_operation_do_for_url($link_bot).'.html" title="'.$final_name.'">'.$final_name.'</a></td>';
                    if(isset($bot_id_to_map[$bot_id]))
                    {
                        $entry=$bot_id_to_map[$bot_id];
                        if(isset($maps_list[$entry]))
                        {
                            if(isset($zone_meta[$maps_list[$entry]['zone']]))
                            {
                                $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$entry).'" title="'.$maps_list[$entry]['name'].'">'.$maps_list[$entry]['name'].'</a></td>';
                                $map_descriptor.='<td>'.$zone_meta[$maps_list[$entry]['zone']]['name'].'</td>';
                            }
                            else
                                $map_descriptor.='<td colspan="2"><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$entry).'" title="'.$maps_list[$entry]['name'].'">'.$maps_list[$entry]['name'].'</a></td>';
                        }
                        else
                            $map_descriptor.='<td colspan="2">Unknown map</td>';
                    }
                    else
                        $map_descriptor.='<td colspan="2">&nbsp;</td>';
                }
                else
                    $map_descriptor.='<td colspan="4"></td>';
                $map_descriptor.='</tr>';
            }
            $map_descriptor.='<tr>
                <td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
            </tr>
            </table><br style="clear:both;" />';
            $map_descriptor.='</div></div>';
        }

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Products</div><div class="value">';
		foreach($industry['products'] as $products)
		{
            $material=$products['item'];
            $quantity=$products['quantity'];
			if(isset($item_meta[$material]))
			{
				$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$material]['name']).'.html" title="'.$item_meta[$material]['name'].'">';
				if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
					$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" />';
				$map_descriptor.='</td><td>';
				if($quantity>1)
					$map_descriptor.=$quantity.'x ';
				$map_descriptor.=$item_meta[$material]['name'].'</td></tr></table>';
				$map_descriptor.='</a>';
			}
			else
				$map_descriptor.='Unknown material: '.$material;
		}
		$map_descriptor.='</div></div>';
	$map_descriptor.='</div>';

	$content=$template;
	$content=str_replace('${TITLE}','Industry #'.$id,$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=clean_html($content);
	filewrite($datapack_explorer_local_path.'industries/'.$id.'.html',$content);
}

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>Industry</th>
	<th>Resources</th>
	<th>Products</th>
    <th>Location</th>
</tr>';
foreach($industrie_meta as $id=>$industry)
{
	$map_descriptor.='<tr class="value">';
	$map_descriptor.='<td>';
	$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'industries/'.$id.'.html">#'.$id.'</a>';
	$map_descriptor.='</td>';
	$map_descriptor.='<td><center>';
	foreach($industry['resources'] as $resources)
	{
        $item=$resources['item'];
        $quantity=$resources['quantity'];
		if(isset($item_meta[$item]))
		{
			$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
			$name=$item_meta[$item]['name'];
			if($item_meta[$item]['image']!='')
				$image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
			else
				$image='';
			$map_descriptor.='<div style="float:left;text-align:middle;">';
			if($image!='')
			{
				if($link!='')
					$map_descriptor.='<a href="'.$link.'">';
				$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
				if($link!='')
					$map_descriptor.='</a>';
			}
			if($link!='')
				$map_descriptor.='<a href="'.$link.'">';
			if($name!='')
				$map_descriptor.=$name;
			else
				$map_descriptor.='Unknown resources name ('.$item.')';
			if($link!='')
				$map_descriptor.='</a></div>';
		}
		else
			$map_descriptor.='Unknown resources ('.$item.')';
	}
	$map_descriptor.='</center></td>';
	$map_descriptor.='<td><center>';
	foreach($industry['products'] as $products)
	{
        $item=$products['item'];
        $quantity=$products['quantity'];
		if(isset($item_meta[$item]))
		{
			$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
			$name=$item_meta[$item]['name'];
			if($item_meta[$item]['image']!='')
				$image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
			else
				$image='';
			$map_descriptor.='<div style="float:left;text-align:middle;">';
			if($image!='')
			{
				if($link!='')
					$map_descriptor.='<a href="'.$link.'">';
				$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
				if($link!='')
					$map_descriptor.='</a>';
			}
			if($link!='')
				$map_descriptor.='<a href="'.$link.'">';
			if($name!='')
				$map_descriptor.=$name;
			else
				$map_descriptor.='Unknown products name ('.$item.')';
			if($link!='')
				$map_descriptor.='</a></div>';
		}
		else
			$map_descriptor.='Unknown products ('.$item.')';
	}
	$map_descriptor.='</center></td><td>';
    if(isset($industry_to_bot[$id]))
    {
        $map_descriptor.='<table class="item_list item_list_type_normal map_list">';
        foreach($industry_to_bot[$id] as $bot_id)
        {
            $map_descriptor.='<tr class="value">';
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
                if($bots_meta[$bot_id]['name']=='')
                    $link_bot=text_operation_do_for_url('bot '.$bot_id);
                else if($bots_name_count[$bots_meta[$bot_id]['name']]==1)
                    $link_bot=text_operation_do_for_url($bots_meta[$bot_id]['name']);
                else
                    $link_bot=text_operation_do_for_url($bot_id.'-'.$bots_meta[$bot_id]['name']);
                $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.'bots/'.text_operation_do_for_url($link_bot).'.html" title="'.$final_name.'">'.$final_name.'</a></td>';
                if(isset($bot_id_to_map[$bot_id]))
                {
                    $entry=$bot_id_to_map[$bot_id];
                    if(isset($maps_list[$entry]))
                    {
                        if(isset($zone_meta[$maps_list[$entry]['zone']]))
                        {
                            $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$entry).'" title="'.$maps_list[$entry]['name'].'">'.$maps_list[$entry]['name'].'</a></td>';
                            $map_descriptor.='<td>'.$zone_meta[$maps_list[$entry]['zone']]['name'].'</td>';
                        }
                        else
                            $map_descriptor.='<td colspan="2"><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$entry).'" title="'.$maps_list[$entry]['name'].'">'.$maps_list[$entry]['name'].'</a></td>';
                    }
                    else
                        $map_descriptor.='<td colspan="2">Unknown map</td>';
                }
                else
                    $map_descriptor.='<td colspan="2">&nbsp;</td>';
            }
            else
                $map_descriptor.='<td colspan="4"></td>';
            $map_descriptor.='</tr>';
        }
        $map_descriptor.='</table>';
    }
    $map_descriptor.='</td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';
$content=$template;
$content=str_replace('${TITLE}','Industries list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=clean_html($content);
filewrite($datapack_explorer_local_path.'industries.html',$content);