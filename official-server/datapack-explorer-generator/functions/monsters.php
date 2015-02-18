<?php
function monsterAndLevelToDisplay($monster,$full=true,$wiki=false)
{
    global $monster_meta,$base_datapack_explorer_site_path,$datapack_path,$base_datapack_site_path,$type_meta,$base_datapack_site_http;
    $map_descriptor='';
    if(isset($monster_meta[$monster['monster']]))
    {
        $monster_full=$monster_meta[$monster['monster']];
        $map_descriptor.='<table class="item_list item_list_type_'.$monster_full['type'][0].' map_list">
        <tr class="item_list_title item_list_title_type_'.$monster_full['type'][0].'">
            <th';
        if(!$full)
            $map_descriptor.=' colspan="3"';
        $map_descriptor.='></th>
        </tr>';
        $map_descriptor.='<tr class="value">';
        if($full)
        {
            $map_descriptor.='<td>';
            $map_descriptor.='<table class="monsterforevolution">';
            if(!$wiki)
            {
                if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/front.png'))
                    $map_descriptor.='<tr><td><center><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$monster['monster'].'/front.png" width="80" height="80" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" /></a></center></td></tr>';
                else if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/front.gif'))
                    $map_descriptor.='<tr><td><center><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$monster['monster'].'/front.gif" width="80" height="80" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" /></a></center></td></tr>';
                $map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html">'.$monster_full['name'].'</a></td></tr>';
            }
            else
            {
                if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/front.png'))
                    $map_descriptor.='<tr><td><center>[[Monsters:'.$monster_full['name'].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster['monster'].'/front.png" width="80" height="80" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" />]]</center></td></tr>';
                else if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/front.gif'))
                    $map_descriptor.='<tr><td><center>[[Monsters:'.$monster_full['name'].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster['monster'].'/front.gif" width="80" height="80" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" />]]</center></td></tr>';
                $map_descriptor.='<tr><td class="evolution_name">[[Monsters:'.$monster_full['name'].'|'.$monster_full['name'].']]</td></tr>';
            }

            $map_descriptor.='<tr><td>';
            $type_list=array();
            if(!$wiki)
                foreach($monster_meta[$monster['monster']]['type'] as $type)
                    if(isset($type_meta[$type]))
                        $type_list[]='<span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
            else
                foreach($monster_meta[$monster['monster']]['type'] as $type)
                    if(isset($type_meta[$type]))
                        $type_list[]='<span class="type_label type_label_'.$type.'">[[Monsters type '.$type_meta[$type]['english_name'].']]</span>';
            $map_descriptor.='<div class="type_label_list">'.implode(' ',$type_list).'</div></td></tr>';
            
            $map_descriptor.='<tr><td>Level '.$monster['level'].'</td></tr>';
            $map_descriptor.='</table>';
            $map_descriptor.='</td>';
        }
        else
        {
            if(!$wiki)
            {
                if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/small.png'))
                    $map_descriptor.='<td><center><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$monster['monster'].'/small.png" width="32" height="32" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" /></a></center></td>';
                else if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/small.gif'))
                    $map_descriptor.='<td><center><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$monster['monster'].'/small.gif" width="32" height="32" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" /></a></center></td>';
                $map_descriptor.='<td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html">'.$monster_full['name'].'</a></td>';
            }
            else
            {
                if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/small.png'))
                    $map_descriptor.='<td><center>[[Monsters:'.$monster_full['name'].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster['monster'].'/small.png" width="32" height="32" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" />]]</center></td>';
                else if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/small.gif'))
                    $map_descriptor.='<td><center>[[Monsters:'.$monster_full['name'].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster['monster'].'/small.gif" width="32" height="32" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" />]]</center></td>';
                $map_descriptor.='<td class="evolution_name">[[Monsters:'.$monster_full['name'].'|'.$monster_full['name'].']]</td>';
            }

            $map_descriptor.='<td>Level '.$monster['level'].'</td>';
        }
        $map_descriptor.='</tr>';
        $map_descriptor.='<tr>
            <th class="item_list_endline item_list_title item_list_title_type_'.$monster_full['type'][0].'"';
        if(!$full)
            $map_descriptor.=' colspan="3"';
        $map_descriptor.='>';
        $map_descriptor.='</th>
        </tr>
        </table>';
    }
    return $map_descriptor;
}
