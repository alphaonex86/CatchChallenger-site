<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load reputation'."\n");

$reputation_meta=array();
if(file_exists($datapack_path.'player/reputation.xml'))
{
	$content=file_get_contents($datapack_path.'player/reputation.xml');
	preg_match_all('#<reputation type="[a-z]+".*</reputation>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		if(!preg_match('#<reputation type="[a-z]+".*</reputation>#isU',$entry))
			continue;
		$type=preg_replace('#^.*<reputation type="([a-z]+)".*</reputation>.*$#isU','$1',$entry);
		preg_match_all('#<level point="-?[0-9]+".*</level>#isU',$entry,$level_list);
		$reputation_meta_list=array();
		foreach($level_list[0] as $level)
		{
			if(!preg_match('#<level point="-?[0-9]+".*</level>#isU',$level))
				continue;
			$point=preg_replace('#^.*<level point="(-?[0-9]+)".*</level>.*$#isU','$1',$level);
			if(!preg_match('#<text( lang="en")?>.*</text>#isU',$level))
				continue;
			$text=preg_replace('#^.*<text( lang="en")?>(.*)</text>.*$#isU','$2',$level);
            $text=str_replace('<![CDATA[','',str_replace(']]>','',$text));
			$reputation_meta_list[(int)$point]=$text;
		}
		if(count($reputation_meta_list)>0)
		{
			ksort($reputation_meta_list);
			$level_offset=0;
			foreach($reputation_meta_list as $point=>$text)
			{
				if($point>=0)
					break;
				$level_offset++;
			}
			$reputation_meta_list_by_level=array();
			foreach($reputation_meta_list as $point=>$text)
			{
				$reputation_meta_list_by_level[-$level_offset]=$text;
				$level_offset--;
			}
			unset($reputation_meta_list);
			$reputation_meta[$type]=$reputation_meta_list_by_level;
			unset($reputation_meta_list_by_level);
		}
	}
}