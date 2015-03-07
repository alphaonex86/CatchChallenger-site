<?php

function map_to_wiki_name($map)
{
    global $maps_list,$duplicate_detection_name,$duplicate_detection_name_and_zone,$current_lang,$zone_meta;
    $zone=$maps_list[$map]['zone'];
    $name=$maps_list[$map]['name'][$current_lang];
    if(!isset($duplicate_detection_name[$current_lang][$name]))
        return $map;
    if($zone!='' && isset($zone_meta[$zone]))
        $final_name_with_zone=$zone_meta[$zone]['name'][$current_lang].' '.$name;
    else
        $final_name_with_zone=$name;
    if(!isset($duplicate_detection_name_and_zone[$current_lang][$final_name_with_zone]))
        return $map;

    if($duplicate_detection_name[$current_lang][$name]==1)
        return $name;
    if($duplicate_detection_name_and_zone[$current_lang][$final_name_with_zone]==1)
        return $final_name_with_zone;
    $map=str_replace('.tmx','',$map);
    return $map;
} 
