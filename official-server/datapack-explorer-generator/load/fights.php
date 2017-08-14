<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load fights'."\n");

$fight_meta=array();
$monster_to_fight=array();
$item_to_fight=array();

$dir = $datapack_path.'map/main/';
$dh  = opendir($dir);
while (false !== ($maindatapackcode = readdir($dh)))
{
    if(is_dir($datapack_path.'map/main/'.$maindatapackcode) && preg_match('#^[a-z0-9]+$#isU',$maindatapackcode))
    {
        $xmlFightList=getXmlList($datapack_path.'map/main/'.$maindatapackcode.'/fight/');
        foreach($xmlFightList as $file)
        {
            $content=file_get_contents($datapack_path.'map/main/'.$maindatapackcode.'/fight/'.$file);
            preg_match_all('#<fight id="[0-9]+".*</fight>#isU',$content,$entry_list);
            foreach($entry_list[0] as $entry)
            {
                $start='';
                $win='';
                $cash=0;
                $items=array();
                if(!preg_match('#<fight id="[0-9]+".*</fight>#isU',$entry))
                    continue;
                $id=preg_replace('#^.*<fight id="([0-9]+)".*</fight>.*$#isU','$1',$entry);
                if(isset($fight_meta[$id]))
                {
                    echo 'duplicate id '.$id.' for the fight'."\n";
                    continue;
                }
                if(preg_match('#<gain cash="([0-9]+)"#isU',$entry))
                    $cash=preg_replace('#^.*<gain cash="([0-9]+)".*$#isU','$1',$entry);
                preg_match_all('#<gain item="([0-9]+)"#isU',$entry,$items_list);
                foreach($items_list[1] as $entry_item)
                {
                    $item=preg_replace('#^.*<gain item="([0-9]+)".*$#isU','$1',$entry_item);
                    $items[]=array('item'=>$item,'quantity'=>1);
                    if(!isset($item_to_fight[$item]))
                        $item_to_fight[$item]=array();
                    $item_to_fight[$item][$maindatapackcode][]=$id;
                    ksort($item_to_fight[$item]);
                }
                if(preg_match('#<start( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</start>#isU',$entry))
                    $start=preg_replace('#^.*<start( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</start>.*$#isU','$3',$entry);
                if(preg_match('#<win( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</win>#isU',$entry))
                    $win=preg_replace('#^.*<win( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</win>.*$#isU','$3',$entry);
                $start=str_replace('<![CDATA[','',$start);
                $win=str_replace('<![CDATA[','',$win);
                $monsters=array();
                preg_match_all('#<monster .*/>#isU',$entry,$monster_text_list);
                foreach($monster_text_list[0] as $monster_text)
                {
                    $monster=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$monster_text);
                    $level=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$monster_text);
                    $monsters[]=array('monster'=>$monster,'level'=>$level);
                    $monster_to_fight[$monster][$maindatapackcode][]=$id;
                }
                $fight_meta[$maindatapackcode][$id]=array('start'=>$start,'win'=>$win,'cash'=>$cash,'monsters'=>$monsters,'items'=>$items);
            }
        }
    }
}
closedir($dh);
ksort($fight_meta);
ksort($item_to_fight);
