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

function textToProperty($text)
{
    $propertyList=array();
    preg_match_all('#<property .*/>#isU',$text,$propertyListText);
    foreach($propertyListText[0] as $entry)
    {
        $name=preg_replace('#^.* name="([^"]+)".*$#isU','$1',$entry);
        $value=preg_replace('#^.* value="([^"]+)".*$#isU','$1',$entry);
        $propertyList[$name]=$value;
    }
    return $propertyList;
}

function monsterMapOrderGreater($monsterA,$monsterB)
{
    $countMonsterA=count($monsterA['sub']);
    $countMonsterB=count($monsterB['sub']);
    if($countMonsterA==$countMonsterB)
    {
        if($countMonsterA==0)
            return 0;
        if(implode($monsterA['sub'],'')<implode($monsterB['sub'],''))
            return 1;
        else
            return -1;
    }
    if($countMonsterA<$countMonsterB)
        return 1;
    else
        return -1;
}

