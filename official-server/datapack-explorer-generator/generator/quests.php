<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator quests'."\n");

foreach($quests_meta as $id=>$quest)
{
	if(!is_dir($datapack_explorer_local_path.'quests/'))
		mkdir($datapack_explorer_local_path.'quests/');
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>'.$quest['name'];
		if($quest['repeatable'])
			$map_descriptor.=' (repeatable)';
		else
			$map_descriptor.=' (one time)';
		$map_descriptor.='</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		$map_descriptor.='</div>';

		if(count($quest['requirements'])>0)
		{
            $map_descriptor.='<div class="subblock"><div class="valuetitle">Requirements</div><div class="value">';
			if(isset($quest['requirements']['quests']))
			{
				foreach($quest['requirements']['quests'] as $quest_id)
				{
					$map_descriptor.='Quest: <a href="'.$base_datapack_explorer_site_path.'quests/'.$quest_id.'-'.text_operation_do_for_url($quests_meta[$quest_id]['name']).'.html" title="'.$quests_meta[$quest_id]['name'].'">';
					$map_descriptor.=$quests_meta[$quest_id]['name'];
					$map_descriptor.='</a><br />';
				}
			}
            if(isset($quest['requirements']['reputation']))
                foreach($quest['requirements']['reputation'] as $reputation)
                    $map_descriptor.='Level '.$reputation['level'].' in '.$reputation['type'].'<br />';
            $map_descriptor.='</div></div>';
		}
		if(count($quest['steps'])>0)
		{
			foreach($quest['steps'] as $id_step=>$step)
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">Step #'.$id_step.'</div><div class="value">';
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
						$map_descriptor.='<th colspan="2">Item</th><th colspan="2">Monster</th><th>Luck</th></tr>';
					else
						$map_descriptor.='<th colspan="2">Item</th></tr>';
					foreach($step['items'] as $item)
					{
						$map_descriptor.='<tr class="value"><td>';
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
						$map_descriptor.='</td>';
						if(isset($item['monster']))
						{
							if(isset($monster_meta[$item['monster']]))
							{
								$name=$monster_meta[$item['monster']]['name'];
								$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
								$map_descriptor.='<td>';
								if(file_exists($datapack_path.'monsters/'.$item['monster'].'/small.png'))
									$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$item['monster']]['name'].'" title="'.$monster_meta[$item['monster']]['name'].'" /></a></div>';
								else if(file_exists($datapack_path.'monsters/'.$item['monster'].'/small.gif'))
									$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$item['monster']]['name'].'" title="'.$monster_meta[$item['monster']]['name'].'" /></a></div>';
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
            $map_descriptor.='<div class="subblock"><div class="valuetitle">Rewards</div><div class="value">';
            if(isset($quest['rewards']['items']))
            {
                $map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">
                <th colspan="2">Item</th></tr>';
                foreach($quest['rewards']['items'] as $item)
                {
                    $map_descriptor.='<tr class="value"><td>';
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
            if(isset($quest['rewards']['reputation']))
                foreach($quest['rewards']['reputation'] as $reputation)
                {
                    if($reputation['point']<0)
                        $map_descriptor.='Less reputation in: '.$reputation['type'];
                    else
                        $map_descriptor.='More reputation in: '.$reputation['type'];
                }
            if(isset($quest['rewards']['allow']))
                foreach($quest['rewards']['allow'] as $allow)
                {
                    if($allow=='clan')
                        $map_descriptor.='Able to create clan';
                    else
                        $map_descriptor.='Allow '.$allow;
                }
            $map_descriptor.='</div></div>';
		}
	$map_descriptor.='</div>';

	$content=$template;
	$content=str_replace('${TITLE}',$quest['name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'quests/'.$id.'-'.text_operation_do_for_url($quest['name']).'.html',$content);
}

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>Quests</th>
    <th>Repeatable</th>
    <th colspan="4">Starting bot</th>
    <th>Rewards</th>
</tr>';
foreach($quests_meta as $id=>$quest)
{
	$map_descriptor.='<tr class="value">';
	$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'quests/'.$id.'-'.text_operation_do_for_url($quest['name']).'.html" title="'.$quest['name'].'">'.$quest['name'].'</a></td>';
    if($quest['repeatable'])
        $map_descriptor.='<td>Yes</td>';
    else
        $map_descriptor.='<td>No</td>';
    if(isset($quest['steps'][1]))
        $bot_id=$quest['steps'][1]['bot'];
    else
        $bot_id=$quest['bot'];
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
                $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></center></div></td>';
            elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
                $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></center></div></td>';
            elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></center></div></td>';
            elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></center></div></td>';
            else
                $skin_found=false;
        }
        else
            $skin_found=false;
        $map_descriptor.='<td';
        if(!$skin_found)
            $map_descriptor.=' colspan="2"';
        $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.'bots/'.text_operation_do_for_url($final_url_name).'.html" title="'.$final_name.'">'.$final_name;
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
        $map_descriptor.='</a></td>';
    }
    else
        $map_descriptor.='<td colspan="4"></td>';
    $map_descriptor.='<td>';
    if(count($quest['rewards'])>0)
    {
        $map_descriptor.='<div class="subblock"><div class="valuetitle">Rewards</div><div class="value">';
        if(isset($quest['rewards']['items']))
        {
            $map_descriptor.='<table>';
            foreach($quest['rewards']['items'] as $item)
            {
                $map_descriptor.='<tr class="value"><td>';
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
            $map_descriptor.='</table>';
        }
        if(isset($quest['rewards']['reputation']))
            foreach($quest['rewards']['reputation'] as $reputation)
            {
                if($reputation['point']<0)
                    $map_descriptor.='Less reputation in: '.$reputation['type'];
                else
                    $map_descriptor.='More reputation in: '.$reputation['type'];
            }
        if(isset($quest['rewards']['allow']))
            foreach($quest['rewards']['allow'] as $allow)
            {
                if($allow=='clan')
                    $map_descriptor.='Able to create clan';
                else
                    $map_descriptor.='Allow '.$allow;
            }
        $map_descriptor.='</div></div>';
    }
    $map_descriptor.='</td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="7" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';

$content=$template;
$content=str_replace('${TITLE}','Quests list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'quests.html',$content);