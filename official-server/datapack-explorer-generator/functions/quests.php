<?php
function questList($id_list,$showbot=true,$wiki=false)
{
    global $quests_meta;
    global $bot_id_to_skin;
    global $bots_meta;
    global $bot_id_to_map;
    global $base_datapack_explorer_site_path;
    global $datapack_path;
    global $bots_name_count;
    global $base_datapack_site_path;
    global $item_meta;
    global $base_datapack_site_http;
    global $maps_list;
    global $zone_meta;
    if(!$wiki)
        $real_base_datapack_site_path=$base_datapack_site_path;
    else
        $real_base_datapack_site_path=$base_datapack_site_http.$base_datapack_site_path;
    $map_descriptor='';

    $map_descriptor.='<table class="item_list item_list_type_normal">
    <tr class="item_list_title item_list_title_type_normal">
        <th>Quests</th>
        <th>Repeatable</th>'."\n";
    if($showbot)
         $map_descriptor.='<th colspan="4">Starting bot</th>'."\n";
    $map_descriptor.='<th>Rewards</th>
    </tr>'."\n";
    foreach($id_list as $id)
    {
        $map_descriptor.='<tr class="value">'."\n";
        if(!$wiki)
            $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'quests/'.$id.'-'.text_operation_do_for_url($quests_meta[$id]['name']).'.html" title="'.$quests_meta[$id]['name'].'">'.$quests_meta[$id]['name'].'</a></td>'."\n";
        else
            $map_descriptor.='<td>[[Quests:'.$id.'_'.$quests_meta[$id]['name'].'|'.$quests_meta[$id]['name'].']]</td>'."\n";
        if($quests_meta[$id]['repeatable'])
            $map_descriptor.='<td>Yes</td>'."\n";
        else
            $map_descriptor.='<td>No</td>'."\n";
        if(isset($quests_meta[$id]['steps'][1]))
            $bot_id=$quests_meta[$id]['steps'][1]['bot'];
        else
            $bot_id=$quests_meta[$id]['bot'];
        if($showbot)
        {
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
                        $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$real_base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
                    elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
                        $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$real_base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
                    elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                        $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$real_base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
                    elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                        $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$real_base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
                    else
                        $skin_found=false;
                }
                else
                    $skin_found=false;
                $map_descriptor.='<td';
                if(!$skin_found)
                    $map_descriptor.=' colspan="2"';
                if(!$wiki)
                    $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.'bots/'.text_operation_do_for_url($final_url_name).'.html" title="'.$final_name.'">'.$final_name.'</a></td>'."\n";
                else
                {
                    if($bots_meta[$bot_id]['name']=='')
                        $link=text_operation_do_for_url('bot '.$bot_id);
                    else if($bots_name_count[$bots_meta[$bot_id]['name']]==1)
                        $link=text_operation_do_for_url($bots_meta[$bot_id]['name']);
                    else
                        $link=text_operation_do_for_url($bot_id.'-'.$bots_meta[$bot_id]['name']);
                    if($bot['name']=='')
                        $map_descriptor.='>[[Bots:'.$link.'|Bot #'.$bot_id.']]</td>'."\n";
                    else
                        $map_descriptor.='>[[Bots:'.$link.'|'.$bot['name'].']]</td>'."\n";
                }
                if(isset($bot_id_to_map[$bot_id]))
                {
                    $entry=$bot_id_to_map[$bot_id];
                    if(isset($maps_list[$entry]))
                    {
                        if(!$wiki)
                        {
                            if(isset($zone_meta[$maps_list[$entry]['zone']]))
                            {
                                $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$entry).'" title="'.$maps_list[$entry]['name'].'">'.$maps_list[$entry]['name'].'</a></td>'."\n";
                                $map_descriptor.='<td>'.$zone_meta[$maps_list[$entry]['zone']]['name'].'</td>'."\n";
                            }
                            else
                                $map_descriptor.='<td colspan="2"><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$entry).'" title="'.$maps_list[$entry]['name'].'">'.$maps_list[$entry]['name'].'</a></td>'."\n";
                        }
                        else
                        {
                            if(isset($zone_meta[$maps_list[$entry]['zone']]))
                            {
                                $map_descriptor.='<td>[[Maps:'.map_to_wiki_name($entry).'|'.$maps_list[$entry]['name'].']]</td>'."\n";
                                $map_descriptor.='<td>[[Zones:'.$zone_meta[$maps_list[$entry]['zone']]['name'].'|'.$zone_meta[$maps_list[$entry]['zone']]['name'].']]</td>'."\n";
                            }
                            else
                                $map_descriptor.='<td colspan="2">[[Maps:'.map_to_wiki_name($entry).'|'.$maps_list[$entry]['name'].']]</td>'."\n";
                        }
                    }
                    else
                        $map_descriptor.='<td colspan="2">Unknown map</td>'."\n";
                }
                else
                    $map_descriptor.='<td colspan="2">&nbsp;</td>'."\n";
            }
            else
                $map_descriptor.='<td colspan="4">&nbsp;</td>'."\n";
        }
        $map_descriptor.='<td>'."\n";
        if(count($quests_meta[$id]['rewards'])>0)
        {
            $map_descriptor.='<div class="subblock"><div class="value">'."\n";
            if(isset($quests_meta[$id]['rewards']['items']))
            {
                $map_descriptor.='<table>'."\n";
                foreach($quests_meta[$id]['rewards']['items'] as $item)
                {
                    $map_descriptor.='<tr class="value"><td>'."\n";
                    if(isset($item_meta[$item['item']]))
                    {
                        $link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item['item']]['name']).'.html';
                        $name=$item_meta[$item['item']]['name'];
                        if($item_meta[$item['item']]['image']!='')
                            $image=$real_base_datapack_site_path.'/items/'.$item_meta[$item['item']]['image'];
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
                        {
                            if(!$wiki)
                                $map_descriptor.='<a href="'.$link.'">'."\n";
                            else
                                $map_descriptor.='[[Items:'.$name.'|';
                        }
                        $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />'."\n";
                        if($link!='')
                        {
                            if(!$wiki)
                                $map_descriptor.='</a>'."\n";
                            else
                                $map_descriptor.=']]';
                        }
                    }
                    $map_descriptor.='</td><td>'."\n";
                    if($link!='')
                    {
                        if(!$wiki)
                            $map_descriptor.='<a href="'.$link.'">'."\n";
                        else
                            $map_descriptor.='[[Items:'.$name.'|';
                    }
                    if($name!='')
                        $map_descriptor.=$quantity_text.$name;
                    else
                        $map_descriptor.=$quantity_text.'Unknown item';
                    if($link!='')
                    {
                        if(!$wiki)
                            $map_descriptor.='</a>'."\n";
                        else
                            $map_descriptor.=']]';
                    }
                    $map_descriptor.='</td></tr>'."\n";
                }
                $map_descriptor.='</table>'."\n";
            }
            if(isset($quests_meta[$id]['rewards']['reputation']))
                foreach($quests_meta[$id]['rewards']['reputation'] as $reputation)
                {
                    if($reputation['point']<0)
                        $map_descriptor.='Less reputation in: '.$reputation['type'];
                    else
                        $map_descriptor.='More reputation in: '.$reputation['type'];
                }
            if(isset($quests_meta[$id]['rewards']['allow']))
                foreach($quests_meta[$id]['rewards']['allow'] as $allow)
                {
                    if($allow=='clan')
                        $map_descriptor.='Able to create clan';
                    else
                        $map_descriptor.='Allow '.$allow;
                }
            $map_descriptor.='</div></div>'."\n";
        }
        $map_descriptor.='</td>'."\n";
        $map_descriptor.='</tr>'."\n";
    }
    if($showbot)
        $map_descriptor.='<tr>
            <td colspan="7" class="item_list_endline item_list_title_type_normal"></td>
        </tr>
        </table>'."\n";
    else
        $map_descriptor.='<tr>
            <td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
        </tr>
        </table>'."\n";
    return $map_descriptor;
} 
