<?php

function map_to_wiki_name($map)
{
    global $maps_list,$duplicate_detection_name,$duplicate_detection_name_and_zone;
    if(!isset($duplicate_detection_name[$maps_list[$map]['name']]) || !isset($duplicate_detection_name_and_zone[$maps_list[$map]['zone'].'_'.$maps_list[$map]['name']]))
        return $maps_list[$map]['name'];
    if($duplicate_detection_name[$maps_list[$map]['name']]==1)
        return $maps_list[$map]['name'];
    if($duplicate_detection_name_and_zone[$maps_list[$map]['zone'].'_'.$maps_list[$map]['name']]==1)
        return $maps_list[$map]['zone'].'_'.$maps_list[$map]['name'];
    $map=str_replace('.tmx','',$map);
    return $map;
} 
