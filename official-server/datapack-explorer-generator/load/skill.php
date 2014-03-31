<?php
if(!isset($datapackexplorergeneratorinclude))
	exit;

$skill_meta=array();
if(file_exists($datapack_path.'monsters/skill.xml'))
{
	$content=file_get_contents($datapack_path.'monsters/skill.xml');
	preg_match_all('#<skill.*</skill>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $entry)
	{
		if(!preg_match('#id="[0-9]+"#isU',$entry))
			continue;
		$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$entry);
		$type='normal';
		if(preg_match('#type="[^"]+"#isU',$entry))
			$type=preg_replace('#^.*type="([^"]+)".*$#isU','$1',$entry);
		if(!isset($type_meta[$type]))
			$type='normal';

		$base_luck=100;
		if(preg_match('#<skill[^>]+luck="([0-9]+)%?"#isU',$entry))
			$base_luck=preg_replace('#^.*<skill[^>]+luck="([0-9]+)%?".*$#isU','$1',$entry);
		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
		$level_list=array();
		preg_match_all('#<level.*</level>#isU',$entry,$temp_level_list);
		foreach($temp_level_list[0] as $level_text)
		{
			if(!preg_match('#number="[0-9]+"#isU',$level_text))
				continue;
			$number=preg_replace('#^.*number="([0-9]+)".*$#isU','$1',$level_text);
			$endurance=40;
			if(preg_match('#endurance="[0-9]+"#isU',$level_text))
				$endurance=preg_replace('#^.*endurance="([0-9]+)".*$#isU','$1',$level_text);
			$base_level_luck=$base_luck;
			if(preg_match('#<level[^>]+luck="([0-9]+)%?"#isU',$level_text))
				$base_level_luck=preg_replace('#^.*<level[^>]+luck="([0-9]+)%?".*$#isU','$1',$level_text);
			$sp=0;
			if(preg_match('#sp="[0-9]+"#isU',$level_text))
				$sp=preg_replace('#^.*sp="([0-9]+)".*$#isU','$1',$level_text);
			$life_quantity=0;
			if(preg_match('#life[^>]+quantity="(-?[0-9]+%?)"#isU',$level_text))
				$life_quantity=preg_replace('#^.*life[^>]+quantity="(-?[0-9]+%?)".*$#isU','$1',$level_text);
			$level_list[$number]=array('endurance'=>$endurance,'sp'=>$sp,'life_quantity'=>$life_quantity,'base_level_luck'=>$base_level_luck);
		}
		$skill_meta[$id]=array('type'=>$type,'name'=>$name,'level_list'=>$level_list);
	}
}