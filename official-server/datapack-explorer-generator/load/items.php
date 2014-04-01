<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load items'."\n");

$item_meta=array();
$item_to_trap=array();
$item_to_regeneration=array();
if(file_exists($datapack_path.'items/items.xml'))
{
	$content=file_get_contents($datapack_path.'items/items.xml');
	preg_match_all('#<item[^>]*>.*</item>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $entry)
	{
		if(!preg_match('#<item[^>]*id="[0-9]+".*</item>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<item[^>]*id="([0-9]+)".*</item>.*$#isU','$1',$entry);
		if(isset($item_meta[$id]))
		{
			echo 'duplicate id '.$id.' for item'."\n";
			continue;
		}
		$price=0;
		if(preg_match('#<item[^>]*price="[0-9]+".*</item>#isU',$entry))
			$price=preg_replace('#^.*<item[^>]*price="([0-9]+)".*</item>.*$#isU','$1',$entry);
		if(preg_match('#<item[^>]*image="[^"]+".*</item>#isU',$entry))
			$image=preg_replace('#^.*<item[^>]*image="([^"]+)".*</item>.*$#isU','$1',$entry);
		else
			$image='';
		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
		if(!preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
			continue;
		$description=text_operation_first_letter_upper(preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry));
		if(preg_match('#<trap[^>]+/>#isU',$entry))
		{
			$temp_text=preg_replace('#^.*(<trap[^>]+/>).*$#isU','$1',$entry);
			if(preg_match('#bonus_rate="([0-9]+(\.[0-9]+)?)"#isU',$temp_text))
			{
				$bonus_rate=preg_replace('#^.*bonus_rate="([0-9]+(\.[0-9]+)?)".*$#isU','$1',$temp_text);
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description,'trap'=>$bonus_rate);
				$item_to_trap[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description,'trap'=>$bonus_rate);
			}
			else
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description);
		}
		else if(preg_match('#<repel[^>]+/>#isU',$entry))
		{
			$temp_text=preg_replace('#^.*(<repel[^>]+/>).*$#isU','$1',$entry);
			if(preg_match('#step="([0-9]+(\.[0-9]+)?)"#isU',$temp_text))
			{
				$step=preg_replace('#^.*step="([0-9]+(\.[0-9]+)?)".*$#isU','$1',$temp_text);
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description,'repel'=>$step);
			}
			else
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description);
		}
		else
		{
			$effect=array();
			if(preg_match('#<regeneration[^>]+/>#isU',$entry))
			{
				$temp_text=preg_replace('#^.*(<regeneration[^>]+/>).*$#isU','$1',$entry);
				if(preg_match('#hp="([0-9]+(\.[0-9]+)?|all)"#isU',$temp_text))
					$effect['regeneration']=preg_replace('#^.*hp="([0-9]+(\.[0-9]+)?|all)".*$#isU','$1',$temp_text);
			}
			if(preg_match('#<hp[^>]+/>#isU',$entry))
			{
				$temp_text=preg_replace('#^.*(<hp[^>]+/>).*$#isU','$1',$entry);
				if(preg_match('#add="([0-9]+(\.[0-9]+)?|all)"#isU',$temp_text))
					$effect['regeneration']=preg_replace('#^.*add="([0-9]+(\.[0-9]+)?|all)".*$#isU','$1',$temp_text);
			}
			if(preg_match('#<buff[^>]+/>#isU',$entry))
			{
				$temp_text=preg_replace('#^.*(<buff[^>]+/>).*$#isU','$1',$entry);
				if(preg_match('#remove="([0-9]+(\.[0-9]+)?|all)"#isU',$temp_text))
					$effect['buff']=preg_replace('#^.*remove="([0-9]+(\.[0-9]+)?|all)".*$#isU','$1',$temp_text);
			}
			if(count($effect)>0)
			{
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description,'effect'=>$effect);
				$item_to_regeneration[$id]=$effect;
			}
			else
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description);
		}
	}
}