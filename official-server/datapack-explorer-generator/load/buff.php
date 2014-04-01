<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load buff'."\n");

$buff_meta=array();
if(file_exists($datapack_path.'monsters/buff.xml'))
{
	$content=file_get_contents($datapack_path.'monsters/buff.xml');
	preg_match_all('#<buff.*</buff>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $entry)
	{
		if(!preg_match('#id="[0-9]+"#isU',$entry))
			continue;
		$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$entry);
		if(isset($buff_meta[$id]))
		{
			echo 'duplicate id '.$id.' for the buff'."\n";
			continue;
		}
		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
		if(!preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
			continue;
		$description=preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry);

		$default_capture_bonus=1;
		if(preg_match('#<effect[^>]+capture_bonus="([0-9]+(\.[0-9]+)?)"#isU',$entry))
			$default_capture_bonus=preg_replace('#^.*<effect[^>]+capture_bonus="([0-9]+(\.[0-9]+)?)".*$#isU','$1',$entry);
		$default_duration='ThisFight';
		if(preg_match('#<effect[^>]+duration="(ThisFight|NumberOfTurn|Always)"#isU',$entry))
			$default_duration=preg_replace('#^.*<effect[^>]+duration="(ThisFight|NumberOfTurn|Always)".*$#isU','$1',$entry);
		$default_durationNumberOfTurn=0;
		if(preg_match('#<effect[^>]+durationNumberOfTurn="([0-9]+)"#isU',$entry))
			$default_durationNumberOfTurn=preg_replace('#^.*<effect[^>]+durationNumberOfTurn="([0-9]+)".*$#isU','$1',$entry);

		$level_list=array();
		preg_match_all('#<level.*</level>#isU',$entry,$temp_level_list);
		foreach($temp_level_list[0] as $level_text)
		{
			if(!preg_match('#number="[0-9]+"#isU',$level_text))
				continue;
			$number=preg_replace('#^.*number="([0-9]+)".*$#isU','$1',$level_text);

			$capture_bonus=$default_capture_bonus;
			if(preg_match('#<level[^>]+capture_bonus="([0-9]+(\.[0-9]+)?)"#isU',$level_text))
				$capture_bonus=preg_replace('#^.*<level[^>]+capture_bonus="([0-9]+(\.[0-9]+)?)".*$#isU','$1',$level_text);
			$duration=$default_duration;
			if(preg_match('#<level[^>]+duration="(ThisFight|NumberOfTurn|Always)"#isU',$level_text))
				$duration=preg_replace('#^.*<level[^>]+duration="(ThisFight|NumberOfTurn|Always)".*$#isU','$1',$level_text);
			$durationNumberOfTurn=$default_durationNumberOfTurn;
			if(preg_match('#<level[^>]+durationNumberOfTurn="([0-9]+)"#isU',$level_text))
				$durationNumberOfTurn=preg_replace('#^.*<level[^>]+durationNumberOfTurn="([0-9]+)".*$#isU','$1',$level_text);

			$effect=array(
				'inFight'=>array(),
				'inWalk'=>array(),
				);
			preg_match_all('#(<inFight[^>]+>)#isU',$level_text,$temp_inFight_list);
			foreach($temp_inFight_list[0] as $inFight)
			{
				if(preg_match('#hp="((-|\\+)?[0-9]+)%?"#isU',$inFight))
					$effect['inFight']['hp']=array('value'=>preg_replace('#^.*hp="((-|\\+)?[0-9]+%?)".*$#isU','$1',$inFight));
				if(preg_match('#defense="((-|\\+)?[0-9]+)%?"#isU',$inFight))
					$effect['inFight']['defense']=array('value'=>preg_replace('#^.*defense="((-|\\+)?[0-9]+%?)".*$#isU','$1',$inFight));
				if(preg_match('#attack="((-|\\+)?[0-9]+)%?"#isU',$inFight))
					$effect['inFight']['attack']=array('value'=>preg_replace('#^.*attack="((-|\\+)?[0-9]+%?)".*$#isU','$1',$inFight));
			}

			preg_match_all('#(<inWalk[^>]+>)#isU',$level_text,$temp_inWalk_list);
			foreach($temp_inWalk_list[0] as $inWalk)
			{
				$steps=0;
				if(preg_match('#steps="((-|\\+)?[0-9]+)%?"#isU',$inWalk))
					$steps=preg_replace('#^.*steps="((-|\\+)?[0-9]+%?)".*$#isU','$1',$inWalk);
				if($steps>0)
				{
					if(preg_match('#hp="((-|\\+)?[0-9]+)%?"#isU',$inWalk))
						$effect['inWalk']['hp']=array('steps'=>$steps,'value'=>preg_replace('#^.*hp="((-|\\+)?[0-9]+%?)".*$#isU','$1',$inWalk));
					if(preg_match('#defense="((-|\\+)?[0-9]+)%?"#isU',$inWalk))
						$effect['inWalk']['defense']=array('steps'=>$steps,'value'=>preg_replace('#^.*defense="((-|\\+)?[0-9]+%?)".*$#isU','$1',$inWalk));
					if(preg_match('#attack="((-|\\+)?[0-9]+)%?"#isU',$inWalk))
						$effect['inWalk']['attack']=array('steps'=>$steps,'value'=>preg_replace('#^.*attack="((-|\\+)?[0-9]+%?)".*$#isU','$1',$inWalk));
				}
			}

			$level_list[$number]=array('capture_bonus'=>$capture_bonus,'duration'=>$duration,'durationNumberOfTurn'=>$durationNumberOfTurn,'effect'=>$effect);
		}
		$buff_meta[$id]=array('name'=>$name,'level_list'=>$level_list);
	}
}
