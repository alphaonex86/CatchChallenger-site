<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load fights'."\n");

$fight_meta=array();
$item_to_fight=array();
$xmlFightList=getXmlList($datapack_path.'fight/');
foreach($xmlFightList as $file)
{
	$content=file_get_contents($datapack_path.'fight/'.$file);
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
            $item_to_fight[$item][]=$id;
        }
		if(preg_match('#<start( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</start>#isU',$entry))
			$start=preg_replace('#^.*<start( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</start>.*$#isU','$3',$entry);
		if(preg_match('#<win( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</win>#isU',$entry))
			$win=preg_replace('#^.*<win( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</win>.*$#isU','$3',$entry);
		$start=str_replace('<![CDATA[','',$start);
		$win=str_replace('<![CDATA[','',$win);
		$monsters=array();
		preg_match_all('#<monster id="([0-9]+)" level="([0-9]+)" />#isU',$entry,$monster_text_list);
		foreach($monster_text_list[0] as $monster_text)
		{
			$monster=preg_replace('#^.*<monster id="([0-9]+)" level="([0-9]+)" />.*$#isU','$1',$monster_text);
			$level=preg_replace('#^.*<monster id="([0-9]+)" level="([0-9]+)" />.*$#isU','$2',$monster_text);
			$monsters[]=array('monster'=>$monster,'level'=>$level);
		}
		$fight_meta[$id]=array('start'=>$start,'win'=>$win,'cash'=>$cash,'monsters'=>$monsters,'items'=>$items);
	}
}
