<?php

function map_to_wiki_name($map)
{
    global $maps_list,$duplicate_detection_name,$duplicate_detection_name_and_zone,$current_lang;
    if(!isset($duplicate_detection_name[$maps_list[$map]['name'][$current_lang]]) || !isset($duplicate_detection_name_and_zone[$maps_list[$map]['zone'].'_'.$maps_list[$map]['name'][$current_lang]]))
        return $maps_list[$map]['name'][$current_lang];
    if($duplicate_detection_name[$maps_list[$map]['name'][$current_lang]]==1)
        return $maps_list[$map]['name'][$current_lang];
    if($duplicate_detection_name_and_zone[$maps_list[$map]['zone'].'_'.$maps_list[$map]['name'][$current_lang]]==1)
        return $maps_list[$map]['zone'].'_'.$maps_list[$map]['name'][$current_lang];
    $map=str_replace('.tmx','',$map);
    return $map;
} 
