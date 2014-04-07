<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load other'."\n");

$xmlZoneList=getXmlList($datapack_path.'map/zone/');
$zone_meta=array();
foreach($xmlZoneList as $file)
{
	$content=file_get_contents($datapack_path.'map/zone/'.$file);
	if(!preg_match('#^([^"\\.]+).xml$#isU',$file))
		continue;
	$code=preg_replace('#^([^"\\.]+).xml$#isU','$1',$file);
	if(!preg_match('#<name( lang="en")?>([^<]+)</name>#isU',$content))
		continue;
	$name=preg_replace('#^.*<name( lang="en")?>([^<]+)</name>.*$#isU','$2',$content);
	$name=preg_replace("#[\n\t\r]+#is",'',$name);
	$zone_meta[$code]=array('name'=>$name);
}

$fight_meta=array();
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
		$fight_meta[$id]=array('start'=>$start,'win'=>$win,'cash'=>$cash,'monsters'=>$monsters);
	}
}

$industries_meta=array();
$xmlFightList=getXmlList($datapack_path.'industries/');
foreach($xmlFightList as $file)
{
	$content=file_get_contents($datapack_path.'industries/'.$file);
	preg_match_all('#<industrialrecipe id="[0-9]+".*</industrialrecipe>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		if(!preg_match('#<industrialrecipe id="([0-9]+)".*</industrialrecipe>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<industrialrecipe id="([0-9]+)".*</industrialrecipe>.*$#isU','$1',$entry);
		if(isset($industries_meta[$id]))
		{
			echo 'duplicate id '.$id.' for the industries'."\n";
			continue;
		}
		if(!preg_match('#time="([0-9]+)"#isU',$entry))
			continue;
		$time=preg_replace('#^.*time="([0-9]+)".*$#isU','$1',$entry);
		if(!preg_match('#cycletobefull="([0-9]+)"#isU',$entry))
			continue;
		$cycletobefull=preg_replace('#^.*cycletobefull="([0-9]+)".*$#isU','$1',$entry);
		$resources=array();
		preg_match_all('#<resource id="([0-9]+)" quantity="([0-9]+)" />#isU',$entry,$monster_text_list);
		foreach($monster_text_list[0] as $monster_text)
		{
			$item=preg_replace('#^.*<resource id="([0-9]+)" quantity="([0-9]+)" />.*$#isU','$1',$monster_text);
			$quantity=preg_replace('#^.*<resource id="([0-9]+)" quantity="([0-9]+)" />.*$#isU','$2',$monster_text);
			$resources[$item]=$quantity;
		}
		$products=array();
		preg_match_all('#<product id="([0-9]+)" quantity="([0-9]+)" />#isU',$entry,$monster_text_list);
		foreach($monster_text_list[0] as $monster_text)
		{
			$item=preg_replace('#^.*<product id="([0-9]+)" quantity="([0-9]+)" />.*$#isU','$1',$monster_text);
			$quantity=preg_replace('#^.*<product id="([0-9]+)" quantity="([0-9]+)" />.*$#isU','$2',$monster_text);
			$products[$item]=$quantity;
		}
		$industries_meta[$id]=array('time'=>$time,'cycletobefull'=>$cycletobefull,'resources'=>$resources,'products'=>$products);
	}
}

$start_meta=array();
if(file_exists($datapack_path.'player/start.xml'))
{
	$content=file_get_contents($datapack_path.'player/start.xml');
	preg_match_all('#<start>.*</start>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
		if(!preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
			continue;
		$description=text_operation_first_letter_upper(preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry));
		if(!preg_match('#<map.*file="([^"]+)".*/>#isU',$entry))
			continue;
		if(!preg_match('#<map.*x="([0-9]+)".*/>#isU',$entry))
			continue;
		if(!preg_match('#<map.*y="([0-9]+)".*/>#isU',$entry))
			continue;
		$map=preg_replace('#^.*<map.*file="([^"]+)".*/>.*$#isU','$1',$entry);
		$x=preg_replace('#^.*<map.*x="([0-9]+)".*/>.*$#isU','$1',$entry);
		$y=preg_replace('#^.*<map.*y="([0-9]+)".*/>.*$#isU','$1',$entry);
		$forcedskin=array();
		if(preg_match('#<forcedskin.*value="([^"]+)".*/>#isU',$entry))
			$forcedskin=explode(';',preg_replace('#^.*<forcedskin.*value="([^"]+)".*/>.*$#isU','$1',$entry));
		$cash=0;
		if(preg_match('#<cash.*value="([^"]+)".*/>#isU',$entry))
			$cash=preg_replace('#^.*<cash.*value="([^"]+)".*/>.*$#isU','$1',$entry);
		
		preg_match_all('#<monster id="[0-9]+" level="[0-9]+" captured_with="[0-9]+" />#isU',$entry,$monster_list);
		$monsters=array();
		foreach($monster_list as $monster)
		{
			if(!preg_match('#<monster.*id="([0-9]+)".*/>#isU',$entry))
				continue;
			if(!preg_match('#<monster.*level="([0-9]+)".*/>#isU',$entry))
				continue;
			if(!preg_match('#<monster.*captured_with="([0-9]+)".*/>#isU',$entry))
				continue;
			$id=preg_replace('#^.*<monster.*id="([0-9]+)".*/>.*$#isU','$1',$entry);
			$level=preg_replace('#^.*<monster.*level="([0-9]+)".*/>.*$#isU','$1',$entry);
			$captured_with=preg_replace('#^.*<monster.*captured_with="([0-9]+)".*/>.*$#isU','$1',$entry);
			$skill_added=0;
			$attack_list=array();
			if(isset($monster_meta[$id]['attack_list']))
				foreach($monster_meta[$id]['attack_list'] as $learn_at_level=>$skill_list)
				{
					foreach($skill_list as $skill)
					{
						if($learn_at_level<=$level)
						{
							$attack_list[]=$skill;
							$skill_added++;
						}
						if(count($attack_list)>=4)
							break;
					}
					if(count($attack_list)>=4)
						break;
				}
			$monsters[]=array('id'=>$id,'level'=>$level,'captured_with'=>$captured_with,'attack_list'=>$attack_list);
		}
		if(count($monsters)<=0)
			continue;

		preg_match_all('#<reputation type="[a-z]+" level="[0-9]+" />#isU',$entry,$reputation_list);
		$reputations=array();
		foreach($reputation_list as $reputation)
		{
			if(!preg_match('#<reputation.*type="([a-z]+)".*/>#isU',$entry))
				continue;
			if(!preg_match('#<reputation.*level="([0-9]+)".*/>#isU',$entry))
				continue;
			$type=preg_replace('#^.*<reputation.*type="([a-z]+)".*/>.*$#isU','$1',$entry);
			$level=preg_replace('#^.*<reputation.*level="([0-9]+)".*/>.*$#isU','$1',$entry);
			$reputations[]=array('type'=>$type,'level'=>$level);
		}

		preg_match_all('#<item id="[0-9]+" quantity="[0-9]+" />#isU',$entry,$item_list);
		$items=array();
		foreach($item_list as $item)
		{
			if(!preg_match('#<item.*id="([0-9]+)".*/>#isU',$entry))
				continue;
			if(!preg_match('#<item.*quantity="([0-9]+)".*/>#isU',$entry))
				continue;
			$id=preg_replace('#^.*<item.*id="([^"]+)".*/>.*$#isU','$1',$entry);
			$quantity=preg_replace('#^.*<item.*quantity="([0-9]+)".*/>.*$#isU','$1',$entry);
			$items[]=array('id'=>$id,'quantity'=>$quantity);
		}
		
		if(!preg_match('#\.tmx$#',$map))
			$map=$map.'.tmx';
		$start_meta[]=array('name'=>$name,'description'=>$description,'map'=>$map,'x'=>$x,'y'=>$y,'forcedskin'=>$forcedskin,'cash'=>$cash,'monsters'=>$monsters,'reputations'=>$reputations,'items'=>$items);
	}
}

$quests_meta=array();
$monster_to_quests=array();
$items_to_quests=array();
$items_to_quests_for_step=array();
$xmlFightList=getDefinitionXmlList($datapack_path.'quests/');
foreach($xmlFightList as $file)
{
	if(!preg_match('#([0-9]+)#is',$file))
		continue;
	$id=preg_replace('#^.*([0-9]+).*$#is','$1',$file);
	if(isset($quests_meta[$id]))
	{
		echo 'duplicate id '.$id.' for the quests'."\n";
		continue;
	}
	$content=file_get_contents($datapack_path.'quests/'.$file);
	$repeatable=false;
	if(preg_match('#repeatable="(yes|true)"#isU',$content))
		$repeatable=true;
	if(!preg_match('#bot="([0-9]+)"#isU',$content))
		continue;
	$bot=preg_replace('#^.*bot="([0-9]+)".*$#isU','$1',$content);
	if(!preg_match('#<name( lang="en")?>.*</name>#isU',$content))
		continue;
	$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
	$name=str_replace('<![CDATA[','',$name);
	$name=str_replace(']]>','',$name);

	$requirements=array();
	preg_match_all('#<requirements.*</requirements>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		preg_match_all('#<quest id="([0-9]+)"[^>]+/>#isU',$entry,$item_text_list);
		foreach($item_text_list[0] as $item_text)
		{
			if(!isset($requirements['quests']))
				$requirements['quests']=array();
			$quest_id=preg_replace('#^.*<quest id="([0-9]+)"[^>]+/>.*$#isU','$1',$item_text);
			$requirements['quests'][]=$quest_id;
		}
	}

	$steps=array();
	preg_match_all('#<step id="[0-9]+".*</step>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		$tempbot=$bot;
		if(preg_match('#bot="([0-9]+)"#isU',$entry))
			$tempbot=preg_replace('#^.*bot="([0-9]+)".*</step>.*$#is','$1',$entry);
		$id_step=preg_replace('#^.*<step id="([0-9]+)".*</step>.*$#is','$1',$entry);
		$text=preg_replace('#^.*<text( lang="en")?>(.*)</text>.*$#isU','$2',$entry);
		$text=str_replace('<![CDATA[','',$text);
		$text=str_replace(']]>','',$text);
		$items=array();
		preg_match_all('#<item id="([0-9]+)"[^>]+/>#isU',$entry,$item_text_list);
		foreach($item_text_list[0] as $item_text)
		{
			$item=preg_replace('#^.*<item id="([0-9]+)"[^>]+/>.*$#isU','$1',$item_text);
			if(preg_match('#quantity="([0-9]+)"#isU',$item_text))
				$quantity=preg_replace('#^.*quantity="([0-9]+)".*$#isU','$1',$item_text);
			else
				$quantity=1;
			if(preg_match('#monster="([0-9]+)"#isU',$item_text))
			{
				$monster=preg_replace('#^.*monster="([0-9]+)".*$#isU','$1',$item_text);
				if(preg_match('#rate="([0-9]+)%?"#isU',$item_text))
					$rate=preg_replace('#^.*rate="([0-9]+)%?".*$#isU','$1',$item_text);
				else
					$rate=100;
			}
			else
				$monster=0;
			if(!isset($items_to_quests_for_step[$item]))
				$items_to_quests_for_step[$item]=array();
			if($monster!=0)
			{
				$items[]=array('item'=>$item,'quantity'=>$quantity,'monster'=>$monster,'rate'=>$rate);
				if(!isset($monster_to_quests[$monster]))
					$monster_to_quests[$monster]=array();
				$monster_to_quests[$monster][]=array('quest'=>$id,'item'=>$item,'quantity'=>$quantity,'rate'=>$rate);
				$items_to_quests_for_step[$item][]=array('quest'=>$id,'quantity'=>$quantity,'monster'=>$monster,'rate'=>$rate);
			}
			else
			{
				$items[]=array('item'=>$item,'quantity'=>$quantity);
				$items_to_quests_for_step[$item][]=array('quest'=>$id,'quantity'=>$quantity);
			}
		}
		$steps[$id_step]=array('text'=>$text,'bot'=>$tempbot,'items'=>$items);
	}

	$rewards=array();
	preg_match_all('#<rewards.*</rewards>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		preg_match_all('#<item id="([0-9]+)"[^>]+/>#isU',$entry,$item_text_list);
		foreach($item_text_list[0] as $item_text)
		{
			if(!isset($rewards['items']))
				$rewards['items']=array();
			$item=preg_replace('#^.*<item id="([0-9]+)"[^>]+/>.*$#isU','$1',$item_text);
			if(!preg_match('#quantity="([0-9]+)"#isU',$content))
				$quantity=preg_replace('#^.*quantity="([0-9]+)".*$#isU','$1',$item_text);
			else
				$quantity=1;
			if(!isset($items_to_quests[$item]))
				$items_to_quests[$item]=array();
			$items_to_quests[$item][$id]=$quantity;
			$rewards['items'][]=array('item'=>$item,'quantity'=>$quantity);
		}
		preg_match_all('#<reputation type="([^"]+)" point="(-?[0-9]+)"[^>]+/>#isU',$entry,$item_text_list);
		foreach($item_text_list[0] as $item_text)
		{
			if(!isset($rewards['reputation']))
				$rewards['reputation']=array();
			$type=preg_replace('#^.*<reputation type="([^"]+)" point="(-?[0-9]+)"[^>]+/>.*$#isU','$1',$item_text);
			$point=preg_replace('#^.*<reputation type="([^"]+)" point="(-?[0-9]+)"[^>]+/>.*$#isU','$2',$item_text);
			$rewards['reputation'][]=array('type'=>$type,'point'=>$point);
		}
	}
	$quests_meta[$id]=array('name'=>$name,'repeatable'=>$repeatable,'steps'=>$steps,'rewards'=>$rewards,'requirements'=>$requirements);
}
