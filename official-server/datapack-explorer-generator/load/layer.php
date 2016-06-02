<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load layer'."\n");

$layer_meta=array();
$layer_event=array();
$layer_toSearch=array();
if(file_exists($datapack_path.'map/layers.xml'))
{
    $content=file_get_contents($datapack_path.'map/layers.xml');
    $new_content=array();
    preg_match_all('#<monstersCollision(.*)</monstersCollision>#isU',$content,$temp_text_list);
    $new_content=array_merge($new_content,$temp_text_list[1]);
    preg_match_all('#<monstersCollision(.*)/>#iU',$content,$temp_text_list);
    $new_content=array_merge($new_content,$temp_text_list[1]);
    foreach($new_content as $layer_content)
    {
        if(!preg_match('#monsterType="([^"]+)"#',$layer_content))
            continue;
        if(!preg_match('#layer="([^"]+)"#',$layer_content))
            $layer='';
        else
            $layer=preg_replace('#^.*layer="([^"]+)".*$#isU','$1',$layer_content);
        if(!preg_match('#type="(walkOn|actionOn)"#',$layer_content))
            continue;
        $monsterType=explode(';',preg_replace('#^.*monsterType="([^"]+)".*$#isU','$1',$layer_content));
        if(count($monsterType)>0)
        {
            $type=preg_replace('#^.*type="(walkOn|actionOn)".*$#isU','$1',$layer_content);
            if($type=='actionOn')
            {
                if(!preg_match('#item="([0-9]+)"#',$layer_content))
                    continue;
                else
                    $item=preg_replace('#^.*item="([0-9]+)".*$#isU','$1',$layer_content);
            }
            foreach($monsterType as $entry)
            {
                if(!in_array($entry,$layer_toSearch))
                    $layer_toSearch[]=$entry;
                if(!isset($layer_meta[$entry]) || (isset($layer_meta[$entry]['item']) && $type=='walkOn'))
                {
                    if($type=='actionOn')
                        $layer_meta[$entry]=array('item'=>$item,'layer'=>$layer);
                    else
                        $layer_meta[$entry]=array('layer'=>$layer);
                }
            }
            preg_match_all('#<event(.*)/>#iU',$layer_content,$layer_content_list);
            foreach($layer_content_list[1] as $event_content)
            {
                if(!preg_match('#id="([^"]+)"#',$event_content))
                    continue;
                if(!preg_match('#value="([^"]+)"#',$event_content))
                    continue;
                if(!preg_match('#monsterType="([^"]+)"#',$event_content))
                    continue;
                $monsterType_sub=explode(';',preg_replace('#^.*monsterType="([^"]+)".*$#isU','$1',$event_content));
                $value=preg_replace('#^.*value="([^"]+)".*$#isU','$1',$event_content);
                $id=preg_replace('#^.*id="([^"]+)".*$#isU','$1',$event_content);
                foreach($monsterType_sub as $entry)
                {
                    if(!in_array($entry,$layer_toSearch))
                        $layer_toSearch[]=$entry;
                    if(!isset($layer_event[$entry]) && !isset($layer_meta[$entry]))
                    {
                        if($type=='actionOn')
                            $layer_event[$entry]=array('item'=>$item,'layer'=>$layer,'monsterType'=>$monsterType[0],'value'=>$value,'id'=>$id);
                        else
                            $layer_event[$entry]=array('layer'=>$layer,'monsterType'=>$monsterType[0],'value'=>$value,'id'=>$id);
                    }
                }
            }
        }
    }
}

ksort($layer_meta);
ksort($layer_event);
ksort($layer_toSearch);
